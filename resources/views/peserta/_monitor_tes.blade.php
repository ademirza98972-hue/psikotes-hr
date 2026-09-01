{{-- Monitor integritas tes: fullscreen enforcement + activity logging --}}
{{-- Requires $sesiId in scope (auto-inherited from parent view) --}}
<script>
(function () {
    const LOG_URL      = "{{ route('peserta.tes.logAktivitas', $sesiId) }}";
    const CSRF         = "{{ csrf_token() }}";
    const FS_KEY       = 'tes_fs_aktif';
    const sudahMulai   = sessionStorage.getItem(FS_KEY) === '1';
    const autoEnter    = sessionStorage.getItem('tes_auto_enter') === '1';
    if (autoEnter) sessionStorage.removeItem('tes_auto_enter');

    let disconnected  = false;
    let isNavigating  = false;

    // Tandai navigasi normal (form submit / link) agar tidak dicatat sebagai mencurigakan
    document.addEventListener('submit', function () { isNavigating = true; });
    document.addEventListener('click', function (e) {
        var a = e.target.closest('a[href]');
        if (a && a.href && !/^(javascript:|#)/.test(a.href)) isNavigating = true;
    }, true);

    // Dipanggil setelah AJAX navigation selesai untuk reset flag
    window._monitorResetNav = function () { isNavigating = false; };

    // ===== Kirim log ke backend =====
    function kirimLog(tipe, keterangan) {
        const fd = new FormData();
        fd.append('_token', CSRF);
        fd.append('tipe', tipe);
        if (keterangan) fd.append('keterangan', keterangan);

        if (tipe === 'halaman_ditutup') {
            navigator.sendBeacon(LOG_URL, fd);
            return;
        }
        fetch(LOG_URL, { method: 'POST', body: fd, keepalive: true }).catch(function () {});
    }

    // ===== Masuk fullscreen =====
    function masukFullscreen() {
        return document.documentElement.requestFullscreen().catch(function () {});
    }

    // ===== Overlay blocking =====
    var overlay = document.createElement('div');
    overlay.id = 'fs-overlay';
    overlay.style.cssText = [
        'position:fixed', 'inset:0', 'z-index:99999',
        'background:#2C5F6F', 'color:white',
        'display:none', 'flex-direction:column',
        'align-items:center', 'justify-content:center',
        'font-family:Work Sans,system-ui,sans-serif',
        'text-align:center', 'padding:20px',
    ].join(';');
    overlay.innerHTML =
        '<div style="font-size:44px;margin-bottom:14px">&#9888;</div>' +
        '<div id="fs-title" style="font-size:22px;font-weight:700;margin-bottom:8px">Mulai Tes</div>' +
        '<div id="fs-msg" style="font-size:14px;opacity:.85;margin-bottom:28px;max-width:380px">' +
        'Tes harus dikerjakan dalam mode layar penuh. Klik tombol di bawah untuk memulai.' +
        '</div>' +
        '<button id="fs-btn" style="background:white;color:#2C5F6F;border:none;padding:14px 32px;' +
        'font-size:16px;font-weight:700;border-radius:10px;cursor:pointer;">Mulai Tes — Layar Penuh</button>';
    document.body.appendChild(overlay);

    function tampilOverlay(judul, pesan, tombol) {
        document.getElementById('fs-title').textContent = judul;
        document.getElementById('fs-msg').textContent   = pesan;
        document.getElementById('fs-btn').textContent   = tombol;
        overlay.style.display = 'flex';
    }
    function sembunyikanOverlay() { overlay.style.display = 'none'; }

    function doFullscreen() {
        masukFullscreen().then(function () {
            sembunyikanOverlay();
            sessionStorage.setItem(FS_KEY, '1');
        });
    }

    // Klik di mana saja pada overlay (termasuk area numpad Kraepelin di baliknya)
    overlay.addEventListener('click', doFullscreen);

    // Keyboard numpad / angka juga trigger (fallback)
    document.addEventListener('keydown', function onKey(e) {
        if (e.key === 'Escape' || overlay.style.display !== 'flex') return;
        doFullscreen();
        document.removeEventListener('keydown', onKey);
    });

    // ===== Init =====
    if (autoEnter) {
        // Dari panduan: langsung auto-enter fullscreen pada interaksi pertama, tanpa overlay
        function onFirstInteraction(e) {
            if (e.key === 'Escape') return;
            masukFullscreen().then(function () { sessionStorage.setItem(FS_KEY, '1'); });
            document.removeEventListener('click',   onFirstInteraction, true);
            document.removeEventListener('keydown', onFirstInteraction, true);
        }
        document.addEventListener('click',   onFirstInteraction, true);
        document.addEventListener('keydown', onFirstInteraction, true);
    } else if (!sudahMulai) {
        tampilOverlay('Mulai Tes', 'Tes harus dikerjakan dalam mode layar penuh. Klik tombol di bawah untuk memulai.', 'Mulai Tes — Layar Penuh');
    } else {
        tampilOverlay('Lanjutkan Tes', 'Klik di mana saja atau tekan tombol di bawah untuk kembali ke layar penuh.', 'Lanjutkan — Layar Penuh');
    }

    // ===== Event: keluar fullscreen (bukan karena navigasi) =====
    document.addEventListener('fullscreenchange', function () {
        if (!document.fullscreenElement && !isNavigating) {
            kirimLog('keluar_fullscreen', null);
            tampilOverlay(
                'Kembali ke Layar Penuh',
                'Anda keluar dari mode layar penuh. Klik tombol di bawah untuk melanjutkan tes.',
                'Kembali — Layar Penuh'
            );
        }
    });

    // ===== Event: pindah tab =====
    document.addEventListener('visibilitychange', function () {
        if (document.hidden && !disconnected) {
            kirimLog('pindah_tab', null);
        }
    });

    // ===== Event: network =====
    window.addEventListener('offline', function () {
        disconnected = true;
        kirimLog('disconnect', null);
    });
    window.addEventListener('online', function () {
        disconnected = false;
        kirimLog('reconnect', null);
    });

    // ===== Event: halaman ditutup (bukan navigasi tes biasa) =====
    window.addEventListener('beforeunload', function () {
        if (!isNavigating) {
            kirimLog('halaman_ditutup', null);
        }
    });

    // ===== Cegah DevTools & right-click =====
    document.addEventListener('contextmenu', function (e) { e.preventDefault(); });
    document.addEventListener('keydown', function (e) {
        var key = e.key ? e.key.toUpperCase() : '';
        if (e.key === 'F12' ||
            (e.ctrlKey && e.shiftKey && (key === 'I' || key === 'J' || key === 'C')) ||
            (e.ctrlKey && key === 'U')) {
            e.preventDefault();
            kirimLog('pindah_tab', 'devtools:' + e.key);
        }
    });
})();
</script>

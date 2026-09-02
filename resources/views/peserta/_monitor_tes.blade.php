{{-- Monitor integritas tes: fullscreen enforcement + activity logging --}}
{{-- Requires $sesiId in scope (auto-inherited from parent view) --}}
<script>
(function () {
    // Bersihkan listener dari instance _monitor_tes sebelumnya (setelah AJAX swap)
    if (window._monitorTeardown) window._monitorTeardown();

    const LOG_URL    = "{{ route('peserta.tes.logAktivitas', $sesiId) }}";
    const CSRF       = "{{ csrf_token() }}";
    const FS_KEY     = 'tes_fs_aktif';
    const sudahMulai = sessionStorage.getItem(FS_KEY) === '1';
    const autoEnter  = sessionStorage.getItem('tes_auto_enter') === '1';
    if (autoEnter) sessionStorage.removeItem('tes_auto_enter');

    let disconnected = false;
    let isNavigating = false;

    function onSubmit()      { isNavigating = true; }
    function onLinkClick(e)  {
        var a = e.target.closest('a[href]');
        if (a && a.href && !/^(javascript:|#)/.test(a.href)) isNavigating = true;
    }

    document.addEventListener('submit', onSubmit);
    document.addEventListener('click',  onLinkClick, true);

    window._monitorResetNav = function () { isNavigating = false; };

    function kirimLog(tipe, keterangan) {
        var fd = new FormData();
        fd.append('_token', CSRF);
        fd.append('tipe', tipe);
        if (keterangan) fd.append('keterangan', keterangan);
        if (tipe === 'halaman_ditutup') { navigator.sendBeacon(LOG_URL, fd); return; }
        fetch(LOG_URL, { method: 'POST', body: fd, keepalive: true }).catch(function () {});
    }

    function masukFullscreen() {
        return document.documentElement.requestFullscreen().catch(function () {});
    }

    var overlay = document.createElement('div');
    overlay.id = 'fs-overlay';
    overlay.style.cssText = [
        'position:fixed','inset:0','z-index:99999',
        'background:#2C5F6F','color:white',
        'display:none','flex-direction:column',
        'align-items:center','justify-content:center',
        'font-family:Work Sans,system-ui,sans-serif',
        'text-align:center','padding:20px',
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

    overlay.addEventListener('click', doFullscreen);

    function onKeyOverlay(e) {
        if (e.key === 'Escape' || overlay.style.display !== 'flex') return;
        doFullscreen();
        document.removeEventListener('keydown', onKeyOverlay);
    }
    document.addEventListener('keydown', onKeyOverlay);

    if (autoEnter) {
        // Dari panduan via AJAX swap: sudah fullscreen, listener ini hanya safety net
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

    function onFullscreenChange() {
        if (!document.fullscreenElement && !isNavigating) {
            kirimLog('keluar_fullscreen', null);
            tampilOverlay('Kembali ke Layar Penuh', 'Anda keluar dari mode layar penuh. Klik tombol di bawah untuk melanjutkan tes.', 'Kembali — Layar Penuh');
        }
    }
    function onVisibilityChange() {
        if (document.hidden && !disconnected) kirimLog('pindah_tab', null);
    }
    function onOffline()      { disconnected = true;  kirimLog('disconnect', null); }
    function onOnline()       { disconnected = false; kirimLog('reconnect',  null); }
    function onBeforeUnload() { if (!isNavigating) kirimLog('halaman_ditutup', null); }
    function onContextMenu(e) { e.preventDefault(); }
    function onKeyDevTools(e) {
        var key = e.key ? e.key.toUpperCase() : '';
        if (e.key === 'F12' ||
            (e.ctrlKey && e.shiftKey && (key === 'I' || key === 'J' || key === 'C')) ||
            (e.ctrlKey && key === 'U')) {
            e.preventDefault();
            kirimLog('pindah_tab', 'devtools:' + e.key);
        }
    }

    document.addEventListener('fullscreenchange',  onFullscreenChange);
    document.addEventListener('visibilitychange',  onVisibilityChange);
    window.addEventListener('offline',             onOffline);
    window.addEventListener('online',              onOnline);
    window.addEventListener('beforeunload',        onBeforeUnload);
    document.addEventListener('contextmenu',       onContextMenu);
    document.addEventListener('keydown',           onKeyDevTools);

    window._monitorTeardown = function () {
        document.removeEventListener('submit',         onSubmit);
        document.removeEventListener('click',          onLinkClick,       true);
        document.removeEventListener('fullscreenchange', onFullscreenChange);
        document.removeEventListener('visibilitychange', onVisibilityChange);
        window.removeEventListener('offline',          onOffline);
        window.removeEventListener('online',           onOnline);
        window.removeEventListener('beforeunload',     onBeforeUnload);
        document.removeEventListener('contextmenu',    onContextMenu);
        document.removeEventListener('keydown',        onKeyOverlay);
        document.removeEventListener('keydown',        onKeyDevTools);
        window._monitorTeardown = null;
    };

    // Intercept submit → requestFullscreen() saat gesture masih aktif → swap body tanpa navigasi
    var pandForm = document.querySelector('form[data-panduan-form]');
    if (pandForm) {
        pandForm.addEventListener('submit', function (e) {
            e.preventDefault();
            sessionStorage.setItem('tes_auto_enter', '1');
            sessionStorage.setItem(FS_KEY, '1');
            masukFullscreen();

            isNavigating = true; // tandai agar beforeunload tidak mencatat sebagai mencurigakan

            var fd = new FormData(pandForm);
            fetch(pandForm.action, { method: 'POST', body: fd, credentials: 'same-origin', redirect: 'follow' })
                .then(function (res) {
                    return res.text().then(function (html) { return { html: html, url: res.url }; });
                })
                .then(function (data) {
                    var doc = new DOMParser().parseFromString(data.html, 'text/html');

                    // Cek apakah target pakai layout yang sama (ada Alpine / sidebar)
                    // Kraepelin standalone tidak punya x-data atau aside
                    var samLayout = doc.body.querySelector('[x-data], #soal-main, aside');
                    if (!samLayout) {
                        // Target beda layout (kraepelin standalone) — navigasi biasa
                        window.location.href = data.url;
                        return;
                    }

                    history.pushState({}, doc.title || '', data.url);
                    document.title = doc.title || '';
                    document.body.innerHTML = doc.body.innerHTML;

                    // Re-eksekusi inline scripts di body baru (termasuk _monitor_tes baru)
                    Array.from(document.body.querySelectorAll('script')).forEach(function (s) {
                        var ns = document.createElement('script');
                        if (s.src) { ns.src = s.src; ns.async = false; }
                        else { ns.textContent = s.textContent; }
                        s.parentNode.replaceChild(ns, s);
                    });

                    // Re-init Alpine untuk x-data directives
                    if (window.Alpine) Alpine.initTree(document.body);
                })
                .catch(function () {
                    // Fallback: submit normal
                    sessionStorage.setItem('tes_auto_enter', '1');
                    pandForm.submit();
                });
        });
    }
})();
</script>

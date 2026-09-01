<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kraepelin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Work Sans', sans-serif;
            background: #f1f5f9;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* HEADER */
        .header {
            background: #2C5F6F;
            color: white;
            padding: 10px 20px;
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            flex-shrink: 0;
        }
        .header-left { }
        .header-center {
            text-align: center;
            font-size: 14px;
            font-weight: 700;
        }
        .header-right {
            text-align: right;
        }
        .header-title { font-weight: 700; font-size: 14px; }
        .header-sub { font-size: 11px; opacity: 0.75; margin-top: 1px; }
        .header-timer {
            font-size: 22px;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            line-height: 1;
        }
        .header-timer-label {
            font-size: 10px;
            opacity: 0.7;
            margin-top: 2px;
        }

        /* PROGRESS BAR */
        .progress-bar-wrap {
            height: 6px;
            background: #e2e8f0;
            flex-shrink: 0;
        }
        .progress-bar-fill {
            height: 100%;
            background: #2C5F6F;
            transition: width 0.3s;
        }

        /* GRID AREA */
        .grid-area {
            flex: 1;
            overflow-y: auto;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 20px 20px 320px 20px;
        }
        .grid-column {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }
        .cell {
            width: 52px;
            height: 52px;
            border-radius: 10px;
            border: 2px solid #cbd5e1;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            transition: all 0.15s;
        }
        .cell.aktif {
            background: #fef08a;
            border-color: #f59e0b;
            transform: scale(1.08);
        }

        /* NUMPAD */
        .numpad-wrap {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e2e8f0;
            padding: 12px 20px 16px;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
        }
        .numpad-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            max-width: 280px;
            margin: 0 auto;
        }
        .numpad-btn {
            height: 60px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            background: #f8fafc;
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            cursor: pointer;
            transition: all 0.1s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .numpad-btn:active { transform: scale(0.93); background: #e2e8f0; }
        .numpad-btn.del { background: #fee2e2; border-color: #fca5a5; color: #ef4444; }
        .numpad-btn.del:active { background: #fca5a5; }
        .numpad-btn.wide { grid-column: span 2; }
    </style>
    <meta name="debug-sisa" content="{{ $sisaDetikPerKolom ?? 'null' }} / detikPerKolom={{ $detikPerKolom ?? 'null' }}">
</head>
<body>

{{-- State data untuk AJAX column swap --}}
<div id="k-state" style="display:none"
     data-angka='@json($angka)'
     data-kolom-ke="{{ $kolomAktif['nomor'] }}"
     data-kolom-selesai="{{ $kolomSelesai }}"
     data-total-kolom="{{ $totalKolom }}"
     data-sisa-per="{{ $sisaDetikPerKolom ?? $detikPerKolom }}"
     data-sisa-total="{{ $sisaDetikKeseluruhan ?? 0 }}"></div>

{{-- HEADER --}}
<div class="header">
    <div class="header-left">
        <div class="header-title">Kraepelin (KRP)</div>
        <div class="header-sub">{{ $nama_sesi }}</div>
    </div>
    <div class="header-center">
        Kolom {{ $kolomSelesai + 1 }} dari {{ $totalKolom }}
    </div>
    <div class="header-right">
        <div class="header-timer" id="timer-display">
            @php
                $menitSisa = floor($sisaDetikKeseluruhan ?? 0 / 60);
                $detikSisaDisplay = ($sisaDetikKeseluruhan ?? 0) % 60;
            @endphp
            @if ($adaTimerPerKolom)
                {{ gmdate('i:s', $detikPerKolom) }}
            @else
                {{ str_pad($menitSisa, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($detikSisaDisplay, 2, '0', STR_PAD_LEFT) }}
            @endif
        </div>
        <div class="header-timer-label">
            @if ($adaTimerPerKolom) Per Kolom @else Keseluruhan @endif
        </div>
    </div>
</div>

{{-- PROGRESS BAR --}}
<div class="progress-bar-wrap">
    <div class="progress-bar-fill"
         style="width: {{ $totalKolom > 0 ? round($kolomSelesai / $totalKolom * 100) : 0 }}%">
    </div>
</div>

{{-- GRID ANGKA --}}
<div class="grid-area" id="grid-area">
    <div class="grid-column" id="grid-column">
        {{-- Render dari atas ke bawah: angka[n-1] ... angka[0] --}}
        @for ($i = count($angka) - 1; $i >= 0; $i--)
            <div class="cell" id="cell-{{ $i }}">{{ $angka[$i] }}</div>
        @endfor
    </div>
</div>

{{-- FORM TERSEMBUNYI --}}
<form method="POST"
      action="{{ route('peserta.tes.simpanKolomGrid', $sesiId) }}"
      id="form-kraepelin">
    @csrf
    <input type="hidden" name="alat_tes_id" value="{{ $alatTesId }}">
    <input type="hidden" name="kolom_ke" value="{{ $kolomAktif['nomor'] }}">
    @foreach ($angka as $i => $val)
        <input type="hidden" name="angka[{{ $i }}]" value="{{ $val }}">
    @endforeach
    @for ($i = 0; $i < count($angka) - 1; $i++)
        <input type="hidden" name="jawaban[{{ $i }}]"
               id="jawaban-{{ $i }}" value="">
    @endfor
</form>

{{-- NUMPAD --}}
<div class="numpad-wrap">
    <div class="numpad-grid">
        <button type="button" class="numpad-btn" onclick="numpadClick(7)">7</button>
        <button type="button" class="numpad-btn" onclick="numpadClick(8)">8</button>
        <button type="button" class="numpad-btn" onclick="numpadClick(9)">9</button>
        <button type="button" class="numpad-btn" onclick="numpadClick(4)">4</button>
        <button type="button" class="numpad-btn" onclick="numpadClick(5)">5</button>
        <button type="button" class="numpad-btn" onclick="numpadClick(6)">6</button>
        <button type="button" class="numpad-btn" onclick="numpadClick(1)">1</button>
        <button type="button" class="numpad-btn" onclick="numpadClick(2)">2</button>
        <button type="button" class="numpad-btn" onclick="numpadClick(3)">3</button>
        <button type="button" class="numpad-btn wide" onclick="numpadClick(0)">0</button>
        @if ($adaTimerPerKolom)
        <button type="button" class="numpad-btn del"
                style="opacity:0.3; cursor:not-allowed;" disabled>←</button>
        @else
        <button type="button" class="numpad-btn del"
                onclick="numpadBack()">←</button>
        @endif
    </div>
</div>

<script>
// let (bukan const) karena diupdate setiap ganti kolom via AJAX
let angka = @json($angka);
const adaTimerPerKolom = @json($adaTimerPerKolom);
const detikPerKolom    = @json($detikPerKolom ?? 60);
const sisaDetikPerKolom       = @json($sisaDetikPerKolom ?? $detikPerKolom);
const sisaDetikKeseluruhan    = @json($sisaDetikKeseluruhan ?? 0);
const FORM_ACTION = document.getElementById('form-kraepelin').action;

let currentIndex  = 0;
let timerInterval = null;

// ===== HIGHLIGHT =====
function updateHighlight() {
    var rows = angka.length - 1;
    for (var i = 0; i < angka.length; i++) {
        var el = document.getElementById('cell-' + i);
        if (el) el.classList.remove('aktif');
    }
    if (currentIndex < rows) {
        var elB = document.getElementById('cell-' + currentIndex);
        var elA = document.getElementById('cell-' + (currentIndex + 1));
        if (elB) elB.classList.add('aktif');
        if (elA) elA.classList.add('aktif');
    }
}

// ===== SCROLL =====
function scrollToActive() {
    var el        = document.getElementById('cell-' + currentIndex);
    var container = document.getElementById('grid-area');
    if (!el || !container) return;
    var targetScroll = el.offsetTop - container.offsetTop - container.clientHeight / 2 + el.offsetHeight / 2;
    container.scrollTo({ top: Math.max(0, targetScroll), behavior: 'smooth' });
}

// ===== NUMPAD =====
function numpadClick(digit) {
    var rows = angka.length - 1;
    if (currentIndex >= rows) return;
    var input = document.getElementById('jawaban-' + currentIndex);
    if (input) input.value = digit;
    currentIndex++;
    if (currentIndex >= rows) { submitForm(); return; }
    updateHighlight();
    scrollToActive();
}

function numpadBack() {
    if (currentIndex <= 0) return;
    currentIndex--;
    var input = document.getElementById('jawaban-' + currentIndex);
    if (input) input.value = '';
    updateHighlight();
    scrollToActive();
}

// ===== TIMER =====
function startTimerWith(sisaPer, sisaTotal) {
    var display = document.getElementById('timer-display');
    if (!display) return;
    var sisa = adaTimerPerKolom ? sisaPer : sisaTotal;
    timerInterval = setInterval(function () {
        sisa--;
        display.textContent = String(Math.floor(sisa / 60)).padStart(2, '0') + ':' + String(sisa % 60).padStart(2, '0');
        if (sisa <= 0) { clearInterval(timerInterval); submitForm(); }
    }, 1000);
}

// ===== UPDATE DOM SETELAH AJAX =====
function updateGrid(newAngka) {
    var col = document.getElementById('grid-column');
    col.innerHTML = '';
    for (var i = newAngka.length - 1; i >= 0; i--) {
        var div = document.createElement('div');
        div.className = 'cell';
        div.id = 'cell-' + i;
        div.textContent = newAngka[i];
        col.appendChild(div);
    }
}

function rebuildFormInputs(newAngka, newKolomKe) {
    var form = document.getElementById('form-kraepelin');
    form.querySelector('[name=kolom_ke]').value = newKolomKe;
    form.querySelectorAll('[name^="angka["], [name^="jawaban["]').forEach(function (el) { el.remove(); });
    newAngka.forEach(function (val, i) {
        var inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'angka[' + i + ']'; inp.value = val;
        form.appendChild(inp);
    });
    for (var i = 0; i < newAngka.length - 1; i++) {
        var inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'jawaban[' + i + ']'; inp.id = 'jawaban-' + i; inp.value = '';
        form.appendChild(inp);
    }
}

// ===== SUBMIT (AJAX — tidak reload halaman, fullscreen tetap aktif) =====
function submitForm() {
    clearInterval(timerInterval);
    var form = document.getElementById('form-kraepelin');
    var fd   = new FormData(form);

    fetch(FORM_ACTION, { method: 'POST', body: fd, credentials: 'same-origin', redirect: 'follow' })
        .then(function (res) { return res.text().then(function (html) { return { html: html, url: res.url }; }); })
        .then(function (data) {
            var doc   = new DOMParser().parseFromString(data.html, 'text/html');
            var state = doc.getElementById('k-state');
            if (!state) { window.location.href = data.url; return; } // tes selesai

            var newAngka     = JSON.parse(state.dataset.angka);
            var newKolomKe   = state.dataset.kolomKe;
            var newSelesai   = parseInt(state.dataset.kolomSelesai);
            var newTotal     = parseInt(state.dataset.totalKolom);
            var newSisaPer   = parseInt(state.dataset.sisaPer);
            var newSisaTotal = parseInt(state.dataset.sisaTotal);

            angka = newAngka;
            updateGrid(newAngka);
            rebuildFormInputs(newAngka, newKolomKe);

            document.querySelector('.header-center').textContent = 'Kolom ' + (newSelesai + 1) + ' dari ' + newTotal;
            document.querySelector('.progress-bar-fill').style.width = (newTotal > 0 ? Math.round(newSelesai / newTotal * 100) : 0) + '%';

            currentIndex = 0;
            updateHighlight();
            setTimeout(scrollToActive, 80);
            startTimerWith(newSisaPer, newSisaTotal);
        })
        .catch(function () { form.submit(); }); // fallback jika fetch gagal
}

// ===== INIT =====
window.addEventListener('load', function () {
    updateHighlight();
    startTimerWith(sisaDetikPerKolom, sisaDetikKeseluruhan);
    setTimeout(scrollToActive, 200);

    document.addEventListener('keydown', function (e) {
        var numpadKeys = {
            'Numpad0':0,'Numpad1':1,'Numpad2':2,'Numpad3':3,'Numpad4':4,
            'Numpad5':5,'Numpad6':6,'Numpad7':7,'Numpad8':8,'Numpad9':9,
            'Digit0':0,'Digit1':1,'Digit2':2,'Digit3':3,'Digit4':4,
            'Digit5':5,'Digit6':6,'Digit7':7,'Digit8':8,'Digit9':9,
        };
        if (e.code in numpadKeys) { e.preventDefault(); numpadClick(numpadKeys[e.code]); }
        if (e.code === 'Backspace' || e.code === 'NumpadDecimal') { e.preventDefault(); numpadBack(); }
    });
});
</script>

@include('peserta._monitor_tes')
</body>
</html>

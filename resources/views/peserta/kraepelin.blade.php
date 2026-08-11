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
    </style>
    <meta name="debug-sisa" content="{{ $sisaDetikPerKolom ?? 'null' }} / detikPerKolom={{ $detikPerKolom ?? 'null' }}">
</head>
<body>

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
        <button type="button" class="numpad-btn" onclick="numpadClick(0)">0</button>
        @if ($adaTimerPerKolom)
        <button type="button" class="numpad-btn del"
                style="opacity:0.3; cursor:not-allowed;" disabled>←</button>
        @else
        <button type="button" class="numpad-btn del"
                onclick="numpadBack()">←</button>
        @endif
        <button type="button" class="numpad-btn confirm"
                onclick="submitForm()"
                style="background:#2C5F6F;color:white;border-color:#2C5F6F;">✓</button>
    </div>
</div>

<script>
// Data dari PHP
const angka = @json($angka);
const totalRows = angka.length - 1;
const adaTimerPerKolom = @json($adaTimerPerKolom);
const detikPerKolom = @json($detikPerKolom ?? 60);
const sisaDetikPerKolom = @json($sisaDetikPerKolom ?? $detikPerKolom);
const sisaDetikKeseluruhan = @json($sisaDetikKeseluruhan ?? 0);

let currentIndex = 0;

// ===== HIGHLIGHT =====
function updateHighlight() {
    for (let i = 0; i < angka.length; i++) {
        const el = document.getElementById('cell-' + i);
        if (el) el.classList.remove('aktif');
    }
    if (currentIndex < totalRows) {
        const elBawah = document.getElementById('cell-' + currentIndex);
        const elAtas  = document.getElementById('cell-' + (currentIndex + 1));
        if (elBawah) elBawah.classList.add('aktif');
        if (elAtas)  elAtas.classList.add('aktif');
    }
}

// ===== SCROLL =====
function scrollToActive() {
    const el = document.getElementById('cell-' + currentIndex);
    const container = document.getElementById('grid-area');
    if (!el || !container) return;

    const numpadHeight = 240;
    const headerHeight = 80;
    const containerHeight = container.clientHeight;

    const elOffsetInContainer = el.offsetTop - container.offsetTop;
    const targetScroll = elOffsetInContainer
        - (containerHeight / 2)
        + (el.offsetHeight / 2);

    container.scrollTo({
        top: Math.max(0, targetScroll),
        behavior: 'smooth'
    });
}

// ===== NUMPAD =====
function numpadClick(digit) {
    if (currentIndex >= totalRows) return;
    const input = document.getElementById('jawaban-' + currentIndex);
    if (input) input.value = digit;
    currentIndex++;
    if (currentIndex >= totalRows) {
        document.getElementById('form-kraepelin').submit();
        return;
    }
    updateHighlight();
    scrollToActive();
}

function numpadBack() {
    if (currentIndex <= 0) return;
    currentIndex--;
    const input = document.getElementById('jawaban-' + currentIndex);
    if (input) input.value = '';
    updateHighlight();
    scrollToActive();
}

function submitForm() {
    document.getElementById('form-kraepelin').submit();
}

// ===== TIMER =====
let timerInterval = null;

function startTimer() {
    const display = document.getElementById('timer-display');
    if (!display) return;

    if (adaTimerPerKolom) {
        // Countdown per kolom dari sisaDetikPerKolom (persisten dari server)
        let sisa = sisaDetikPerKolom;
        timerInterval = setInterval(() => {
            sisa--;
            const m = Math.floor(sisa / 60).toString().padStart(2, '0');
            const s = (sisa % 60).toString().padStart(2, '0');
            display.textContent = m + ':' + s;
            if (sisa <= 0) {
                clearInterval(timerInterval);
                submitForm();
            }
        }, 1000);
    } else {
        // Countdown keseluruhan dari sisaDetikKeseluruhan
        let sisa = sisaDetikKeseluruhan;
        timerInterval = setInterval(() => {
            sisa--;
            const m = Math.floor(sisa / 60).toString().padStart(2, '0');
            const s = (sisa % 60).toString().padStart(2, '0');
            display.textContent = m + ':' + s;
            if (sisa <= 0) {
                clearInterval(timerInterval);
                submitForm();
            }
        }, 1000);
    }
}

// ===== INIT =====
window.addEventListener('load', function() {
    updateHighlight();
    startTimer();

    // Scroll ke posisi aktif setelah render
    setTimeout(scrollToActive, 200);

    // Keyboard numpad
    document.addEventListener('keydown', function(e) {
        const numpadKeys = {
            'Numpad0': 0, 'Numpad1': 1, 'Numpad2': 2,
            'Numpad3': 3, 'Numpad4': 4, 'Numpad5': 5,
            'Numpad6': 6, 'Numpad7': 7, 'Numpad8': 8,
            'Numpad9': 9,
            'Digit0': 0, 'Digit1': 1, 'Digit2': 2,
            'Digit3': 3, 'Digit4': 4, 'Digit5': 5,
            'Digit6': 6, 'Digit7': 7, 'Digit8': 8,
            'Digit9': 9,
        };
        if (e.code in numpadKeys) {
            e.preventDefault();
            numpadClick(numpadKeys[e.code]);
        }
        if (e.code === 'Backspace' || e.code === 'NumpadDecimal') {
            e.preventDefault();
            numpadBack();
        }
    });

});
</script>

</body>
</html>

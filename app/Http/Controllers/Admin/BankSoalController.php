<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlatTes;
use App\Models\BobotOpsiDimensi;
use App\Models\DimensiAlatTes;
use App\Models\OpsiJawaban;
use App\Models\Soal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class BankSoalController extends Controller
{
    public function index(Request $request): View
    {
        $alatTesSemua = AlatTes::orderBy('nama')->get();

        $idDipilih = (int) $request->query('alat_tes_id', 0);

        $totalSemuaSoal = Soal::count();

        $alatTesTerpilih = null;
        $daftarSoal = [];
        $kelompokSoalSemua = [];

        if ($idDipilih > 0) {
            $alatTesTerpilih = $alatTesSemua->firstWhere('id', $idDipilih);
            if ($alatTesTerpilih) {
                $soalRow = Soal::with(['alatTes', 'opsiJawaban.bobotOpsiDimensi.dimensi'])
                    ->where('alat_tes_id', $idDipilih)
                    ->orderBy('urutan')
                    ->orderBy('id')
                    ->get();
                $daftarSoal = $soalRow->map(fn (Soal $s) => $this->mapSoalUntukView($s, $alatTesTerpilih->format_dasar))->all();
            }
        } else {
            $semuaSoal = Soal::with(['alatTes', 'opsiJawaban.bobotOpsiDimensi.dimensi'])
                ->orderBy('alat_tes_id')
                ->orderBy('urutan')
                ->orderBy('id')
                ->get()
                ->groupBy('alat_tes_id');

            foreach ($semuaSoal as $alatId => $rows) {
                $alat = $alatTesSemua->firstWhere('id', $alatId);
                if (!$alat) {
                    continue;
                }
                $kelompokSoalSemua[$alatId] = [
                    'alat'       => $alat,
                    'soal'       => $rows->map(fn (Soal $s) => $this->mapSoalUntukView($s, $alat->format_dasar))->all(),
                    'jumlahSoal' => $rows->count(),
                ];
            }
        }

        return view('admin.bank-soal.index', [
            'alatTesSemua'      => $alatTesSemua,
            'kelompokSoalSemua' => $kelompokSoalSemua,
            'alatTesTerpilih'   => $alatTesTerpilih,
            'daftarSoal'        => $daftarSoal,
            'totalSemuaSoal'    => $totalSemuaSoal,
        ]);
    }

    public function tambah(int $alatTesId): View
    {
        $alatTes = AlatTes::findOrFail($alatTesId);

        return view('admin.bank-soal.tambah', [
            'alatTes' => $alatTes,
        ]);
    }

    public function simpan(Request $request, int $alatTesId)
    {
        $alatTes = AlatTes::findOrFail($alatTesId);
        $format = $alatTes->format_dasar;

        $rules = $format === 'Forced Choice'
            ? $this->forcedChoiceRules()
            : $this->pilihanAtauLikertRules();

        $validated = $request->validate($rules);

        DB::transaction(function () use ($alatTes, $format, $validated, $request) {
            $nomorBerikutnya = (int) (Soal::where('alat_tes_id', $alatTes->id)->max('nomor') ?? 0) + 1;
            $urutanBerikutnya = (int) (Soal::where('alat_tes_id', $alatTes->id)->max('urutan') ?? 0) + 1;

            if ($format === 'Forced Choice') {
                $soal = Soal::create([
                    'alat_tes_id' => $alatTes->id,
                    'nomor'       => $nomorBerikutnya,
                    'teks_soal'   => $validated['pernyataan_a'],
                    'tipe_format' => 'forced_choice',
                    'urutan'      => $urutanBerikutnya,
                ]);

                $opsiA = OpsiJawaban::create([
                    'soal_id'   => $soal->id,
                    'teks_opsi' => $validated['pernyataan_a'],
                    'urutan'    => 1,
                ]);
                $opsiB = OpsiJawaban::create([
                    'soal_id'   => $soal->id,
                    'teks_opsi' => $validated['pernyataan_b'],
                    'urutan'    => 2,
                ]);

                $dimensiA = DimensiAlatTes::where('alat_tes_id', $alatTes->id)
                    ->where('nama_dimensi', $validated['dimensi_a'])
                    ->first();
                $dimensiB = DimensiAlatTes::where('alat_tes_id', $alatTes->id)
                    ->where('nama_dimensi', $validated['dimensi_b'])
                    ->first();

                if (!$dimensiA || !$dimensiB) {
                    throw new \Exception('Dimensi tidak ditemukan pada alat tes ini.');
                }

                BobotOpsiDimensi::create([
                    'opsi_jawaban_id' => $opsiA->id,
                    'dimensi_id'      => $dimensiA->id,
                    'bobot'           => 1,
                    'is_reverse'      => false,
                ]);
                BobotOpsiDimensi::create([
                    'opsi_jawaban_id' => $opsiB->id,
                    'dimensi_id'      => $dimensiB->id,
                    'bobot'           => 1,
                    'is_reverse'      => false,
                ]);

                return;
            }

            $soal = Soal::create([
                'alat_tes_id'   => $alatTes->id,
                'nomor'         => $nomorBerikutnya,
                'teks_soal'     => $validated['teks_soal'],
                'tipe_format'   => $format === 'Pilihan Ganda' ? 'pilihan_ganda' : 'skala_likert',
                'kunci_jawaban' => $request->input('kunci'),
                'urutan'        => $urutanBerikutnya,
            ]);

            if ($format === 'Pilihan Ganda') {
                $kunciHuruf = $request->input('kunci');
                foreach ($validated['opsi'] as $huruf => $teks) {
                    OpsiJawaban::create([
                        'soal_id'   => $soal->id,
                        'teks_opsi' => $teks,
                        'urutan'    => array_search($huruf, ['A','B','C','D'], true) + 1,
                    ]);
                }
            } else {
                // Skala Likert: teks_soal sudah di teks_soal; skor 1–5 mengikuti skala standar
                for ($i = 1; $i <= 5; $i++) {
                    OpsiJawaban::create([
                        'soal_id'   => $soal->id,
                        'teks_opsi' => 'Skala ' . $i,
                        'urutan'    => $i,
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.bank-soal.index', ['alat_tes_id' => $alatTes->id])
            ->with('sukses', 'Soal berhasil ditambahkan.');
    }

    public function edit(int $id): View
    {
        $soal = Soal::with(['alatTes', 'opsiJawaban.bobotOpsiDimensi.dimensi'])
            ->findOrFail($id);

        return view('admin.bank-soal.edit', compact('soal'));
    }

    public function update(Request $request, int $id)
    {
        $soal = Soal::with('alatTes')->findOrFail($id);
        $alatTes = $soal->alatTes;
        $format = $alatTes->format_dasar;

        $rules = $format === 'Forced Choice'
            ? $this->forcedChoiceRules()
            : $this->pilihanAtauLikertRules();

        $validated = $request->validate($rules);

        DB::transaction(function () use ($soal, $format, $validated, $request, $alatTes) {
            if ($format === 'Forced Choice') {
                $soal->update([
                    'teks_soal' => $validated['pernyataan_a'],
                ]);

                $opsiList = $soal->opsiJawaban()->orderBy('urutan')->get();
                $opsiA = $opsiList->first();
                $opsiB = $opsiList->count() > 1 ? $opsiList->skip(1)->first() : null;

                if ($opsiA) {
                    $opsiA->update(['teks_opsi' => $validated['pernyataan_a']]);
                } else {
                    $opsiA = OpsiJawaban::create([
                        'soal_id'   => $soal->id,
                        'teks_opsi' => $validated['pernyataan_a'],
                        'urutan'    => 1,
                    ]);
                }
                if ($opsiB) {
                    $opsiB->update(['teks_opsi' => $validated['pernyataan_b']]);
                } else {
                    $opsiB = OpsiJawaban::create([
                        'soal_id'   => $soal->id,
                        'teks_opsi' => $validated['pernyataan_b'],
                        'urutan'    => 2,
                    ]);
                }

                $dimensiA = DimensiAlatTes::where('alat_tes_id', $alatTes->id)
                    ->where('nama_dimensi', $validated['dimensi_a'])
                    ->first();
                $dimensiB = DimensiAlatTes::where('alat_tes_id', $alatTes->id)
                    ->where('nama_dimensi', $validated['dimensi_b'])
                    ->first();

                if (!$dimensiA || !$dimensiB) {
                    throw new \Exception('Dimensi tidak ditemukan pada alat tes ini.');
                }

                BobotOpsiDimensi::updateOrCreate(
                    ['opsi_jawaban_id' => $opsiA->id],
                    ['dimensi_id' => $dimensiA->id, 'bobot' => 1, 'is_reverse' => false]
                );
                BobotOpsiDimensi::updateOrCreate(
                    ['opsi_jawaban_id' => $opsiB->id],
                    ['dimensi_id' => $dimensiB->id, 'bobot' => 1, 'is_reverse' => false]
                );

                return;
            }

            $soal->update([
                'teks_soal'     => $validated['teks_soal'],
                'kunci_jawaban' => $validated['kunci'] ?? null,
            ]);

            if ($format === 'Pilihan Ganda') {
                $existingOps = $soal->opsiJawaban()->orderBy('urutan')->get();
                $hurufMap = ['A' => 1, 'B' => 2, 'C' => 3, 'D' => 4];
                foreach ($validated['opsi'] as $huruf => $teks) {
                    $urutan = $hurufMap[$huruf] ?? 0;
                    if ($urutan === 0) {
                        continue;
                    }
                    $existing = $existingOps->firstWhere('urutan', $urutan);
                    if ($existing) {
                        $existing->update(['teks_opsi' => $teks]);
                    } else {
                        OpsiJawaban::create([
                            'soal_id'   => $soal->id,
                            'teks_opsi' => $teks,
                            'urutan'    => $urutan,
                        ]);
                    }
                }
            } else {
                // Skala Likert: 5 opsi tetap (urutan 1-5)
                $existingOps = $soal->opsiJawaban()->orderBy('urutan')->get();
                foreach ($existingOps as $opsi) {
                    $label = 'Skala ' . $opsi->urutan;
                    $opsi->update(['teks_opsi' => $label]);
                }
                // Jika ada opsi ekstra (kurang dari 5), buat yang kurang
                for ($i = 1; $i <= 5; $i++) {
                    if (!$existingOps->firstWhere('urutan', $i)) {
                        OpsiJawaban::create([
                            'soal_id'   => $soal->id,
                            'teks_opsi' => 'Skala ' . $i,
                            'urutan'    => $i,
                        ]);
                    }
                }
            }
        });

        return redirect()
            ->route('admin.bank-soal.index', ['alat_tes_id' => $alatTes->id])
            ->with('sukses', 'Soal berhasil diperbarui.');
    }

    public function hapus(int $id)
    {
        $soal = Soal::findOrFail($id);
        $alatTesId = $soal->alat_tes_id;
        $soal->delete();

        return redirect()
            ->route('admin.bank-soal.index', ['alat_tes_id' => $alatTesId])
            ->with('sukses', 'Soal berhasil dihapus.');
    }

    /**
     * Bangun array sesuai key yang dipakai kartu-soal.blade.php:
     *   Pilihan Ganda   → teks_soal, opsi[A..D], kunci
     *   Skala Likert    → pernyataan, dimensi
     *   Forced Choice   → pernyataan_a/b, dimensi_a/b (string nama dimensi)
     */
    protected function mapSoalUntukView(Soal $soal, ?string $format): array
    {
        $format = $format ?? $soal->alatTes?->format_dasar;

        if ($format === 'Pilihan Ganda') {
            $opsi = $soal->opsiJawaban->sortBy('urutan')->values();
            return [
                'id'        => $soal->id,
                'teks_soal' => $soal->teks_soal,
                'opsi'      => [
                    'A' => $opsi[0]->teks_opsi ?? '',
                    'B' => $opsi[1]->teks_opsi ?? '',
                    'C' => $opsi[2]->teks_opsi ?? '',
                    'D' => $opsi[3]->teks_opsi ?? '',
                ],
                'kunci_jawaban' => $soal->kunci_jawaban,
            ];
        }

        if ($format === 'Skala Likert') {
            $dimensi = $soal->alatTes?->dimensiAlatTes->first();
            return [
                'id'          => $soal->id,
                'pernyataan'  => $soal->teks_soal,
                'dimensi'     => $dimensi?->nama_dimensi ?? '-',
            ];
        }

        if ($format === 'Forced Choice') {
            $opsi = $soal->opsiJawaban->sortBy('urutan')->values();
            $dimensiA = $opsi[0]->bobotOpsiDimensi->first()?->dimensi?->nama_dimensi ?? '-';
            $dimensiB = $opsi[1]->bobotOpsiDimensi->first()?->dimensi?->nama_dimensi ?? '-';

            return [
                'id'            => $soal->id,
                'pernyataan_a'  => $opsi[0]->teks_opsi ?? '',
                'dimensi_a'     => $dimensiA,
                'pernyataan_b'  => $opsi[1]->teks_opsi ?? '',
                'dimensi_b'     => $dimensiB,
            ];
        }

        return ['id' => $soal->id, 'teks_soal' => $soal->teks_soal];
    }

    protected function forcedChoiceRules(): array
    {
        return [
            'pernyataan_a' => ['required', 'string'],
            'dimensi_a'    => ['required', 'string', 'max:100'],
            'pernyataan_b' => ['required', 'string'],
            'dimensi_b'    => ['required', 'string', 'max:100'],
        ];
    }

    protected function pilihanAtauLikertRules(): array
    {
        return [
            'teks_soal' => ['required', 'string'],
            'kunci'     => ['nullable', 'string', 'in:A,B,C,D'],
            'opsi'      => ['required', 'array', 'min:2'],
            'opsi.*'    => ['required', 'string'],
        ];
    }
}

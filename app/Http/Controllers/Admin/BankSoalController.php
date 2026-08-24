<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlatTes;
use App\Models\BobotOpsiDimensi;
use App\Models\DimensiAlatTes;
use App\Models\OpsiJawaban;
use App\Models\Soal;
use Illuminate\Http\RedirectResponse;
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
            $counts = Soal::select('alat_tes_id', DB::raw('count(*) as jumlah'))
                ->groupBy('alat_tes_id')
                ->pluck('jumlah', 'alat_tes_id');

            foreach ($alatTesSemua as $alat) {
                $jumlah = $counts[$alat->id] ?? 0;
                if ($jumlah === 0) continue;
                $kelompokSoalSemua[$alat->id] = [
                    'alat'       => $alat,
                    'jumlahSoal' => $jumlah,
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
        $dimensiList = DimensiAlatTes::where('alat_tes_id', $alatTes->id)
            ->orderBy('urutan')
            ->get(['id', 'kode_dimensi', 'nama_dimensi']);

        return view('admin.bank-soal.tambah', compact('alatTes', 'dimensiList'));
    }

    public function simpan(Request $request, int $alatTesId): RedirectResponse
    {
        $alatTes = AlatTes::findOrFail($alatTesId);
        $format = $alatTes->format_dasar;

        $rules = $this->soalRules($format);
        $messages = $this->soalMessages($format);
        $attributes = $this->soalAttributes($format);
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages, $attributes);
        $validator->validate();
        $validated = $validator->validated();

        DB::transaction(function () use ($alatTes, $format, $validated, $request) {
            $nomorBerikutnya = (int) (Soal::where('alat_tes_id', $alatTes->id)->max('nomor') ?? 0) + 1;
            $urutanBerikutnya = (int) (Soal::where('alat_tes_id', $alatTes->id)->max('urutan') ?? 0) + 1;

            if ($format === 'Grid') {
                Soal::create([
                    'alat_tes_id' => $alatTes->id,
                    'nomor'       => $nomorBerikutnya,
                    'teks_soal'   => $validated['teks_soal'],
                    'tipe_format' => 'grid',
                    'urutan'      => $urutanBerikutnya,
                ]);
                return;
            }

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
                    ->where('kode_dimensi', $validated['dimensi_a'])
                    ->first();
                $dimensiB = DimensiAlatTes::where('alat_tes_id', $alatTes->id)
                    ->where('kode_dimensi', $validated['dimensi_b'])
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

            // Mixed atau default
            $tipeFormat = $request->input('tipe_format', 'pilihan_ganda');
            $soal = Soal::create([
                'alat_tes_id'   => $alatTes->id,
                'nomor'         => $nomorBerikutnya,
                'teks_soal'     => $validated['teks_soal'] ?? null,
                'gambar_soal'   => $request->hasFile('gambar_soal')
                    ? $request->file('gambar_soal')->store('ist/soal', 'public')
                    : null,
                'tipe_format'   => $tipeFormat,
                'kunci_jawaban' => in_array($tipeFormat, ['isian_teks', 'isian_angka'])
                    ? $validated['kunci_jawaban'] ?? null
                    : $request->input('kunci_jawaban'),
                'urutan'        => $urutanBerikutnya,
            ]);

            if ($tipeFormat === 'isian_teks' || $tipeFormat === 'isian_angka') {
                // Tidak ada opsi jawaban
                return;
            }

            // Pilihan Ganda / Pilihan Gambar: simpan opsi
            $hurufMap = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5];
            foreach ($validated['opsi'] ?? [] as $huruf => $teks) {
                $urutan = $hurufMap[$huruf] ?? 0;
                if ($urutan === 0) continue;
                $opsi = OpsiJawaban::create([
                    'soal_id' => $soal->id,
                    'teks_opsi' => $teks,
                    'urutan' => $urutan,
                ]);
                if ($request->hasFile("gambar_opsi.$huruf")) {
                    $opsi->gambar_opsi = $request->file("gambar_opsi.$huruf")->store('ist/opsi', 'public');
                    $opsi->save();
                }
            }
        });

        return redirect()
            ->route('admin.bank-soal.index', ['alat_tes_id' => $alatTes->id])
            ->with($this::FLASH_SUCCESS, 'Soal berhasil ditambahkan.');
    }

    public function edit(int $id): View
    {
        $soal = Soal::with(['alatTes', 'opsiJawaban.bobotOpsiDimensi.dimensi'])
            ->findOrFail($id);
        $format = $soal->alatTes->format_dasar ?? 'Pilihan Ganda';

        $dimensiList = DimensiAlatTes::where('alat_tes_id', $soal->alat_tes_id)
            ->orderBy('urutan')
            ->get(['id', 'kode_dimensi', 'nama_dimensi']);

        return view('admin.bank-soal.edit', compact('soal', 'format', 'dimensiList'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $soal = Soal::with('alatTes')->findOrFail($id);
        $alatTes = $soal->alatTes;
        $format = $alatTes->format_dasar;

        $rules = $this->soalRules($format);
        $messages = $this->soalMessages($format);
        $attributes = $this->soalAttributes($format);
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages, $attributes);
        $validator->validate();
        $validated = $validator->validated();

        DB::transaction(function () use ($soal, $format, $validated, $request, $alatTes) {
            if ($format === 'Grid') {
                $soal->update(['teks_soal' => $validated['teks_soal']]);
                return;
            }

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
                    ->where('kode_dimensi', $validated['dimensi_a'])
                    ->first();
                $dimensiB = DimensiAlatTes::where('alat_tes_id', $alatTes->id)
                    ->where('kode_dimensi', $validated['dimensi_b'])
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

            if ($format === 'Mixed') {
                $tipeFormat = $request->input('tipe_format', $soal->tipe_format);
                $soal->update([
                    'teks_soal'     => in_array($tipeFormat, ['isian_teks', 'isian_angka']) ? null : ($validated['teks_soal'] ?? null),
                    'gambar_soal'   => $request->hasFile('gambar_soal')
                        ? $request->file('gambar_soal')->store('ist/soal', 'public')
                        : $soal->gambar_soal,
                    'tipe_format'   => $tipeFormat,
                    'kunci_jawaban' => in_array($tipeFormat, ['isian_teks', 'isian_angka'])
                        ? ($validated['kunci_jawaban'] ?? null)
                        : $request->input('kunci_jawaban'),
                ]);

                if (in_array($tipeFormat, ['isian_teks', 'isian_angka'])) {
                    $soal->opsiJawaban()->delete();
                    return;
                }
            } else {
                $soal->update([
                    'teks_soal'     => $validated['teks_soal'] ?? null,
                    'kunci_jawaban' => $validated['kunci_jawaban'] ?? $request->input('kunci_jawaban'),
                ]);

                if ($request->hasFile('gambar_soal')) {
                    $soal->gambar_soal = $request->file('gambar_soal')->store('ist/soal', 'public');
                    $soal->save();
                }
            }

            if ($format === 'Pilihan Ganda' || isset($validated['opsi'])) {
                $existingOps = $soal->opsiJawaban()->orderBy('urutan')->get();
                $hurufMap = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5];
                foreach ($validated['opsi'] as $huruf => $teks) {
                    $urutan = $hurufMap[$huruf] ?? 0;
                    if ($urutan === 0) {
                        continue;
                    }
                    $existing = $existingOps->firstWhere('urutan', $urutan);
                    if ($existing) {
                        $existing->update(['teks_opsi' => $teks ?? '']);
                        // Upload gambar opsi jika ada
                        if ($request->hasFile("gambar_opsi.$huruf")) {
                            $existing->gambar_opsi = $request->file("gambar_opsi.$huruf")->store('ist/opsi', 'public');
                            $existing->save();
                        }
                    } else {
                        $opsi = OpsiJawaban::create([
                            'soal_id'   => $soal->id,
                            'teks_opsi' => $teks ?? '',
                            'urutan'    => $urutan,
                        ]);
                        // Upload gambar opsi jika ada
                        if ($request->hasFile("gambar_opsi.$huruf")) {
                            $opsi->gambar_opsi = $request->file("gambar_opsi.$huruf")->store('ist/opsi', 'public');
                            $opsi->save();
                        }
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
            ->with($this::FLASH_SUCCESS, 'Soal berhasil diperbarui.');
    }

    public function hapus(int $id): RedirectResponse
    {
        $soal = Soal::findOrFail($id);
        $alatTesId = $soal->alat_tes_id;
        $soal->delete();

        return redirect()
            ->route('admin.bank-soal.index', ['alat_tes_id' => $alatTesId])
            ->with($this::FLASH_SUCCESS, 'Soal berhasil dihapus.');
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

        if ($soal->tipe_format === 'grid') {
            return [
                'id'            => $soal->id,
                'tipe_format'   => 'grid',
                'format_dasar'  => 'Grid',
                'teks_soal'     => $soal->teks_soal,
            ];
        }

        if ($format === 'Pilihan Ganda') {
            $opsi = $soal->opsiJawaban->sortBy('urutan')->values();
            return [
                'id'            => $soal->id,
                'tipe_format'   => $soal->tipe_format ?? 'pilihan_ganda',
                'teks_soal'     => $soal->teks_soal,
                'opsi'          => [
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
                'id'            => $soal->id,
                'tipe_format'   => $soal->tipe_format ?? 'skala_likert',
                'pernyataan'    => $soal->teks_soal,
                'dimensi'       => $dimensi?->nama_dimensi ?? '-',
            ];
        }

        if ($format === 'Forced Choice') {
            $opsi = $soal->opsiJawaban->sortBy('urutan')->values();
            $dimensiA = $opsi[0]->bobotOpsiDimensi->first()?->dimensi?->nama_dimensi ?? '-';
            $dimensiB = $opsi[1]->bobotOpsiDimensi->first()?->dimensi?->nama_dimensi ?? '-';

            return [
                'id'            => $soal->id,
                'tipe_format'   => $soal->tipe_format ?? 'forced_choice',
                'pernyataan_a'  => $opsi[0]->teks_opsi ?? '',
                'dimensi_a'     => $dimensiA,
                'pernyataan_b'  => $opsi[1]->teks_opsi ?? '',
                'dimensi_b'     => $dimensiB,
            ];
        }

        return [
            'id'            => $soal->id,
            'nomor'         => $soal->nomor,
            'tipe_format'   => $soal->tipe_format ?? 'pilihan_ganda',
            'teks_soal'     => $soal->teks_soal,
            'gambar_soal'   => $soal->gambar_soal ?? null,
            'opsi'          => $soal->opsiJawaban->sortBy('urutan')->values()->map(fn ($o) => [
                'teks'   => $o->teks_opsi,
                'gambar' => $o->gambar_opsi ?? null,
            ])->toArray(),
            'kunci_jawaban' => $soal->kunci_jawaban,
        ];
    }

    protected function soalRules(string $format): array
    {
        return match ($format) {
            'Grid'          => [
                'teks_soal' => ['required', 'string'],
            ],
            'Forced Choice' => [
                'pernyataan_a' => ['required', 'string'],
                'dimensi_a'    => ['required', 'string', 'max:100'],
                'pernyataan_b' => ['required', 'string'],
                'dimensi_b'    => ['required', 'string', 'max:100'],
            ],
            'Mixed'         => [
                'tipe_format'     => ['required', 'in:pilihan_ganda,pilihan_gambar,isian_teks,isian_angka'],
                'teks_soal'       => ['required_if:tipe_format,pilihan_ganda,pilihan_gambar', 'nullable', 'string'],
                'gambar_soal'     => ['nullable', 'image', 'max:2048'],
                'kunci_jawaban'   => ['nullable', 'string', 'in:a,b,c,d,e'],
                'opsi'            => ['nullable', 'array'],
                'opsi.*'          => ['nullable', 'string', 'max:500'],
                'gambar_opsi'     => ['nullable', 'array'],
                'gambar_opsi.*'   => ['nullable', 'image', 'max:2048'],
            ],
            default         => [
                'teks_soal' => ['required', 'string'],
                'kunci'     => ['nullable', 'string', 'in:A,B,C,D'],
                'opsi'      => ['required', 'array', 'min:2'],
                'opsi.*'    => ['required', 'string'],
            ],
        };
    }

    protected function soalMessages(string $format): array
    {
        $messages = match ($format) {
            'Grid'          => ['teks_soal.required' => 'Pertanyaan wajib diisi.'],
            'Forced Choice' => [
                'pernyataan_a.required' => 'Pernyataan A wajib diisi.',
                'dimensi_a.required'    => 'Dimensi A wajib dipilih.',
                'pernyataan_b.required' => 'Pernyataan B wajib diisi.',
                'dimensi_b.required'    => 'Dimensi B wajib dipilih.',
            ],
            'Mixed'         => [
                'gambar_soal.image'     => 'File harus berupa gambar.',
                'gambar_soal.max'       => 'Ukuran gambar maksimal 2 MB.',
                'kunci_jawaban.in'      => 'Kunci jawaban harus a, b, c, d, atau e.',
                'opsi.*.max'            => 'Setiap opsi maksimal 500 karakter.',
                'gambar_opsi.*.image'   => 'File opsi harus berupa gambar.',
                'gambar_opsi.*.max'     => 'Ukuran gambar opsi maksimal 2 MB.',
            ],
            default         => [
                'teks_soal.required' => 'Pernyataan wajib diisi.',
                'kunci.in'           => 'Kunci jawaban harus A, B, C, atau D.',
                'opsi.required'      => 'Minimal satu opsi jawaban wajib diisi.',
                'opsi.min'           => 'Minimal :min opsi jawaban.',
                'opsi.*.required'    => 'Opsi jawaban wajib diisi.',
            ],
        };

        return $messages;
    }

    protected function soalAttributes(string $format): array
    {
        $attributes = match ($format) {
            'Grid'          => ['teks_soal' => 'pertanyaan'],
            'Forced Choice' => [
                'pernyataan_a' => 'pernyataan A',
                'dimensi_a'    => 'dimensi A',
                'pernyataan_b' => 'pernyataan B',
                'dimensi_b'    => 'dimensi B',
            ],
            'Mixed'         => [
                'teks_soal'     => 'pertanyaan',
                'gambar_soal'   => 'gambar soal',
                'kunci_jawaban' => 'kunci jawaban',
                'opsi.*'        => 'opsi jawaban',
                'gambar_opsi.*' => 'gambar opsi',
            ],
            default         => ['teks_soal' => 'pernyataan'],
        };

        return $attributes;
    }
}

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
    // ponytail: salinan rentang di ScoringEngineService::scoreIST — ubah keduanya bila subtes IST bergeser
    public const SUBTES_IST = [
        'SE' => [1, 20], 'WA' => [21, 40], 'AN' => [41, 60], 'GE' => [61, 76], 'RA' => [77, 96],
        'ZR' => [97, 116], 'FA' => [117, 136], 'WU' => [137, 156], 'ME' => [157, 176],
    ];

    public static function subtesIst(int $nomor): string
    {
        foreach (self::SUBTES_IST as $kode => [, $maks]) {
            if ($nomor <= $maks) {
                return $kode;
            }
        }

        return 'ME';
    }

    public function index(Request $request): View
    {
        $alatTesSemua = AlatTes::withCount('soal')->orderBy('nama')->get();
        $alatTesTerpilih = $alatTesSemua->firstWhere('id', (int) $request->query('alat_tes_id'));
        $daftarSoal = collect();
        $cari = trim((string) $request->query('cari', ''));
        $subtes = (string) $request->query('subtes', '');

        if ($alatTesTerpilih) {
            $daftarSoal = Soal::with('opsiJawaban.bobotOpsiDimensi.dimensi')
                ->where('alat_tes_id', $alatTesTerpilih->id)
                ->when($cari !== '', fn ($q) => $q->where(fn ($q) => $q
                    ->where('teks_soal', 'like', "%{$cari}%")
                    ->orWhereHas('opsiJawaban', fn ($q) => $q->where('teks_opsi', 'like', "%{$cari}%"))))
                ->when($alatTesTerpilih->format_dasar === 'Mixed' && isset(self::SUBTES_IST[$subtes]),
                    fn ($q) => $q->whereBetween('nomor', self::SUBTES_IST[$subtes]))
                ->orderBy('urutan')
                ->orderBy('id')
                ->get();
        }

        return view('admin.bank-soal.index', compact('alatTesSemua', 'alatTesTerpilih', 'daftarSoal', 'cari', 'subtes'));
    }

    public function tambah(int $alatTesId): View
    {
        $alatTes = AlatTes::findOrFail($alatTesId);
        $soal = new Soal(['nomor' => (int) Soal::where('alat_tes_id', $alatTes->id)->max('nomor') + 1, 'tipe_format' => 'pilihan_ganda']);

        return $this->form($alatTes, $soal);
    }

    public function edit(int $id): View
    {
        $soal = Soal::with('opsiJawaban.bobotOpsiDimensi.dimensi')->findOrFail($id);

        return $this->form(AlatTes::findOrFail($soal->alat_tes_id), $soal);
    }

    protected function form(AlatTes $alatTes, Soal $soal): View
    {
        $dimensiList = DimensiAlatTes::where('alat_tes_id', $alatTes->id)
            ->orderBy('urutan')
            ->get(['id', 'kode_dimensi', 'nama_dimensi']);

        return view('admin.bank-soal.form', compact('alatTes', 'soal', 'dimensiList'));
    }

    public function simpan(Request $request, int $alatTesId): RedirectResponse
    {
        $alatTes = AlatTes::findOrFail($alatTesId);
        $format = $alatTes->format_dasar;

        $rules = $this->soalRules($format, $request->input('tipe_format'));
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

            // Mixed atau Pilihan Ganda
            $tipeFormat = $validated['tipe_format'] ?? 'pilihan_ganda';
            $soal = Soal::create([
                'alat_tes_id'   => $alatTes->id,
                'nomor'         => $nomorBerikutnya,
                'teks_soal'     => $validated['teks_soal'] ?? '',
                'gambar_soal'   => $request->hasFile('gambar_soal')
                    ? $request->file('gambar_soal')->store('ist/soal', 'public')
                    : null,
                'tipe_format'   => $tipeFormat,
                'kunci_jawaban' => $validated['kunci_jawaban'] ?? null,
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

    public function update(Request $request, int $id): RedirectResponse
    {
        $soal = Soal::with('alatTes')->findOrFail($id);
        $alatTes = $soal->alatTes;
        $format = $alatTes->format_dasar;

        $rules = $this->soalRules($format, $request->input('tipe_format'));
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
                $tipeFormat = $validated['tipe_format'];
                $soal->update([
                    'teks_soal'     => $validated['teks_soal'] ?? '',
                    'gambar_soal'   => $request->hasFile('gambar_soal')
                        ? $request->file('gambar_soal')->store('ist/soal', 'public')
                        : $soal->gambar_soal,
                    'tipe_format'   => $tipeFormat,
                    'kunci_jawaban' => $validated['kunci_jawaban'] ?? null,
                ]);

                if (in_array($tipeFormat, ['isian_teks', 'isian_angka'])) {
                    $soal->opsiJawaban()->delete();
                    return;
                }
            } else {
                $soal->update([
                    'teks_soal'     => $validated['teks_soal'],
                    'kunci_jawaban' => $validated['kunci_jawaban'] ?? null,
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

    protected function soalRules(string $format, ?string $tipe): array
    {
        $pilihan = in_array($tipe, ['pilihan_ganda', 'pilihan_gambar'], true);

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
                // pilihan: huruf opsi; isian: teks/angka, atau JSON {"2":[..],"1":[..]} untuk GE
                'kunci_jawaban'   => $pilihan ? ['nullable', 'in:a,b,c,d,e'] : ['required', 'string', 'max:255'],
                'opsi'            => ['nullable', 'array'],
                'opsi.*'          => ['nullable', 'string', 'max:500'],
                'gambar_opsi'     => ['nullable', 'array'],
                'gambar_opsi.*'   => ['nullable', 'image', 'max:2048'],
            ],
            default         => [
                'teks_soal'     => ['required', 'string'],
                'kunci_jawaban' => ['nullable', 'in:a,b,c,d'],
                'opsi'          => ['required', 'array:a,b,c,d', 'min:2'],
                'opsi.*'        => ['required', 'string', 'max:500'],
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
                'kunci_jawaban.in'       => 'Kunci jawaban harus a, b, c, d, atau e.',
                'kunci_jawaban.required' => 'Kunci jawaban wajib diisi untuk soal isian.',
                'opsi.*.max'            => 'Setiap opsi maksimal 500 karakter.',
                'gambar_opsi.*.image'   => 'File opsi harus berupa gambar.',
                'gambar_opsi.*.max'     => 'Ukuran gambar opsi maksimal 2 MB.',
            ],
            default         => [
                'teks_soal.required' => 'Pernyataan wajib diisi.',
                'kunci_jawaban.in'   => 'Kunci jawaban harus A, B, C, atau D.',
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
                'tipe_format'   => 'tipe soal',
                'teks_soal'     => 'pertanyaan',
                'gambar_soal'   => 'gambar soal',
                'kunci_jawaban' => 'kunci jawaban',
                'opsi.*'        => 'opsi jawaban',
                'gambar_opsi.*' => 'gambar opsi',
            ],
            default         => ['teks_soal' => 'pertanyaan', 'opsi.*' => 'opsi jawaban'],
        };

        return $attributes;
    }
}

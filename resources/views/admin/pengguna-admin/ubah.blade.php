@extends('layouts.admin', ['judulHalaman' => 'Ubah Admin/Staff'])

@section('content')
    <div class="w-full rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Ubah Pengguna Internal: {{ $pengguna->name }}</h2>
            <a href="{{ route('admin.pengguna-admin.index') }}" class="text-sm text-slate-600 hover:text-slate-800">&larr; Kembali</a>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-md border border-rose-600 bg-rose-600 px-4 py-3 text-sm text-white">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.pengguna-admin.perbarui', $pengguna->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="peran_id" class="block text-sm font-medium text-slate-700">Peran</label>
                <select id="peran_id" name="peran_id" required
                        class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                    <option value="">-- Pilih Peran Internal --</option>
                    @foreach ($daftarPeran as $peran)
                        <option value="{{ $peran->id }}" {{ (string)old('peran_id', $pengguna->peran_id) === (string)$peran->id ? 'selected' : '' }}>
                            {{ $peran->nama_peran }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-4">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Akun</p>
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $pengguna->name) }}" required
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $pengguna->email) }}" required
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
                <div>
                    <label for="no_hp" class="block text-sm font-medium text-slate-700">No HP</label>
                    <input id="no_hp" name="no_hp" type="text" value="{{ old('no_hp', $pengguna->no_hp) }}" required
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
                <select id="status" name="status" required
                        class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                    <option value="aktif" {{ old('status', $pengguna->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ old('status', $pengguna->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="space-y-4 border-t border-slate-100 pt-4">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Password Baru <span class="text-slate-400 font-normal normal-case">(kosongkan jika tidak ingin mengubah)</span></p>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                    <input id="password" name="password" type="password" minlength="8"
                           placeholder="Minimal 8 karakter"
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm placeholder:text-slate-400 focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" minlength="8"
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
            </div>

            <div class="sticky bottom-0 -mx-6 mt-4 flex justify-end gap-2 border-t border-slate-200 bg-white px-6 py-3 shadow-[0_-2px_4px_rgba(0,0,0,0.04)]">
                <a href="{{ route('admin.pengguna-admin.index') }}" class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">Batal</a>
                <button type="submit" class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853]">Perbarui</button>
            </div>
        </form>
    </div>
<style>[x-cloak] { display: none !important; }</style>
@endsection
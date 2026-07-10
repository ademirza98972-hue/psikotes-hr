@extends('layouts.auth', ['judulHalaman' => 'Login'])

@section('content')
    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-6 text-lg font-semibold text-slate-900">Masuk ke akun</h2>

        @if ($errors->any())
            <div class="mb-4 rounded-md border border-rose-600 bg-rose-600 px-4 py-3 text-sm text-white">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                >
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                >
            </div>

            <div class="flex items-center">
                <input id="ingat_saya" name="ingat_saya" type="checkbox" value="1" {{ old('ingat_saya') ? 'checked' : '' }}
                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                <label for="ingat_saya" class="ml-2 text-sm text-slate-600">Ingat saya</label>
            </div>

            <button type="submit"
                class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Masuk
            </button>
        </form>
    </div>

    <p class="mt-6 text-center text-sm text-slate-600">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-700">Daftar di sini</a>
    </p>
@endsection
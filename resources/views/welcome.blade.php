@extends('layouts.app')

@section('title', 'Frontend - Starter')

@section('content')
    <main class="mx-auto max-w-3xl p-8">
        <h1 class="text-3xl font-semibold tracking-tight">Frontend Starter</h1>
        <p class="mt-2 text-slate-600">
            Estrutura mínima para start Front (Vite com Tailwind).
        </p>

        <div class="mt-8 rounded-lg border border-slate-200 p-4">
            <p class="text-sm text-slate-700">
                Editar:
                <code class="rounded bg-slate-100 px-1 py-0.5">resources/views/welcome.blade.php</code>
                e
                <code class="rounded bg-slate-100 px-1 py-0.5">resources/js/app.js</code>
            </p>
        </div>
    </main>
@endsection


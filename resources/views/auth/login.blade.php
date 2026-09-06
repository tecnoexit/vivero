@extends('layouts.app')

@section('content')
<div class="grid cols-2" style="align-items:center; min-height:70vh;">
    <div>
        <h1 style="font-size:44px; margin:0 0 12px;">Sistema de vivero de cafe</h1>
        <p class="muted" style="font-size:18px; line-height:1.5;">Gestiona plantas, mediciones, eventos, fotos, etiquetas y seleccion desde una sola aplicacion Laravel.</p>
    </div>
    <div class="card">
        <h2 style="margin-top:0;">Ingresar</h2>
        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <div class="field">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
                @error('email')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label>Password</label>
                <input type="password" name="password" required>
                @error('password')<div class="error">{{ $message }}</div>@enderror
            </div>
            <label style="display:flex; gap:8px; align-items:center; margin-bottom:16px;">
                <input type="checkbox" name="remember" value="1" style="width:auto;">
                Recordarme
            </label>
            <button class="btn primary" type="submit">Entrar</button>
        </form>
    </div>
</div>
@endsection

@extends('rostpack.layouts.app')

@section('title', 'Настройки - RostPack')

@section('content')
    <div style="text-align:center;margin-top:2rem">
        <p style="opacity:.8;margin-bottom:1rem">Настройки</p>
        @auth
            <a href="{{ route('rostpack.profile.index') }}">Перейти в профиль</a>
        @endauth
    </div>
@endsection

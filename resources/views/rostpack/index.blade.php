@extends('rostpack.layouts.app')

@section('title', 'RostPack - Игровой интерфейс')

@section('content')
    <div class="totem">
      <img src="{{ asset('images/rostpack/kotenok.png') }}" alt="totem">
    </div>

    <div class="stat">
      <span>Дух</span>
      <div class="bar"><div class="bar-fill" style="width:60%"></div></div>
    </div>

    <div class="stat">
      <span>Разум</span>
      <div class="bar"><div class="bar-fill" style="width:85%"></div></div>
    </div>

    <div class="stat">
      <span>Тело</span>
      <div class="bar"><div class="bar-fill" style="width:40%"></div></div>
    </div>

    <div class="stat">
      <span>Социальность</span>
      <div class="bar"><div class="bar-fill" style="width:85%"></div></div>
    </div>
@endsection
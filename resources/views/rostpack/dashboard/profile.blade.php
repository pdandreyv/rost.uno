@extends('rostpack.layouts.app')

@section('title', 'Профиль пользователя')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Заголовок -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Мой профиль</h1>
            <p class="mt-2 text-gray-600">Управляйте информацией о себе</p>
        </div>

        <!-- Сообщения -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Левая колонка - Фото и основная информация -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <!-- Фото профиля -->
                    <div class="text-center">
                        <div class="relative inline-block">
                            @if($user->photo_url)
                                <img src="{{ $user->photo_url }}" 
                                     alt="{{ $user->display_name }}" 
                                     class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg cursor-pointer hover:shadow-xl transition-shadow duration-200"
                                     id="profile-photo"
>
                            @else
                                <div class="w-32 h-32 rounded-full bg-gray-300 flex items-center justify-center border-4 border-white shadow-lg cursor-pointer hover:shadow-xl transition-shadow duration-200"
                                     id="profile-photo"
>
                                    <svg class="w-16 h-16 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @endif
                            
                            <!-- Выпадающее меню -->
                            <div id="profile-menu" class="absolute top-full right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-10">
                                <div class="py-1">
                                    <a href="{{ route('rostpack.profile.index') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Профиль
                                    </a>
                                    <a href="{{ route('rostpack.index') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                                        </svg>
                                        Панель управления
                                    </a>
                                    <hr class="my-1">
                                    <form method="POST" action="{{ route('rostpack.auth.logout') }}" class="block">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            Выйти
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <h2 class="mt-4 text-xl font-semibold text-gray-900">{{ $user->display_name }}</h2>
                        @if($user->telegram_username)
                            <p class="text-sm text-gray-500">@{{ $user->telegram_username }}</p>
                        @endif
                        
                        @if($user->verified)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-2">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Подтвержден
                            </span>
                        @endif
                    </div>

                    <!-- Статистика -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Статистика</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Telegram ID:</span>
                                <span class="font-medium">{{ $user->telegram_id }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Дата регистрации:</span>
                                <span class="font-medium">{{ $user->created_at->format('d.m.Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Правая колонка - Форма редактирования -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Редактировать профиль</h3>
                    
                    <form method="POST" action="{{ route('rostpack.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Имя -->
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Имя
                                </label>
                                <input type="text" 
                                       id="first_name" 
                                       name="first_name" 
                                       value="{{ old('first_name', $user->first_name) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('first_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Отчество -->
                            <div>
                                <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Отчество
                                </label>
                                <input type="text" 
                                       id="middle_name" 
                                       name="middle_name" 
                                       value="{{ old('middle_name', $user->middle_name) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('middle_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Фамилия -->
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Фамилия
                                </label>
                                <input type="text" 
                                       id="last_name" 
                                       name="last_name" 
                                       value="{{ old('last_name', $user->last_name) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('last_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Псевдоним -->
                            <div>
                                <label for="alias" class="block text-sm font-medium text-gray-700 mb-2">
                                    Псевдоним
                                </label>
                                <input type="text" 
                                       id="alias" 
                                       name="alias" 
                                       value="{{ old('alias', $user->alias) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('alias')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Дата рождения -->
                            <div>
                                <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Дата рождения
                                </label>
                                <input type="date" 
                                       id="birth_date" 
                                       name="birth_date" 
                                       value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('birth_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Пол -->
                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                                    Пол
                                </label>
                                <select id="gender" 
                                        name="gender"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Не указан</option>
                                    <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Мужской</option>
                                    <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Женский</option>
                                </select>
                                @error('gender')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Город -->
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                    Город
                                </label>
                                <input type="text" 
                                       id="city" 
                                       name="city" 
                                       value="{{ old('city', $user->city) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Телефон -->
                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Телефон
                                </label>
                                <input type="tel" 
                                       id="phone_number" 
                                       name="phone_number" 
                                       value="{{ old('phone_number', $user->phone_number) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('phone_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Фото -->
                        <div class="mt-6">
                            <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                                Фото профиля
                            </label>
                            <input type="file" 
                                   id="photo" 
                                   name="photo" 
                                   accept="image/*"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('photo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">JPG, PNG, GIF до 2MB</p>
                        </div>

                        <!-- Настройки приватности -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <h4 class="text-md font-medium text-gray-900 mb-4">Настройки приватности</h4>
                            
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           id="birth_date_view" 
                                           name="birth_date_view" 
                                           value="1"
                                           {{ old('birth_date_view', $user->birth_date_view) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="birth_date_view" class="ml-2 text-sm text-gray-700">
                                        Показывать дату рождения
                                    </label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           id="phone_number_view" 
                                           name="phone_number_view" 
                                           value="1"
                                           {{ old('phone_number_view', $user->phone_number_view) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="phone_number_view" class="ml-2 text-sm text-gray-700">
                                        Показывать номер телефона
                                    </label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           id="city_view" 
                                           name="city_view" 
                                           value="1"
                                           {{ old('city_view', $user->city_view) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="city_view" class="ml-2 text-sm text-gray-700">
                                        Показывать город
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Кнопки -->
                        <div class="mt-8 flex justify-end space-x-3">
                            <a href="{{ route('rostpack.index') }}" 
                               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                Отмена
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Сохранить изменения
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

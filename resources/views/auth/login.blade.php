@php $isRtl = app()->getLocale() === 'ur'; @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Login') }} – The Suit Tailor</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @if($isRtl)
    <link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        [dir=rtl],[dir=rtl] *{font-family:'Inter',ui-sans-serif,system-ui,sans-serif!important}
        [dir=rtl] .urdu-content,[dir=rtl] label,[dir=rtl] h1,[dir=rtl] p{font-family:'Noto Nastaliq Urdu','Inter',ui-sans-serif,system-ui,sans-serif!important}
        [dir=rtl] input,[dir=rtl] button.lang-btn{font-family:'Inter',ui-sans-serif,system-ui,sans-serif!important}
    </style>
    @endif
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center">
    <div class="w-full max-w-md">
        {{-- Language switcher --}}
        <div class="flex justify-end mb-3 gap-2">
            <a href="{{ route('lang.switch', 'en') }}"
               class="text-xs px-3 py-1 rounded-full font-semibold {{ app()->getLocale() === 'en' ? 'bg-blue-600 text-white' : 'bg-white text-slate-500 border border-slate-200 hover:bg-slate-50' }}">
                EN
            </a>
            <a href="{{ route('lang.switch', 'ur') }}"
               class="text-xs px-3 py-1 rounded-full font-semibold {{ app()->getLocale() === 'ur' ? 'bg-blue-600 text-white' : 'bg-white text-slate-500 border border-slate-200 hover:bg-slate-50' }}"
               style="font-family:'Noto Nastaliq Urdu',serif">
                اردو
            </a>
        </div>
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-slate-800">✂️ The Suit Tailor</h1>
            <p class="text-slate-500 mt-1">{{ __('Management System') }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-xl font-semibold text-slate-700 mb-6">{{ __('Sign In') }}</h2>
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">{{ __('Email') }}</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required autofocus>
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">{{ __('Password') }}</label>
                    <input type="password" id="password" name="password"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                    {{ __('Sign In') }}
                </button>
            </form>
        </div>
    </div>
</body>
</html>

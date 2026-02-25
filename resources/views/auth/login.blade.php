<x-guest-layout>
    @section('title', 'Connexion')

    <div class="auth-card-header">
        <div class="greeting">Bienvenue</div>
        <h1>Connexion</h1>
        <p>Accédez à votre espace de gestion de certification biologique</p>
    </div>

    {{-- Session Status --}}
    @if (session('status'))
        <div class="auth-flash auth-flash-success">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('status') }}
        </div>
    @endif

    <div class="auth-form-card">
        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf

            {{-- Email --}}
            <div class="auth-form-group">
                <label class="auth-label" for="email">Adresse email</label>
                <div class="auth-input-wrapper">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    <input id="email" type="email" name="email" class="auth-input {{ $errors->has('email') ? 'input-error' : '' }}" value="{{ old('email') }}" placeholder="votre@email.com" required autofocus autocomplete="username">
                </div>
                @error('email')
                    <div class="auth-error">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="auth-form-group">
                <label class="auth-label" for="password">Mot de passe</label>
                <div class="auth-input-wrapper">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <input id="password" type="password" name="password" class="auth-input {{ $errors->has('password') ? 'input-error' : '' }}" placeholder="••••••••" required autocomplete="current-password" style="padding-right:48px">
                    <button type="button" class="toggle-password" tabindex="-1">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                @error('password')
                    <div class="auth-error">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Remember / Forgot --}}
            <div class="auth-checkbox-row">
                <label class="auth-checkbox-label">
                    <input type="checkbox" name="remember" class="auth-checkbox" {{ old('remember') ? 'checked' : '' }}>
                    Se souvenir de moi
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="auth-forgot-link">Mot de passe oublié ?</a>
                @endif
            </div>

            {{-- Submit --}}
            <button type="submit" class="auth-submit">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                Se connecter
            </button>
        </form>
    </div>

    <div class="auth-footer">
        Pas encore de compte ? <a href="{{ route('register') }}">Créer un compte</a>
    </div>
</x-guest-layout>

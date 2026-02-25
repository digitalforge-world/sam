<x-guest-layout>
    @section('title', 'Inscription')

    <div class="auth-card-header">
        <div class="greeting">Rejoignez-nous</div>
        <h1>Créer un compte</h1>
        <p>Configurez votre accès à la plateforme SAM de certification biologique</p>
    </div>

    <div class="auth-form-card">
        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- Name --}}
            <div class="auth-form-group">
                <label class="auth-label" for="name">Nom complet</label>
                <div class="auth-input-wrapper">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <input id="name" type="text" name="name" class="auth-input {{ $errors->has('name') ? 'input-error' : '' }}" value="{{ old('name') }}" placeholder="Entrez votre nom" required autofocus autocomplete="name">
                </div>
                @error('name')
                    <div class="auth-error">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Email --}}
            <div class="auth-form-group">
                <label class="auth-label" for="email">Adresse email</label>
                <div class="auth-input-wrapper">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    <input id="email" type="email" name="email" class="auth-input {{ $errors->has('email') ? 'input-error' : '' }}" value="{{ old('email') }}" placeholder="votre@email.com" required autocomplete="username">
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
                    <input id="password" type="password" name="password" class="auth-input {{ $errors->has('password') ? 'input-error' : '' }}" placeholder="Min. 6 caractères" required autocomplete="new-password" style="padding-right:48px">
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

            {{-- Confirm Password --}}
            <div class="auth-form-group">
                <label class="auth-label" for="password_confirmation">Confirmer le mot de passe</label>
                <div class="auth-input-wrapper">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="auth-input" placeholder="Retapez votre mot de passe" required autocomplete="new-password" style="padding-right:48px">
                    <button type="button" class="toggle-password" tabindex="-1">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                @error('password_confirmation')
                    <div class="auth-error">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Submit --}}
            <button type="submit" class="auth-submit">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                Créer mon compte
            </button>
        </form>
    </div>

    <div class="auth-footer">
        Déjà inscrit ? <a href="{{ route('login') }}">Se connecter</a>
    </div>
</x-guest-layout>

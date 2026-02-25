<x-guest-layout>
    @section('title', 'Réinitialiser le mot de passe')

    <div class="auth-card-header">
        <div class="greeting">Sécurité</div>
        <h1>Nouveau mot de passe</h1>
        <p>Choisissez un nouveau mot de passe sécurisé pour votre compte</p>
    </div>

    <div class="auth-form-card">
        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            {{-- Email --}}
            <div class="auth-form-group">
                <label class="auth-label" for="email">Adresse email</label>
                <div class="auth-input-wrapper">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    <input id="email" type="email" name="email" class="auth-input {{ $errors->has('email') ? 'input-error' : '' }}" value="{{ old('email', $request->email) }}" placeholder="votre@email.com" required autofocus autocomplete="username">
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
                <label class="auth-label" for="password">Nouveau mot de passe</label>
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
                    <input id="password_confirmation" type="password" name="password_confirmation" class="auth-input" placeholder="Retapez le mot de passe" required autocomplete="new-password" style="padding-right:48px">
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
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Réinitialiser le mot de passe
            </button>
        </form>
    </div>

    <div class="auth-footer">
        <a href="{{ route('login') }}">← Retour à la connexion</a>
    </div>
</x-guest-layout>

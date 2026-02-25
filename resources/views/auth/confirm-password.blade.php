<x-guest-layout>
    @section('title', 'Confirmer le mot de passe')

    <div class="auth-card-header">
        <div class="greeting">Zone sécurisée</div>
        <h1>Confirmer le mot de passe</h1>
        <p>Ceci est une zone sécurisée de l'application. Veuillez confirmer votre mot de passe avant de continuer.</p>
    </div>

    <div class="auth-form-card">
        {{-- Security illustration --}}
        <div style="text-align:center;margin-bottom:24px;">
            <div style="width:80px;height:80px;margin:0 auto;background:linear-gradient(135deg,#FEF9C3,#FEF3C7);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#CA8A04" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
        </div>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            {{-- Password --}}
            <div class="auth-form-group">
                <label class="auth-label" for="password">Mot de passe</label>
                <div class="auth-input-wrapper">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <input id="password" type="password" name="password" class="auth-input {{ $errors->has('password') ? 'input-error' : '' }}" placeholder="Entrez votre mot de passe" required autocomplete="current-password" style="padding-right:48px">
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

            {{-- Submit --}}
            <button type="submit" class="auth-submit">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Confirmer
            </button>
        </form>
    </div>

    <div class="auth-footer">
        <a href="{{ route('dashboard') }}">← Retour au tableau de bord</a>
    </div>
</x-guest-layout>

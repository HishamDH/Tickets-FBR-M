<div class="language-switcher">
    <div class="flex items-center gap-2">
        <a href="{{ route('language.switch', ['lang' => 'en']) }}" class="language-item {{ app()->getLocale() == 'en' ? 'active' : '' }}" title="{{ __('app.english') }}">
            <span class="flag">🇬🇧</span>
            <span class="lang-name">{{ __('app.english') }}</span>
        </a>
        <span class="language-divider">|</span>
        <a href="{{ route('language.switch', ['lang' => 'ar']) }}" class="language-item {{ app()->getLocale() == 'ar' ? 'active' : '' }}" title="{{ __('app.arabic') }}">
            <span class="flag">🇸🇦</span>
            <span class="lang-name">{{ __('app.arabic') }}</span>
        </a>
    </div>
</div>

<style>
.language-switcher {
    display: flex;
    justify-content: flex-end;
    margin: 0.5rem 1rem;
}
.language-item {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    text-decoration: none;
    color: #333;
    transition: all 0.2s ease;
}
.language-item:hover {
    background-color: #f3f4f6;
    transform: translateY(-1px);
}
.language-item.active {
    background-color: #e5e7eb;
    font-weight: bold;
}
.language-divider {
    color: #d1d5db;
    margin: 0 0.25rem;
}
.flag {
    font-size: 1.2rem;
}
.lang-name {
    font-size: 0.875rem;
}

/* Hide language names on small screens, show only flags */
@media (max-width: 640px) {
    .lang-name {
        display: none;
    }
    .language-item {
        padding: 0.5rem;
    }
    .flag {
        font-size: 1.5rem;
    }
}
</style>

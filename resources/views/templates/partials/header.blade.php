@php
    $user = auth()->user();
    $displayName = $user->nickname ?: $user->username;
@endphp
<header class="app-header">
    <div class="app-header-start">
        <button type="button" class="app-icon-btn" data-sidebar-toggle aria-controls="sidebar-nav"
            aria-expanded="true" aria-label="@lang('Toggle navigation')">
            <svg width="20" height="20" viewBox="0 0 20 20" aria-hidden="true">
                <path d="M3 5h14M3 10h14M3 15h14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
        <a class="app-brand" href="{{ route('auth.dashboard') }}" aria-label="IVote — @lang('Dashboard')">
            <img src="{{ Vite::image('logo.svg') }}" alt="IVote" width="140" height="27">
        </a>
    </div>

    <div class="app-header-end">
        @include('templates.partials.language_switcher')

        <div class="ui-dropdown app-profile" data-dropdown data-align="end">
            <button type="button" class="ui-dropdown-trigger app-profile-trigger" aria-haspopup="menu"
                aria-expanded="false" aria-label="@lang('Account menu'): {{ $displayName }}">
                <img src="{{ Vite::image('user.png') }}" alt="" class="app-avatar" width="38" height="38">
                <span class="app-profile-name">{{ $user->username }}</span>
                <svg class="ui-dropdown-chevron" width="12" height="8" viewBox="0 0 12 8" aria-hidden="true">
                    <path d="M1 1l5 5 5-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <div class="ui-dropdown-menu app-profile-menu" role="menu">
                <div class="app-profile-card" role="presentation">
                    <img src="{{ Vite::image('user.png') }}" alt="" class="app-avatar app-avatar-lg" width="48" height="48">
                    <div class="app-profile-identity">
                        <strong>{{ $displayName }}</strong>
                        <span class="app-profile-username">{{ '@' . $user->username }}</span>
                        <span class="app-role-badge">{{ __($user->role->label()) }}</span>
                    </div>
                </div>
                <div class="ui-dropdown-separator" role="separator"></div>
                <a role="menuitem" class="ui-dropdown-item" href="{{ route('auth.password.change') }}">
                    <i class="lnr lnr-lock" aria-hidden="true"></i>
                    <span>@lang('Change password')</span>
                </a>
                <form action="{{ route('auth.logout') }}" method="post" class="m-0" role="none">
                    @method('delete')
                    @csrf
                    <button role="menuitem" type="submit" class="ui-dropdown-item ui-dropdown-item-danger">
                        <i class="lnr lnr-exit" aria-hidden="true"></i>
                        <span>@lang('Sign out')</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

@php
    $role = auth()->user()->role;

    // Navigation of each back-office role. A group opens by itself when it contains the current page.
    $menus = [
        \App\Enums\Role::EA->value => [
            ['label' => 'Dashboard', 'icon' => 'lnr-home', 'route' => 'ea.dashboard'],
            ['label' => 'Elections', 'icon' => 'lnr-inbox', 'children' => [
                ['label' => 'New election', 'icon' => 'lnr-file-add', 'route' => 'ea.addElection'],
                ['label' => 'Election list', 'icon' => 'lnr-list', 'route' => 'ea.election'],
            ]],
            ['label' => 'Blockchain', 'icon' => 'lnr-link', 'children' => [
                ['label' => 'Balances', 'icon' => 'lnr-chart-bars', 'route' => 'ea.balance'],
                ['label' => 'Voting fees', 'icon' => 'lnr-cart', 'route' => 'ea.fee'],
            ]],
            ['label' => 'New RA account', 'icon' => 'lnr-user', 'route' => 'auth.register'],
            ['label' => 'Settings', 'icon' => 'lnr-cog', 'route' => 'ea.setting'],
        ],
        \App\Enums\Role::RA->value => [
            ['label' => 'Dashboard', 'icon' => 'lnr-home', 'route' => 'ra.dashboard'],
            ['label' => 'Candidates', 'icon' => 'lnr-users', 'children' => [
                ['label' => 'New candidate', 'icon' => 'lnr-file-add', 'route' => 'ra.addCandidate'],
                ['label' => 'Candidate list', 'icon' => 'lnr-list', 'route' => 'ra.candidates'],
            ]],
            ['label' => 'Voters', 'icon' => 'lnr-license', 'children' => [
                ['label' => 'New voter', 'icon' => 'lnr-file-add', 'route' => 'ra.addVoter'],
                ['label' => 'Ballot codes', 'icon' => 'lnr-cloud', 'route' => 'ra.ballot'],
            ]],
            ['label' => 'Results', 'icon' => 'lnr-pie-chart', 'route' => 'tally.home'],
        ],
    ];
    $items = $menus[$role->value] ?? [];
@endphp
<aside id="sidebar-nav" class="app-sidebar" aria-label="@lang('Main navigation')">
    <nav class="app-nav">
        <ul class="app-nav-list">
            @foreach ($items as $index => $item)
                @if (isset($item['children']))
                    @php
                        $open = collect($item['children'])->contains(fn ($child) => request()->routeIs($child['route']));
                        $groupId = 'nav-group-' . $index;
                    @endphp
                    <li class="app-nav-group {{ $open ? 'is-open' : '' }}">
                        <button type="button" class="app-nav-link" data-nav-group aria-expanded="{{ $open ? 'true' : 'false' }}"
                            aria-controls="{{ $groupId }}" data-tooltip="{{ __($item['label']) }}">
                            <i class="lnr {{ $item['icon'] }} app-nav-icon" aria-hidden="true"></i>
                            <span class="app-nav-label">@lang($item['label'])</span>
                            <svg class="app-nav-caret" width="10" height="10" viewBox="0 0 10 10" aria-hidden="true">
                                <path d="M3 2l3 3-3 3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="app-subnav" id="{{ $groupId }}">
                            <ul class="app-subnav-list">
                                @foreach ($item['children'] as $child)
                                    @php $active = request()->routeIs($child['route']); @endphp
                                    <li>
                                        <a class="app-nav-link app-subnav-link {{ $active ? 'is-active' : '' }}"
                                            href="{{ route($child['route']) }}" @if ($active) aria-current="page" @endif>
                                            <span class="app-nav-label">@lang($child['label'])</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                @else
                    @php $active = request()->routeIs($item['route']); @endphp
                    <li>
                        <a class="app-nav-link {{ $active ? 'is-active' : '' }}" href="{{ route($item['route']) }}"
                            data-tooltip="{{ __($item['label']) }}" @if ($active) aria-current="page" @endif>
                            <i class="lnr {{ $item['icon'] }} app-nav-icon" aria-hidden="true"></i>
                            <span class="app-nav-label">@lang($item['label'])</span>
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </nav>

    <div class="app-sidebar-footer">
        <form action="{{ route('auth.logout') }}" method="post" class="m-0">
            @method('delete')
            @csrf
            <button type="submit" class="app-nav-link app-nav-logout" data-tooltip="@lang('Sign out')">
                <i class="lnr lnr-exit app-nav-icon" aria-hidden="true"></i>
                <span class="app-nav-label">@lang('Sign out')</span>
            </button>
        </form>
        <button type="button" class="app-nav-link app-collapse-btn" data-sidebar-collapse
            aria-label="@lang('Collapse sidebar')" data-label-collapse="@lang('Collapse sidebar')"
            data-label-expand="@lang('Expand sidebar')">
            <svg class="app-nav-icon" width="18" height="18" viewBox="0 0 18 18" aria-hidden="true">
                <path d="M11 4l-5 5 5 5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="app-nav-label">@lang('Collapse')</span>
        </button>
    </div>
</aside>
<div class="app-sidebar-backdrop" data-sidebar-close hidden></div>

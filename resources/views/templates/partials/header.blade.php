<!-- NAVBAR -->
<style>
    .user-profile-name {
        font-size: 20px;
    }

    a.dropdown-toggle.profile {
        display: flex;
        gap: 8px;
        justify-content: center;
        align-items: center;
        padding: 0px;
        text-decoration: none;
        color: #B8B8B8 !important;
    }

    .nav-link .text-center {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    i.lnr.lnr-home {
        margin-bottom: 4px;
    }

    i.lnr.lnr-lock {
        margin-bottom: 2px;
    }

    .dropdown-toggle::after {
        display: none;
    }

    .toggle-icon {
        transition: transform 0.3s;
        transition: transform 0.3s;
        position: relative;
        left: 231px;
        top: 15px;
    }

    .rotated {
        transform: rotate(180deg);
    }

    .dropdown-menu {
        width: 100%;
        background-color: #fff;
        border: 0;
        border-radius: 10px;
        padding: 0;
    }

    .dropdown-menu.show {
        width: 50%;
    }

    .dropdown-menu>li>a {
        color: black !important;
        border-radius: 6px;
        padding: 10px;
    }

    .dropdown-menu>li {
        padding: 5px;
    }

    .dropdown-item:hover,
    .logout-btn:hover {
        background-color: #6610F2 !important;
        color: #fff !important;
    }

    .logout-btn {
        border: 0;
        background-color: transparent;
        width: 100%;
        padding: 9px;
        border-radius: 6px;
    }

    .navbar-toggler {
        background-color: rgba(217, 217, 217, 0.20);
        border-radius: 15px
    }
</style>
<nav class="navbar navbar-expand-lg fixed-top" style="background-color: #2D2D2D; border-bottom: 2px solid #B8B8B8;">
    <div class="container-fluid">
        <a class="navbar-brand me-3" href="#">
            <img src="{{ Vite::image('logo.svg') }}" alt="Logo"
                class="img-responsive logo d-inline-block align-text-top" width="180" height="35">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-between" id="navbarSupportedContent">
            <div class="div-toggle-icon btn-toggle-fullwidth">
            </div>
            <div class="phone-nav" style="display: none;">
                @if (\Illuminate\Support\Facades\Auth::check())
                    <a class="menu-dashboard nav-link active text-center p-3" aria-current="page"
                        href="{{ route('auth.dashboard') }}">
                        <i class="lnr lnr-home"></i>
                        <span>Dashboard</span>
                    </a>
                    @if (\Illuminate\Support\Facades\Auth::user()->role === \App\Enums\Role::RA)
                        <a class="nav-link text-center p-3" aria-current="page" href="#subVoters"
                            data-bs-toggle="collapse" data-bs-target="#subVoters" aria-expanded="false">
                            <i class="lnr lnr-file-empty"></i>
                            <span>Voters</span>
                            <i class="icon-submenu lnr lnr-chevron-right"></i>
                        </a>
                        <div class="collapse w-100" id="subVoters">
                            <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                <li class="menu-addVoter">
                                    <a class="nav-link text-center p-3" href="{{ route('ra.addVoter') }}">
                                        <i class="lnr lnr-file-add"></i> <span>Add a voter</span>
                                    </a>
                                </li>
                                <li class="menu-ballot">
                                    <a class="nav-link  text-center p-3" href="{{ route('ra.ballot') }}">
                                        <i class="lnr lnr-cloud"></i> <span>Ballot Code</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <a class="nav-link text-center p-3" href="#subCandidates" data-bs-toggle="collapse"
                            data-bs-target="#subCandidates" aria-expanded="false">
                            <i class="lnr lnr-inbox"></i>
                            <span>Candidates</span>
                            <i class="icon-submenu lnr lnr-chevron-right"></i>
                        </a>
                        <div class="collapse w-100" id="subCandidates">
                            <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                <li class="menu-addcandidate sub-menu">
                                    <a class="nav-link text-center p-3" href="{{ route('ra.addCandidate') }}">
                                        <i class="lnr lnr-file-add"></i> <span>Add a candidate</span>
                                    </a>
                                </li>
                                <li class="menu-ballot">
                                    <a class="nav-link text-center p-3" href="{{ route('ra.candidates') }}">
                                        <i class="lnr lnr-cloud"></i> <span>Candidate List</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <a class="menu-result nav-link text-center p-3" href="{{ route('tally.home') }}">
                            <i class="lnr lnr-list"></i> <span>View Result</span>
                        </a>
                    @endif
                    @if (\Illuminate\Support\Facades\Auth::user()->role === \App\Enums\Role::EA)
                        <a class="nav-link text-center p-3" aria-current="page" href="#subBlockChain"
                            data-bs-toggle="collapse" data-bs-target="#subBlockChain" aria-expanded="false">
                            <i class="lnr lnr-bold"></i>
                            <span>Blockchain</span>
                            <i class="icon-submenu lnr lnr-chevron-left"></i>
                        </a>
                        <div class="collapse w-100" id="subBlockChain">
                            <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                <li>
                                    <a class="nav-link text-center p-3" href="{{ route('ea.balance') }}">
                                        <i class="lnr lnr-list"></i> <span>Balance List</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link text-center p-3" href="{{ route('ea.fee') }}">
                                        <i class="lnr lnr-cart"></i> <span>Voting Fee</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <a class="nav-link text-center p-3" href="#subVoting" data-bs-toggle="collapse"
                            data-bs-target="#subVoting" aria-expanded="false">
                            <i class="lnr lnr-inbox"></i>
                            <span>Voting</span>
                            <i class="icon-submenu lnr lnr-chevron-left"></i>
                        </a>
                        <div class="collapse w-100" id="subVoting">
                            <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                <li>
                                    <a class="nav-link text-center p-3" href="{{ route('ea.addElection') }}">
                                        <i class="lnr lnr-file-add"></i> <span>Add an Election</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link text-center p-3" href="{{ route('ea.election') }}">
                                        <i class="lnr lnr-cloud"></i> <span>Voting List</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <a class="menu-setting nav-link text-center p-3" href="{{ route('ea.setting') }}">
                            <i class="lnr lnr-cog"></i> <span>Setting</span>
                        </a>
                    @endif
                    @if (\Illuminate\Support\Facades\Auth::user()->role === \App\Enums\Role::VOTER)
                        <a class="menu-vote nav-link text-center p-3" href="{{ route('voter.vote') }}">
                            <i class="lnr lnr-inbox"></i>
                            <span>View Result</span>
                        </a>
                        <a class="nav-link text-center p-3" href="{{ route('auth.password.change') }}">
                            <i class="fa fa-star"></i> <span>Vote Now</span>
                        </a>
                        <a class="nav-link text-center p-3" href="{{ route('auth.password.change') }}">
                            <i class="fa fa-th-list"></i> <span>View Result</span>
                        </a>
                    @endif
                    <a class="menu-change nav-link text-center p-3" href="{{ route('auth.password.change') }}">
                        <i class="lnr lnr-lock"></i> <span>Change Password</span>
                    </a>
                    <form class="menu-singout nav-link text-center p-3" action="{{ route('auth.logout') }}"
                        method="post">
                        @method('delete')
                        @csrf
                        <button class="logout-btn" type="submit" value="Log Out" style="color: #CED5E1"><i
                                class="lnr lnr-exit"></i>
                            Sign Out</button>
                    </form>
                @endif
            </div>
            <div class="nav-item dropdown d-flex align-items-center justify-content-center flex-wrap">
                <a href="#" class="dropdown-toggle profile text-center p-3" id="navbarDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ Vite::image('user.png') }}" class="" alt="user profile" width="40"
                        height="40">
                    <span class="user-profile-name">{{ \Illuminate\Support\Facades\Auth::user()->nickname }}</span>
                    <i class="fa fa-circle-chevron-down"></i>
                </a>
                <ul class="dropdown-menu" id="navbarDropdown">
                    <li>
                        <a class="dropdown-item text-center" href="{{ route('auth.password.change') }}">
                            <i class="lnr lnr-lock"></i> <span>Change Password</span>
                        </a>
                    </li>
                    <li>
                        <form action="{{ route('auth.logout') }}" method="post">
                            @method('delete')
                            @csrf
                            <button class="logout-btn" type="submit" value="Log Out"><i class="lnr lnr-exit"></i>
                                Logout</button>
                        </form>
                    </li>
                </ul>
                <div
                    class="d-flex justify-content-center pt-8 justify-content-sm-start bg-light p-1 align-items-center rounded-2 p-2 ps-3">
                    @include('templates.partials.language_switcher')
                </div>
            </div>

        </div>
    </div>
</nav>
<!-- END NAVBAR -->

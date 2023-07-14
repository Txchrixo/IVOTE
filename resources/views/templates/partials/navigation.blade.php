<!-- LEFT SIDEBAR -->
<style>
    .nav-link, .nav-link:focus  {
        color: #CED5E1;
    }
    .nav-link:hover {
        color: white;
    }
    .nav-link.active {
        color: #6610F2;
    }
</style>
<div id="sidebar-nav" class="sidebar sidebar-fixed"
    style="background-image: url('{{ Vite::image('background.jpg') }}'); background-repeat: no-repeat; background-size: cover;">
    <div class="div-toggle-icon btn-toggle-fullwidth">
        <img src="{{ Vite::image('menu-swip.png') }}" class="toggle-icon" alt="toggle bouton" width="45"
            height="45">
    </div>
    <nav class="nav d-flex flex-column gap-2">
        @if (\Illuminate\Support\Facades\Auth::check())
            <a class="menu-dashboard nav-link @if(url()->current() == url()->route('ea.dashboard')) active disabled @endif" aria-current="page" href="{{ route('auth.dashboard') }}">
                <i class="lnr lnr-home"></i>
                <span>Dashboard</span>
            </a>
            @if (\Illuminate\Support\Facades\Auth::user()->role === \App\Enums\Role::RA)
                <a class="nav-link" href="#subVoters" data-bs-toggle="collapse" data-bs-target="#subVoters" aria-expanded="false">
                    <i class="lnr lnr-file-empty"></i>
                    <span>Voters</span>
                    <i class="icon-submenu lnr lnr-chevron-right"></i>
                </a>
                <div class="collapse w-100" id="subVoters">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-0 small">
                        <li class="menu-addVoter">
                            <a  class="nav-link" href="{{ route('ra.addVoter') }}">
                                <i class="lnr lnr-file-add"></i> <span>Add a voter</span>
                            </a>
                        </li>
                        <li class="menu-ballot">
                            <a class="nav-link" href="{{ route('ra.ballot') }}">
                                <i class="lnr lnr-cloud"></i> <span>Ballot Code</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <a class="nav-link" href="#subCandidates" data-bs-toggle="collapse" data-bs-target="#subCandidates" aria-expanded="false">
                    <i class="lnr lnr-inbox"></i>
                    <span>Candidates</span>
                    <i class="icon-submenu lnr lnr-chevron-right"></i>
                </a>
                <div class="collapse w-100" id="subCandidates">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-0 small">
                        <li class="menu-addcandidate sub-menu">
                            <a  class="nav-link" href="{{ route('ra.addCandidate') }}">
                                <i class="lnr lnr-file-add"></i> <span>Add a candidate</span>
                            </a>
                        </li>
                        <li class="menu-ballot">
                            <a class="nav-link" href="{{ route('ra.candidates') }}">
                                <i class="lnr lnr-cloud"></i> <span>Candidate List</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <a class="menu-result nav-link" href="{{ route('tally.home') }}">
                    <i class="lnr lnr-list"></i> <span>View Result</span>
                </a>
            @endif
            @if (\Illuminate\Support\Facades\Auth::user()->role === \App\Enums\Role::EA)
                <a class="nav-link" aria-current="page" href="#subBlockChain" data-bs-toggle="collapse" data-bs-target="#subBlockChain" aria-expanded="false">
                    <i class="lnr lnr-bold"></i>
                    <span>Blockchain</span>
                    <i class="icon-submenu lnr lnr-chevron-right"></i>
                </a>
                <div class="collapse w-100" id="subBlockChain">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-0 small">
                        <li>
                            <a class="nav-link" href="{{ route('ea.balance') }}">
                                <i class="lnr lnr-list"></i> <span>Balance List</span>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link" href="{{ route('ea.fee') }}">
                                <i class="lnr lnr-cart"></i> <span>Voting Fee</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <a class="nav-link" href="#subVoting" data-bs-toggle="collapse" data-bs-target="#subVoting" aria-expanded="false">
                    <i class="lnr lnr-inbox"></i>
                    <span>Voting</span>
                    <i class="icon-submenu lnr lnr-chevron-right"></i>
                </a>
                <div class="collapse w-100" id="subVoting">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-0 small">
                        <li>
                            <a class="nav-link" href="{{ route('ea.addElection') }}">
                                <i class="lnr lnr-file-add"></i> <span>Add an Election</span>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link" href="{{ route('ea.election') }}">
                                <i class="lnr lnr-cloud"></i> <span>Voting List</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <a class="menu-setting nav-link" href="{{ route('ea.setting') }}">
                    <i class="lnr lnr-cog"></i> <span>Setting</span>
                </a>
            @endif

            @if (\Illuminate\Support\Facades\Auth::user()->role === \App\Enums\Role::VOTER)
                <a class="menu-vote nav-link" href="{{ route('voter.vote') }}">
                    <i class="lnr lnr-inbox"></i>
                    <span>View Result</span>
                </a>
                <a class="nav-link" href="{{ route('auth.password.change') }}">
                    <i class="fa fa-star"></i> <span>Vote Now</span>
                </a>
                <a class="nav-link" href="{{ route('auth.password.change') }}">
                    <i class="fa fa-th-list"></i> <span>View Result</span>
                </a>
            @endif
            <a class="menu-change nav-link" href="{{ route('auth.password.change') }}">
                <i class="lnr lnr-lock"></i> <span>Change Password</span>
            </a>


        @endif
        <form class="menu-singout nav-link" action="{{ route('auth.logout') }}" method="post" style="margin-top: 95%;">
            @method('delete')
            @csrf
            <button class="logout-btn" type="submit" value="Log Out" style="color: #CED5E1"><i class="lnr lnr-exit"></i>
                Logout</button>
        </form>
    </nav>
</div>
<!-- END LEFT SIDEBAR -->

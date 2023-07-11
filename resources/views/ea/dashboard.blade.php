@extends('templates.app')

@section('content')
    <input type="hidden" class="blockchain" value="bitcoin" data-network="testnet"
        data-address="{{ $data['bitcoin_address'] }}">
    <input type="hidden" class="blockchain" value="bitcoin" data-network="testnet"
        data-address="{{ $data['bitcoin_address'] }}">
    <div class="panel panel-headline"
        style="background-image: url('{{ Vite::image('bg.jpg') }}'); border-radius: 15px; background-repeat: no-repeat; background-size: 100%;">
        <div class="panel-heading">
            <h3 class="panel-title">Overview</h3>
            <p class="panel-subtitle">Welcome back, </p>
        </div>
        <div class="panel-body">
            <div class="col">
                <div class="row py-3 mb-3"
                    style="background-color: rgba(217, 217, 217, 0.14); border-radius: 15px; border:1px solid #DBE5EA;">
                    <h3 class="card-title p-3">Blockchains</h3>
                    <div class="col-md-4 pt-2">
                        <div class="metric" style="border-radius: 15px;">
                            <div class="d-flex justify-content-around align-items-center p-2">
                                <div>
                                    <img src="{{ Vite::image('bitcoin.png') }}" alt="toggle bouton" width="50%"
                                        height="auto">
                                </div>
                                <div class="d-flex flex-column gap-3">
                                    <p class="d-flex flex-column align-items-end justify-content-center">
                                        <span class="number number-btc">Loading..</span>
                                        <span class="title">Balance</span>
                                    </p>
                                    <p>
                                        <span class="number number-network">Loading..</span>
                                        <span class="title">Network</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 pt-2">
                        <div class="metric" style="border-radius: 15px;">
                            <div class="d-flex justify-content-around align-items-center p-2">
                                <div>
                                    <img src="{{ Vite::image('eth.png') }}" alt="toggle bouton" width="30%"
                                        height="auto">
                                </div>
                                <div class="d-flex flex-column gap-3">
                                    <p class="d-flex flex-column align-items-end justify-content-center">
                                        <span class="number number-eth">Loading..</span>
                                        <span class="title">Balance</span>
                                    </p>
                                    <p>
                                        <span class="number number-eth-network">Loading..</span>
                                        <span class="title">Network</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 pt-2">
                        <div class="metric" style="border-radius: 15px;">
                            <div class="d-flex justify-content-around align-items-center p-2">
                                <div>
                                    <img src="{{ Vite::image('solana.png') }}" alt="toggle bouton" width="50%"
                                        height="auto">
                                </div>
                                <div class="d-flex flex-column gap-3">
                                    <p class="d-flex flex-column align-items-end justify-content-center">
                                        <span class="number number-solana">Loading..</span>
                                        <span class="title">Balance</span>
                                    </p>
                                    <p>
                                        <span class="number number-solana-network">Loading..</span>
                                        <span class="title">Network</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row py-3 mb-3"
                    style="background-color: rgba(217, 217, 217, 0.14); border-radius: 15px; border:1px solid #DBE5EA;">
                    <h3 class="card-title p-3">System Information</h3>
                    <div class="col-md-6 pt-2">
                        <div class="metric" style="border-radius: 15px;">
                            <div class="d-flex justify-content-around align-items-center p-2">
                                <div>
                                    <img src="{{ Vite::image('elections.png') }}" alt="toggle bouton" width="20%"
                                        height="auto">
                                </div>
                                <div class="d-flex flex-column gap-3">
                                    <p class="d-flex flex-column align-items-end justify-content-center">
                                        <span class="number">{{ $data['items'] }}</span>
                                        <span class="title">Total Elections</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 pt-2">
                        <div class="metric" style="border-radius: 15px;">
                            <div class="d-flex justify-content-around align-items-center p-2">
                                <div>
                                    <img src="{{ Vite::image('vote.png') }}" alt="toggle bouton" width="20%"
                                        height="auto">
                                </div>
                                <div class="d-flex flex-column gap-3">
                                    <p class="d-flex flex-column align-items-end justify-content-center">
                                        <span class="number">{{ $data['voters'] }}</span>
                                        <span class="title">Total Voters</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col py-3 mb-3"
                    style="background-color: rgba(217, 217, 217, 0.14); border-radius: 15px; border:1px solid #DBE5EA;">
                    <h3 class="card-title p-3">Ongoing Elections</h3>
                    @foreach ($election as $item)
                        <div class="row py-3 m-3"
                            style="background-color: rgba(217, 217, 217, 0.14); border-radius: 15px; border:1px solid #DBE5EA;">
                            <div class="col-md-8">
                                <div class="profile-header">
                                    <div class="overlay"></div>
                                    <div class="profile-main"
                                        style='background-image: url({{ $item['bg'] != null ? Vite::bg($item['bg']) : Vite::bg('voting-bg.jpg') }}); height:220px;'>
                                        <h3 class="name">{{ $item['title'] }}</h3>
                                        @if ($item['stage'] == 0)
                                            <span class="offline-status status-available">Unbegun</span>
                                        @elseif($item['stage'] == 1)
                                            <span class="online-status status-available">Veriff</span>
                                            @if ($item['start_vote'])
                                                <h2 class="mt-5 position-relative h5"
                                                    style="z-index: 999; color:#DBE5EA;">La vérification se terminera le
                                                    {{ $item['start_veriff'] }}</h2>
                                            @endif
                                        @elseif ($item['stage'] == 2)
                                            <span class="offline-status status-available">Waiting</span>
                                            @if ($item['start_vote'])
                                                <h2 class="mt-5 position-relative h5"
                                                    style="z-index: 999; color:#DBE5EA;">La vérification des électeurs a
                                                    pri fin le {{ $item['end_veriff'] }}. Cette election débutera le
                                                    {{ $item['start_vote'] }}</h2>
                                            @endif
                                        @elseif($item['stage'] == 3)
                                            <span class="online-status status-available">Voting Ongoing</span>
                                            @if ($item['end_vote'])
                                                <h2 class="mt-5 position-relative h5"
                                                    style="z-index: 999; color:#DBE5EA;">L'élection s'achèvera le
                                                    {{ $item['end_vote'] }}</h2>
                                            @endif
                                        @elseif($item['stage'] == 4)
                                            <span class="online-status status-available">Done</span>
                                            @if ($item['start_vote'])
                                                <h2 class="mt-5 position-relative h5"
                                                    style="z-index: 999; color:#DBE5EA;">Cette élection a débuté le
                                                    {{ $item['start_vote'] }} et s'est achevé le {{ $item['end_vote'] }},
                                                    vous pouvez voir les résultats</h2>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="profile-stat">
                                        <div class="row mx-0">
                                            @if ($item['stage'] == 0)
                                                <a class="col-md-4 stat-item btn-toggleStage btn-a"
                                                    data-id="{{ $item['id'] }}"
                                                    style="border-bottom-left-radius: 15px;">Start Veriff</a>
                                            @elseif($item['stage'] == 1)
                                                <a class="col-md-4 stat-item btn-toggleStage a"
                                                    data-id="{{ $item['id'] }}"
                                                    style="border-bottom-left-radius: 15px;">Stop Veriff</a>
                                            @elseif ($item['stage'] == 2)
                                                <a class="col-md-4 stat-item btn-toggleStage a"
                                                    data-id="{{ $item['id'] }}"
                                                    style="border-bottom-left-radius: 15px;">Start Voting</a>
                                            @elseif($item['stage'] == 3)
                                                <a class="col-md-4 stat-item btn-toggleStage a"
                                                    data-id="{{ $item['id'] }}"
                                                    style="border-bottom-left-radius: 15px;">Stop Voting</a>
                                            @elseif($item['stage'] == 4)
                                                <a class="col-md-4 stat-item btn-toggleStage a"
                                                    data-id="{{ $item['id'] }}"
                                                    style="border-bottom-left-radius: 15px;">See Results</a>
                                            @endif
                                            <a href="#" class="col-md-4 stat-item btn-getElection" data-bs-toggle="modal"
                                                value="Manage" data-bs-target="#electionModal" data-id="{{ $item['id'] }}">Manage</a>
                                            <a href="#" class="col-md-4 stat-item btn-toggle c"
                                                data-id="{{ $item['id'] }}"
                                                style="border-bottom-right-radius: 15px;">Tally</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 px-3 ps-0">
                                <div class="metric bg-transparent border-0 p-4" style="border-radius: 15px;">
                                    <div class="d-flex justify-content-end align-items-center p-2">
                                        <div class="d-flex flex-column gap-4">
                                            <p class="d-flex flex-column align-items-end justify-content-center">
                                                <span class="number">{{ $data['voters'] }}</span>
                                                <span class="title">Registered Voters Numbers</span>
                                            </p>
                                            <p class="d-flex flex-column align-items-end justify-content-center">
                                                <span class="number">{{ $data['voters'] }}</span>
                                                <span class="title">Blockchain Address Numbers</span>
                                            </p>
                                            <p class="d-flex flex-column align-items-end justify-content-center">
                                                <span class="number">{{ $data['voters'] }}</span>
                                                <span class="title">Voted Numbers</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if (empty($election))
                        No data
                    @endif

                    <div class="w-100 d-flex justify-content-center align-items-center">
                        <a class="p-2 text-decoration-none" href="{{route("ea.election")}}">Voir Plus</a>
                    </div>
                </div>
            </div>
            @include('templates.partials.footer')
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="electionModal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1"
        role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content rounded-4">
                <form id='electionForm'>
                    {{ csrf_field() }}
                    <div class="modal-header justify-content-center p-4">
                        <h4 class="modal-title" style="font-family: 'Helvetica Bold', sans-serif;">Manage Election</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group my-2">
                                    <label for="title">Title</label>
                                    <input type="text" name="title" id="title"
                                        class="form-control title p-3 rounded-3">
                                    @error('title')
                                        <span class="text-white p-1 m-3"
                                            style="background-color: #DD090B">{{ $message }}</span>
                                    @enderror
                                    <span class="text-danger error-title" hidden></span>
                                </div>

                                <div class="form-group my-3">
                                    <label for="number">Numbers of voters</label>
                                    <input type="text" name="number" id="number"
                                        class="form-control number p-3 rounded-3">

                                    <span class="text-danger error-number" hidden></span>
                                </div>
                                <div class="form-group my-3">
                                    <label for="description">Description</label>
                                    <textarea id="description" name="description" class="form-control description p-3 rounded-3" placeholder=""
                                        rows="10"></textarea>
                                    <span class="text-danger error-description" hidden></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row row-cols-2">
                                    <!-- Date de début de la veriff -->
                                    <div class="col">
                                        <div class="form-group my-3">
                                            <label for="start_veriff">Start Veriff</label>
                                            <input type="datetime-local" name="start_veriff" id="start_veriff"
                                                class="form-control p-3 rounded-3">
                                            <span class="text-danger error-start_veriff" hidden></span>
                                        </div>
                                    </div>

                                    <!-- Date de fin de la veriff -->
                                    <div class="col">
                                        <div class="form-group my-3">
                                            <label for="end_veriff">End Veriff</label>
                                            <input type="datetime-local" name="end_veriff" id="end_veriff"
                                                class="form-control p-3 rounded-3">
                                            <span class="text-danger error-end_veriff" hidden></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-cols-2">
                                    <!-- Date de début de vote -->
                                    <div class="col">
                                        <div class="form-group my-3">
                                            <label for="start_vote">Start Vote</label>
                                            <input type="datetime-local" name="start_vote" id="start_vote"
                                                class="form-control p-3 rounded-3">
                                            <span class="text-danger error-start_vote" hidden></span>
                                        </div>
                                    </div>

                                    <!-- Date de fin de vote -->
                                    <div class="col">
                                        <div class="form-group my-3">
                                            <label for="end_vote">End Vote</label>
                                            <input type="datetime-local" name="end_vote" id="end_vote"
                                                class="form-control p-3 rounded-3">
                                            <span class="text-danger error-end_vote" hidden></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Phase de l'élection -->
                                <div class="form-group my-3">
                                    <label for="stage">Stage</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="stage"
                                                id="stage_initial" value="0">
                                            <label class="form-check-label" for="stage_initial">Initial</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="stage"
                                                id="stage_verif" value="1">
                                            <label class="form-check-label" for="stage_verif">Verification</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="stage"
                                                id="stage_waiting" value="2">
                                            <label class="form-check-label" for="stage_waiting">Waiting</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="stage"
                                                id="stage_ongoing" value="3">
                                            <label class="form-check-label" for="stage_ongoing">Ongoing</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="stage"
                                                id="stage_done" value="4">
                                            <label class="form-check-label" for="stage_done">Done</label>
                                        </div>
                                    </div>
                                    <span class="text-danger error-stage" hidden></span>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-primary btn-postElection rounded-3" value="Save changes">
                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

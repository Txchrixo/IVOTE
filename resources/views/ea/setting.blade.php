@extends('templates.app')

@section('content')
    <style>
        .tab-content>.active {
            display: flex !important;
        }
    </style>
    <div class="row">
        <div class="col-md-12">
            <!-- PANEL HEADLINE -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="bitcoin-tab" data-bs-toggle="tab" data-bs-target="#bitcoin"
                        type="button" role="tab" aria-controls="bitcoin" aria-selected="true">Bitcoin</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="eth-tab" data-bs-toggle="tab" data-bs-target="#eth" type="button"
                        role="tab" aria-controls="eth" aria-selected="false">Ethereum</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="solana-tab" data-bs-toggle="tab" data-bs-target="#solana" type="button"
                        role="tab" aria-controls="solana" aria-selected="false">Solana</button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="row tab-pane fade show active" id="bitcoin" role="tabpanel"
                    aria-labelledby="bitcoin-tab">
                    <div class="col-md-5">
                        <div class="panel panel-headline" style="border-radius: 15px;">
                            <div class="panel-heading">
                                <h3 class="panel-title">Blockchain Network</h3>
                                <p class="panel-subtitle">Select your blockchain network to publish your vote sigs</p>
                            </div>
                            <div class="panel-body network-select">
                                <div class="row">
                                    <div class="col-md-6 d-flex flex-column justify-content-center align-items-center">
                                        <img src="{{ Vite::image('bitcoin.png') }}" alt="" class="img-rounded"
                                            width="50%" height="auto">
                                        <label class="fancy-radio">
                                            <input name="bitcoin_network" value="bitcoin" type="radio">
                                            <span><i></i>Bitcoin</span>
                                        </label>
                                    </div>
                                    <div class="col-md-6 d-flex flex-column justify-content-center align-items-center">
                                        <img src="{{ Vite::image('testnet.png') }}" alt="" class="img-rounded"
                                            width="50%" height="auto">
                                        <label class="fancy-radio">
                                            <input name="bitcoin_network" value="testnet" type="radio" checked>
                                            <span><i></i>Testnet</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END PANEL HEADLINE -->
                    </div>
                    <div class="col-md-7">
                        <!-- PANEL NO PADDING -->
                        <div class="panel  panel-headline" style="border-radius: 15px;">
                            <div class="panel-heading">
                                <h3 class="panel-title">Address & Keys</h3>
                                <p class="panel-subtitle">The EA's address and private keys</p>
                            </div>
                            <div class="panel-body">
                                <form action="{{ route('ea.bitcoin.setting') }}" method="post">
                                    @csrf
                                    @if ($message = Session::get('bitcoin_success'))
                                        <div class="alert alert-success alert-block">{{ $message }}</div>
                                    @endif
                                    <div class="form-group mb-3">
                                        <label for="bitcoin_address">Bitcoin Address</label>
                                        <input type="text"
                                            class="form-control mt-1 rounded-2 @error('bitcoin_address') is-invalid @enderror"
                                            id="bitcoin_address" name="bitcoin_address"
                                            value="{{ $user['bitcoin_address'] }}">
                                        @error('bitcoin_address')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group mt-2">
                                        <label for="bitcoin_priv_key">Private Key</label>
                                        <textarea name="bitcoin_priv_key" class="form-control mt-1 rounded-2 @error('bitcoin_priv_key') is-invalid @enderror"
                                            id="bitcoin_priv_key" rows="5" autocomplete="off" spellcheck="false" placeholder="{{ $user->getRawOriginal('bitcoin_priv_key') ? __('Clé configurée (chiffrée). Laisser vide pour la conserver.') : __('Aucune clé configurée') }}"></textarea>
                                    </div>
                                    @error('bitcoin_priv_key')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <div class="button-right mt-3">
                                        <button type="submit" class="btn btn-primary rounded-2 p-2 px-3">Confirm</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- END PANEL NO PADDING -->
                    </div>
                </div>
                <div class="row tab-pane fade show" id="eth" role="tabpanel" aria-labelledby="eth-tab">
                    <div class="col-md-5">
                        <div class="panel panel-headline" style="border-radius: 15px;">
                            <div class="panel-heading">
                                <h3 class="panel-title">Blockchain Network</h3>
                                <p class="panel-subtitle">Select your blockchain network to publish your vote sigs</p>
                            </div>
                            <div class="panel-body network-select">
                                <div class="row">
                                    <div class="col-md-6 d-flex flex-column justify-content-end align-items-center">
                                        <img src="{{ Vite::image('eth.png') }}" alt="" class="img-rounded"
                                            width="32%" height="auto">
                                        <label class="fancy-radio">
                                            <input name="eth_network" value="eth" type="radio">
                                            <span><i></i>Ethereum</span>
                                        </label>
                                    </div>
                                    <div class="col-md-6 d-flex flex-column justify-content-center align-items-center">
                                        <img src="{{ Vite::image('testnet.png') }}" alt="" class="img-rounded"
                                            width="50%" height="auto">
                                        <label class="fancy-radio">
                                            <input name="eth_network" value="testnet" type="radio" checked>
                                            <span><i></i>Testnet</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END PANEL HEADLINE -->
                    </div>
                    <div class="col-md-7">
                        <!-- PANEL NO PADDING -->
                        <div class="panel  panel-headline" style="border-radius: 15px;">
                            <div class="panel-heading">
                                <h3 class="panel-title">Address & Keys</h3>
                                <p class="panel-subtitle">The EA's address and private keys</p>
                            </div>
                            <div class="panel-body">
                                <form action="{{ route('ea.eth.setting') }}" method="post">
                                    @csrf
                                    @if ($message = Session::get('eth_success'))
                                        <div class="alert alert-success alert-block">{{ $message }}</div>
                                    @endif
                                    <div class="form-group mb-3">
                                        <label for="eth_address">Ethereum Address</label>
                                        <input type="text"
                                            class="form-control mt-1 rounded-2 @error('eth_address') is-invalid @enderror"
                                            id="eth_address" name="eth_address" value="{{ $user['eth_address'] }}">
                                        @error('eth_address')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group mt-2">
                                        <label for="eth_priv_key">Private Key</label>
                                        <textarea name="eth_priv_key" class="form-control mt-1 rounded-2 @error('eth_priv_key') is-invalid @enderror"
                                            id="eth_priv_key" rows="5" autocomplete="off" spellcheck="false" placeholder="{{ $user->getRawOriginal('eth_priv_key') ? __('Clé configurée (chiffrée). Laisser vide pour la conserver.') : __('Aucune clé configurée') }}"></textarea>
                                    </div>
                                    @error('eth_priv_key')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <div class="button-right mt-3">
                                        <button type="submit" class="btn btn-primary rounded-2 p-2 px-3">Confirm</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- END PANEL NO PADDING -->
                    </div>
                </div>
                <div class="row tab-pane fade show" id="solana" role="tabpanel"
                    aria-labelledby="solana-tab">
                    <div class="col-md-5">
                        <div class="panel panel-headline" style="border-radius: 15px;">
                            <div class="panel-heading">
                                <h3 class="panel-title">Blockchain Network</h3>
                                <p class="panel-subtitle">Select your blockchain network to publish your vote sigs</p>
                            </div>
                            <div class="panel-body network-select">
                                <div class="row">
                                    <div class="col-md-6 d-flex flex-column justify-content-end align-items-center">
                                        <img src="{{ Vite::image('solana.png') }}" alt="" class="img-rounded"
                                            width="50%" height="auto">
                                        <label class="fancy-radio">
                                            <input name="solana_network" value="solana" type="radio">
                                            <span><i></i>Solana</span>
                                        </label>
                                    </div>
                                    <div class="col-md-6 d-flex flex-column justify-content-center align-items-center">
                                        <img src="{{ Vite::image('testnet.png') }}" alt="" class="img-rounded"
                                            width="50%" height="auto">
                                        <label class="fancy-radio">
                                            <input name="solana_network" value="testnet" type="radio" checked>
                                            <span><i></i>Testnet</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END PANEL HEADLINE -->
                    </div>
                    <div class="col-md-7">
                        <!-- PANEL NO PADDING -->
                        <div class="panel  panel-headline" style="border-radius: 15px;">
                            <div class="panel-heading">
                                <h3 class="panel-title">Address & Keys</h3>
                                <p class="panel-subtitle">The EA's address and private keys</p>
                            </div>
                            <div class="panel-body">
                                <form action="{{ route('ea.solana.setting') }}" method="post">
                                    @csrf
                                    @if ($message = Session::get('solana_success'))
                                        <div class="alert alert-success alert-block">{{ $message }}</div>
                                    @endif
                                    <div class="form-group mb-3">
                                        <label for="solana_address">Solana Address</label>
                                        <input type="text"
                                            class="form-control mt-1 rounded-2 @error('solana_address') is-invalid @enderror"
                                            id="solana_address" name="solana_address"
                                            value="{{ $user['solana_address'] }}">
                                        @error('solana_address')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group mt-2">
                                        <label for="solana_priv_key">Private Key</label>
                                        <textarea name="solana_priv_key" class="form-control mt-1 rounded-2 @error('solana_priv_key') is-invalid @enderror"
                                            id="pubkey" rows="5" autocomplete="off" spellcheck="false" placeholder="{{ $user->getRawOriginal('solana_priv_key') ? __('Clé configurée (chiffrée). Laisser vide pour la conserver.') : __('Aucune clé configurée') }}"></textarea>
                                    </div>
                                    @error('solana_priv_key')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <div class="button-right mt-3">
                                        <button type="submit" class="btn btn-primary rounded-2 p-2 px-3">Confirm</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- END PANEL NO PADDING -->
                    </div>
                </div>
            </div>


        </div>
    @endsection

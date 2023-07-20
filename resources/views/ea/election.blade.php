@extends('templates.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-headline" style="border-radius: 15px;">
                <div class="panel-heading">
                    <h3 class="panel-title">Voting List</h3>
                </div>
                <div class="panel-body">
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block">{{ $message }}</div>
                    @endif

                    <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Voters Numbers</th>
                                <th>Created Time</th>
                                <th>Operation</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td>{{ $item['id'] }}</td>
                                    <td>{{ $item['title'] }}</td>
                                    <td>{{ $item['number'] }}</td>
                                    <td>{{ $item['created_at'] }}</td>

                                    @if ($item['stage'] == 0)
                                        <td>
                                            <button type="button" data-id="{{ $item['id'] }}"
                                                class="btn btn-toggle btn-warning btn btn-toggleStage">Start Veriff</button>
                                        </td>
                                    @elseif ($item['stage'] == 1)
                                        <td>
                                            <button type="button" data-id="{{ $item['id'] }}"
                                                class="btn btn-toggle btn-danger btn-toggleStage">Stop Veriff</button>
                                        </td>
                                    @elseif ($item['stage'] == 2)
                                        <td>
                                            <button type="button" data-id="{{ $item['id'] }}"
                                                class="btn btn-toggle btn-success btn-toggleStage">Start Vote</button>
                                        </td>
                                    @elseif ($item['stage'] == 3)
                                        <td>
                                            <button type="button" data-id="{{ $item['id'] }}"
                                                class="btn btn-toggle btn-danger btn-toggleStage">End Vote</button>
                                        </td>
                                    @elseif ($item['stage'] == 4)
                                        <td>
                                            <button type="button" data-id="{{ $item['id'] }}"
                                                class="btn btn-toggle btn-secondary btn-toggleStage">See Results</button>
                                        </td>
                                    @endif
                                    <td>
                                        <input type="button" class="btn btn-primary btn-getElection" data-bs-toggle="modal"
                                            value="Manage" data-bs-target="#electionModal" data-id="{{ $item['id'] }}">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-getCandidates"
                                            data-bs-toggle="modal" data-bs-target="#candidateModal"
                                            data-id="{{ $item['id'] }}">Candidates</button>
                                    </td>

                                </tr>
                            @endforeach
                            @if (empty($data))
                                <tr>
                                    <td>No data</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
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


    <!-- Manage Candidate -->
    <div class="modal fade" id="candidateModal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1"
        role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">View Candidates</h4>
                </div>
                <div class="modal-body">
                    <br />
                    <div class="row row-cols-auto row-cols-md-3 row-cols-xl-4 box-candidate">

                        <h2 class="text-center">Loading...</h2>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary rounded-3" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @include('templates.partials.footer')
@endsection

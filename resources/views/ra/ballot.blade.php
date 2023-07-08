@extends('templates.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-headline rounded-4">
                <div class="panel-heading h3 mb-0">Ballot Code List</div>
                <div class="panel-body">
                    <form action="{{ route('ra.ballot') }}" method="post">
                        <div class="form-group my-3">
                            <label for="number" class="mb-1">Numbers of Ballots</label>
                            <input type="text" name="number" id="ballots" class="form-control p-2 rounded-3">
                        </div>
                        <div class="form-group my-3">
                            <label for="election_id" class="mb-1">Vote ID</label>
                            <input type="text" name="election_id" id="election_id" class="form-control p-2 rounded-3">
                        </div>
                        <button type="submit" class="btn btn-primary p-2 px-3 rounded-3">Generate</button>
                        @csrf

                    </form>

                    <hr>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Code</th>
                                <th>isUsed</th>
                                <th>UseFor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $item)
                                <tr>
                                    <td>{{ $item['id'] }}</td>
                                    <td>{{ $item['code'] }}</td>
                                    <td>
                                        @if ($item['used'] == 0)
                                            no
                                        @else
                                            yes
                                        @endif
                                    </td>
                                    <td>{{ $item['item_id'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td>No data</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

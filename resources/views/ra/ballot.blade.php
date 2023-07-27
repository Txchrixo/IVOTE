@extends('templates.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-headline rounded-4">
                <div class="panel-heading h3 mb-0">Ballot Code List</div>
                <div class="panel-body">
                    <form action="{{ route('ra.ballot') }}" method="post">
                        <div class="form-group my-3">
                            <label for="ballots" class="mb-1">Number of registration codes</label>
                            <input type="number" name="number" id="ballots" class="form-control p-2 rounded-3"
                                min="1" max="128" value="{{ old('number', 1) }}" required>
                            @error('number') <p class="text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group my-3">
                            @include('ra.electionSelect')
                        </div>
                        <button type="submit" class="btn btn-primary p-2 px-3 rounded-3">Generate</button>
                        @csrf

                    </form>

                    <hr>
                    <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Code</th>
                                <th>Redeemed</th>
                                <th>Election</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $item)
                                <tr>
                                    <td>{{ $item['id'] }}</td>
                                    <td>{{ $item['code'] }}</td>
                                    <td>
                                        @if (!$item['is_used'])
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
                    {{ $data->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

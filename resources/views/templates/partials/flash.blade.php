@if (session()->get('info'))
    <div class="alert alert-info">
        {{ session()->get('info') }}
    </div>
@endif

@if (session()->get('error'))
    <div  class="text-white p-2 m-3" style="background-color: #DD090B">
        {{ session()->get('error') }}
    </div>
@endif

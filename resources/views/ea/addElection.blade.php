@extends('templates.app')

@section('content')
    <div class="row">
        <div class="col-md-7">
            <div class="panel panel-headline" style="border-radius: 15px;">
                <h2 class="panel-heading">Add an Election</h2>
                <div class="panel-body">
                    <form method="post">
                        @csrf
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" name="title" id="title"
                                class="form-control my-1 rounded-2 @error('title') is-invalid @enderror">
                            @error('title')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mt-3">
                            <label for="number">Numbers of voters</label>
                            <input type="text" name="number" id="number"
                                class="form-control my-1 rounded-2 @error('number') is-invalid @enderror">
                            @error('number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mt-3">
                            <label for="description">Description</label>
                            <textarea class="form-control my-1 rounded-2 @error('description') is-invalid @enderror" name="description"
                                id="description" placeholder="" rows="6"></textarea>
                            @error('description')
                                <span class="text-white p-1 m-3" style="background-color: #DD090B">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="button-right mt-3">
                            <button type="submit" class="btn btn-primary rounded-2 p-2 px-3 w-25">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="panel panel-headline" style="border-radius: 15px;">
                <h2 class="panel-heading">Background</h2>
                <div class="panel-body">
                    <form id="uploadForm" action="{{ route('ea.uploadBg') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success alert-block">{{ $message }}</div>
                        @endif
                        <div class="form-group">
                            <img id="bgElection" src="{{ Vite::image('upload.jpg') }}" style="border-radius: 15px;"
                                width="100%" height="auto">
                        </div>
                        <div class="form-group">
                            <select
                                class="select-election form-control input-id p-2 rounded-3 mt-2 @error('election') is-invalid @enderror"
                                name="election">
                                <option value="">Select election</option>
                                @foreach ($elections as $election)
                                    <option value="{{ $election['id'] }}"
                                        @if ($election['bg']) data-bg="{{ $election['bg'] }}" @endif>
                                        {{ $election['title'] }}</option>
                                @endforeach
                                @if (empty($elections))
                                    <option>Please add an election first</option>
                                @endif
                            </select>
                            @error('election')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input type="file" name="file" id="inputFile"
                                class="form-control @error('file') is-invalid @enderror rounded-3 mt-2">
                            @error('file')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div id="progressContainer" style="display: none;">
                            <div id="progressBar" class="progress-bar w-100" role="progressbar" aria-valuenow="0"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="button-right mt-3">
                            <button type="button" class="btn btn-secondary btn-set-default rounded-2 p-2 px-3"
                                style="display: none;">Set Default</button>
                            <input class="btn btn-primary btn-upload rounded-2 p-2 px-3" type="submit" value="Upload">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script nonce="{{ Vite::cspNonce() }}">
        document.addEventListener('DOMContentLoaded', function() {
            // Event listener for when an election is selected
            var selectElection = document.querySelector('.select-election');
            var inputFile = document.querySelector('#inputFile');
            var bgElection = document.querySelector('#bgElection');
            var uploadButton = document.querySelector('.btn-upload');
            var old = Number('{{ old('election') }}');

            // Function to enable/disable the input file based on select value
            function toggleInputFile() {
                if (selectElection.value !== "") {
                    inputFile.disabled = false;
                } else {
                    inputFile.disabled = true;
                }
            }
            // Function to enable/disable the upload button based on file selection
            function toggleUploadButton() {
                if (inputFile.files && inputFile.files[0]) {
                    uploadButton.disabled = false;
                } else {
                    uploadButton.disabled = true;
                }
            }

            function hideSuccessMessage() {
                var successMessage = document.querySelector('.alert-success');
                if (successMessage) {
                    setTimeout(() => {
                        successMessage.style.display = 'none';
                    }, 5000);
                }
            }

            // Trigger the function on initial page load
            toggleInputFile();
            toggleUploadButton();
            hideSuccessMessage();


            // Function to handle the change in selected election
            function handleSelectChange() {
                var selectedElection = selectElection.options[selectElection.selectedIndex].dataset.bg;
                if (selectedElection) {
                    // Check if a background exists for the selected election
                    // Replace 'your-background-path' with the actual path to the background image
                    var fileName = selectedElection;
                    var backgroundPath = `{{ Vite::bg('${fileName}') }}`;
                    // Update the input file and background image
                    var file = new File([], fileName, {
                        type: 'image/jpeg'
                    });
                    var fileArray = [file];
                    var dataTransfer = new DataTransfer();
                    dataTransfer.items.add(fileArray[0]);
                    inputFile.files = dataTransfer.files;
                    bgElection.src = backgroundPath;


                    if (selectedElection !== inputFile.files[0].name) {
                        uploadButton.disabled = false;
                    } else {
                        uploadButton.disabled = true;
                    }
                } else {
                    inputFile.value = '';
                    bgElection.src = "{{ Vite::image('upload.jpg') }}";
                }

                // Call the function to enable/disable the input file
                toggleInputFile();

            }

            // Event listener for when an election is selected
            selectElection.addEventListener('change', function() {
                handleSelectChange();
            });

            inputFile.addEventListener('change', function() {
                if (inputFile.files && inputFile.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        bgElection.src = e.target.result;
                    };
                    reader.readAsDataURL(inputFile.files[0]);
                }
                toggleUploadButton();
            });

            if (old !== 0) {
                selectElection.value = old;
                handleSelectChange();
            }
        });
    </script>
    @include('templates.partials.footer')
@endsection

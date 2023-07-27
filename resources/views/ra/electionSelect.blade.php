<label for="election_id" class="form-label">Election</label>
<select id="election_id" name="election_id" class="form-select" required>
    <option value="">Choose an election</option>
    @foreach ($elections as $election)
        <option value="{{ $election->id }}" @selected(old('election_id') == $election->id)>{{ $election->title }}</option>
    @endforeach
</select>
@error('election_id')
    <p class="text-danger">{{ $message }}</p>
@enderror

@if (auth()->user()->hasRole('admin'))
    <label class="switch">
        <input class="statuss" type="checkbox" data-id="{{ $id }}" name="status"
            {{ $status == '1' ? 'checked' : '' }}>
        <span class="knob"></span>
    </label>
@else
    {{ $status == '1' ? 'Active' : 'Not Active' }}
@endif
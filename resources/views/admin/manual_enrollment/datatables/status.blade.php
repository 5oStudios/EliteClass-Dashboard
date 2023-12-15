<form action="{{ route('enrollment.status') }}" method="POST">
    {{ csrf_field() }}

    <label class="switch">
        <input id="enrollment" type="checkbox" data-id="{{ $id }}"
            name="status" {{ $status == '1' ? 'checked' : '' }}>
        <span class="knob"  style="cursor: pointer;"></span>
    </label>
</form>
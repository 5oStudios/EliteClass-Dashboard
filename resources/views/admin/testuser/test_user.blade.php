<form action="{{ route('testuser.status',$id) }}" method="POST">
    {{ csrf_field() }}
    <label class="switch">
        <input class="test_user" type="checkbox" data-id="{{$id}}"
            name="test_user" {{ $test_user == '1' ? 'checked' : '' }}>
        <span class="knob2"></span>
    </label>
</form>
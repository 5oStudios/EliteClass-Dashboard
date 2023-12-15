@if (auth()->user()->hasRole('admin'))
    <label class="switch">
        <input class="slider" type="checkbox" data-id="{{ $id }}" name="status"
            {{ $status == '1' ? 'checked' : '' }} onchange="courceclassstatus('{{ $id }}')" />
        <span class="knob"></span>
    </label>
@else
    {{ $status == '1' ? 'Active' : 'Not Active' }}
@endif

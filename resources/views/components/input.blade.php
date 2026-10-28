@props(['name', 'label', 'type'=>'text', 'value' => ''])

<div class="field">
    <label for="{{ $name }}">{{ $label }}</label>
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" value="{{ $type ==='password' ? '' :old($name, $value)}}">
    @error($name) <div class="error">{{ $message }}</div> @enderror
</div>
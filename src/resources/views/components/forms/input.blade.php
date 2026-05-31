@props([
    'errorBag' => 'default',
    'feedbackClass' => 'invalid-feedback',
    'id' => null,
    'inputClass' => '',
    'label',
    'labelClass' => 'form-label',
    'name',
    'type' => 'text',
    'value' => null,
])

@php
    $fieldId = $id ?? str_replace(['.', '[', ']'], '_', $name);
    $error = $errors->getBag($errorBag)->first($name);
    $inputClasses = trim('form-control ' . $inputClass . ($error ? ' is-invalid' : ''));
@endphp

<label class="{{ $labelClass }}" for="{{ $fieldId }}">{{ $label }}</label>
<input
    id="{{ $fieldId }}"
    name="{{ $name }}"
    type="{{ $type }}"
    @if (! is_null($value))
        value="{{ $value }}"
    @endif
    {{ $attributes->class($inputClasses) }}
>

@if ($error)
    <div class="{{ $feedbackClass }}">{{ $error }}</div>
@endif

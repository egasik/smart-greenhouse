@props(['value'])

<label {{ $attributes->merge(['class' => 'gh-label']) }}>
    {{ $value ?? $slot }}
</label>

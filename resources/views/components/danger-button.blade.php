<button {{ $attributes->merge(['type' => 'submit', 'class' => 'gh-button gh-button--danger']) }}>
    {{ $slot }}
</button>

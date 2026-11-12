<button {{ $attributes->merge(['type' => 'submit', 'class' => 'gh-button gh-button--primary']) }}>
    {{ $slot }}
</button>

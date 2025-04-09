<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-md']) }}>
    {{ $slot }}
</button>

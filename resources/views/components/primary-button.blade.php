<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-brand-gold border border-transparent rounded-md font-semibold text-xs text-brand-navy uppercase tracking-widest hover:bg-brand-gold-400 focus:bg-brand-gold-400 active:bg-brand-gold-600 active:text-white focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

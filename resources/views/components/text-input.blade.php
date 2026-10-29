@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-brand-gold dark:focus:border-brand-gold focus:ring-brand-gold dark:focus:ring-brand-gold rounded-md shadow-sm']) }}>

@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full rounded-input border-border-custom shadow-sm focus:border-primary focus:ring focus:ring-primary/20 transition-colors bg-gray-50/50']) }}>

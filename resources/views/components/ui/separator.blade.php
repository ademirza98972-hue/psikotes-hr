@props(['orientation' => 'horizontal'])

<div role="separator" {{ $attributes->class('shrink-0 bg-border ' . ($orientation === 'vertical' ? 'h-full w-px' : 'h-px w-full')) }}></div>

<div class="relative">
    <select {{ $attributes->class('h-9 w-full cursor-pointer appearance-none rounded-md border border-input bg-transparent py-1 pl-3 pr-8 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50') }}>{{ $slot }}</select>
    <span class="material-symbols-outlined pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[18px] text-muted-foreground">expand_more</span>
</div>

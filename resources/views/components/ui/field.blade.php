{{-- Field shadcn: label + kontrol + deskripsi + pesan error validasi Laravel. --}}
@props(['label' => null, 'for' => null, 'description' => null, 'error' => null])

<div {{ $attributes->class('grid content-start gap-2') }}>
    @if ($label)
        <x-ui.label :for="$for">{{ $label }}</x-ui.label>
    @endif
    {{ $slot }}
    @if ($description)
        <p class="text-xs text-muted-foreground">{{ $description }}</p>
    @endif
    @if ($error)
        @error($error)
            <p class="text-xs font-medium text-destructive">{{ $message }}</p>
        @enderror
    @endif
</div>

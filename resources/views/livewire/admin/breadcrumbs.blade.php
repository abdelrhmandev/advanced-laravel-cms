<flux:breadcrumbs>

    @foreach ($breadcrumbs as $item)

        @if (!empty($item['active']))
            <flux:breadcrumbs.item current>
                {{ $item['label'] }}
            </flux:breadcrumbs.item>
        @else
            <flux:breadcrumbs.item
                href="{{ $item['url'] }}"
                wire:navigate
            >
                {{ $item['label'] }}
            </flux:breadcrumbs.item>
        @endif

    @endforeach

</flux:breadcrumbs>

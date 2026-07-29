@props(['label', 'model', 'placeholder', 'options'])





<flux:field variant="inline">
    <flux:label>{{ $label }}</flux:label>





    @if (count($options) > 0)
        @foreach ($options as $block)
            <div class="flex items-center gap-3" wire:key="block-{{ $block->id }}">
                <flux:checkbox wire:model.live="selectedBlocks" value="{{ $block->id }}" />
                <span class="text-sm text-zinc-700 dark:text-zinc-300 capitalize">
                    {{ $block->getTranslatedTitle('en') }}
                </span>
            </div>
        @endforeach
        <flux:error name="selectedBlocks" />
    @else
        <div>

            <flux:subheading>
                {{ $label }} not found.
            </flux:subheading>
        </div>




    @endif
</flux:field>

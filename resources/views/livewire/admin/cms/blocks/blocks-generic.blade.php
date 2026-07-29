@php
    // helper: get value for a field key, translated if needed
    $val = function(string $key) use ($pb, $locale) {
        $value = $pb->getValueForKey($key);

        if (is_array($value) && array_key_exists($locale, $value)) {
            return $value[$locale]
                ?? $value[config('app.fallback_locale')]
                ?? null;
        }

        return $value;
    };
@endphp

<div class="generic-block space-y-4">
    @foreach ($pb->block->fields as $field)
        @php $value = $pb->getValueFor($field); @endphp

        <div class="field field--{{ $field->type }}" data-key="{{ $field->key }}">

            @if ($field->type === 'repeater')
                @if (is_array($value))
                    @foreach ($value as $row)
                        <div class="repeater-row">
                            @foreach ($field->getSubFields() as $sub)
                                @php
                                    $subVal = $row[$sub['key']] ?? null;
                                    if (is_array($subVal) && isset($subVal[$locale])) {
                                        $subVal = $subVal[$locale];
                                    }
                                @endphp
                                <span data-sub="{{ $sub['key'] }}">{{ $subVal }}</span>
                            @endforeach
                        </div>
                    @endforeach
                @endif

            @elseif ($field->translatable)
                {{ is_array($value) ? ($value[$locale] ?? '') : $value }}

            @elseif ($field->type === 'image')
                @if ($value)
                    <img src="{{ asset('storage/' . $value) }}" alt="{{ $field->label }}" />
                @endif

            @else
                {{ $value }}
            @endif

        </div>
    @endforeach
</div>

{{-- ============================================================
     resources/views/blocks/hero.blade.php
     مثال على block partial مخصص لـ hero block
============================================================ --}}
{{--

@php
    $title      = $pb->getValueForKey('title');
    $subtitle   = $pb->getValueForKey('subtitle');
    $background = $pb->getValueForKey('background');
    $alignment  = $pb->getValueForKey('alignment') ?? 'center';
    $overlay    = $pb->getValueForKey('overlay_color') ?? '#000000';

    // translate
    $t = fn($v) => is_array($v) ? ($v[$locale] ?? '') : $v;
@endphp

<div
    class="relative min-h-[60vh] flex items-center justify-{{ $alignment }}"
    style="background-image: url('{{ asset('storage/' . $background) }}'); background-size: cover;"
>
    <div class="absolute inset-0" style="background-color: {{ $overlay }}; opacity: 0.5;"></div>

    <div class="relative z-10 text-white text-{{ $alignment }} px-8">
        <h1 class="text-4xl font-bold">{{ $t($title) }}</h1>
        @if ($subtitle)
            <p class="text-xl mt-4">{{ $t($subtitle) }}</p>
        @endif
    </div>
</div>

--}}

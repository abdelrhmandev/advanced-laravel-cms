<div class="flex flex-wrap gap-2">

    {{-- Existing Permissions in Database --}}
    @foreach ($existingGrouped[Str::title($module)] ?? [] as $perm)
        @php
            $parts = explode('.', $perm);
            if (count($parts) >= 3) {
                $action = strtoupper($parts[1]);
                $id = $parts[2];
                $target = $titlesMap[$id] ?? $id;
                $label = $action . ' ' . $target;
            } elseif (isset($parts[1])) {
                $label = strtoupper($parts[1]);
            } else {
                $label = strtoupper($perm);
            }
        @endphp
        <span
            class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold uppercase bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 truncate max-w-[200px]"
            title="{{ $label }}"
        >
            {{ $label }}
        </span>
    @endforeach

    {{-- New Detected Permissions (Pending Sync) --}}
    @foreach ($generatedPermissions[$module] ?? [] as $permName => $uiLabel)
        @php
            $newParts = explode('.', $permName);
            if (count($newParts) >= 3) {
                $newAction = strtoupper($newParts[1]);
                $newId = $newParts[2];
                $newTarget = $titlesMap[$newId] ?? $uiLabel;
                $display = $newAction . ' ' . $newTarget;
            } else {
                $display = str_replace(['.', '-'], ' ', $uiLabel);
            }
        @endphp
        <span
            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold uppercase bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border border-dashed border-blue-300 dark:border-blue-700 truncate max-w-[200px]"
            title="Pending Sync: {{ $permName }}"
        >
            <flux:icon name="plus" class="w-3 h-3" />
            {{ $display }}
        </span>

    @endforeach

</div>

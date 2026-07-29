{{-- partials/sync-status.blade.php --}}
<div class="flex items-center mt-1">
    @if (empty($generatedPermissions[$module]))
        <span class="w-2 h-2 rounded-full bg-green-500 me-2"></span>
        <span class="text-xs text-zinc-500 font-semibold">Fully Synced</span>
    @else
        <span class="w-2 h-2 rounded-full bg-blue-500 me-2"></span>
        <span class="text-xs text-blue-600 font-bold">
            {{ count($generatedPermissions[$module]) }} New Detected
        </span>
    @endif
</div>

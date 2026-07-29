<div class="card card-flush shadow-sm">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold text-dark">Menu Structure Builder</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Add menu items from the column on the left.</span>
        </h3>
    </div>
    <div class="card-body pt-5">
            @if (session()->has('success'))
                <div class="alert alert-success mb-4">
                    {{ session('success') }}
                </div>
            @endif
        @if(isset($items) && $items->count() > 0)
        <div id="menu-items-container" wire:ignore>
            {!! buildNestedHtml($items) !!}
        </div>
    @else
        <div class="text-center py-10">
            <p class="text-gray-400">No items in this menu yet.</p>
        </div>
    @endif
    </div>
</div>

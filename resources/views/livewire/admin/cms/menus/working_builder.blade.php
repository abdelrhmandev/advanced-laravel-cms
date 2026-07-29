@push('styles')
    <style>
  .menu-list {
    list-style: none;
    margin: 0;
    padding-left: 20px;
}

.menu-list li {
    margin: 4px 0;
    padding: 6px;
    border: 1px solid #ccc;
    background: #fafafa;
    cursor: grab;
}

.menu-list li .menu-item {
    display: flex;
    align-items: center;
}

.menu-list li .handle {
    cursor: grab;
    margin-right: 8px;
}

.sortable-ghost {
    opacity: 0.4;
    background: #cce5ff;
}



        /* Simplified Nestable CSS */
        .dd { position: relative; display: block; margin: 0; padding: 0; list-style: none; }
        .dd-list { display: block; position: relative; margin: 0; padding: 0; list-style: none; }
        .dd-item { display: block; position: relative; margin: 0; padding: 0; min-height: 20px; }
        .dd3-content { display: block; margin: 5px 0; padding: 12px 10px 12px 50px; border: 1px solid #e1e3ea; background: #fff; border-radius: 6px; }
        .dd3-handle { position: absolute; margin: 0; left: 0; top: 0; cursor: move; width: 40px; height: 100%; border: 1px solid #e1e3ea; background: #e97d51; border-radius: 6px 0 0 6px; }
        .dd3-handle:before { content: ':::'; display: block; position: absolute; left: 0; top: 50%; width: 100%; text-align: center; color: #fff; transform: translateY(-50%); }

    </style>
@endpush

<div class="p-4 border rounded">
    <h3 class="font-bold mb-3">Editing: {{ $menu?->name }}</h3>

    <div class="mb-4 grid grid-cols-3 gap-3">
        <input wire:model.defer="title" type="text" placeholder="Title" class="border p-2 col-span-1">
        <input wire:model.defer="url" type="text" placeholder="/path or https://..." class="border p-2 col-span-1">
        <button wire:click="addItem" class="bg-green-600 px-3 py-2 rounded col-span-1">Add Item</button>
    </div>

    <div>
        <p class="mb-2">Drag & drop to reorder / nest items</p>

        <div id="menu-root" wire:ignore>
            @if ($items->count())
                {{-- Build nested lists by generating HTML (client will send structure) --}}
                {!! buildNestedHtml($items->toArray(), null) !!}


            @else
                <div class="text-gray-500">No items yet.</div>
            @endif
        </div>

        <div class="mt-4 text-sm text-gray-600">
            <button id="save-structure" class="bg-blue-600 text-success px-3 py-2 rounded mt-3">Save Structure</button>
        </div>
    </div>

    <div class="mt-6">
        <h4 class="font-semibold">All items (flat)</h4>
        <ul>
            @foreach ($items as $it)
                <li class="flex justify-between items-center py-1">
                    <div>{{ $it->title }} <span class="text-gray-500 text-xs">({{ $it->id }})</span></div>
                    <div>
                        <button wire:click="deleteItem({{ $it->id }})" class="text-red-600">Delete</button>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    function getNestedOrder(ul) {
        let order = [];
        ul.querySelectorAll(':scope > li').forEach(li => {
            let id = li.dataset.id;
            let childUl = li.querySelector(':scope > ul');
            let children = childUl ? getNestedOrder(childUl) : [];
            order.push({ id: id, children: children });
        });
        return order;
    }

    function initSortable(el) {
        new Sortable(el, {
            group: 'nested',
            animation: 150,
            fallbackOnBody: true,
            swapThreshold: 0.65,
            handle: '.handle',
            ghostClass: 'sortable-ghost',
            onEnd: function () {
                Livewire.dispatch('reorder', { structure: getNestedOrder(document.querySelector('#menu-root > ul')) });
            }
        });

        // also apply sortable to children
        el.querySelectorAll('ul').forEach(childUl => {
            initSortable(childUl);
        });
    }

    let root = document.querySelector('#menu-root > ul');
    if (root) {
        initSortable(root);
    }

     // Manual Save Button (if you want a "Save Structure" button)
    let saveBtn = document.getElementById('save-structure');
    if (saveBtn) {
        saveBtn.addEventListener('click', function () {
            let structure = getNestedOrder(document.querySelector('#menu-root > ul'));
            Livewire.dispatch('reorder', { structure });
        });
    }

});
</script>

@endpush




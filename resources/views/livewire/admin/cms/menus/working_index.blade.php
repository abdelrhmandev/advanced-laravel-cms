<div class="grid grid-cols-4 gap-6 p-4">
    <!-- Sidebar Menu -->
    <div class="col-span-1 bg-white shadow rounded-xl p-4">
        <h3 class="text-xl font-bold mb-4">Menus</h3>
        <ul>
            @foreach($menus as $menu)
                <li class="flex items-center justify-between mb-2">
                    <button wire:click="$set('selectedMenuId', {{ $menu->id }})"
                            class="text-left w-full pr-2 @if($selectedMenuId==$menu->id) font-semibold text-blue-600 @endif">
                        {{ $menu->name }}
                    </button>

                    <button wire:click="deleteMenu({{ $menu->id }})"
                            onclick="confirm('Delete menu?') || event.stopImmediatePropagation()"
                            class="text-red-600 hover:text-red-800">✖</button>
                </li>
            @endforeach
        </ul>

        <div class="mt-4">
            <input wire:model.defer="name" type="text" placeholder="New menu name"
                   class="border p-2 w-full rounded">
            <button wire:click="createMenu"
                    class="mt-2 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded w-full">
                Submit
            </button>
        </div>
    </div>

    <!-- Builder/Main Content -->
    <div class="col-span-3 space-y-6">
        @if($selectedMenuId)
            <livewire:backend.menu.builder :menuId="$selectedMenuId" :wire:key="'builder-'.$selectedMenuId" />
        @else
            <div class="p-6 border-2 border-dashed border-gray-300 rounded-xl text-gray-500 text-center">
                No menu selected. Create a new menu.
            </div>
        @endif
    </div>
</div>

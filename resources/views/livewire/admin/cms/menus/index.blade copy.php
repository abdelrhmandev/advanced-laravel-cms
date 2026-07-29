@push('styles')
    <link href="{{ asset('assets/backend/css/custom/menu-builder.css') }}" rel="stylesheet">
@endpush
<div class="content flex-row-fluid" id="kt_content">
<div class="row g-5 g-xl-8">

    <!-- Sidebar -->
    <div class="col-xl-3">

        <div class="card card-flush h-100">

            <div class="card-header pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-dark">Menus</span>
                </h3>
            </div>

            <div class="card-body pt-2">

                <div class="scroll-y me-n5 pe-5" style="max-height: 350px">
                    <ul class="nav nav-pills nav-pills-custom flex-column gap-1">

                        @foreach ($menus as $menu)
                            <li class="nav-item">
                                <a
                                    href="#"
                                    wire:click.prevent="$set('selectedMenuId', {{ $menu->id }})"
                                    class="nav-link d-flex justify-content-between align-items-center
                                    {{ $selectedMenuId == $menu->id ? 'active' : '' }}"
                                >

                                    <span class="text-gray-800 fw-semibold">
                                        {{ $menu->name }}
                                    </span>

                                    <span
                                        wire:click.stop="deleteMenu({{ $menu->id }})"
                                        onclick="confirm('Delete menu?') || event.stopImmediatePropagation()"
                                        class="btn btn-sm btn-icon btn-light-danger"
                                    >
                                        <i class="ki-duotone ki-trash fs-6"></i>
                                    </span>
                                </a>
                            </li>
                        @endforeach

                    </ul>

                </div>

                <!-- Add Menu -->
                <div class="separator my-5"></div>

                <div>
                    <input
                        wire:model.defer="name"
                        type="text"
                        class="form-control form-control-solid"
                        placeholder="New menu name"
                    >

                    <button
                        wire:click="createMenu"
                        class="btn btn-primary w-100 mt-3"
                    >
                        <i class="ki-duotone ki-plus fs-2"></i>
                        Add Menu
                    </button>

                    @error('name')
                        <div class="text-danger fs-7 mt-2">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <div class="card-body pt-2">
                      <h4 class="font-semibold mb-3">Available Pages</h4>

        @if (count($availablePages))
            <div class="space-y-2 max-h-60 overflow-y-auto">
                @foreach ($availablePages as $page)
                    <label class="flex items-center gap-2 p-2 border rounded hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" value="{{ $page->id }}" wire:model="selectedPages">
                        <span>{{ $page->translate->title ?? $page->slug }}</span>
                    </label>
                @endforeach
            </div>

            @error('selectedPages')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror

            <button wire:click="addSelectedPagesToMenu"
                    class="mt-3 btn btn-sm btn-primary me-3">
                Add to Menu
            </button>
        @else
            <div class="text-red-500 text-sm mt-2">No available pages to add.</div>
        @endif
            </div>

               <div class="card-body pt-2">

                        <h4 class="font-semibold mb-3">Add New Menu Item</h4>

        <form wire:submit.prevent="addItem" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach (\LaravelLocalization::getSupportedLocales() as $langCode => $langName)
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Title ({{ strtoupper($langCode) }})
                    </label>
                    <input wire:model.defer="title.{{ $langCode }}" type="text"
                           placeholder="Enter Title ({{ $langName['native'] }})"
                           class="border rounded p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @error("title.$langCode")
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">
                        URL ({{ strtoupper($langCode) }})
                    </label>
                    <input wire:model.defer="url.{{ $langCode }}" type="text"
                           placeholder="Enter URL ({{ $langName['native'] }})"
                           class="border rounded p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @error("url.$langCode")
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            @endforeach

            <div class="col-span-2">
                <button type="submit" class="btn btn-sm btn-primary me-3 mt-2">
                    Add Item
                </button>
            </div>
        </form>

        @if (session()->has('success'))
            <div class="mt-3 p-2 bg-green-100 text-success">
                {{ session('success') }}
            </div>
        @endif



            </div>
        </div>

    </div>

    <!-- Builder -->
    <div class="col-xl-9">

        <div class="card card-flush h-100">

            <div class="card-header pt-5">
                <h3 class="card-title">
                    <span class="card-label fw-bold text-dark">Menu Builder</span>
                </h3>
            </div>

            <div class="card-body">

                @if ($selectedMenuId)
                    <livewire:backend.menu.builder
                        :menuId="$selectedMenuId"
                        :wire:key="'builder-'.$selectedMenuId"
                    />
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center py-20 text-gray-400">
                        <i class="ki-duotone ki-folder fs-5x mb-5"></i>
                        <div class="fw-semibold fs-4">No menu selected</div>
                        <div class="fs-7">Create or select a menu to start building</div>
                    </div>
                @endif

            </div>

        </div>

    </div>

</div>

</div>
@push('scripts')
    <script src="{{ asset('assets/backend/js/custom/Sortable.min.js') }}"></script>
    <script src="{{ asset('assets/backend/js/custom/menu-builder.js') }}"></script>
@endpush

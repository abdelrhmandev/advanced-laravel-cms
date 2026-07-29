@push('styles')
    <link href="{{ asset('assets/backend/css/custom/menu-builder.css') }}" rel="stylesheet">
@endpush
<div class="content flex-row-fluid" id="kt_content">

    <div class="row g-5 g-xl-8">
        <div class="col-xl-4">
            <div class="card card-flush mb-5">
                <div class="card-header pt-5">
                    <h3 class="card-title fw-bold">Select Menu</h3>
                </div>
                <div class="card-body pt-2">
                    <div class="scroll-y me-n5 pe-5 mb-5" style="max-height: 200px">
                        <ul class="nav nav-pills nav-pills-custom flex-column gap-1">
                            @foreach ($menus as $menu)
                                <li class="nav-item">
                                    <a href="#" wire:click.prevent="$set('selectedMenuId', {{ $menu->id }})"
                                        class="nav-link d-flex justify-content-between align-items-center {{ $selectedMenuId == $menu->id ? 'active' : '' }}">
                                        <span class="fw-semibold">{{ $menu->name }}</span>
                                        <span x-data @click.stop=" if (confirm('Delete menu?')) { $wire.deleteMenu({{ $menu->id }}) }"
class="btn btn-sm btn-icon btn-light-danger w-25px h-25px">
<i class="ki-outline ki-trash fs-6"></i>
</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="separator mb-5"></div>
                    <input wire:model.defer="name" type="text" class="form-control form-control-solid mb-2"
                        placeholder="Menu Name">
                    <button wire:click="createMenu" class="btn btn-primary btn-sm w-100">Add Menu</button>
                    @error('name')
                        <div class="text-danger fs-7 mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="card card-flush mb-5">
                <div class="card-header pt-5">
                    <h3 class="card-title fw-bold">Add Pages</h3>
                </div>
                <div class="card-body pt-2">
                    @if (count($availablePages))
                        <div class="scroll-y pe-3" style="max-height: 250px">
                            @foreach ($availablePages as $page)
                                <label
                                    class="d-flex align-items-center gap-2 p-2 border rounded mb-2 cursor-pointer hover-bg-light">
                                    <input type="checkbox" value="{{ $page->id }}" wire:model="selectedPages"
                                        class="form-check-input">
                                    <span class="fs-7">{{ $page->translate->title ?? $page->slug }}</span>
                                </label>
                            @endforeach
                        </div>
                        <button wire:click="addSelectedPagesToMenu" class="btn btn-sm btn-light-primary w-100 mt-3">Add
                            to Menu</button>

                        @error('selectedPages')
                        <div class="text-danger fs-7 mt-2">{{ $message }}</div>
                        @enderror

                    @else
                        <p class="text-muted fs-7">No pages available.</p>
                    @endif
                </div>
            </div>

            <div class="card card-flush">
                <div class="card-header pt-5">
                    <h3 class="card-title fw-bold">Add Custom Link</h3>
                </div>
                <div class="card-body pt-2">
                    <form wire:submit.prevent="addItem" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach (\LaravelLocalization::getSupportedLocales() as $langCode => $langName)
                            <div class="mb-4 p-3 border rounded bg-light">
                                <span class="badge badge-secondary mb-2">{{ strtoupper($langCode) }}</span>
                                <input wire:model.defer="title.{{ $langCode }}" type="text"
                                    class="form-control form-control-sm mb-2" placeholder="Link Text">

                                @error("title.$langCode")
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror




                                <input wire:model.defer="url.{{ $langCode }}" type="text"
                                    class="form-control form-control-sm" placeholder="URL (e.g. /contact)">


                                @error("url.$langCode")
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                            </div>
                        @endforeach
                        <button type="submit" class="btn btn-sm btn-primary me-3 mt-2">
                            Add Custom Item To menu
                        </button>
                    </form>


                    @if (session('success'))
                        <div class="alert alert-success mt-3 py-2 fs-7">{{ session('success') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card card-flush h-100">
                <div class="card-header pt-5">
                    <h3 class="card-title fw-bold text-dark">Menu Structure</h3>
                </div>
                <div class="card-body">
                    @if ($selectedMenuId)
                        <livewire:backend.menu.builder :menuId="$selectedMenuId" :wire:key="'builder-'.$selectedMenuId" />
                    @else
                        <div class="text-center py-20 text-gray-400">
                            <i class="ki-outline ki-folder fs-5x mb-5"></i>
                            <p>Select a menu to begin building</p>
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

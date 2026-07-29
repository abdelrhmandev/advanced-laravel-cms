<?php
namespace App\Livewire\Admin\CMS\Menus;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

#[Layout('layouts.admin.app')]
#[Title('Menu Index')]

class Index extends Component
{
    public $menus;
    public $selectedMenuId;
    public $name;

    public $selectedPages = [];
    public $availablePages = [];
    public $title = [];
    public $url = [];



public $editingMenuId = null; // Track which menu we are renaming

    public function editMenu($id)
    {
        $menu = Menu::findOrFail($id);
        $this->editingMenuId = $id;
        $this->name = $menu->name; // Put current name into the input field
    }

    public function cancelEdit()
    {
        $this->editingMenuId = null;
        $this->name = ''; // Clear input
    }

    public function updateMenuName()
    {
        $this->validate([
            'name' => 'required|string|max:191|unique:menus,name,' . $this->editingMenuId
        ]);

        $menu = Menu::findOrFail($this->editingMenuId);
        $menu->update(['name' => $this->name]);

        $this->cancelEdit();
        $this->loadMenus();
        session()->flash('success', 'Menu renamed successfully!');
    }

    // Override the updatedSelectedMenuId to reset edit state when switching menus
    public function updatedSelectedMenuId($value)
    {
        $this->cancelEdit();
        $this->loadAvailablePages();
        $this->dispatch('refresh-builder', menuId: $value);
    }


    public function mount()
    {
        $this->loadMenus();
        $this->loadAvailablePages();
    }

    #[On('refresh-index-sidebar')]
    public function loadAvailablePages()
    {
        if (!$this->selectedMenuId) {
            $this->availablePages = collect();
            return;
        }

        $this->availablePages = Page::with('translate')
            ->whereNotIn('id', MenuItem::where('menu_id', $this->selectedMenuId)
                ->whereNotNull('page_id')
                ->pluck('page_id')
            )
            ->get();
    }

    public function loadMenus()
    {
        $this->menus = Menu::orderBy('name')->get();
        if (!$this->selectedMenuId && $this->menus->count()) {
            $this->selectedMenuId = $this->menus->first()->id;
        }
    }

    protected function rules()
    {
        $rules = [];
        foreach (LaravelLocalization::getSupportedLocales() as $langCode => $langName) {
            $rules["title.$langCode"] = 'required|string|max:255';
            $rules["url.$langCode"] = 'required|string|max:255';
        }
        return $rules;
    }

    public function createMenu()
    {
        $this->validate(['name' => 'required|string|max:191|unique:menus,name']);
        $menu = Menu::create(['name' => $this->name]);

        $this->name = '';
        $this->loadMenus();
        $this->selectedMenuId = $menu->id;
        $this->loadAvailablePages();

        $this->dispatch('refresh-builder', menuId: $menu->id);

        session()->flash('success', 'Menu added!');
    }

    public function deleteMenu($id)
    {
        Menu::find($id)?->delete();
        $this->loadMenus();
        $this->selectedMenuId = $this->menus->first()->id ?? null;
        $this->loadAvailablePages();


        $this->dispatch('refresh-builder', menuId: $this->selectedMenuId);
        session()->flash('success', 'Menu deleted');
    }

    public function addSelectedPagesToMenu()
    {
        if (!$this->selectedMenuId) return;

        $this->validate(
            ['selectedPages' => 'required|array|min:1'],
            ['selectedPages.required' => 'Please select at least one page.']
        );

        foreach ($this->selectedPages as $pageId) {
            $maxOrder = MenuItem::where('menu_id', $this->selectedMenuId)->max('order');
            MenuItem::create([
                'menu_id' => $this->selectedMenuId,
                'page_id' => $pageId,
                'order'   => ($maxOrder ?? 0) + 1,
            ]);
        }

        $this->selectedPages = [];
        $this->loadAvailablePages();

        // Signal the Builder to rebuild the HTML
        $this->dispatch('refresh-builder', menuId: $this->selectedMenuId);
    }

    public function addItem()
    {
        if (!$this->selectedMenuId) {
            session()->flash('error', 'Select a menu first');
            return;
        }

        $this->validate();

        $maxOrder = MenuItem::where('menu_id', $this->selectedMenuId)->max('order');

        MenuItem::create([
            'menu_id' => $this->selectedMenuId,
            'title'   => $this->title,
            'url'     => $this->url,
            'order'   => ($maxOrder ?? 0) + 1,
        ]);

        $this->title = [];
        $this->url = [];

        $this->loadAvailablePages();

        // Signal the Builder to rebuild the HTML
        $this->dispatch('refresh-builder', menuId: $this->selectedMenuId);
        session()->flash('success', 'Custom link added!');
    }



    public function render()
    {
        return view('livewire.admin.cms.menus.index');
    }
}

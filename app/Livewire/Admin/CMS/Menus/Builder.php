<?php
namespace App\Livewire\Admin\CMS\Menus;
use App\Models\Menu;
use Livewire\Component;
use App\Models\MenuItem;
use Livewire\Attributes\On;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class Builder extends Component
{
    public $menuId;
    public $items;
    public $menu;

    public $title = [];
    public $url = [];

    public array $permittedActions = ['edit', 'delete'];

    protected $listeners = [
        'reorder' => 'handleReorder',
    ];

    public function mount($menuId)
    {
        $this->menuId = $menuId;
        $this->loadMenu();
        $this->refreshInputsFromDb();
    }

    /**
     * Responds to changes from the Index sidebar (Adding pages/links)
     */
    #[On('refresh-builder')]
    public function updateMenuId($menuId)
    {
        $this->menuId = $menuId;
        $this->loadMenu();
        $this->refreshInputsFromDb();

        // Regenerate HTML for the wire:ignore container
        $this->triggerRefresh();
    }

    public function loadMenu()
    {
        if (!$this->menuId) {
            return;
        }

        $this->menu = Menu::with(['items.page.translate'])->find($this->menuId);
        $this->items = $this->menu ? $this->menu->allItems()->with('page.translate')->get() : collect();
    }

    public function refreshInputsFromDb()
    {
        if (!$this->menuId) {
            return;
        }

        $items = MenuItem::where('menu_id', $this->menuId)->get();

        foreach ($items as $item) {
            foreach (LaravelLocalization::getSupportedLocales() as $langCode => $langName) {
                $this->title[$item->id][$langCode] = $item->title[$langCode] ?? '';
                $this->url[$item->id][$langCode] = $item->url[$langCode] ?? '';
            }
        }
    }

    /**
     * Updates an individual menu item's text/url
     */
    public function updateItem($id)
    {
        $item = MenuItem::findOrFail($id);

        $item->update([
            'title' => $this->title[$id],
            'url' => $this->url[$id] ?? null,
        ]);

        $this->loadMenu();
        $this->refreshInputsFromDb();

        session()->flash('success', 'Item updated successfully');

        $this->triggerRefresh();
    }

    /**
     * Handles Nestable2 drag-and-drop reordering
     */
    public function handleReorder($structure)
    {
        $data = is_array($structure) && isset($structure['structure']) ? $structure['structure'] : $structure;

        $this->updateStructure($data, null);
        $this->loadMenu();
        $this->refreshInputsFromDb();

        session()->flash('success', 'Order updated successfully');

        $this->triggerRefresh();
    }

    protected function updateStructure($items, $parentId)
    {
        if (!is_array($items)) {
            return;
        }
        foreach ($items as $index => $item) {
            MenuItem::where('id', $item['id'])->update([
                'parent_id' => $parentId,
                'order' => $index,
            ]);

            if (!empty($item['children'])) {
                $this->updateStructure($item['children'], $item['id']);
            }
        }
    }

    public function deleteItem($id)
    {
        $item = MenuItem::find($id);
        if ($item) {
            $item->delete();
            $this->dispatch('refresh-index-sidebar');
            $this->loadMenu();
            $this->refreshInputsFromDb();

            $this->triggerRefresh();
        }
    }

    /**
     * Helper to dispatch the fresh HTML to the frontend
     */
    private function triggerRefresh()
    {
        $html = buildNestedHtml($this->items);
        $this->dispatch('refreshMenuItems', html: $html);
    }

    public function render()
    {
        return view('livewire.backend.cms.menus.builder');
    }
}

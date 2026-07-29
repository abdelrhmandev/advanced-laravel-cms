<?php

namespace App\Livewire\Admin\CMS\Blocks;

use App\Models\Block;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin.app')]
#[Title('Block Manager')]
class BlockManager extends Component
{
    use WithPagination;

    public string $search = '';
    public bool   $showForm = false;
    public ?int   $deletingId = null;

    // form fields
    public ?int  $editingId     = null;
    public array $title         = ['ar' => '', 'en' => ''];
    public bool  $show_title    = true;
    public bool  $is_active     = true;
    public bool  $is_repeatable = false;

    public string $module = 'Blocks';
    public string $route = 'blcoks';
    public string $permissionPrefix = 'blocks';

    // -----------------------------------------------
    // Validation
    // -----------------------------------------------

    protected function rules(): array
    {
        return [
            'title.ar'      => 'required|string|max:255',
            'title.en'      => 'required|string|max:255',
            'show_title'    => 'boolean',
            'is_active'     => 'boolean',
            'is_repeatable' => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'title.ar.required' => __('Arabic title is required'),
            'title.en.required' => __('English title is required'),
        ];
    }

    // -----------------------------------------------
    // Lifecycle
    // -----------------------------------------------

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    // -----------------------------------------------
    // Helpers
    // -----------------------------------------------

    protected function findBlock(int $id): Block
    {
        return Block::findOrFail($id);
    }

    protected function resetForm(): void
    {
        $this->reset(['editingId', 'show_title', 'is_active', 'is_repeatable']);
        $this->title = ['ar' => '', 'en' => ''];
    }

    // -----------------------------------------------
    // CRUD
    // -----------------------------------------------

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $block = $this->findBlock($id);

        $this->editingId     = $block->id;
        $this->title         = is_array($block->title)
            ? $block->title
            : ['ar' => $block->title, 'en' => $block->title];
        $this->show_title    = $block->show_title;
        $this->is_active     = $block->is_active;
        $this->is_repeatable = $block->is_repeatable;
        $this->showForm      = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        Block::updateOrCreate(
            ['id' => $this->editingId],
            [
                'title'         => $data['title'],
                'show_title'    => $data['show_title'],
                'is_active'     => $data['is_active'],
                'is_repeatable' => $data['is_repeatable'],
            ]
        );

        $this->showForm = false;
        $this->resetForm();
        Flux::toast(variant: 'success', heading: 'Success!', text: 'Block saved successfully.');
    }

    public function toggleActive(int $id): void
    {
        $block = $this->findBlock($id);
        $block->update(['is_active' => ! $block->is_active]);
        Flux::toast(variant: 'success', heading: 'Success!', text: 'Status updated successfully.');
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        Flux::modal('confirm-delete')->show();
    }

    public function delete(): void
    {
        if (! $this->deletingId) return;


        $this->findBlock($this->deletingId)->delete();
        $this->deletingId = null;
        Flux::modal('confirm-delete')->close();
        Flux::toast(variant: 'success', heading: 'Success!', text: 'Block deleted successfully.');
    }

    public function cancelForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    // -----------------------------------------------
    // Render
    // -----------------------------------------------

    public function render()
    {
        $blocks = Block::query()
            ->when($this->search, fn($q) => $q->where('title->ar', 'like', '%' . $this->search . '%')
                ->orWhere('title->en', 'like', '%' . $this->search . '%'))
            ->withCount('fields')
            ->latest()
            ->paginate(15);

        return view('livewire.admin.cms.blocks.block-manager', compact('blocks'));
    }
}

<?php
namespace App\Livewire\Admin\CMS\Blocks;
use App\Models\Block;
use App\Models\BlockField;
use App\Models\BlockFieldValue;
use App\Models\Page;
use App\Models\PageBlock;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
#[Layout('layouts.admin.app')]
#[Title('Block Builder')]
class BlockBuilder extends Component
{
    public Page $page;

    // active page_block being filled
    public ?int $activePageBlockId = null;

    // flat values array: [field_id => value]

    // available blocks to attach
    public bool $showBlockPicker = false;

    // -----------------------------------------------
    // Mount
    // -----------------------------------------------

    public function mount(Page $page): void
    {
        $this->page = $page;
    }

    // -----------------------------------------------

    public function openBlock(int $pageBlockId): void
    {
        $this->activePageBlockId = $pageBlockId;
        $this->values = [];

        $pageBlock = PageBlock::with(['block.fields', 'fieldValues'])->findOrFail($pageBlockId);

        // load existing values
        foreach ($pageBlock->fieldValues as $fv) {
            $this->values[$fv->block_field_id] = $fv->value;
        }

        // fill defaults for fields with no value yet
        foreach ($pageBlock->block->fields as $field) {
            if (!array_key_exists($field->id, $this->values)) {
                $this->values[$field->id] = $this->emptyValue($field);
            }
        }
    }

    // -----------------------------------------------
    // Save values
    // -----------------------------------------------

    public function saveValues(): void
    {
        foreach ($this->values as $group => $fields) {
            foreach ($fields as $fieldId => $value) {
                BlockFieldValue::updateOrCreate(
                    [
                        'page_block_id' => $this->activePageBlockId,
                        'block_field_id' => $fieldId,
                        'group' => $group,
                        'index' => null,
                    ],
                    ['value' => $value],
                );
            }
        }

        BlockFieldValue::where('page_block_id', $this->activePageBlockId)
            ->whereNotIn('group', array_keys($this->values))
            ->delete();

        Flux::toast(variant: 'success', heading: 'Success!', text: 'Block saved successfully.');
    }

    // -----------------------------------------------
    // Repeater helpers
    // -----------------------------------------------

    public function addRepeaterRow(int $fieldId): void
    {
        $field = BlockField::findOrFail($fieldId);
        $row = [];

        foreach ($field->getSubFields() as $sub) {
            $row[$sub['key']] = $sub['translatable'] ?? false ? $this->emptyTranslatable() : null;
        }

        $this->values[$fieldId][] = $row;
    }

    public function removeRepeaterRow(int $fieldId, int $index): void
    {
        array_splice($this->values[$fieldId], $index, 1);
    }

    public function moveRepeaterRow(int $fieldId, int $from, string $dir): void
    {
        $to = $dir === 'up' ? $from - 1 : $from + 1;
        $rows = $this->values[$fieldId];

        if (!isset($rows[$to])) {
            return;
        }

        [$rows[$from], $rows[$to]] = [$rows[$to], $rows[$from]];
        $this->values[$fieldId] = array_values($rows);
    }

    // -----------------------------------------------
    // Attach / detach blocks to page
    // -----------------------------------------------

    public function attachBlock(int $blockId): void
    {
        $maxOrder = $this->page->blocks()->max('page_block.order') ?? 0;

        $this->page->blocks()->attach($blockId, [
            'order' => $maxOrder + 1,
            'is_visible' => true,
        ]);

        $this->showBlockPicker = false;
        $this->page->refresh();
    }

    public function detachBlock(int $pageBlockId): void
    {
        PageBlock::findOrFail($pageBlockId)->delete();

        if ($this->activePageBlockId === $pageBlockId) {
            $this->activePageBlockId = null;
            $this->values = [];
        }

        $this->page->refresh();
    }

    public function toggleVisibility(int $pageBlockId): void
    {
        $pb = PageBlock::findOrFail($pageBlockId);
        $pb->update(['is_visible' => !$pb->is_visible]);
        $this->page->refresh();
    }

    // -----------------------------------------------
    // Drag & drop reorder
    // -----------------------------------------------

    #[On('blocks-reordered')]
    public function reorderBlocks(array $order): void
    {
        foreach ($order as $position => $pageBlockId) {
            PageBlock::where('id', $pageBlockId)->update(['order' => $position + 1]);
        }

        $this->page->refresh();
    }

    // -----------------------------------------------
    // Helpers
    // -----------------------------------------------

    private function emptyValue(BlockField $field): mixed
    {
        if ($field->isRepeater()) {
            return [];
        }

        if ($field->isTranslatable()) {
            return $this->emptyTranslatable();
        }

        if ($field->isRepeatable()) {
            return [];
        }

        return $field->default_value ?? null;
    }

    private function emptyTranslatable(): array
    {
        return collect(config('app.locales', ['ar', 'en']))
            ->mapWithKeys(fn($l) => [$l => ''])
            ->all();
    }

    public function getActivePageBlock(): ?PageBlock
    {
        if (!$this->activePageBlockId) {
            return null;
        }

        return PageBlock::with(['block.fields', 'fieldValues'])->find($this->activePageBlockId);
    }

    // -----------------------------------------------
    // Render
    // -----------------------------------------------

    public function render()
    {
        $pageBlocks = $this->page
            ->blocks()
            ->using(PageBlock::class)
            ->withPivot(['id', 'order', 'is_visible'])
            ->orderByPivot('order')
            ->get();

        $availableBlocks = Block::active()->whereNotIn('id', $pageBlocks->pluck('id'))->orderBy('title')->get();

        $activePageBlock = $this->getActivePageBlock();

        return view('livewire.admin.cms.blocks.block-builder', compact('pageBlocks', 'availableBlocks', 'activePageBlock'));
    }
}

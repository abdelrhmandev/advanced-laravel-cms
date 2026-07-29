<?php
namespace App\Livewire\Admin\CMS\Blocks;
use App\Models\Block;
use App\Models\BlockField;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
#[Layout('layouts.admin.app')]
#[Title('Block Editor')]
class BlockEditor extends Component
{
    public Block $block;

    public ?int $activeFieldId = null;
    public ?int $deletingFieldId = null;
    public bool $showFieldForm = false;
    public bool $showAddPanel = false;

    // field form
    public string $f_key = '';
    public string $f_label = '';
    public string $f_type = 'text';
    public bool $f_translatable = false;
    public bool $f_required = false;
    public array $f_settings = [];

    // select options builder
    public array $selectOptions = [];

    // repeater sub-fields builder (temp UI state)
    public array $repeaterFields = [];

    public array $fieldTypes = [
        'text' => ['label' => 'Text', 'icon' => 'bars-2'],
        'textarea' => ['label' => 'Textarea', 'icon' => 'bars-3'],
        'richtext' => ['label' => 'Rich Text', 'icon' => 'document-text'],
        'image' => ['label' => 'Image', 'icon' => 'photo'],
        'icon' => ['label' => 'Icon', 'icon' => 'clipboard-document'],
        'file' => ['label' => 'File', 'icon' => 'paper-clip'],
        'number' => ['label' => 'Number', 'icon' => 'hashtag'],
        'select' => ['label' => 'Select', 'icon' => 'chevron-up-down'],
        'color' => ['label' => 'Color', 'icon' => 'swatch'],
        // 'repeater' => ['label' => 'Repeater', 'icon' => 'table-cells'],
        // 'relation' => ['label' => 'Relation', 'icon' => 'link'],
    ];

    // -----------------------------------------------
    // Validation
    // -----------------------------------------------
    public $iconSearch = '';

    public function getIconsProperty()
    {
        return ['bars-3', 'home', 'users', 'cog-6-tooth', 'document-text', 'folder', 'chart-bar', 'bell', 'tag', 'photo', 'video-camera', 'heart', 'star', 'magnifying-glass', 'shopping-cart'];
    }

    public function getFilteredIconsProperty()
    {
        return collect($this->icons)->filter(fn($icon) => str_contains($icon, $this->iconSearch))->values();
    }

    public function selectIcon($icon)
    {
        $this->formValues[$this->currentPivot][$this->currentRow][$this->currentField] = $icon;
    }

    protected function rules(): array
    {
        $typeList = implode(',', array_keys($this->fieldTypes));

        $rules = [
            'f_key' => 'required|string|alpha_dash|max:100',
            'f_label' => 'required|string|max:255',
            'f_type' => "required|in:{$typeList}",
            'f_translatable' => 'boolean',
            'f_required' => 'boolean',
        ];

        if ($this->f_type === 'select') {
            $rules['selectOptions'] = 'array|min:1';
            $rules['selectOptions.*.value'] = 'required|string';
            $rules['selectOptions.*.label'] = 'required|string';
        }

        if ($this->f_type === 'repeater') {
            $rules['repeaterFields'] = 'array|min:1';
            $rules['repeaterFields.*.key'] = 'required|string|alpha_dash|max:100';
            $rules['repeaterFields.*.label'] = 'required|string|max:255';
            $rules['repeaterFields.*.type'] = 'required|in:text,textarea,richtext,icon,image,file,number,select,color';
        }

        return $rules;
    }

    // -----------------------------------------------
    // Add field panel
    // -----------------------------------------------

    public function openAddPanel(): void
    {
        $this->resetFieldForm();
        $this->showAddPanel = true;
        $this->showFieldForm = false;
        $this->activeFieldId = null;
    }

    public function selectType(string $type): void
    {
        $this->f_type = $type;
        $this->showAddPanel = false;
        $this->showFieldForm = true;
    }

    // -----------------------------------------------
    // Edit existing field
    // -----------------------------------------------

    public function editField(int $id): void
    {
        $field = BlockField::with('children')->findOrFail($id);

        $this->activeFieldId = $field->id;
        $this->f_key = $field->key;
        $this->f_label = $field->label;
        $this->f_type = $field->type;
        $this->f_translatable = $field->translatable;
        $this->f_required = $field->required;
        $this->f_settings = $field->settings ?? [];
        $this->selectOptions = $field->getOptions();

        // load sub-fields من الـ DB عن طريق children relation
        $this->repeaterFields = $field->children
            ->map(
                fn($c) => [
                    'id' => $c->id,
                    'key' => $c->key,
                    'label' => $c->label,
                    'type' => $c->type,
                    'translatable' => $c->translatable,
                ],
            )
            ->toArray();

        $this->showFieldForm = true;
        $this->showAddPanel = false;
    }

    // -----------------------------------------------
    // Save field
    // -----------------------------------------------

    public function saveField(): void
    {
        $this->validate();

        $settings = $this->buildSettings();
        $order = $this->resolveOrder();

        $translatable = $this->f_translatable;

        if ($this->f_type == 'repeater' || $this->f_type == 'number' || $this->f_type == 'color') {
            $translatable = 0;
        }

        $field = BlockField::updateOrCreate(
            ['id' => $this->activeFieldId],
            [
                'block_id' => $this->block->id,
                'parent_id' => null,
                'key' => $this->f_key,
                'label' => $this->f_label,
                'type' => $this->f_type,
                'translatable' => $translatable,
                'required' => $this->f_required,
                'settings' => $settings,
                'order' => $order,
            ],
        );


        if ($this->f_type === 'repeater') {
            $this->saveRepeaterChildren($field);
        }

        $this->block->refresh();
        $this->closeFieldForm();
        Flux::toast(variant: 'success', heading: 'Success!', text: 'Field saved successfully.');
    }

    private function saveRepeaterChildren(BlockField $parent): void
    {
        $existingIds = collect($this->repeaterFields)->pluck('id')->filter()->values();


        $parent->children()->whereNotIn('id', $existingIds)->delete();

        foreach ($this->repeaterFields as $order => $sub) {
            BlockField::updateOrCreate(
                [
                    'id' => $sub['id'] ?? null,
                ],
                [
                    'block_id' => $this->block->id,
                    'parent_id' => $parent->id,
                    'key' => $sub['key'],
                    'label' => $sub['label'],
                    'type' => $sub['type'],
                    'translatable' => $sub['translatable'] ?? false,
                    'required' => false,
                    'order' => $order + 1,
                ],
            );
        }
    }

    public function confirmDeleteField(int $id): void
    {
        $this->deletingFieldId = $id;
        Flux::modal('confirm-delete-field')->show();
    }

    public function deleteField(): void
    {
        if (!$this->deletingFieldId) {
            return;
        }

        $field = BlockField::findOrFail($this->deletingFieldId);

        $field->delete();

        $this->block->refresh();

        if ($this->activeFieldId === $this->deletingFieldId) {
            $this->closeFieldForm();
        }

        $this->deletingFieldId = null;
        Flux::modal('confirm-delete-field')->close();
        Flux::toast(variant: 'success', heading: 'Success!', text: 'Field deleted successfully.');
    }

    // -----------------------------------------------
    // Drag & drop reorder
    // -----------------------------------------------

    #[On('fields-reordered')]
    public function reorderFields(array $order): void
    {
        foreach ($order as $position => $fieldId) {
            BlockField::where('id', $fieldId)->update(['order' => $position + 1]);
        }
        Flux::toast(variant: 'success', heading: 'Success!', text: 'Fields reordered successfully.');
        $this->block->refresh();
    }

    // -----------------------------------------------
    // Select options builder
    // -----------------------------------------------

    public function addOption(): void
    {
        $this->selectOptions[] = ['value' => '', 'label' => ''];
    }

    public function removeOption(int $index): void
    {
        array_splice($this->selectOptions, $index, 1);
    }

    // -----------------------------------------------
    // Repeater sub-fields builder
    // -----------------------------------------------

    public function addRepeaterField(): void
    {
        $this->repeaterFields[] = [
            'id' => null,
            'key' => '',
            'label' => '',
            'type' => 'text',
            'translatable' => false,
        ];
    }

    public function addTitleIconRow(): void
    {
        $this->repeaterFields[] = [
            'id' => null,
            'key' => 'title',
            'label' => 'Title',
            'type' => 'text',
            'translatable' => true,
        ];

        $this->repeaterFields[] = [
            'id' => null,
            'key' => 'icon',
            'label' => 'Icon',
            'type' => 'image',
            'translatable' => false,
        ];
    }

    public function removeRepeaterField(int $index): void
    {
        array_splice($this->repeaterFields, $index, 1);
    }

    // -----------------------------------------------
    // Helpers
    // -----------------------------------------------

    private function resolveOrder(): int
    {
        if ($this->activeFieldId) {
            return (int) BlockField::where('id', $this->activeFieldId)->value('order');
        }

        return (int) $this->block->fields()->max('order') + 1;
    }

    private function buildSettings(): array
    {
        return match ($this->f_type) {
            'text', 'textarea', 'richtext' => [
                'max_length' => (int) ($this->f_settings['max_length'] ?? 255),
                'placeholder' => $this->f_settings['placeholder'] ?? '',
            ],
            'image', 'file' => [
                'disk' => $this->f_settings['disk'] ?? 'public',
                'max_size' => (int) ($this->f_settings['max_size'] ?? 2048),
            ],

            'icon' => [
                'disk' => $this->f_settings['disk'] ?? 'public',
                'max_size' => (int) ($this->f_settings['max_size'] ?? 2048),
            ],

            'select' => [
                'options' => $this->selectOptions,
                'multiple' => $this->f_settings['multiple'] ?? false,
            ],
            'color' => [
                'format' => $this->f_settings['format'] ?? 'hex',
            ],
            'number' => [
                'min' => $this->f_settings['min'] ?? null,
                'max' => $this->f_settings['max'] ?? null,
                'step' => $this->f_settings['step'] ?? 1,
            ],
            'relation' => [
                'model' => 'Page',
            ],
            'repeater' => [], // الـ sub-fields بتتحفظ كـ children في الـ DB
            default => [],
        };
    }

    private function resetFieldForm(): void
    {
        $this->reset(['activeFieldId', 'f_key', 'f_label', 'f_settings', 'selectOptions', 'repeaterFields']);

        $this->f_type = 'text';
        $this->f_translatable = false;
        $this->f_required = false;
    }

    public function closeFieldForm(): void
    {
        $this->showFieldForm = false;
        $this->showAddPanel = false;
        $this->resetFieldForm();
    }

    // -----------------------------------------------
    // Render
    // -----------------------------------------------

    public function render()
    {
        return view('livewire.admin.cms.blocks.block-editor');
    }
}

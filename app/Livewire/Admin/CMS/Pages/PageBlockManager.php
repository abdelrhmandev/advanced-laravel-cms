<?php
namespace App\Livewire\Admin\CMS\Pages;
use App\Models\BlockField;
use App\Models\BlockFieldValue;
use App\Models\Page;
use App\Models\PageBlock;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
#[Layout('layouts.admin.app')]
#[Title('Page Block Manager')]
class PageBlockManager extends Component
{
    use WithFileUploads;
    public Page $page;
    public $deletePivotId = null;
    public $deletePageBlockId = null;
    public $deleteRowIndex = null;
    public $showDeleteModal = false;

    // [page_block_id => [row => [field_id => value]]]
    public array $formValues = [];

    // -----------------------------------------------
    // Mount
    // -----------------------------------------------

    public function mount(int $id): void
    {
        $this->page = Page::with(['blocks.fields.children', 'blocks' => fn($q) => $q->withPivot(['id', 'order', 'is_visible'])])->findOrFail($id);

        $this->loadFormValues();
    }

    // -----------------------------------------------
    // Load
    // -----------------------------------------------

    private function loadFormValues(): void
    {
        foreach ($this->page->blocks as $block) {
            $pivot = $block->pivot;

            $dbValues = BlockFieldValue::where('page_block_id', $pivot->id)->get()->groupBy('row');

            if ($block->is_repeatable) {
                if ($dbValues->isEmpty()) {
                    $this->formValues[$pivot->id][1] = $this->emptyRow($block->fields);
                } else {
                    foreach ($dbValues as $row => $values) {
                        foreach ($values as $fv) {
                            $field = $block->fields->firstWhere('id', $fv->block_field_id);
                            $this->formValues[$pivot->id][$row][$fv->block_field_id] = $this->normalizeValue($field, $fv->value);
                        }
                    }
                }
            } else {
                $row0 = $dbValues->get(0, collect());
                foreach ($block->fields as $field) {
                    $fv = $row0->firstWhere('block_field_id', $field->id);
                    $this->formValues[$pivot->id][0][$field->id] = $fv ? $this->normalizeValue($field, $fv->value) : $this->defaultFor($field);
                }
            }
        }
    }

    private function normalizeValue(?BlockField $field, mixed $value): mixed
    {
        if (!$field) {
            return $value;
        }

        if ($field->type === 'repeater') {
            return is_array($value) ? $value : [];
        }

        if ($field->isTranslatable()) {
            $locales = collect(LaravelLocalization::getSupportedLocales())->keys();

            if (is_array($value)) {
                return $locales->mapWithKeys(fn($l) => [$l => $value[$l] ?? null])->all();
            }

            $fallback = config('app.fallback_locale');
            return $locales->mapWithKeys(fn($l) => [$l => $l === $fallback ? $value : null])->all();
        }

        if (is_array($value)) {
            $fallback = config('app.fallback_locale');
            return $value[$fallback] ?? reset($value) ?: null;
        }

        return $value;
    }
    private function decodeValue(?string $rawValue): mixed
    {
        if ($rawValue === null) {
            return null;
        }

        $decoded = json_decode($rawValue, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $rawValue;
    }

    private function emptyRow($fields): array
    {
        $row = [];
        foreach ($fields as $field) {
            $row[$field->id] = $this->defaultFor($field);
        }
        return $row;
    }

    private function defaultFor(BlockField $field): mixed
    {
        if ($field->type === 'repeater') {
            return [];
        }

        if ($this->isEffectivelyTranslatable($field)) {
            return collect(LaravelLocalization::getSupportedLocales())->keys()->mapWithKeys(fn($locale) => [$locale => null])->all();
        }

        return null;
    }
    // -----------------------------------------------
    // Repeatable block rows
    // -----------------------------------------------

    public function addRow(int $pageBlockId): void
    {
        $pageBlock = PageBlock::with('block.fields.children')->findOrFail($pageBlockId);
        $rows = $this->formValues[$pageBlockId] ?? [];
        $nextRow = empty($rows) ? 1 : max(array_keys($rows)) + 1;

        $this->formValues[$pageBlockId][$nextRow] = $this->emptyRow($pageBlock->block->fields);
    }

    public function confirmDelete(int $pageBlockId, int $row): void
    {
        $this->deletePageBlockId = $pageBlockId;
        $this->deleteRowIndex = $row;

        $this->showDeleteModal = true;
    }

    public function resetDeleteState()
    {
        $this->deletePivotId = null;
        $this->deleteRowIndex = null;
        $this->showDeleteModal = false;
    }

    public function deleteRow(): void
    {
        $pageBlockId = $this->deletePageBlockId;
        $row = $this->deleteRowIndex;
        if (!isset($this->formValues[$pageBlockId][$row])) {
            return;
        }
        unset($this->formValues[$pageBlockId][$row]);
        $this->formValues[$pageBlockId] = array_values($this->formValues[$pageBlockId]);
        $this->saveBlock($pageBlockId);

        Flux::toast(variant: 'success', heading: 'Deleted', text: 'Row removed successfully.');

        $this->resetDeleteState();
    }

    // -----------------------------------------------
    // Repeater field items
    // -----------------------------------------------

    public function addRepeaterRow(int $pageBlockId, int $row, int $fieldId): void
    {
        $field = BlockField::with('children')->findOrFail($fieldId);

        $subRow = [];
        foreach ($field->children as $child) {
            $subRow[$child->id] = $this->defaultFor($child);
        }

        $this->formValues[$pageBlockId][$row][$fieldId][] = $subRow;
    }

    public function removeRepeaterRow(int $pageBlockId, int $row, int $fieldId, int $index): void
    {
        array_splice($this->formValues[$pageBlockId][$row][$fieldId], $index, 1);
    }

    // -----------------------------------------------
    // Validation rule builder (recursive — handles repeater children)
    // -----------------------------------------------
    public function isEffectivelyTranslatable($field): bool
    {
        return (bool) $field->translatable && count(LaravelLocalization::getSupportedLocales()) > 1;
    }

    protected function fileFieldRule(BlockField $field, mixed $value, string $type = 'image'): string
    {
        $isNewUpload = $value instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

        if (!$isNewUpload) {
            // Existing stored path (string) or empty → treat as string
            return $field->required ? 'required|string' : 'nullable|string';
        }

        $maxSize = $field->settings['max_size'] ?? 2048;
        $rule = $field->required ? "required|{$type}|max:{$maxSize}" : "nullable|{$type}|max:{$maxSize}";

        if ($type === 'file' && !empty($field->settings['mimes'])) {
            $rule .= '|mimes:' . $field->settings['mimes'];
        }

        return $rule;
    }

    ////////////////////////////////////////////
    private function buildFieldRules(BlockField $field, string $base, int $pageBlockId, int $row): array
    {
        $rules = [];
        $messages = [];

        if ($field->type === 'repeater') {
            if ($field->required) {
                $rules[$base] = 'required|array|min:1';
                $messages["{$base}.min"] = "{$field->label} " . __('requires at least one item');
            } else {
                $rules[$base] = 'nullable|array';
            }

            $items = $this->formValues[$pageBlockId][$row][$field->id] ?? [];

            foreach (array_keys($items) as $index) {
                foreach ($field->children as $child) {
                    $childBase = "{$base}.{$index}.{$child->id}";
                    [$childRules, $childMessages] = $this->buildFieldRules($child, $childBase, $pageBlockId, $row);
                    $rules = array_merge($rules, $childRules);
                    $messages = array_merge($messages, $childMessages);
                }
            }

            return [$rules, $messages];
        }

        if ($this->isEffectivelyTranslatable($field)) {
            foreach (LaravelLocalization::getSupportedLocales() as $locale => $properties) {
                if (in_array($field->type, ['image', 'file'])) {
                    $localeValue = $this->formValues[$pageBlockId][$row][$field->id][$locale] ?? null;
                    $rules["{$base}.{$locale}"] = $this->fileFieldRule($field, $localeValue, $field->type);
                } else {
                    $rules["{$base}.{$locale}"] = $field->required ? 'required|string' : 'nullable|string';
                }

                $messages["{$base}.{$locale}.required"] = "{$field->label} ({$locale}) " . __('is required');
            }

            return [$rules, $messages];
        }

        if (in_array($field->type, ['image', 'file'])) {
            $value = $this->formValues[$pageBlockId][$row][$field->id] ?? null;
            $rules[$base] = $this->fileFieldRule($field, $value, $field->type);
            $messages["{$base}.required"] = "{$field->label} " . __('is required');

            return [$rules, $messages];
        }

        $rules[$base] = match ($field->type) {
            'number' => (function () use ($field) {
                $rules = $field->required ? ['required', 'numeric'] : ['nullable', 'numeric'];

                if (isset($field->settings['min']) && $field->settings['min'] !== '') {
                    $rules[] = 'min:' . $field->settings['min'];
                }

                if (isset($field->settings['max']) && $field->settings['max'] !== '') {
                    $rules[] = 'max:' . $field->settings['max'];
                }

                return implode('|', $rules);
            })(),
            'select' => $field->required ? 'required|in:' . collect($field->getOptions())->pluck('value')->implode(',') : 'nullable|in:' . collect($field->getOptions())->pluck('value')->implode(','),
            default => $field->required ? 'required|string' : 'nullable|string',
        };
        $messages["{$base}.required"] = "{$field->label} " . __('is required');

        return [$rules, $messages];
    }
    ///////////////////////////////////////////////

    // -----------------------------------------------
    // Save
    // -----------------------------------------------

    public function saveBlock(int $pageBlockId): void
    {
        $pageBlock = PageBlock::with('block.fields.children')->findOrFail($pageBlockId);
        $rules = [];
        $messages = [];

        foreach ($pageBlock->block->fields as $field) {
            $rows = $this->formValues[$pageBlockId] ?? [];

            foreach ($rows as $row => $fields) {
                $base = "formValues.{$pageBlockId}.{$row}.{$field->id}";
                [$fieldRules, $fieldMessages] = $this->buildFieldRules($field, $base, $pageBlockId, $row);
                $rules = array_merge($rules, $fieldRules);
                $messages = array_merge($messages, $fieldMessages);
            }
        }

        $this->validate($rules, $messages);

        // Grab old values BEFORE deleting, so we can clean up replaced files
        $oldValues = BlockFieldValue::where('page_block_id', $pageBlockId)->get()->groupBy('row');

        // Flatten fields (including repeater children): type + settings lookup by id
        $fieldTypes = [];
        $fieldSettings = [];
        foreach ($pageBlock->block->fields as $field) {
            $fieldTypes[$field->id] = $field->type;
            $fieldSettings[$field->id] = $field->settings ?? [];
            foreach ($field->children as $child) {
                $fieldTypes[$child->id] = $child->type;
                $fieldSettings[$child->id] = $child->settings ?? [];
            }
        }

        // Resolve new uploads to stored paths, delete replaced files
        foreach ($this->formValues[$pageBlockId] as $row => $fields) {
            foreach ($fields as $fieldId => $value) {
                if (!in_array($fieldTypes[$fieldId] ?? null, ['image', 'file'])) {
                    continue;
                }

                $disk = $fieldSettings[$fieldId]['disk'] ?? 'public';
                $oldFv = $oldValues->get($row, collect())->firstWhere('block_field_id', $fieldId);

                if ($value instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                    // Non-translatable image
                    $oldPath = $oldFv ? trim($oldFv->value ?? '', '"') : null;

                    if ($oldPath && Storage::disk($disk)->exists($oldPath)) {
                        Storage::disk($disk)->delete($oldPath);
                    }

                    $this->formValues[$pageBlockId][$row][$fieldId] = $value->store('uploads/blocks', $disk);
                } elseif (is_array($value)) {
                    // Translatable image: value is [locale => TemporaryUploadedFile|string|null]
                    $oldDecoded = $oldFv ? $oldFv->value : null;

                    foreach ($value as $locale => $localeValue) {
                        if ($localeValue instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                            $oldPath = $oldDecoded[$locale] ?? null;

                            if ($oldPath && Storage::disk($disk)->exists($oldPath)) {
                                Storage::disk($disk)->delete($oldPath);
                            }

                            $this->formValues[$pageBlockId][$row][$fieldId][$locale] = $localeValue->store('uploads/blocks', $disk);
                        }
                    }
                }
            }
        }

        BlockFieldValue::where('page_block_id', $pageBlockId)->delete();

        $rows = [];

        foreach ($this->formValues[$pageBlockId] as $row => $fields) {
            foreach ($fields as $fieldId => $value) {
                $rows[] = [
                    'page_block_id' => $pageBlockId,
                    'block_field_id' => $fieldId,
                    'row' => $row,
                    'index' => 0,
                    'value' => json_encode($value),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($rows)) {
            BlockFieldValue::insert($rows);
        }

        Flux::toast(variant: 'success', heading: 'Success!', text: 'Block saved successfully.');

        $this->page->load(['blocks.fields.children', 'blocks' => fn($q) => $q->withPivot(['id', 'order', 'is_visible'])]);
    }

    public function removeFile(int $pageBlockId, int $row, int $fieldId, ?string $locale = null): void
    {
        $field = BlockField::find($fieldId);
        $disk = $field->settings['disk'] ?? 'public';

        if ($locale) {
            $currentValue = $this->formValues[$pageBlockId][$row][$fieldId][$locale] ?? null;

            if (is_string($currentValue) && $currentValue !== '' && Storage::disk($disk)->exists($currentValue)) {
                Storage::disk($disk)->delete($currentValue);
            }

            $this->formValues[$pageBlockId][$row][$fieldId][$locale] = null;
        } else {
            $currentValue = $this->formValues[$pageBlockId][$row][$fieldId] ?? null;

            if (is_string($currentValue) && $currentValue !== '' && Storage::disk($disk)->exists($currentValue)) {
                Storage::disk($disk)->delete($currentValue);
            }

            $this->formValues[$pageBlockId][$row][$fieldId] = null;
        }

        $existingRow = BlockFieldValue::where('page_block_id', $pageBlockId)->where('row', $row)->where('block_field_id', $fieldId)->first();

        if ($existingRow) {
            if ($locale) {
                $dbValue = is_array($existingRow->value) ? $existingRow->value : [];
                $dbValue[$locale] = null;
                $existingRow->update(['value' => $dbValue]);
            } else {
                $existingRow->update(['value' => null]);
            }
        }

        Flux::toast(variant: 'success', heading: 'Success!', text: 'File deleted successfully.');
    }

    ////////////

    public function render()
    {
        return view('livewire.admin.cms.pages.show');
    }
}

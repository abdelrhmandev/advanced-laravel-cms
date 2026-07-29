<?php

namespace App\Services\Block;

use App\Models\Block;
use App\Models\BlockField;

class BlockFieldService
{
    public function save(Block $block, array $data): BlockField
    {
        $field = BlockField::updateOrCreate(
            ['id' => $data['id'] ?? null],
            [
                'block_id' => $block->id,
                'parent_id' => null,
                'key' => $data['key'],
                'label' => $data['label'],
                'type' => $data['type'],
                'translatable' => $data['translatable'],
                'required' => $data['required'],
                'settings' => $data['settings'],
                'order' => $data['order'],
            ]
        );

        if ($data['type'] === 'repeater') {
            $this->saveRepeaterChildren($field, $data['repeaterFields'] ?? []);
        }

        return $field;
    }

    private function saveRepeaterChildren(BlockField $parent, array $children): void
    {
        $existingIds = collect($children)->pluck('id')->filter();

        $parent->children()->whereNotIn('id', $existingIds)->delete();

        foreach ($children as $order => $child) {
            BlockField::updateOrCreate(
                ['id' => $child['id'] ?? null],
                [
                    'block_id' => $parent->block_id,
                    'parent_id' => $parent->id,
                    'key' => $child['key'],
                    'label' => $child['label'],
                    'type' => $child['type'],
                    'translatable' => $child['translatable'] ?? false,
                    'required' => false,
                    'order' => $order + 1,
                ]
            );
        }
    }

    public function delete(int $fieldId): void
    {
        BlockField::findOrFail($fieldId)->delete();
    }

    public function reorder(array $order): void
    {
        foreach ($order as $position => $fieldId) {
            BlockField::where('id', $fieldId)
                ->update(['order' => $position + 1]);
        }
    }
}

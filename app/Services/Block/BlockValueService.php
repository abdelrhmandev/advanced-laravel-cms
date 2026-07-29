<?php

namespace App\Services\Block;

use App\Models\BlockFieldValue;

class BlockValueService
{
    public function save(int $pageBlockId, array $values): void
    {
        foreach ($values as $group => $fields) {
            foreach ($fields as $fieldId => $value) {
                BlockFieldValue::updateOrCreate(
                    [
                        'page_block_id' => $pageBlockId,
                        'block_field_id' => $fieldId,
                        'group' => $group,
                        'index' => null,
                    ],
                    [
                        'value' => $value,
                    ]
                );
            }
        }

        BlockFieldValue::where('page_block_id', $pageBlockId)
            ->whereNotIn('group', array_keys($values))
            ->delete();
    }
}

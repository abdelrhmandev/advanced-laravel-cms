<?php
namespace App\Services\Page;

use App\Models\Page;
use App\Models\PageBlock;

class PageBlockService
{
    public function attach(Page $page, int $blockId): void
    {
        $maxOrder = $page->blocks()->max('page_block.order') ?? 0;

        $page->blocks()->attach($blockId, [
            'order' => $maxOrder + 1,
            'is_visible' => true,
        ]);
    }

    public function detach(int $pageBlockId): void
    {
        PageBlock::findOrFail($pageBlockId)->delete();
    }

    public function toggle(int $pageBlockId): void
    {
        $pb = PageBlock::findOrFail($pageBlockId);
        $pb->update(['is_visible' => !$pb->is_visible]);
    }

    public function reorder(array $order): void
    {
        foreach ($order as $position => $pageBlockId) {
            PageBlock::where('id', $pageBlockId)
                ->update(['order' => $position + 1]);
        }
    }
}

<?php
namespace App\Livewire\Admin\CMS\Pages;
use App\Http\Requests\Admin\CMS\PageRequest;
use App\Models\Block;
use App\Models\Page;
use App\Models\PageBlock;
use App\Services\TranslationService;
use App\Traits\ManagesSeo;
use App\Traits\Upload;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin.app')]
class PageForm extends Component
{
    use WithFileUploads, ManagesSeo, Upload;

    public array $permittedActions = ['create', 'edit'];
    public Page $page;

    public ?string $UploadDir = 'pages';
    public string $module = 'Pages';
    public string $route = 'pages';
    public string $permissionPrefix = 'pages';

    public ?string $pagetitle = null;
    public ?int $Id = null;
    public ?int $deletingId = null;

    public string|\Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null $image = null;
    public ?string $existingImage = null;
    public ?bool $is_active = true;
    public ?string $template = null;

    public array $translations = [];
    public $blocks = [];
    public array $selectedBlocks = [];

    protected $translationService = [];

    // SEO base fields
    public string|\Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null $ogImage = null;
    public string|\Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null $twitterImage = null;
    public ?string $existingOgImage = null;
    public ?string $existingTwitterImage = null;
    public ?string $canonicalUrl = null;
    public bool $noIndex = false;
    public bool $noFollow = false;

    // SEO translations
    public array $seoTranslations = [];

    protected function rules()
    {
        return (new PageRequest())->rules($this->Id, 'page_translations', 'page_id');
    }

    protected function messages()
    {
        return (new PageRequest())->messages();
    }

    public function updatedImage(): void
    {
        $this->validateOnly('image');
    }

    public function updatedOgImage(): void
    {
        $this->validateOnly('ogImage');
    }

    public function updatedTwitterImage(): void
    {
        $this->validateOnly('twitterImage');
    }

    ////////////////////

    public function save(TranslationService $translationService)
    {
        $data = $this->validate();
        $page = Page::updateOrCreate(
            ['id' => $this->Id ?? null],
            [
                'is_active' => $data['is_active'] ?? false,
                'template' => $data['template'] ?? null,
                'image' => $this->upload($this->image, $uploadDir ?? 'pages', $this->existingImage) ?? null,
            ],
        );
        $page->touch();

                $blockIds = collect($data['selectedBlocks'])
                    ->filter()
                    ->map(fn($id) => (int) $id)
                    ->values();

                $page->pageBlocks()
                    ->whereNotIn('block_id', $blockIds)
                    ->delete();

                if ($blockIds->isNotEmpty()) {
                    $now = now();
                    $upsertData = $blockIds->map(fn($blockId, $index) => [
                        'page_id'    => $page->id,
                        'block_id'   => $blockId,
                        'order'      => $index,
                        'is_visible' => true,
                        'anchor'     => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])->toArray();

                    PageBlock::upsert(
                        $upsertData,
                        ['page_id', 'block_id'],
                        ['order', 'updated_at']
                    );
                }


        $translationService->saveTrans($page, $data['translations']);
        $seoData = $this->prepareSeoData('pages');
        $seo = $page->seo()->updateOrCreate([], $seoData);
        if(!empty($data['seoTranslations'])){
            foreach ($data['seoTranslations'] as $localeCode => $seoTransData) {
                $seo->translations()->updateOrCreate(['locale' => $localeCode], $seoTransData);
            }
        }

        Flux::toast(variant: 'success', heading: 'Success!', text: $this->Id ? ucfirst(Str::singular($this->module)) . ' updated successfully' : ucfirst(Str::singular($this->module)) . ' created successfully');
        $this->redirect($this->Id ? route('admin.' . $this->route . '.edit', $this->Id) : route('admin.' . $this->route . '.index'), navigate: true);
    }

    public function mount(?Page $page = null, TranslationService $translationService): void
    {
        $this->pagetitle = $this->module;
        $this->module = $this->module;
        $this->blocks = Block::select(['id', 'title'])->get();

        if ($page?->exists) {
            $this->page = $page;
            $this->Id = $page->id;
            $this->is_active = $page->is_active;
            $this->pagetitle = "{$this->module} | Edit {$page->translate->title}";
            $this->template = $page->template;
            $this->existingImage = $page->image;

            $this->translations = $translationService->mountTrans($page); #translations
            $this->mountSeo($page); # seos
            $this->selectedBlocks = $page->pageBlocks->pluck('id')->toArray();
        } else {
            $this->page = new Page();
            $this->pagetitle .= ' | Add';
            $this->existingImage = null;
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        Flux::modal('confirm-delete')->show();
    }

    public function delete(): void
    {
        if (!$this->deletingId) {
            return;
        }

        $page = Page::find($this->deletingId);

        if (!$page) {
            $this->deletingId = null;
            return;
        }

        $page->delete();

        \Illuminate\Support\Facades\Cache::forget('admin_dashboard_data');

        Flux::toast(variant: 'success', heading: 'Success!', text: ucfirst(Str::singular($this->module)) . ' deleted successfully.');
        Flux::modal('confirm-delete')->close();
        $this->redirect(route('admin.' . $this->route . '.index'), navigate: true);

        $this->deletingId = null;
    }

    public function rendering($view, $data): void
    {
        $view->layoutData([
            'title' => $this->pagetitle,
            'module' => $this->module,
            'route' => $this->route,
            'permissionPrefix' => $this->permissionPrefix,
        ]);
    }
    public function render()
    {
        return view('livewire.admin.cms.pages.form');
    }
}

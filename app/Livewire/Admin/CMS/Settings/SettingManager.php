<?php
namespace App\Livewire\Admin\CMS\Settings;
use App\Models\Setting;
use App\Traits\AuthorizesActions;
use Flux\Flux;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
#[Layout('layouts.admin.app')]
#[Title('Settings')]
class SettingManager extends Component
{
    use WithFileUploads, AuthorizesActions;

    public $settings = [];
    public $uploads = [];
    public array $permittedActions = ['edit', 'create'];
    public ?int $deletingId = null;

    public function refreshCache()
    {
        \Illuminate\Support\Facades\Cache::forget('settings');
        app()->forgetInstance('settings');
        app('settings');
        Flux::toast(variant: 'success', heading: 'Success System Synced', text: 'The settings cache has been fully refreshed successfully.');
    }

    public function mount()
    {
        $this->settings = Setting::all()->toArray();
    }
    public $new_setting = ['label' => '', 'key' => '', 'type' => 'textbox', 'is_translatable' => false];

    public function updatedNewSettingLabel($value)
    {
        $slug = str($value)->slug('_')->lower()->value();
        $this->new_setting['key'] = $slug;
    }

    public function addNewSetting()
    {
        $this->authorizeAction('create');
        $prefix = $this->new_setting['type'] === 'social' ? 'social_' : 'site_';
        $baseKey = str($this->new_setting['key'])->slug('_')->lower();

        $this->validate([
            'new_setting.label' => 'required|string|min:3',
            'new_setting.type' => 'required',
            'new_setting.key' => 'required|string|alpha_dash',
        ]);

        if ($this->new_setting['is_translatable']) {
            $languages = ['en', 'ar'];

            foreach ($languages as $lang) {
                $finalKey = $prefix . $baseKey . '_' . $lang;
                $langLabel = $this->new_setting['label'] . ' (' . strtoupper($lang) . ')';

                // Only create if it doesn't already exist to avoid crashes
                if (!Setting::where('key', $finalKey)->exists()) {
                    Setting::create([
                        'label' => $langLabel,
                        'key' => $finalKey,
                        'type' => $this->new_setting['type'] === 'social' ? 'textbox' : $this->new_setting['type'],
                        'value' => '',
                    ]);
                }
            }
        } else {
            // Standard single key creation if checkbox is NOT ticked
            $finalKey = $prefix . $baseKey;

            if (Setting::where('key', $finalKey)->exists()) {
                $this->addError('new_setting.key', __('This key already exists.'));
                return;
            }

            Setting::create([
                'label' => $this->new_setting['label'],
                'key' => $finalKey,
                'type' => $this->new_setting['type'] === 'social' ? 'textbox' : $this->new_setting['type'],
                'value' => '',
            ]);
        }

        $this->reset('new_setting');
        $this->mount();
        $this->dispatch('closeModal', id: '#kt_modal_add_setting');

        Flux::toast(variant: 'success', heading: 'Success!', text: 'Settings generated successfully.');
    }
    ////////////

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        Flux::modal('confirm-delete-image')->show();
    }

    public function removeImage(): void
    {
        if (!$this->deletingId) {
            return;
        }

        $setting = $this->settings[$this->deletingId] ?? null;
        if (!$setting) {
            return;
        }
        if (!empty($setting['value'])) {
            Storage::disk('public')->delete($setting['value']);
        }
        Setting::where('id', $setting['id'])->update(['value' => null]);
        if (isset($this->uploads[$this->deletingId])) {
            unset($this->uploads[$this->deletingId]);
        }
        $this->settings[$this->deletingId]['value'] = null;
        Flux::toast(variant: 'success', heading: 'Success!', text: 'Image deleted successfully.');
    }
    ///////////////

    public function save()
    {
        $this->authorizeAction('edit');

        foreach ($this->settings as $index => $setting) {
            $value = $setting['value'];

            if (isset($this->uploads[$index])) {
                if (!empty($setting['value'])) {
                    Storage::disk('public')->delete($setting['value']);
                }

                $value = $this->uploads[$index]->store('uploads/settings', 'public');
            }

            $model = Setting::find($setting['id']);

            if ($model) {
                $model->value = $value;
                $model->save();
            }
        }

        Cache::forget('settings');

        $this->reset('uploads');
        $this->settings = Setting::all()->toArray();

        Flux::toast(variant: 'success', heading: 'Success!', text: 'Settings saved successfully.');
    }

    public function render()
    {
        return view('livewire.admin.cms.settings.index', [
            'settingsCollection' => collect($this->settings),
        ]);
    }
}

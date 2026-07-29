<?php
namespace App\Livewire\Admin;
use Livewire\Component;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class Breadcrumbs extends Component
{
    public $breadcrumbs = [];

    private function formatLabel($segment)
    {
        return match ($segment) {
            'users' => 'Users',
            'create' => 'Create',
            'edit' => 'Edit',
            default => Str::title(str_replace('-', ' ', $segment)),
        };
    }
    public function mount()
    {
        $url = '';
        $segments = Request::segments();

        $ignore = ['admin'];

        foreach ($segments as $segment) {
            if (is_numeric($segment) || in_array($segment, $ignore)) {
                continue;
            }

            $url .= '/' . $segment;

            $this->breadcrumbs[] = [
                'label' => $this->formatLabel($segment),
                'url' => url($url),
            ];
        }

        if (!empty($this->breadcrumbs)) {
            $lastIndex = count($this->breadcrumbs) - 1;
            $this->breadcrumbs[$lastIndex]['active'] = true;
            unset($this->breadcrumbs[$lastIndex]['url']);
        }
    }

    public function render()
    {
        return view('livewire.admin.breadcrumbs');
    }
}

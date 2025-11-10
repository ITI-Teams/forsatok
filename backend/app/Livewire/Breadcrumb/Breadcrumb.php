<?php

namespace App\Livewire\Breadcrumb;

use Livewire\Component;

class Breadcrumb extends Component
{

    public $segments = [];

    public function mount()
    {
        $this->buildFromUrl();
    }

    protected function buildFromUrl()
    {
        $segments = request()->segments(); // array of URL parts
        $breadcrumb = [];
        $acc = '';

        foreach ($segments as $segment) {
            $acc .= '/' . $segment;

            if (is_numeric($segment)) {
                $label = $segment;
            } else {
                $label = ucfirst(str_replace('-', ' ', $segment));
            }

            $breadcrumb[] = [
                'label' => $label,
                'url' => url($acc)
            ];
        }

        if (!empty($breadcrumb)) {
            $breadcrumb[count($breadcrumb) - 1]['url'] = null;
        }

        $this->segments = $breadcrumb;
    }
    public function render()
    {
        return view('livewire.breadcrumb.breadcrumb');
    }
}

<?php

namespace App\Livewire\Search;

use Livewire\Component;

class Search extends Component
{
    public string $query = '';
    public array $searchFields = [];
    public string $placeholder = 'Search...';
    public string $emitEvent = '';
    public int $perPage = 10;

    public function mount(
        array $searchFields = [],
        string $placeholder = 'Search...',
        string $emitEvent = '',
        int $perPage = 10
    ) {
        $this->searchFields = $searchFields;
        $this->placeholder = $placeholder;
        $this->emitEvent = $emitEvent ?: 'globalSearchUpdated';
        $this->perPage = $perPage;
    }

    public function updatedQuery()
    {
        if (!$this->emitEvent) {
            return;
        }

        $this->dispatch($this->emitEvent, [
            'fields' => $this->searchFields,
            'query' => trim($this->query),
            'perPage' => $this->perPage,
        ]);
    }

    public function render()
    {
        return view('livewire.search.search');
    }
}

<?php

namespace App\Domains\Shared\Services\FrontendDetection;

class FrontendUrlService
{
    protected string $source = 'web'; // Default

    public function setSource(string $source): void
    {
        $this->source = $source;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * Get the base URL for the current frontend.
     */
    public function getBaseUrl(): string
    {
        return match ($this->source) {
            'react', 'react_dashboard' => config('app.hive_url'), // New React URL
            'angular' => config('app.jobhub_url'), // Angular URL (adjust as needed)
            default => config('app.url'), // Default Livewire URL
        };
    }

    /**
     * Generate a full URL for a specific page.
     */
    public function makeUrl(string $path, array $queryParams = []): string
    {
        $baseUrl = rtrim($this->getBaseUrl(), '/');
        $path = ltrim($path, '/');
        $url = "{$baseUrl}/{$path}";

        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        return $url;
    }
}
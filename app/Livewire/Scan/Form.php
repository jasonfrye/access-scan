<?php

namespace App\Livewire\Scan;

use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

class Form extends Component
{
    #[Validate('required|url|max:2048')]
    public string $url = '';

    public bool $isLoading = false;

    public ?string $errorMessage = null;

    public ?int $scanId = null;

    protected int $maxAttempts = 5;

    public function initiateScan()
    {
        $this->validate();

        $this->errorMessage = null;
        $this->isLoading = true;

        try {
            // Check rate limit
            $rateLimitKey = 'guest-scan:' . request()->ip();
            if (!RateLimiter::attempt($rateLimitKey, 1, now()->hours(24))) {
                $this->errorMessage = 'Rate limit exceeded. You can run 1 free scan per 24 hours.';
                $this->isLoading = false;
                return;
            }

            // Validate URL
            $validation = $this->validateUrl($this->url);
            if ($validation !== true) {
                $this->errorMessage = $validation;
                $this->isLoading = false;
                return;
            }

            // Create scan via API
            $response = Http::post(route('api.scans.store'), [
                'url' => $this->url,
            ]);

            if ($response->successful()) {
                $this->scanId = $response->json()['scan_id'];
                $this->dispatch('scan-created', scanId: $this->scanId);
                $this->dispatch('scan-started', scanId: $this->scanId);
            } else {
                $this->errorMessage = $response->json()['error'] ?? 'Failed to start scan. Please try again.';
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'An error occurred. Please try again.';
        } finally {
            $this->isLoading = false;
        }
    }

    protected function validateUrl(string $url): string|true
    {
        // Check for localhost
        if (preg_match('/(localhost|127\.0\.0\.1|\.local|\.test)/i', $url)) {
            return 'Cannot scan localhost or local URLs';
        }

        // Check for IP addresses
        if (preg_match('/^https?:\/\/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $url)) {
            return 'Cannot scan IP addresses';
        }

        // Check protocol
        $scheme = parse_url($url, PHP_URL_SCHEME);
        if (!in_array($scheme, ['http', 'https'])) {
            return 'URL must use HTTP or HTTPS protocol';
        }

        return true;
    }

    public function render()
    {
        return view('livewire.scan.form');
    }
}

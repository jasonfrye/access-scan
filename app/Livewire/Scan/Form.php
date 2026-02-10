<?php

namespace App\Livewire\Scan;

use App\Jobs\RunScanJob;
use App\Models\GuestScan;
use App\Models\Scan;
use App\Services\UrlValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Form extends Component
{
    #[Validate('required|url|max:2048')]
    public string $url = '';

    public bool $isLoading = false;

    public ?string $errorMessage = null;

    public ?int $scanId = null;

    public function initiateScan(): void
    {
        $this->validate();

        $this->errorMessage = null;
        $this->isLoading = true;

        try {
            $urlValidator = app(UrlValidator::class);
            $validation = $urlValidator->validateForScanning($this->url);
            if ($validation !== true) {
                $this->errorMessage = $validation;
                $this->isLoading = false;

                return;
            }

            $rateLimitKey = 'guest-scan:'.request()->ip();
            if (RateLimiter::tooManyAttempts($rateLimitKey, 1)) {
                $this->errorMessage = 'Rate limit exceeded. You can run 1 free scan per 24 hours.';
                $this->isLoading = false;

                return;
            }

            RateLimiter::hit($rateLimitKey, 60 * 60 * 24);

            $scan = Scan::create([
                'user_id' => null,
                'url' => $this->url,
                'status' => Scan::STATUS_PENDING,
                'scan_type' => Scan::TYPE_QUICK,
            ]);

            GuestScan::create([
                'ip_address' => request()->ip(),
                'scan_id' => $scan->id,
            ]);

            dispatch(new RunScanJob($scan));

            session()->put('guest_scan_id', $scan->id);

            Log::info('Guest scan initiated', [
                'scan_id' => $scan->id,
                'ip' => request()->ip(),
                'url' => $this->url,
            ]);

            $this->scanId = $scan->id;
            $this->dispatch('scan-created', scanId: $this->scanId);
            $this->dispatch('scan-started', scanId: $this->scanId);

            $this->redirect(route('scan.pending', $scan));
        } catch (\Exception $e) {
            Log::error('Failed to initiate scan from form', ['error' => $e->getMessage()]);
            $this->errorMessage = 'An error occurred. Please try again.';
        } finally {
            $this->isLoading = false;
        }
    }

    public function render()
    {
        return view('livewire.scan.form');
    }
}

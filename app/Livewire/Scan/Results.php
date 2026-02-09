<?php

namespace App\Livewire\Scan;

use App\Models\Scan;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;

class Results extends Component
{
    public ?int $scanId = null;

    public ?Scan $scan = null;

    public bool $showEmailForm = true;

    public ?string $email = null;

    public function mount(?int $scanId = null)
    {
        $this->scanId = $scanId;
        if ($this->scanId) {
            $this->loadScan();
        }
    }

    #[On('scan-started')]
    public function loadScanOnStart($scanId)
    {
        $this->scanId = $scanId;
        $this->loadScan();
    }

    public function loadScan()
    {
        $this->scan = Scan::find($this->scanId);
    }

    #[Computed]
    public function scoreColor(): string
    {
        if (!$this->scan || $this->scan->score === null) {
            return 'text-gray-500';
        }

        return match (true) {
            $this->scan->score >= 90 => 'text-green-600',
            $this->scan->score >= 70 => 'text-yellow-600',
            $this->scan->score >= 50 => 'text-orange-600',
            default => 'text-red-600',
        };
    }

    #[Computed]
    public function gradeColor(): string
    {
        if (!$this->scan || !$this->scan->grade) {
            return 'text-gray-500';
        }

        return match ($this->scan->grade) {
            'A' => 'text-green-600',
            'B' => 'text-green-500',
            'C' => 'text-yellow-600',
            'D' => 'text-orange-500',
            'F' => 'text-red-600',
            default => 'text-gray-500',
        };
    }

    #[Computed]
    public function isComplete(): bool
    {
        return $this->scan && $this->scan->isCompleted();
    }

    #[Computed]
    public function progress(): int
    {
        if (!$this->scan) {
            return 0;
        }

        return match ($this->scan->status) {
            'pending' => 10,
            'running' => 50,
            'completed' => 100,
            'failed' => 100,
            default => 0,
        };
    }

    public function render()
    {
        return view('livewire.scan.results');
    }
}

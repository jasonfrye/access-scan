<?php

namespace App\Livewire\Scan;

use App\Models\Scan;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.guest')]
class Index extends Component
{
    #[Url]
    public ?int $scanId = null;

    public ?Scan $scan = null;

    public function mount()
    {
        if ($this->scanId) {
            $this->loadScan();
        }
    }

    public function loadScan()
    {
        $this->scan = Scan::where('id', $this->scanId)
            ->where('status', Scan::STATUS_COMPLETED)
            ->first();
    }

    #[On('scan-created')]
    public function updatedScanId(int $scanId)
    {
        $this->scanId = $scanId;
        $this->loadScan();
    }

    public function render()
    {
        return view('livewire.scan.index');
    }
}

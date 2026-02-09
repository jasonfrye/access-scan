<?php

namespace App\Livewire\Scan;

use App\Models\Scan;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

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
            ->where('is_completed', true)
            ->first();
    }

    #[On('scan-created')]
    public function updatedScanId($id)
    {
        $this->scanId = $id;
        $this->loadScan();
    }

    public function render()
    {
        return view('livewire.scan.index');
    }
}

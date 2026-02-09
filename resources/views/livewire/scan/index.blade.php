<div>
    @if($scan && $scan->isCompleted())
        @livewire('scan.results', ['scanId' => $scan->id])
    @else
        @livewire('scan.form')
    @endif
</div>

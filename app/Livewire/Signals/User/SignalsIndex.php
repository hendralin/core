<?php

namespace App\Livewire\Signals\User;

use App\Models\StockSignal;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class SignalsIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public bool $hasActiveSubscription = false;
    public $subscription;

    public string $search = '';
    public string $signalType = '';
    public int $perPage = 10;
    public string $sortField = 'published_at';
    public string $sortDirection = 'desc';

    public function mount(): void
    {
        $user = Auth::user();

        if ($user) {
            $this->subscription = $user->subscription;
            $this->hasActiveSubscription = $user->hasActiveSubscription();
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSignalType(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->signalType = '';
        $this->perPage = 10;
        $this->sortField = 'published_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    private function getSignalsQuery()
    {
        return StockSignal::query()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->when($this->search, function ($query) {
                $search = trim($this->search);
                $query->where('kode_emiten', 'like', '%' . $search . '%');
            })
            ->when($this->signalType, fn ($q) => $q->where('signal_type', $this->signalType))
            ->with(['stockCompany'])
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        $signals = null;
        $signalTypes = collect();

        if ($this->hasActiveSubscription) {
            $signals = $this->getSignalsQuery()->paginate($this->perPage);
            $signalTypes = StockSignal::query()
                ->published()
                ->distinct('signal_type')
                ->pluck('signal_type');
        }

        return view('livewire.signals.user.signals-index', [
            'hasActiveSubscription' => $this->hasActiveSubscription,
            'subscription' => $this->subscription,
            'signals' => $signals,
            'signalTypes' => $signalTypes,
        ])->layout('layouts.app');
    }
}

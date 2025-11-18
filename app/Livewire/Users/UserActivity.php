<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use App\Models\Activity;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class UserActivity extends Component
{
    use WithPagination, WithoutUrlPagination;

    public User $user;
    public $perPage = 10;
    public $search = '';
    public $logFilter = '';
    public $dateFilter = '7';

    protected $queryString = [
        'search' => ['except' => ''],
        'logFilter' => ['except' => ''],
        'dateFilter' => ['except' => '7'],
    ];

    public function mount(User $user)
    {
        $this->user = $user;
    }

    public function clearFilters()
    {
        $this->reset(['search', 'logFilter', 'dateFilter']);
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingLogFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->forUser($this->user->id)
            ->when($this->search, function ($query) {
                $query->where('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->logFilter, function ($query) {
                $query->byLogName($this->logFilter);
            })
            ->when($this->dateFilter, function ($query) {
                $days = $this->dateFilter === 'all' ? null : (int) $this->dateFilter;
                if ($days) {
                    $query->recent($days);
                }
            })
            ->with('causer')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $logTypes = Activity::forUser($this->user->id)
            ->select('log_name')
            ->distinct()
            ->pluck('log_name')
            ->filter()
            ->values();

        return view('livewire.users.user-activity', compact('activities', 'logTypes'));
    }
}

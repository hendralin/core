<?php

namespace App\Livewire\Schedules;

use App\Models\Session;
use Livewire\Component;
use App\Models\Schedule;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\Traits\HasWahaConfig;

#[Title('Schedules')]
class SchedulesIndex extends Component
{
    use WithPagination, WithoutUrlPagination, HasWahaConfig;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'selectedSession' => ['except' => ''],
        'frequencyFilter' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public $search = '';
    public $statusFilter = '';
    public $selectedSession = '';
    public $frequencyFilter = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;

    public $scheduleToDelete;
    public $sessions = [];

    public function mount()
    {
        // Only show sessions created by current user
        $this->sessions = Session::where('created_by', Auth::id())->get();
    }

    public function updatedStatusFilter()
    {
        $this->clearPage();
    }

    public function updatedSelectedSession()
    {
        $this->clearPage();
    }

    public function updatedFrequencyFilter()
    {
        $this->clearPage();
    }

    public function updatingSearch()
    {
        $this->clearPage();
    }

    public function updatingPerPage()
    {
        $this->clearPage();
    }

    public function gotoPage($page)
    {
        $this->setPage($page);
    }

    public function clearPage()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'statusFilter',
            'selectedSession',
            'frequencyFilter'
        ]);

        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function setScheduleToDelete($id)
    {
        $this->scheduleToDelete = $id;
    }

    public function delete()
    {
        $this->authorize('schedule.delete');

        try {
            $schedule = Schedule::where('created_by', Auth::id())->find($this->scheduleToDelete);

            if (!$schedule) {
                session()->flash('error', 'Schedule not found, already deleted, or you do not have permission to delete this schedule.');
                $this->scheduleToDelete = null;
                return;
            }

            DB::transaction(function () use ($schedule) {
                // Log activity before deletion
                activity()
                    ->performedOn($schedule)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'ip' => Request::ip(),
                        'user_agent' => Request::userAgent(),
                        'deleted_schedule_data' => [
                            'name' => $schedule->name,
                            'description' => $schedule->description,
                            'frequency' => $schedule->frequency,
                            'is_active' => $schedule->is_active,
                            'usage_count' => $schedule->usage_count,
                        ]
                    ])
                    ->log('deleted schedule');

                $schedule->delete();
            });

            session()->flash('success', 'Schedule deleted.');
            $this->scheduleToDelete = null;
        } catch (\Throwable $e) {
            if ($e instanceof \PDOException && isset($e->errorInfo[0]) && $e->errorInfo[0] == 23000) {
                session()->flash('error', "The {$schedule->name} cannot be deleted because it is already in use.");
            } else {
                session()->flash('error', $e->getMessage());
            }
        }
    }

    public function toggleActive($id)
    {
        $this->authorize('schedule.edit');

        try {
            $schedule = Schedule::where('created_by', Auth::id())->find($id);

            if (!$schedule) {
                session()->flash('error', 'Schedule not found.');
                return;
            }

            $schedule->is_active = !$schedule->is_active;
            $schedule->save();

            activity()
                ->performedOn($schedule)
                ->causedBy(Auth::user())
                ->withProperties([
                    'attributes' => [
                        'is_active' => $schedule->is_active,
                    ],
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ])
                ->log($schedule->is_active ? 'activated schedule' : 'deactivated schedule');

            session()->flash('success', $schedule->is_active ? 'Schedule activated.' : 'Schedule deactivated.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        if (!$this->isWahaConfigured()) {
            return view('livewire.schedules.schedules-index', [
                'schedules' => collect(),
                'wahaConfigured' => false,
                'sessions' => $this->sessions,
            ]);
        }

        $schedules = Schedule::with(['createdBy', 'wahaSession', 'group', 'contact'])
            ->where('created_by', Auth::id()) // Only show schedules created by current user
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('description', 'like', '%' . $this->search . '%')
                          ->orWhere('message', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter !== '', fn($q) => $q->where('is_active', $this->statusFilter === 'active' ? 1 : 0))
            ->when($this->selectedSession !== '', fn($q) => $q->where('waha_session_id', $this->selectedSession))
            ->when($this->frequencyFilter !== '', fn($q) => $q->where('frequency', $this->frequencyFilter))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.schedules.schedules-index', [
            'schedules' => $schedules,
            'wahaConfigured' => true,
            'sessions' => $this->sessions,
        ]);
    }
}

<?php

namespace App\Livewire\Schedules;

use App\Models\Session;
use Livewire\Component;
use App\Models\Activity;
use App\Models\Schedule;
use App\Traits\HasWahaConfig;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Auth;

#[Title('Schedule Audit Trail')]
class SchedulesAudit extends Component
{
    use WithPagination, WithoutUrlPagination, HasWahaConfig;

    public $search = '';
    public $perPage = 10;
    public $selectedSchedule = null;
    public $selectedSession = null;
    public $schedules = [];
    public $sessions = [];

    public function mount()
    {
        if (!$this->isWahaConfigured()) {
            session()->flash('error', 'WAHA belum dikonfigurasi. Silakan konfigurasi WAHA terlebih dahulu.');
            return $this->redirect(route('schedules.index'), true);
        }

        // Only show schedules and sessions created by current user
        $this->schedules = Schedule::where('created_by', Auth::id())->get();
        $this->sessions = Session::where('created_by', Auth::id())->get();
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'selectedSchedule', 'selectedSession'])) {
            $this->resetPage();
        }
    }

    public function updatedSelectedSchedule()
    {
        $this->resetPage();
    }

    public function updatedSelectedSession()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedSchedule', 'selectedSession']);
        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->with(['causer', 'subject.wahaSession'])
            ->where('subject_type', Schedule::class)
            ->whereRaw("EXISTS (SELECT 1 FROM schedules WHERE schedules.id = activity_log.subject_id AND schedules.created_by = ?)", [Auth::id()])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            $subQuery->whereRaw("EXISTS (SELECT 1 FROM schedules WHERE schedules.id = activity_log.subject_id AND schedules.name LIKE ?)", ['%' . $this->search . '%']);
                        })
                        ->orWhere(function ($sessionQuery) {
                            $sessionQuery->whereRaw("EXISTS (SELECT 1 FROM schedules s JOIN waha_sessions ws ON s.waha_session_id = ws.id WHERE s.id = activity_log.subject_id AND ws.name LIKE ?)", ['%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedSchedule, function ($query) {
                $query->where('subject_id', $this->selectedSchedule);
            })
            ->when($this->selectedSession, function ($query) {
                $query->whereRaw("EXISTS (SELECT 1 FROM schedules WHERE schedules.id = activity_log.subject_id AND schedules.waha_session_id = ?)", [$this->selectedSession]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get statistics - only for schedules created by current user
        $stats = [
            'total_activities' => Activity::where('subject_type', Schedule::class)
                ->whereRaw("EXISTS (SELECT 1 FROM schedules WHERE schedules.id = activity_log.subject_id AND schedules.created_by = ?)", [Auth::id()])
                ->count(),
            'today_activities' => Activity::where('subject_type', Schedule::class)
                ->whereRaw("EXISTS (SELECT 1 FROM schedules WHERE schedules.id = activity_log.subject_id AND schedules.created_by = ?)", [Auth::id()])
                ->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', Schedule::class)
                ->whereRaw("EXISTS (SELECT 1 FROM schedules WHERE schedules.id = activity_log.subject_id AND schedules.created_by = ?)", [Auth::id()])
                ->where('description', 'created a new schedule')->count(),
            'updated_count' => Activity::where('subject_type', Schedule::class)
                ->whereRaw("EXISTS (SELECT 1 FROM schedules WHERE schedules.id = activity_log.subject_id AND schedules.created_by = ?)", [Auth::id()])
                ->where('description', 'updated schedule information')->count(),
            'deleted_count' => Activity::where('subject_type', Schedule::class)
                ->whereRaw("EXISTS (SELECT 1 FROM schedules WHERE schedules.id = activity_log.subject_id AND schedules.created_by = ?)", [Auth::id()])
                ->where('description', 'deleted schedule')->count(),
        ];

        return view('livewire.schedules.schedules-audit', compact('activities', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }
}

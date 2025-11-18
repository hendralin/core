<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\Activity;
use App\Models\User;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('User Audit Trail')]
class UserAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedUser = null;
    public $users = [];

    public function mount()
    {
        $this->users = User::all();
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'selectedUser'])) {
            $this->resetPage();
        }
    }

    public function updatedSelectedUser()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedUser']);
        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->with(['causer', 'subject'])
            ->where('subject_type', User::class)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            // Since we know subject_type is always User, we can directly query the users table
                            $subQuery->whereRaw("EXISTS (SELECT 1 FROM users WHERE users.id = activity_log.subject_id AND users.name LIKE ?)", ['%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedUser, function ($query) {
                $query->where('subject_id', $this->selectedUser);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get statistics
        $stats = [
            'total_activities' => Activity::where('subject_type', User::class)->count(),
            'today_activities' => Activity::where('subject_type', User::class)
                ->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', User::class)
                ->where('description', 'created a new user account')->count(),
            'updated_count' => Activity::where('subject_type', User::class)
                ->where('description', 'updated user profile information')->count(),
            'deleted_count' => Activity::where('subject_type', User::class)
                ->where('description', 'deleted user account')->count(),
        ];

        return view('livewire.users.user-audit', compact('activities', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }
}

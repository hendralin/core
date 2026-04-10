<?php

namespace App\Livewire\Roles;

use App\Models\Role;
use Livewire\Component;
use App\Models\Activity;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('Role Audit Trail')]
class RoleAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedRole = null;
    public $roles = [];

    public function mount()
    {
        $this->roles = Role::all();
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'selectedRole'])) {
            $this->resetPage();
        }
    }

    public function updatedSelectedRole()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedRole']);
        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->with(['causer', 'subject'])
            ->where('subject_type', Role::class)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            // Since we know subject_type is always Role, we can directly query the roles table
                            $subQuery->whereRaw("EXISTS (SELECT 1 FROM roles WHERE roles.id = activity_log.subject_id AND roles.name LIKE ?)", ['%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedRole, function ($query) {
                $query->where('subject_id', $this->selectedRole);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get statistics
        $stats = [
            'total_activities' => Activity::where('subject_type', Role::class)->count(),
            'today_activities' => Activity::where('subject_type', Role::class)
                ->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', Role::class)
                ->where('description', 'created role')->count(),
            'updated_count' => Activity::where('subject_type', Role::class)
                ->where('description', 'updated role')->count(),
            'deleted_count' => Activity::where('subject_type', Role::class)
                ->where('description', 'deleted role')->count(),
        ];

        return view('livewire.roles.role-audit', compact('activities', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }
}

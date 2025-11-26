<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Livewire\WithoutUrlPagination;
use Spatie\Permission\Models\Role;

#[Title('Users')]
class UserIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'warehouseFilter' => ['except' => ''],
        'verificationFilter' => ['except' => ''],
        'createdDateFrom' => ['except' => ''],
        'createdDateTo' => ['except' => ''],
        'loginDateFrom' => ['except' => ''],
        'loginDateTo' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public $search = '';
    public $statusFilter = '';
    public $roleFilter = '';
    public $warehouseFilter = '';
    public $verificationFilter = '';
    public $dateRangeFilter = '';
    public $createdDateFrom = '';
    public $createdDateTo = '';
    public $loginDateFrom = '';
    public $loginDateTo = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;
    public $showAdvancedFilters = false;

    public $roles;

    public $selected = [];
    public $selectAll = false;

    public function updatedSelected()
    {
        // Get ALL users that match current filters (not just current page)
        $allMatchingUsers = User::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->when($this->statusFilter !== '', fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->roleFilter, function ($q) {
                $q->whereHas('roles', function ($query) {
                    $query->where('id', $this->roleFilter);
                });
            })
            ->with('roles')
            ->orderBy($this->sortField, $this->sortDirection)
            ->whereDoesntHave('roles', function ($q) {
                $q->whereIn('name', ['salesman', 'customer', 'supplier', 'cashier']);
            })
            ->pluck('id')
            ->toArray();

        // Check if all matching users are selected
        $matchingSelected = array_intersect($this->selected, $allMatchingUsers);
        $this->selectAll = count($matchingSelected) === count($allMatchingUsers) && count($allMatchingUsers) > 0;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Select ALL users that match current filters (not just current page)
            $allMatchingUsers = User::query()
                ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                ->when($this->statusFilter !== '', fn($q) => $q->where('status', $this->statusFilter))
                ->when($this->roleFilter, function ($q) {
                    $q->whereHas('roles', function ($query) {
                        $query->where('id', $this->roleFilter);
                    });
                })
                ->with('roles')
                ->orderBy($this->sortField, $this->sortDirection)
                ->whereDoesntHave('roles', function ($q) {
                    $q->whereIn('name', ['salesman', 'customer', 'supplier', 'cashier']);
                })
                ->pluck('id')
                ->toArray();

            $this->selected = array_unique(array_merge($this->selected, $allMatchingUsers));
        } else {
            // Deselect ALL users that match current filters
            $allMatchingUsers = User::query()
                ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                ->when($this->statusFilter !== '', fn($q) => $q->where('status', $this->statusFilter))
                ->when($this->roleFilter, function ($q) {
                    $q->whereHas('roles', function ($query) {
                        $query->where('id', $this->roleFilter);
                    });
                })
                ->with('roles')
                ->orderBy($this->sortField, $this->sortDirection)
                ->whereDoesntHave('roles', function ($q) {
                    $q->whereIn('name', ['salesman', 'customer', 'supplier', 'cashier']);
                })
                ->pluck('id')
                ->toArray();

            $this->selected = array_diff($this->selected, $allMatchingUsers);
        }

        // Update selectAll state based on current page
        $this->updatedSelected();
    }

    public function updatedStatusFilter()
    {
        $this->clearPage();
        $this->clearSelected();
    }

    public function updatedRoleFilter()
    {
        $this->clearPage();
        $this->clearSelected();
    }

    public function updatingSearch()
    {
        $this->clearPage();
        $this->clearSelected();
    }

    public function updatingPerPage()
    {
        $this->clearPage();
        $this->clearSelected();
    }

    public function gotoPage($page)
    {
        $this->setPage($page);
        // Update selectAll state for new page
        $this->updatedSelected();
    }

    public function clearPage()
    {
        $this->resetPage();

        $this->clearSelected();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'statusFilter',
            'roleFilter',
            'warehouseFilter',
            'verificationFilter',
            'createdDateFrom',
            'createdDateTo',
            'loginDateFrom',
            'loginDateTo',
            'selected',
            'selectAll'
        ]);

        $this->resetPage();
    }

    public function clearSelected()
    {
        $this->reset(['selected', 'selectAll']);
    }

    public function toggleAdvancedFilters()
    {
        $this->showAdvancedFilters = !$this->showAdvancedFilters;
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

    public function mount()
    {
        $this->roles = Role::whereNotIn('name', ['salesman', 'customer', 'supplier', 'cashier'])->get();
    }

    public function delete($id)
    {
        try {
            $user = User::find($id);

            DB::transaction(function () use ($user) {
                // Log activity before deletion
                activity()
                    ->performedOn($user)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'ip' => Request::ip(),
                        'user_agent' => Request::userAgent(),
                        'deleted_user_data' => [
                            'name' => $user->name,
                            'email' => $user->email,
                            'roles' => $user->roles->pluck('name')->toArray(),
                        ]
                    ])
                    ->log('deleted user account');

                $user->delete();
            });

            session()->flash('success', 'User deleted.');
        } catch (\Throwable $e) {
            if ($e->errorInfo[0] == 23000) {
                session()->flash('error', "The {$user->name} cannot be deleted because it is already in use.");
            } else {
                session()->flash('error', $e->getMessage());
            }
        }
    }

    public function deleteSelected()
    {
        if (count($this->selected)) {
            $deletedCount = 0;
            foreach ($this->selected as $userId) {
                $user = User::find($userId);
                if ($user) {
                    // Log activity before deletion
                    activity()
                        ->performedOn($user)
                        ->causedBy(Auth::user())
                        ->withProperties([
                            'ip' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                            'deleted_user_data' => [
                                'name' => $user->name,
                                'email' => $user->email,
                                'roles' => $user->roles->pluck('name')->toArray(),
                            ]
                        ])
                        ->log('deleted user account (bulk)');

                    $user->delete();
                    $deletedCount++;
                }
            }

            session()->flash('success', $deletedCount . ' Users deleted.');
        }

        $this->reset(['selected', 'selectAll']);
    }

    public function bulkStatusChange($status)
    {
        if (!count($this->selected)) {
            session()->flash('error', 'Please select users to update.');
            return;
        }

        // Validate status enum values (0=inactive, 1=active, 2=pending)
        $validStatuses = [0, 1, 2];
        if (!in_array((int)$status, $validStatuses)) {
            session()->flash('error', 'Invalid status value provided.');
            return;
        }

        try {
            $updatedCount = 0;
            $statusText = match((int)$status) {
                0 => 'deactivated',
                1 => 'activated',
                2 => 'set to pending',
                default => 'updated'
            };

            DB::transaction(function () use ($status, &$updatedCount, &$statusText) {
                foreach ($this->selected as $userId) {
                    $user = User::find($userId);
                    if ($user) {
                        // Use fill() and save() to properly handle enum casting
                        $user->fill(['status' => (int)$status]);
                        $user->save();

                        // Log activity
                        activity()
                            ->performedOn($user)
                            ->causedBy(Auth::user())
                            ->withProperties([
                                'ip' => Request::ip(),
                                'user_agent' => Request::userAgent(),
                                'old_status' => $user->getOriginal('status'),
                                'new_status' => (int)$status,
                            ])
                            ->log("user account {$statusText}");

                        $updatedCount++;
                    }
                }
            });

            session()->flash('success', "{$updatedCount} users {$statusText} successfully.");
            $this->reset(['selected', 'selectAll']);

        } catch (\Exception $e) {
            \Log::error('bulkStatusChange error: ' . $e->getMessage());
            session()->flash('error', 'Failed to update user status: ' . $e->getMessage());
        }
    }

    public function exportSelected()
    {
        if (!count($this->selected)) {
            session()->flash('error', 'Please select users to export.');
            return;
        }

        try {
            // Get selected users with relationships
            $users = User::with(['roles', 'warehouses'])
                ->whereIn('id', $this->selected)
                ->get();

            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->withProperties([
                    'ip' => Request::ip(),
                    'user_agent' => Request::userAgent(),
                    'exported_user_count' => count($users),
                    'exported_user_ids' => $this->selected,
                ])
                ->log('exported selected users data');

            // Generate CSV content
            $csvContent = $this->generateUserCsv($users);
            $filename = 'users_export_' . now()->format('Y-m-d_H-i-s') . '.csv';

            // Store temporary file
            $tempPath = 'temp/' . $filename;
            \Illuminate\Support\Facades\Storage::disk('local')->put($tempPath, $csvContent);

            // Return download URL
            $downloadUrl = route('users.download', ['filename' => $filename]);

            $this->dispatch('download-file', url: $downloadUrl);

            session()->flash('success', 'Export completed. Download will start automatically.');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to export users: ' . $e->getMessage());
        }
    }

    private function generateUserCsv($users)
    {
        $headers = [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Birth Date',
            'Address',
            'Status',
            'Email Verified',
            'Roles',
            'Warehouses',
            'Joined Date',
            'Last Login',
            'Timezone'
        ];

        $csv = implode(',', array_map(function($header) {
            return '"' . str_replace('"', '""', $header) . '"';
        }, $headers)) . "\n";

        foreach ($users as $user) {
            $row = [
                $user->id,
                $user->name,
                $user->email,
                $user->phone ?? '',
                $user->birth_date ? $user->birth_date->format('Y-m-d') : '',
                $user->address ?? '',
                $user->status == 1 ? 'Active' : ($user->status == 0 ? 'Inactive' : 'Pending'),
                $user->is_email_verified ? 'Yes' : 'No',
                $user->roles->pluck('name')->implode('; '),
                $user->warehouses->pluck('name')->implode('; '),
                $user->created_at->format('Y-m-d H:i:s'),
                $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : '',
                $user->timezone
            ];

            $csv .= implode(',', array_map(function($field) {
                return '"' . str_replace('"', '""', $field) . '"';
            }, $row)) . "\n";
        }

        return $csv;
    }

    public function render()
    {
        $users = User::with(['roles', 'warehouses']) // Eager load relationships
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('email', 'like', '%' . $this->search . '%')
                          ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter !== '', fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->roleFilter, function ($q) {
                $q->whereHas('roles', function ($query) {
                    $query->where('id', $this->roleFilter);
                });
            })
            ->when($this->warehouseFilter, function ($q) {
                $q->whereHas('warehouses', function ($query) {
                    $query->where('id', $this->warehouseFilter);
                });
            })
            ->when($this->verificationFilter !== '', function ($q) {
                if ($this->verificationFilter === 'verified') {
                    $q->where('is_email_verified', true);
                } elseif ($this->verificationFilter === 'unverified') {
                    $q->where('is_email_verified', false);
                }
            })
            ->when($this->createdDateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->createdDateFrom))
            ->when($this->createdDateTo, fn($q) => $q->whereDate('created_at', '<=', $this->createdDateTo))
            ->when($this->loginDateFrom, fn($q) => $q->whereDate('last_login_at', '>=', $this->loginDateFrom))
            ->when($this->loginDateTo, fn($q) => $q->whereDate('last_login_at', '<=', $this->loginDateTo))
            ->whereDoesntHave('roles', function ($q) {
                $q->whereIn('name', ['salesman', 'customer', 'supplier', 'cashier']);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        // Get warehouses for filter dropdown
        $warehouses = \App\Models\Warehouse::all();

        return view('livewire.users.user-index', compact('users', 'warehouses'));
    }
}

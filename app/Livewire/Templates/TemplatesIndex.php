<?php

namespace App\Livewire\Templates;

use App\Models\Session;
use Livewire\Component;
use App\Models\Template;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\Traits\HasWahaConfig;

#[Title('Templates')]
class TemplatesIndex extends Component
{
    use WithPagination, WithoutUrlPagination, HasWahaConfig;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'selectedSession' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public $search = '';
    public $statusFilter = '';
    public $selectedSession = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;

    public $templateToDelete;
    public $templateToPreview;
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
            'selectedSession'
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

    public function setTemplateToDelete($id)
    {
        $this->templateToDelete = $id;
    }

    public function setTemplateToPreview($id)
    {
        $this->templateToPreview = Template::where('created_by', Auth::id())
            ->with(['createdBy', 'updatedBy'])
            ->find($id);
    }

    public function delete()
    {
        $this->authorize('template.delete');

        try {
            $template = Template::where('created_by', Auth::id())->find($this->templateToDelete);

            if (!$template) {
                session()->flash('error', 'Template not found, already deleted, or you do not have permission to delete this template.');
                $this->templateToDelete = null;
                return;
            }

            DB::transaction(function () use ($template) {
                // Log activity before deletion
                activity()
                    ->performedOn($template)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'ip' => Request::ip(),
                        'user_agent' => Request::userAgent(),
                        'deleted_template_data' => [
                            'name' => $template->name,
                            'header' => $template->header,
                            'body' => $template->body,
                            'usage_count' => $template->usage_count,
                            'is_active' => $template->is_active,
                        ]
                    ])
                    ->log('deleted template');

                $template->delete();
            });

            session()->flash('success', 'Template deleted.');
            $this->templateToDelete = null;
        } catch (\Throwable $e) {
            if ($e instanceof \PDOException && isset($e->errorInfo[0]) && $e->errorInfo[0] == 23000) {
                session()->flash('error', "The {$template->name} cannot be deleted because it is already in use.");
            } else {
                session()->flash('error', $e->getMessage());
            }
        }
    }

    public function render()
    {
        if (!$this->isWahaConfigured()) {
            return view('livewire.templates.templates-index', [
                'templates' => collect(),
                'wahaConfigured' => false,
                'sessions' => $this->sessions,
            ]);
        }

        $templates = Template::with(['createdBy', 'updatedBy', 'wahaSession'])
            ->where('created_by', Auth::id()) // Only show templates created by current user
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('header', 'like', '%' . $this->search . '%')
                          ->orWhere('body', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter !== '', fn($q) => $q->where('is_active', $this->statusFilter === 'active' ? 1 : 0))
            ->when($this->selectedSession !== '', fn($q) => $q->where('waha_session_id', $this->selectedSession))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.templates.templates-index', [
            'templates' => $templates,
            'wahaConfigured' => true,
            'sessions' => $this->sessions,
        ]);
    }
}

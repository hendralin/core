<?php

namespace App\Livewire\Broadcast\Messages;

use App\Models\Message;
use App\Models\Session;
use Livewire\Component;
use App\Models\Activity;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Auth;

#[Title('Message Audit Trail')]
class MesssagesAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedSession = null;
    public $sessions = [];

    public function mount()
    {
        // Only show sessions created by current user
        $this->sessions = Session::where('created_by', Auth::id())->get();
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'selectedSession'])) {
            $this->resetPage();
        }
    }

    public function updatedSelectedSession()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedSession']);
        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->with(['causer', 'subject.wahaSession', 'subject.template', 'subject.contact', 'subject.group'])
            ->where('subject_type', Message::class)
            ->whereHas('subject', function ($query) {
                $query->where('created_by', Auth::id());
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            // Search in message content and recipient info
                            $subQuery->whereRaw("EXISTS (SELECT 1 FROM messages WHERE messages.id = activity_log.subject_id AND (messages.message LIKE ? OR messages.received_number LIKE ?))", ['%' . $this->search . '%', '%' . $this->search . '%']);
                        })
                        ->orWhere(function ($sessionQuery) {
                            // Search in session names
                            $sessionQuery->whereRaw("EXISTS (SELECT 1 FROM messages m JOIN waha_sessions ws ON m.waha_session_id = ws.id WHERE m.id = activity_log.subject_id AND ws.name LIKE ?)", ['%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedSession, function ($query) {
                $query->whereRaw("EXISTS (SELECT 1 FROM messages WHERE messages.id = activity_log.subject_id AND messages.waha_session_id = ?)", [$this->selectedSession]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get statistics - only for messages created by current user
        $stats = [
            'total_activities' => Activity::where('subject_type', Message::class)
                ->whereHas('subject', function ($query) {
                    $query->where('created_by', Auth::id());
                })->count(),
            'today_activities' => Activity::where('subject_type', Message::class)
                ->whereHas('subject', function ($query) {
                    $query->where('created_by', Auth::id());
                })
                ->whereDate('created_at', today())->count(),
            'sent_count' => Activity::where('subject_type', Message::class)
                ->whereHas('subject', function ($query) {
                    $query->where('created_by', Auth::id());
                })
                ->where('description', 'sent a message')->count(),
            'failed_count' => Activity::where('subject_type', Message::class)
                ->whereHas('subject', function ($query) {
                    $query->where('created_by', Auth::id());
                })
                ->where(function ($query) {
                    $query->where('description', 'like', '%failed%');
                })->count(),
            'resent_count' => Activity::where('subject_type', Message::class)
                ->whereHas('subject', function ($query) {
                    $query->where('created_by', Auth::id());
                })
                ->where('description', 'resent a message')->count(),
        ];

        return view('livewire.broadcast.messages.messsages-audit', compact('activities', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }
}

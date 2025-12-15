<?php

namespace App\Livewire\Broadcast\Messages;

use Livewire\Component;
use App\Models\Activity;
use App\Models\Message;
use App\Models\Session;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

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
        $this->sessions = Session::all();
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
            ->with(['causer', 'subject.wahaSession'])
            ->where('subject_type', Message::class)
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

        // Get statistics
        $stats = [
            'total_activities' => Activity::where('subject_type', Message::class)->count(),
            'today_activities' => Activity::where('subject_type', Message::class)
                ->whereDate('created_at', today())->count(),
            'sent_count' => Activity::where('subject_type', Message::class)
                ->where('description', 'sent a message')->count(),
            'failed_count' => Activity::where('subject_type', Message::class)
                ->where('description', 'like', '%failed%')->count(),
        ];

        return view('livewire.broadcast.messages.messsages-audit', compact('activities', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }
}

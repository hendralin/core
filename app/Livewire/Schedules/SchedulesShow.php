<?php

namespace App\Livewire\Schedules;

use Livewire\Component;
use App\Models\Schedule;
use App\Traits\HasWahaConfig;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Show Schedule')]
class SchedulesShow extends Component
{
    use HasWahaConfig;

    public Schedule $schedule;

    public function mount(Schedule $schedule)
    {
        if (!$this->isWahaConfigured()) {
            session()->flash('error', 'WAHA belum dikonfigurasi. Silakan konfigurasi WAHA terlebih dahulu.');
            return $this->redirect(route('schedules.index'), true);
        }

        // Check if schedule belongs to current user
        if ($schedule->created_by !== Auth::id()) {
            abort(403, 'You do not have permission to view this schedule.');
        }

        $this->schedule = $schedule->load(['createdBy', 'wahaSession', 'group', 'contact', 'recipients.contact', 'recipients.group']);
    }

    public function render()
    {
        return view('livewire.schedules.schedules-show');
    }
}

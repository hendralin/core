<?php

namespace App\Livewire\Schedules;

use Carbon\Carbon;
use App\Models\Session;
use App\Models\Group;
use App\Models\Contact;
use Livewire\Component;
use App\Models\Schedule;
use App\Traits\HasWahaConfig;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Edit Schedule')]
class SchedulesEdit extends Component
{
    use HasWahaConfig;

    public Schedule $schedule;

    public $waha_session_id, $name, $description, $message;
    public $recipientType = 'contact';
    public $contact_id, $group_id, $wa_id;
    public $frequency = 'daily';
    public $time;
    public $day_of_week, $day_of_month;
    public $is_active;

    public $sessions = [];
    public $groups = [];
    public $contacts = [];
    public $userTimezone;

    public function mount(Schedule $schedule)
    {
        if (!$this->isWahaConfigured()) {
            session()->flash('error', 'WAHA belum dikonfigurasi. Silakan konfigurasi WAHA terlebih dahulu.');
            return $this->redirect(route('schedules.index'), true);
        }

        $this->authorize('schedule.edit');

        // Check if schedule belongs to current user
        if ($schedule->created_by !== Auth::id()) {
            abort(403, 'You do not have permission to edit this schedule.');
        }

        $this->schedule = $schedule;
        $this->waha_session_id = $schedule->waha_session_id;
        $this->name = $schedule->name;
        $this->description = $schedule->description;
        $this->message = $schedule->message;
        $this->frequency = $schedule->frequency;
        $this->time = $schedule->time ? $schedule->time->format('H:i') : '09:00';
        $this->day_of_week = $schedule->day_of_week;
        $this->day_of_month = $schedule->day_of_month;
        $this->is_active = $schedule->is_active;

        // Determine recipient type - prioritize group_wa_id and received_number
        if ($schedule->group_wa_id) {
            $this->recipientType = 'group';
            // Try to find group by group_wa_id
            $group = Group::where('group_wa_id', $schedule->group_wa_id)->first();
            $this->group_id = $group ? $group->id : null;
        } elseif ($schedule->received_number) {
            // Check if received_number matches a contact
            $contact = Contact::where('wa_id', $schedule->received_number)->first();
            if ($contact) {
                $this->recipientType = 'contact';
                $this->contact_id = $contact->id;
            } else {
                $this->recipientType = 'number';
                // Extract original number from received_number (remove @s.whatsapp.net if exists)
                $this->wa_id = preg_replace('/@.+$/', '', $schedule->received_number);
            }
        } elseif ($schedule->wa_id) {
            $this->recipientType = 'number';
            // Extract original number from wa_id (remove @s.whatsapp.net if exists)
            $this->wa_id = preg_replace('/@.+$/', '', $schedule->wa_id);
        }

        $this->sessions = Session::where('created_by', Auth::id())->get();
        $this->loadRecipients();

        // Get user timezone for display
        $this->userTimezone = Auth::user()->timezone ?? config('app.timezone', 'UTC');
    }

    public function updatedWahaSessionId()
    {
        $this->loadRecipients();
        $this->contact_id = null;
        $this->group_id = null;
    }

    public function updatedRecipientType()
    {
        $this->contact_id = null;
        $this->group_id = null;
        $this->wa_id = null;
    }

    public function updatedFrequency()
    {
        if ($this->frequency === 'daily') {
            $this->day_of_week = null;
            $this->day_of_month = null;
        } elseif ($this->frequency === 'weekly') {
            $this->day_of_week = $this->day_of_week ?? 1;
            $this->day_of_month = null;
        } elseif ($this->frequency === 'monthly') {
            $this->day_of_week = null;
            $this->day_of_month = $this->day_of_month ?? 1;
        }
    }

    private function loadRecipients()
    {
        if ($this->waha_session_id) {
            $this->groups = Group::where('waha_session_id', $this->waha_session_id)
                ->forUser(Auth::id())
                ->get();
            $this->contacts = Contact::where('waha_session_id', $this->waha_session_id)
                ->forUser(Auth::id())
                ->orderBy('name')
                ->get();
        } else {
            $this->groups = [];
            $this->contacts = [];
        }
    }

    public function submit()
    {
        $rules = [
            'waha_session_id' => ['required', 'exists:waha_sessions,id', function ($attribute, $value, $fail) {
                $session = Session::where('created_by', Auth::id())->find($value);
                if (!$session) {
                    $fail('Selected session does not exist or you do not have permission to use this session.');
                }
            }],
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'message' => 'required|string|max:4096',
            'recipientType' => 'required|in:contact,group,number',
            'frequency' => 'required|in:daily,weekly,monthly',
            'time' => 'required|date_format:H:i',
            'is_active' => 'boolean',
        ];

        // Recipient validation
        if ($this->recipientType === 'contact') {
            $rules['contact_id'] = ['required', 'exists:contacts,id', function ($attribute, $value, $fail) {
                $contact = Contact::where('waha_session_id', $this->waha_session_id)
                    ->forUser(Auth::id())
                    ->find($value);
                if (!$contact) {
                    $fail('Selected contact does not exist or does not belong to the selected session.');
                }
            }];
        } elseif ($this->recipientType === 'group') {
            $rules['group_id'] = ['required', 'exists:groups,id', function ($attribute, $value, $fail) {
                $group = Group::where('waha_session_id', $this->waha_session_id)
                    ->forUser(Auth::id())
                    ->find($value);
                if (!$group) {
                    $fail('Selected group does not exist or does not belong to the selected session.');
                }
            }];
        } elseif ($this->recipientType === 'number') {
            $rules['wa_id'] = 'required|string|max:255|regex:/^(\+?\d{1,3}[-.\s]?)?\(?\d{1,4}\)?[-.\s]?\d{1,4}[-.\s]?\d{1,9}$/';
        }

        // Frequency-specific validation
        if ($this->frequency === 'weekly') {
            $rules['day_of_week'] = 'required|integer|min:0|max:6';
        } elseif ($this->frequency === 'monthly') {
            $rules['day_of_month'] = 'required|integer|min:1|max:28';
        }

        $this->validate($rules, [
            'waha_session_id.required' => 'Please select a session.',
            'waha_session_id.exists' => 'Selected session does not exist.',
            'name.required' => 'Schedule name is required.',
            'name.string' => 'Schedule name must be text.',
            'name.max' => 'Schedule name cannot exceed 255 characters.',
            'description.required' => 'Description is required.',
            'description.string' => 'Description must be text.',
            'description.max' => 'Description cannot exceed 500 characters.',
            'message.required' => 'Message content is required.',
            'message.string' => 'Message must be text.',
            'message.max' => 'Message cannot exceed 4096 characters.',
            'recipientType.required' => 'Please select a recipient type.',
            'contact_id.required' => 'Please select a contact.',
            'contact_id.exists' => 'Selected contact does not exist.',
            'group_id.required' => 'Please select a group.',
            'group_id.exists' => 'Selected group does not exist.',
            'wa_id.required' => 'Please enter a WhatsApp number.',
            'wa_id.regex' => 'Please enter a valid phone number.',
            'frequency.required' => 'Please select a frequency.',
            'time.required' => 'Please select a time.',
            'time.date_format' => 'Please enter a valid time format (HH:mm).',
            'day_of_week.required' => 'Please select a day of week.',
            'day_of_week.integer' => 'Day of week must be a number.',
            'day_of_week.min' => 'Day of week must be between 0-6.',
            'day_of_week.max' => 'Day of week must be between 0-6.',
            'day_of_month.required' => 'Please select a day of month.',
            'day_of_month.integer' => 'Day of month must be a number.',
            'day_of_month.min' => 'Day of month must be between 1-28.',
            'day_of_month.max' => 'Day of month must be between 1-28.',
        ]);

        // Store old values for logging
        $oldValues = [
            'waha_session_id' => $this->schedule->waha_session_id,
            'name' => $this->schedule->name,
            'description' => $this->schedule->description,
            'message' => $this->schedule->message,
            'frequency' => $this->schedule->frequency,
            'time' => $this->schedule->time,
            'is_active' => $this->schedule->is_active,
        ];

        // Determine recipient values - only store wa_id, group_wa_id, and received_number
        $waId = null;
        $groupWaId = null;
        $receivedNumber = null;

        if ($this->recipientType === 'contact' && $this->contact_id) {
            $contact = Contact::find($this->contact_id);
            if ($contact) {
                // Clean wa_id format for WAHA (remove any existing suffix and add proper one)
                $cleanNumber = preg_replace('/@.+$/', '', $contact->wa_id);
                $waId = $cleanNumber . '@s.whatsapp.net';
                $receivedNumber = $contact->wa_id;
            }
        } elseif ($this->recipientType === 'group' && $this->group_id) {
            $group = Group::find($this->group_id);
            if ($group) {
                $groupWaId = $group->group_wa_id;
            }
        } elseif ($this->recipientType === 'number' && $this->wa_id) {
            // Clean contact number format (remove any non-numeric characters except +)
            $cleanNumber = preg_replace('/[^\d+]/', '', $this->wa_id);
            // Remove leading + if present for WAHA format
            $cleanNumber = ltrim($cleanNumber, '+');
            $waId = $cleanNumber . '@s.whatsapp.net';
            $receivedNumber = $this->wa_id;
        }

        $this->schedule->waha_session_id = $this->waha_session_id;
        $this->schedule->name = $this->name;
        $this->schedule->description = $this->description;
        $this->schedule->message = $this->message;
        $this->schedule->wa_id = $waId;
        $this->schedule->group_wa_id = $groupWaId;
        $this->schedule->received_number = $receivedNumber;
        $this->schedule->frequency = $this->frequency;
        $this->schedule->time = $this->time;
        $this->schedule->day_of_week = $this->frequency === 'weekly' ? $this->day_of_week : null;
        $this->schedule->day_of_month = $this->frequency === 'monthly' ? $this->day_of_month : null;
        $this->schedule->is_active = $this->is_active;

        // Recalculate next_run if frequency or time changed
        if ($oldValues['frequency'] !== $this->frequency ||
            $oldValues['time']?->format('H:i') !== $this->time ||
            ($this->frequency === 'weekly' && $this->schedule->day_of_week !== $this->day_of_week) ||
            ($this->frequency === 'monthly' && $this->schedule->day_of_month !== $this->day_of_month)) {
            $this->schedule->next_run = $this->schedule->calculateNextRun();
        }

        $this->schedule->save();

        // Log activity
        activity()
            ->performedOn($this->schedule)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldValues,
                'attributes' => [
                    'waha_session_id' => $this->waha_session_id,
                    'name' => $this->name,
                    'description' => $this->description,
                    'frequency' => $this->frequency,
                    'time' => $this->time,
                    'is_active' => $this->is_active,
                ],
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('updated schedule information');

        session()->flash('success', 'Schedule updated successfully.');

        return $this->redirect('/schedules', true);
    }

    public function render()
    {
        return view('livewire.schedules.schedules-edit', [
            'sessions' => $this->sessions,
            'groups' => $this->groups,
            'contacts' => $this->contacts,
        ]);
    }
}

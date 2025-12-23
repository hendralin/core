<?php

namespace App\Livewire\Schedules;

use Carbon\Carbon;
use App\Models\Session;
use App\Models\Group;
use App\Models\Contact;
use Livewire\Component;
use App\Models\Schedule;
use App\Models\ScheduleRecipient;
use App\Traits\HasWahaConfig;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Create Schedule')]
class SchedulesCreate extends Component
{
    use HasWahaConfig;

    public $waha_session_id, $name, $description, $message;
    public $recipientType = 'number'; // 'contact', 'group', 'number'
    public $contact_ids = []; // Array for multiple contacts
    public $group_ids = []; // Array for multiple groups
    public $wa_ids = []; // Array for multiple phone numbers
    public $frequency = 'daily';
    public $time;
    public $day_of_week, $day_of_month;
    public $is_active = true;

    public $sessions = [];
    public $groups = [];
    public $contacts = [];
    public $userTimezone;

    public function mount()
    {
        if (!$this->isWahaConfigured()) {
            session()->flash('error', 'WAHA belum dikonfigurasi. Silakan konfigurasi WAHA terlebih dahulu.');
            return $this->redirect(route('schedules.index'), true);
        }

        $this->sessions = Session::where('created_by', Auth::id())->get();
        $this->time = '09:00';
        $this->wa_ids = ['']; // Initialize with one empty field

        // Get user timezone for display
        $this->userTimezone = Auth::user()->timezone ?? config('app.timezone', 'UTC');
    }

    public function updatedWahaSessionId()
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
        $this->contact_ids = [];
        $this->group_ids = [];
    }

    public function updatedRecipientType()
    {
        $this->contact_ids = [];
        $this->group_ids = [];
        $this->wa_ids = [];
    }

    public function addWaId()
    {
        $this->wa_ids[] = '';
    }

    public function removeWaId($index)
    {
        unset($this->wa_ids[$index]);
        $this->wa_ids = array_values($this->wa_ids); // Re-index array
    }

    public function updatedFrequency()
    {
        if ($this->frequency === 'daily') {
            $this->day_of_week = null;
            $this->day_of_month = null;
        } elseif ($this->frequency === 'weekly') {
            $this->day_of_week = $this->day_of_week ?? 1; // Monday default
            $this->day_of_month = null;
        } elseif ($this->frequency === 'monthly') {
            $this->day_of_week = null;
            $this->day_of_month = $this->day_of_month ?? 1;
        }
    }

    public function submit()
    {
        $this->authorize('schedule.create');

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

        // Recipient validation - now supports multiple recipients
        if ($this->recipientType === 'contact') {
            $rules['contact_ids'] = ['required', 'array', 'min:1'];
            $rules['contact_ids.*'] = ['required', 'exists:contacts,id', function ($attribute, $value, $fail) {
                $contact = Contact::where('waha_session_id', $this->waha_session_id)
                    ->forUser(Auth::id())
                    ->find($value);
                if (!$contact) {
                    $fail('Selected contact does not exist or does not belong to the selected session.');
                }
            }];
        } elseif ($this->recipientType === 'group') {
            $rules['group_ids'] = ['required', 'array', 'min:1'];
            $rules['group_ids.*'] = ['required', 'exists:groups,id', function ($attribute, $value, $fail) {
                $group = Group::where('waha_session_id', $this->waha_session_id)
                    ->forUser(Auth::id())
                    ->find($value);
                if (!$group) {
                    $fail('Selected group does not exist or does not belong to the selected session.');
                }
            }];
        } elseif ($this->recipientType === 'number') {
            $rules['wa_ids'] = ['required', 'array', 'min:1'];
            $rules['wa_ids.*'] = 'required|string|max:255|regex:/^(\+?\d{1,3}[-.\s]?)?\(?\d{1,4}\)?[-.\s]?\d{1,4}[-.\s]?\d{1,9}$/';
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
            'contact_ids.required' => 'Please select at least one contact.',
            'contact_ids.array' => 'Contacts must be an array.',
            'contact_ids.min' => 'Please select at least one contact.',
            'contact_ids.*.required' => 'Please select a valid contact.',
            'contact_ids.*.exists' => 'Selected contact does not exist.',
            'group_ids.required' => 'Please select at least one group.',
            'group_ids.array' => 'Groups must be an array.',
            'group_ids.min' => 'Please select at least one group.',
            'group_ids.*.required' => 'Please select a valid group.',
            'group_ids.*.exists' => 'Selected group does not exist.',
            'wa_ids.required' => 'Please enter at least one WhatsApp number.',
            'wa_ids.array' => 'Phone numbers must be an array.',
            'wa_ids.min' => 'Please enter at least one WhatsApp number.',
            'wa_ids.*.required' => 'Please enter a WhatsApp number.',
            'wa_ids.*.regex' => 'Please enter a valid phone number.',
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

        // Time is stored as-is (HH:mm format), timezone is handled in calculateNextRun
        // Create schedule (legacy fields kept for backward compatibility, but recipients stored in pivot table)
        $schedule = Schedule::create([
            'waha_session_id' => $this->waha_session_id,
            'name' => $this->name,
            'description' => $this->description,
            'message' => $this->message,
            'wa_id' => null, // Legacy field, kept for backward compatibility
            'group_wa_id' => null, // Legacy field, kept for backward compatibility
            'received_number' => null, // Legacy field, kept for backward compatibility
            'frequency' => $this->frequency,
            'time' => $this->time, // Store as HH:mm, timezone handled in calculateNextRun
            'day_of_week' => $this->frequency === 'weekly' ? $this->day_of_week : null,
            'day_of_month' => $this->frequency === 'monthly' ? $this->day_of_month : null,
            'is_active' => $this->is_active,
            'next_run' => null, // Will be calculated on first run
            'created_by' => Auth::id(),
        ]);

        // Create recipients in pivot table
        if ($this->recipientType === 'contact' && !empty($this->contact_ids)) {
            foreach ($this->contact_ids as $contactId) {
                $contact = Contact::find($contactId);
                if ($contact) {
                    // Clean wa_id format for WAHA (remove any existing suffix and add proper one)
                    $cleanNumber = preg_replace('/@.+$/', '', $contact->wa_id);
                    $waId = $cleanNumber . '@s.whatsapp.net';

                    ScheduleRecipient::create([
                        'schedule_id' => $schedule->id,
                        'recipient_type' => 'contact',
                        'contact_id' => $contact->id,
                        'wa_id' => $waId,
                        'received_number' => $contact->wa_id,
                    ]);
                }
            }
        } elseif ($this->recipientType === 'group' && !empty($this->group_ids)) {
            foreach ($this->group_ids as $groupId) {
                $group = Group::find($groupId);
                if ($group) {
                    ScheduleRecipient::create([
                        'schedule_id' => $schedule->id,
                        'recipient_type' => 'group',
                        'group_id' => $group->id,
                        'group_wa_id' => $group->group_wa_id,
                    ]);
                }
            }
        } elseif ($this->recipientType === 'number' && !empty($this->wa_ids)) {
            foreach ($this->wa_ids as $waIdInput) {
                // Clean contact number format (remove any non-numeric characters except +)
                $cleanNumber = preg_replace('/[^\d+]/', '', $waIdInput);
                // Remove leading + if present for WAHA format
                $cleanNumber = ltrim($cleanNumber, '+');
                $waId = $cleanNumber . '@s.whatsapp.net';

                ScheduleRecipient::create([
                    'schedule_id' => $schedule->id,
                    'recipient_type' => 'number',
                    'wa_id' => $waId,
                    'received_number' => $waIdInput,
                ]);
            }
        }

        // Calculate and set next_run (uses user timezone from createdBy relationship)
        $schedule->next_run = $schedule->calculateNextRun();
        $schedule->save();

        // Log activity
        activity()
            ->performedOn($schedule)
            ->causedBy(Auth::user())
            ->withProperties([
                'attributes' => [
                    'name' => $this->name,
                    'description' => $this->description,
                    'frequency' => $this->frequency,
                    'time' => $this->time,
                    'is_active' => $this->is_active,
                ],
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('created a new schedule');

        session()->flash('success', 'Schedule created successfully.');

        return $this->redirect('/schedules', true);
    }

    public function render()
    {
        return view('livewire.schedules.schedules-create', [
            'sessions' => $this->sessions,
            'groups' => $this->groups,
            'contacts' => $this->contacts,
        ]);
    }
}

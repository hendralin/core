<?php

namespace App\Livewire\Templates;

use App\Models\Session;
use Livewire\Component;
use App\Models\Template;
use App\Traits\HasWahaConfig;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Edit Template')]
class TemplatesEdit extends Component
{
    use HasWahaConfig;

    public Template $template;

    public $waha_session_id, $name, $header, $body, $is_active;

    public function mount(Template $template)
    {
        if (!$this->isWahaConfigured()) {
            session()->flash('error', 'WAHA belum dikonfigurasi. Silakan konfigurasi WAHA terlebih dahulu.');
            return $this->redirect(route('templates.index'), true);
        }

        $this->authorize('template.edit');

        // Check if template belongs to current user
        if ($template->created_by !== Auth::id()) {
            abort(403, 'You do not have permission to edit this template.');
        }

        $this->template = $template;
        $this->waha_session_id = $template->waha_session_id;
        $this->name = $template->name;
        $this->header = $template->header;
        $this->body = $template->body;
        $this->is_active = $template->is_active;
    }

    public function submit()
    {
        $this->validate([
            'waha_session_id' => ['required', 'exists:waha_sessions,id', function ($attribute, $value, $fail) {
                $session = Session::where('created_by', Auth::id())->find($value);
                if (!$session) {
                    $fail('Selected session does not exist or you do not have permission to use this session.');
                }
            }],
            'name' => 'required|string|max:255|unique:templates,name,' . $this->template->id . '|regex:/^[a-z_]+$/',
            'header' => 'nullable|string|max:60',
            'body' => 'required|string|max:1024',
            'is_active' => 'boolean',
        ], [
            'waha_session_id.required' => 'Please select a session.',
            'waha_session_id.exists' => 'Selected session does not exist.',

            'name.required' => 'Template name is required.',
            'name.string' => 'Template name must be text.',
            'name.max' => 'Template name cannot exceed 255 characters.',
            'name.unique' => 'This template name already exists.',
            'name.regex' => 'Use only lowercase letters and underscores. No spaces, numbers or special characters allowed.',

            'header.string' => 'Header must be text.',
            'header.max' => 'Header cannot exceed 60 characters.',

            'body.required' => 'Body text is required.',
            'body.string' => 'Body text must be text.',
            'body.max' => 'Body text cannot exceed 1024 characters.',

            'is_active.boolean' => 'Active status must be true or false.',
        ]);

        // Store old values for logging
        $oldValues = [
            'waha_session_id' => $this->template->waha_session_id,
            'name' => $this->template->name,
            'header' => $this->template->header,
            'body' => $this->template->body,
            'is_active' => $this->template->is_active,
        ];

        $this->template->waha_session_id = $this->waha_session_id;
        $this->template->name = $this->name;
        $this->template->header = $this->header;
        $this->template->body = $this->body;
        $this->template->is_active = $this->is_active;
        $this->template->updated_by = Auth::id();

        $this->template->save();

        // Log activity with detailed before/after information
        activity()
            ->performedOn($this->template)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldValues,
                'attributes' => [
                    'waha_session_id' => $this->waha_session_id,
                    'name' => $this->name,
                    'header' => $this->header,
                    'body' => $this->body,
                    'is_active' => $this->is_active,
                ],
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('updated template information');

        session()->flash('success', 'Template updated successfully.');

        return $this->redirect('/templates', true);
    }

    public function render()
    {
        return view('livewire.templates.templates-edit', [
            // Only show sessions created by current user
            'sessions' => Session::where('created_by', Auth::id())->get(),
        ]);
    }
}

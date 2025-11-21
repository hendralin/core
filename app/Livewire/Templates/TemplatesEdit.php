<?php

namespace App\Livewire\Templates;

use App\Models\Template;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

#[Title('Edit Template')]
class TemplatesEdit extends Component
{
    public Template $template;

    public $name, $header, $body, $is_active;

    public function mount(Template $template): void
    {
        $this->template = $template;
        $this->name = $template->name;
        $this->header = $template->header;
        $this->body = $template->body;
        $this->is_active = $template->is_active;
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:templates,name,' . $this->template->id . '|regex:/^[a-z_]+$/',
            'header' => 'nullable|string|max:60',
            'body' => 'required|string|max:1024',
            'is_active' => 'boolean',
        ], [
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
            'name' => $this->template->name,
            'header' => $this->template->header,
            'body' => $this->template->body,
            'is_active' => $this->template->is_active,
        ];

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
        return view('livewire.templates.templates-edit');
    }
}

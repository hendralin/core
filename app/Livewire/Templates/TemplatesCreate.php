<?php

namespace App\Livewire\Templates;

use App\Models\Template;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Create Template')]
class TemplatesCreate extends Component
{
    public $name, $header, $body, $is_active = true;

    public function submit()
    {
        $this->authorize('template.create');

        $this->validate([
            'name' => 'required|string|max:255|unique:templates,name|regex:/^[a-z_]+$/',
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

        $template = Template::create([
            'name' => $this->name,
            'header' => $this->header,
            'body' => $this->body,
            'is_active' => $this->is_active,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Log activity with detailed information
        activity()
            ->performedOn($template)
            ->causedBy(Auth::user())
            ->withProperties([
                'attributes' => [
                    'name' => $this->name,
                    'header' => $this->header,
                    'body' => $this->body,
                    'is_active' => $this->is_active,
                ],
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('created a new template');

        session()->flash('success', 'Template created successfully.');

        return $this->redirect('/templates', true);
    }

    public function render()
    {
        return view('livewire.templates.templates-create');
    }
}

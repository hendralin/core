<?php

namespace App\Livewire\Templates;

use Livewire\Component;
use App\Models\Template;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Show Template')]
class TemplatesShow extends Component
{
    public Template $template;

    public function mount(Template $template): void
    {
        // Check if template belongs to current user
        if ($template->created_by !== Auth::id()) {
            abort(403, 'You do not have permission to view this template.');
        }

        $this->template = $template->load(['createdBy', 'updatedBy', 'wahaSession']); // Eager load relationships
    }

    public function render()
    {
        return view('livewire.templates.templates-show');
    }
}

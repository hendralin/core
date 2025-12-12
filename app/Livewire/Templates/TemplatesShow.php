<?php

namespace App\Livewire\Templates;

use App\Models\Template;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Show Template')]
class TemplatesShow extends Component
{
    public Template $template;

    public function mount(Template $template): void
    {
        $this->template = $template->load(['createdBy', 'updatedBy', 'wahaSession']); // Eager load relationships
    }

    public function render()
    {
        return view('livewire.templates.templates-show');
    }
}

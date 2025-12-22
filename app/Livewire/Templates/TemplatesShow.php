<?php

namespace App\Livewire\Templates;

use Livewire\Component;
use App\Models\Template;
use App\Traits\HasWahaConfig;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Show Template')]
class TemplatesShow extends Component
{
    use HasWahaConfig;

    public Template $template;

    public function mount(Template $template)
    {
        if (!$this->isWahaConfigured()) {
            session()->flash('error', 'WAHA belum dikonfigurasi. Silakan konfigurasi WAHA terlebih dahulu.');
            return $this->redirect(route('templates.index'), true);
        }

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

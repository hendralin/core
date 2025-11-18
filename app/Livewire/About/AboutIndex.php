<?php

namespace App\Livewire\About;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('About Boilerplate v1.0.0')]
class AboutIndex extends Component
{
    public function render()
    {
        $systemInfo = [
            'version' => '1.0.0',
            'php_version' => PHP_VERSION,
            'laravel_version' => 'Laravel ' . app()->version(),
            'database' => config('database.default'),
            'timezone' => config('app.timezone'),
            'environment' => config('app.env'),
        ];

        return view('livewire.about.about-index', compact('systemInfo'));
    }
}

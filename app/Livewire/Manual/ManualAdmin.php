<?php

namespace App\Livewire\Manual;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Manual Administrator')]
class ManualAdmin extends Component
{
    /** @var array<int, array{id: string, text: string, level: int}> */
    public array $toc = [];

    public function mount(): void
    {
        if (! auth()->user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403);
        }

        $this->toc = $this->adminTableOfContents();
    }

    /**
     * @return array<int, array{id: string, text: string, level: int}>
     */
    protected function adminTableOfContents(): array
    {
        return [
            ['id' => 'ringkasan-peran', 'text' => __('Ringkasan peran admin'), 'level' => 2],
            ['id' => 'prasyarat', 'text' => __('Prasyarat infrastruktur'), 'level' => 2],
            ['id' => 'instalasi', 'text' => __('Instalasi aplikasi'), 'level' => 2],
            ['id' => 'waha-lingkungan', 'text' => __('Konfigurasi WAHA di lingkungan'), 'level' => 2],
            ['id' => 'queue-worker', 'text' => __('Queue worker'), 'level' => 2],
            ['id' => 'scheduler-cron', 'text' => __('Laravel Scheduler dan cron'), 'level' => 2],
            ['id' => 'users-roles', 'text' => __('Pengguna, peran, dan izin'), 'level' => 2],
            ['id' => 'backup-restore', 'text' => __('Backup dan restore'), 'level' => 2],
            ['id' => 'lisensi', 'text' => __('Lisensi'), 'level' => 2],
            ['id' => 'troubleshooting', 'text' => __('Pemantauan dan troubleshooting server'), 'level' => 2],
            ['id' => 'faq-admin', 'text' => __('FAQ administrator'), 'level' => 2],
            ['id' => 'referensi', 'text' => __('Referensi'), 'level' => 2],
        ];
    }

    public function render()
    {
        return view('livewire.manual.manual-page', [
            'title' => __('Manual Administrator'),
            'subtitle' => __('Instalasi server, antrian, jadwal, peran, dan backup'),
            'variant' => 'admin',
            'manualInclude' => 'manual.admin-html',
        ]);
    }
}

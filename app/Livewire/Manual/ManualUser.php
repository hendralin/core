<?php

namespace App\Livewire\Manual;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Manual Pengguna')]
class ManualUser extends Component
{
    /** @var array<int, array{id: string, text: string, level: int}> */
    public array $toc = [];

    public function mount(): void
    {
        if (! auth()->user()->hasRole('user')) {
            abort(403);
        }

        $this->toc = $this->userTableOfContents();
    }

    /**
     * @return array<int, array{id: string, text: string, level: int}>
     */
    protected function userTableOfContents(): array
    {
        return [
            ['id' => 'apa-itu', 'text' => __('Apa itu The Broadcaster'), 'level' => 2],
            ['id' => 'sebelum-mulai', 'text' => __('Sebelum memulai'), 'level' => 2],
            ['id' => 'akun', 'text' => __('Masuk, keluar, dan pengaturan akun'), 'level' => 2],
            ['id' => 'navigasi', 'text' => __('Navigasi menu'), 'level' => 2],
            ['id' => 'alur-kerja', 'text' => __('Alur kerja yang disarankan'), 'level' => 2],
            ['id' => 'panduan-fitur', 'text' => __('Panduan per fitur'), 'level' => 2],
            ['id' => 'fitur-waha', 'text' => 'WAHA Configuration', 'level' => 3],
            ['id' => 'fitur-sessions', 'text' => 'Sessions', 'level' => 3],
            ['id' => 'fitur-contacts', 'text' => __('Contacts & Groups'), 'level' => 3],
            ['id' => 'fitur-templates', 'text' => 'Templates', 'level' => 3],
            ['id' => 'fitur-messages', 'text' => 'Messages', 'level' => 3],
            ['id' => 'fitur-schedules', 'text' => 'Schedules', 'level' => 3],
            ['id' => 'status-pesan', 'text' => __('Status pesan, jadwal, dan kirim ulang'), 'level' => 2],
            ['id' => 'faq-user', 'text' => __('FAQ pengguna'), 'level' => 2],
        ];
    }

    public function render()
    {
        return view('livewire.manual.manual-page', [
            'title' => __('Manual Pengguna'),
            'subtitle' => __('Login, menu, WAHA, sesi, kontak, template, pesan, dan jadwal'),
            'variant' => 'user',
            'manualInclude' => 'manual.user-html',
        ]);
    }
}

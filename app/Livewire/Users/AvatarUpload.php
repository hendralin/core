<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class AvatarUpload extends Component
{
    use WithFileUploads;

    public User $user;
    public $avatar;
    public $isUploading = false;

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount(User $user)
    {
        $this->user = $user;
    }

    public function updatedAvatar()
    {
        $this->validate([
            'avatar' => 'image|max:2048|mimes:jpeg,png,jpg,gif,webp',
        ], [
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.max' => 'Ukuran gambar maksimal 2MB.',
            'avatar.mimes' => 'Format gambar harus JPEG, PNG, JPG, GIF, atau WebP.',
        ]);
    }

    public function uploadAvatar()
    {
        $this->validate([
            'avatar' => 'required|image|max:2048|mimes:jpeg,png,jpg,gif,webp',
        ], [
            'avatar.required' => 'Pilih gambar avatar terlebih dahulu.',
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.max' => 'Ukuran gambar maksimal 2MB.',
            'avatar.mimes' => 'Format gambar harus JPEG, PNG, JPG, GIF, atau WebP.',
        ]);

        $this->isUploading = true;

        try {
            // Delete old avatar if exists
            if ($this->user->avatar && Storage::disk('avatars')->exists($this->user->avatar)) {
                Storage::disk('avatars')->delete($this->user->avatar);
            }

            // Generate unique filename (without avatars/ prefix since we're using avatars disk)
            $filename = Str::uuid() . '.' . $this->avatar->getClientOriginalExtension();

            // Store new avatar using avatars disk
            $path = $this->avatar->storeAs('', $filename, 'avatars');

            // Update user avatar path
            $this->user->update(['avatar' => $filename]);

            // Log activity
            activity()
                ->performedOn($this->user)
                ->causedBy(Auth::user())
                ->withProperties([
                    'ip' => Request::ip(),
                    'user_agent' => Request::userAgent(),
                    'old_avatar' => $this->user->getOriginal('avatar'),
                    'new_avatar' => $filename,
                ])
                ->log('uploaded a new avatar');

            // Reset form
            $this->avatar = null;

            session()->flash('success', 'Avatar berhasil diperbarui.');

            $this->dispatch('refreshComponent');

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengupload avatar: ' . $e->getMessage());
        } finally {
            $this->isUploading = false;
        }
    }

    public function removeAvatar()
    {
        try {
            if ($this->user->avatar && Storage::disk('avatars')->exists($this->user->avatar)) {
                Storage::disk('avatars')->delete($this->user->avatar);
            }

            $oldAvatar = $this->user->avatar;
            $this->user->update(['avatar' => null]);

            // Log activity
            activity()
                ->performedOn($this->user)
                ->causedBy(Auth::user())
                ->withProperties([
                    'ip' => Request::ip(),
                    'user_agent' => Request::userAgent(),
                    'removed_avatar' => $oldAvatar,
                ])
                ->log('removed avatar');

            session()->flash('success', 'Avatar berhasil dihapus.');

            $this->dispatch('refreshComponent');

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus avatar: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.users.avatar-upload');
    }
}

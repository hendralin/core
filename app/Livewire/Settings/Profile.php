<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

#[Title('Settings - Profile')]
class Profile extends Component
{
    use WithFileUploads;

    public string $name = '';

    public string $email = '';

    public $avatar;
    public $isUploading = false;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Regenerate API token for the current user.
     */
    public function regenerateApiToken(): void
    {
        $user = Auth::user();
        $user->regenerateApiToken();

        Session::flash('api-token-regenerated', true);
        Session::flash('new-api-token', $user->api_token);
    }

    /**
     * Validate avatar when updated.
     */
    public function updatedAvatar(): void
    {
        $this->validate([
            'avatar' => 'image|max:2048|mimes:jpeg,png,jpg,gif,webp',
        ], [
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.max' => 'Ukuran gambar maksimal 2MB.',
            'avatar.mimes' => 'Format gambar harus JPEG, PNG, JPG, GIF, atau WebP.',
        ]);
    }

    /**
     * Upload new avatar for the current user.
     */
    public function uploadAvatar(): void
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
            $user = Auth::user();

            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('avatars')->exists($user->avatar)) {
                Storage::disk('avatars')->delete($user->avatar);
            }

            // Generate unique filename
            $filename = Str::uuid() . '.' . $this->avatar->getClientOriginalExtension();

            // Store new avatar using avatars disk
            $this->avatar->storeAs('', $filename, 'avatars');

            // Update user avatar path
            $user->update(['avatar' => $filename]);

            // Log activity
            activity()
                ->performedOn($user)
                ->causedBy($user)
                ->withProperties([
                    'ip' => Request::ip(),
                    'user_agent' => Request::userAgent(),
                    'old_avatar' => $user->getOriginal('avatar'),
                    'new_avatar' => $filename,
                ])
                ->log('uploaded a new avatar');

            // Reset form
            $this->avatar = null;

            Session::flash('avatar-success', 'Avatar berhasil diperbarui.');

        } catch (\Exception $e) {
            Session::flash('avatar-error', 'Gagal mengupload avatar: ' . $e->getMessage());
        } finally {
            $this->isUploading = false;
        }
    }

    /**
     * Remove avatar for the current user.
     */
    public function removeAvatar(): void
    {
        try {
            $user = Auth::user();

            if ($user->avatar && Storage::disk('avatars')->exists($user->avatar)) {
                Storage::disk('avatars')->delete($user->avatar);
            }

            $oldAvatar = $user->avatar;
            $user->update(['avatar' => null]);

            // Log activity
            activity()
                ->performedOn($user)
                ->causedBy($user)
                ->withProperties([
                    'ip' => Request::ip(),
                    'user_agent' => Request::userAgent(),
                    'removed_avatar' => $oldAvatar,
                ])
                ->log('removed avatar');

            Session::flash('avatar-success', 'Avatar berhasil dihapus.');

        } catch (\Exception $e) {
            Session::flash('avatar-error', 'Gagal menghapus avatar: ' . $e->getMessage());
        }
    }
}

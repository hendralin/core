<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as RequestFacade;

class ImpersonationController extends Controller
{
    /**
     * Log in as the given user (superadmin only).
     */
    public function start(Request $request, User $user): RedirectResponse
    {
        if (! Auth::user()?->hasRole('superadmin')) {
            abort(403);
        }

        if (session('is_impersonating')) {
            return redirect()->back()->with('error', __('You are already impersonating another user.'));
        }

        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', __('You cannot impersonate yourself.'));
        }

        if ($user->isInactive()) {
            return redirect()->back()->with('error', __('Cannot impersonate an inactive user.'));
        }

        $impersonatorId = Auth::id();

        session([
            'impersonator_id' => $impersonatorId,
            'is_impersonating' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        activity()
            ->performedOn($user)
            ->causedBy(User::find($impersonatorId))
            ->withProperties([
                'ip' => RequestFacade::ip(),
                'user_agent' => RequestFacade::userAgent(),
                'impersonator_id' => $impersonatorId,
                'target_user_id' => $user->id,
            ])
            ->log('started impersonating user');

        return redirect()->route('dashboard')->with('success', __('You are now viewing the application as :name.', ['name' => $user->name]));
    }

    /**
     * Return to the original account (while impersonating).
     */
    public function leave(Request $request): RedirectResponse
    {
        if (! session('is_impersonating') || ! session('impersonator_id')) {
            return redirect()->route('dashboard');
        }

        $impersonatorId = (int) session('impersonator_id');
        $impersonator = User::find($impersonatorId);
        $targetUserId = Auth::id();

        session()->forget(['impersonator_id', 'is_impersonating']);

        if (! $impersonator) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', __('Your original session could not be restored. Please sign in again.'));
        }

        $targetUser = $targetUserId ? User::find($targetUserId) : null;

        Auth::login($impersonator);
        $request->session()->regenerate();

        if ($targetUser) {
            activity()
                ->performedOn($targetUser)
                ->causedBy($impersonator)
                ->withProperties([
                    'ip' => RequestFacade::ip(),
                    'user_agent' => RequestFacade::userAgent(),
                    'stopped_impersonating_user_id' => $targetUserId,
                ])
                ->log('stopped impersonating user');
        }

        return redirect()->route('users.index')->with('success', __('You have returned to your own account.'));
    }
}

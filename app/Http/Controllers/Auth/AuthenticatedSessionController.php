<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use App\Mail\verfiedEmail;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use Illuminate\Support\Facades\Hash;



class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }


    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $credentials['email'])->first();

        // User not found
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.'
            ]);
        }

        // Check if user has attempts left
        if ($user->user_attempt <= 0) {
            $this->sendLoginLink($user, 'You have exceeded login attempts. Click below to login securely.');
            return response()->json([
                'status' => 'error',
                'message' => 'You have exceeded the maximum login attempts. A login link has been sent to your email and will expire in 5 minutes.'
            ]);
        }

        // Check password
        if (!Hash::check($credentials['password'], $user->password)) {
            $user->decrement('user_attempt');
            return response()->json([
                'status' => 'error',
                'message' => "Hi {$user->name}, your password is incorrect. You have {$user->user_attempt} attempts left.",
                'user' => $user->name,
            ]);
        }

        // Check email verification
        if (!$user->email_verified_at) {
            $this->sendVerificationLink($user, 'Please verify your email address to activate your account.');
            return response()->json([
                'status' => 'error',
                'message' => 'Your email address has not been verified. Please check your inbox for a verification link.'
            ]);
        }

        // Check if user is active
        if ($user->status == 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your User Status is Deactivated. Please Contact Admin.'
            ]);
        }

        // Attempt login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user->update(['user_attempt' => 3]); // Reset attempts

            session([
                'user_id'    => $user->id,
                'user_name'  => $user->name,
                'user_email' => $user->email,
                'user_role'  => $user->user_role,
            ]);

            return response()->json([
                'status'       => 'success',
                'message'      => 'Successfully Logged In',
                'user'         => $user->name,
                'redirect_url' => url('/dashboard')
            ]);
        }

        // Fallback (shouldn't reach here)
        return response()->json([
            'status' => 'error',
            'message' => 'Login failed. Please try again.'
        ]);
    }


    protected function sendVerificationLink($user, $message)
    {
        $link = URL::temporarySignedRoute(
            'handle.login.link',
            now()->addMinutes(5),
            ['user' => $user->id]
        );

        Mail::to($user->email)->send(new verfiedEmail(
            $link,
            'Verify Your Email Address',
            $message,
            $user->name
        ));
    }

    protected function sendLoginLink($user, $message)
    {
        $link = URL::temporarySignedRoute(
            'handle.login.link',
            now()->addMinutes(5),
            ['user' => $user->id]
        );

        Mail::to($user->email)->send(new verfiedEmail(
            $link,
            'Your Login Link',
            $message,
            $user->name
        ));
    }



    public function handleLoginLink(Request $request, User $user)
    {
        $user->update([
            'email_verified_at' => now(),
            'user_attempt' => 3
        ]);

        Auth::login($user);

        return view('Login');
    }



    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }


    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

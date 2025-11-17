<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'middle_name' => ['nullable', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'profile_picture_base64' => ['nullable', 'string'],
                'remove_profile_picture' => ['nullable', 'boolean'],
            ]);

            // Handle profile picture removal
            if ($request->input('remove_profile_picture') == '1') {
                $user->profile_picture = null;
            }
            // Handle profile picture upload
            elseif ($request->filled('profile_picture_base64')) {
                $base64Image = $request->input('profile_picture_base64');
                
                // Validate base64 image
                if ($this->isValidBase64Image($base64Image)) {
                    // Check file size (2MB limit)
                    $imageData = substr($base64Image, strpos($base64Image, ',') + 1);
                    $decodedImage = base64_decode($imageData);
                    
                    if (strlen($decodedImage) > 2 * 1024 * 1024) {
                        return Redirect::route('profile.edit')
                            ->with('error', 'Image size must be less than 2MB');
                    }
                    
                    // Store as base64
                    $user->profile_picture = $base64Image;
                } else {
                    return Redirect::route('profile.edit')
                        ->with('error', 'Invalid image format. Only PNG, JPEG, and JPG are allowed.');
                }
            }

            // Update name fields
            $user->first_name = $validated['first_name'];
            $user->middle_name = $validated['middle_name'];
            $user->last_name = $validated['last_name'];

            $user->save();

            return Redirect::route('profile.edit')
                ->with('success', 'Profile updated successfully!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return Redirect::route('profile.edit')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Profile update failed: ' . $e->getMessage());
            return Redirect::route('profile.edit')
                ->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validateWithBag('updatePassword', [
                'current_password' => ['required', 'current_password'],
                'password' => ['required', Password::defaults(), 'confirmed'],
            ]);

            $request->user()->update([
                'password' => Hash::make($validated['password']),
            ]);

            return Redirect::route('profile.edit')
                ->with('success', 'Password updated successfully!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return Redirect::route('profile.edit')
                ->withErrors($e->errors(), 'updatePassword');
        } catch (\Exception $e) {
            \Log::error('Password update failed: ' . $e->getMessage());
            return Redirect::route('profile.edit')
                ->with('error', 'Failed to update password: ' . $e->getMessage());
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        try {
            $request->validateWithBag('userDeletion', [
                'password' => ['required', 'current_password'],
            ]);

            $user = $request->user();

            Auth::logout();

            $user->delete();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return Redirect::to('/')
                ->with('success', 'Your account has been deleted.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return Redirect::route('profile.edit')
                ->withErrors($e->errors(), 'userDeletion');
        } catch (\Exception $e) {
            \Log::error('Account deletion failed: ' . $e->getMessage());
            return Redirect::route('profile.edit')
                ->with('error', 'Failed to delete account: ' . $e->getMessage());
        }
    }

    /**
     * Validate if the base64 string is a valid image (PNG, JPEG, JPG)
     */
    private function isValidBase64Image($base64String)
    {
        // Check if it's a valid base64 data URI
        if (!preg_match('/^data:image\/(png|jpeg|jpg);base64,/', $base64String)) {
            return false;
        }

        // Extract the base64 data
        $imageData = preg_replace('/^data:image\/(png|jpeg|jpg);base64,/', '', $base64String);
        
        // Validate base64 encoding
        if (!base64_decode($imageData, true)) {
            return false;
        }

        return true;
    }
}
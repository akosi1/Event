<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('middle_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('role', 'LIKE', "%{$search}%")
                  ->orWhere('status', 'LIKE', "%{$search}%")
                  ->orWhere('department', 'LIKE', "%{$search}%");
            });
        }

        // Handle AJAX autocomplete request
        if ($request->ajax() || $request->get('autocomplete')) {
            $users = $query->limit(10)->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'id_number' => $user->id_number,
                    'department' => $user->department,
                    'year_level' => $user->year_level_name,
                    'role' => $user->role,
                    'status' => $user->status,
                    'profile_picture' => $user->profile_picture_url,
                    'initials' => $user->initials,
                ];
            });

            return response()->json(['users' => $users]);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSorts = ['first_name', 'last_name', 'email', 'role', 'status', 'department', 'year_level', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $users = $query->paginate($perPage)->appends($request->query());

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_number' => 'required|string|max:50|unique:users',
                'first_name' => 'required|string|max:50|regex:/^[a-zA-Z\s]+$/',
                'middle_name' => 'nullable|string|max:50|regex:/^[a-zA-Z\s]+$/',
                'last_name' => 'required|string|max:50|regex:/^[a-zA-Z\s]+$/',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'department' => 'required|in:BSIT,BSBA,BSED,BEED,BSHM',
                'year_level' => 'required|in:1,2,3,4',
                'role' => 'required|in:admin,user',
                'status' => 'required|in:active,inactive',
                'profile_picture' => 'nullable|string',
            ]);

            $validated['password'] = Hash::make($validated['password']);

            // Handle base64 profile picture
            if ($request->filled('profile_picture')) {
                $base64Image = $request->input('profile_picture');
                
                // Validate and process base64 image
                if ($this->isValidBase64Image($base64Image)) {
                    $validated['profile_picture'] = $base64Image;
                } else {
                    return back()
                        ->withErrors(['profile_picture' => 'Invalid image format. Only PNG, JPEG, and JPG are allowed.'])
                        ->withInput();
                }
            } else {
                unset($validated['profile_picture']);
            }

            $user = User::create($validated);

            // FIXED: Redirect to users index, not notifications
            return redirect()->route('admin.users.index')
                ->with('success', 'User "' . $user->full_name . '" created successfully.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('User creation failed: ' . $e->getMessage());
            return back()
                ->with('error', 'Failed to create user: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'id_number' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
                'first_name' => 'required|string|max:50|regex:/^[a-zA-Z\s]+$/',
                'middle_name' => 'nullable|string|max:50|regex:/^[a-zA-Z\s]+$/',
                'last_name' => 'required|string|max:50|regex:/^[a-zA-Z\s]+$/',
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'password' => 'nullable|string|min:8|confirmed',
                'department' => 'required|in:BSIT,BSBA,BSED,BEED,BSHM',
                'year_level' => 'required|in:1,2,3,4',
                'role' => 'required|in:admin,user',
                'status' => 'required|in:active,inactive',
                'profile_picture' => 'nullable|string',
                'remove_profile_picture' => 'nullable|boolean',
            ]);

            // Handle password update
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            // Handle profile picture removal
            if ($request->filled('remove_profile_picture') && $request->remove_profile_picture == '1') {
                $validated['profile_picture'] = null;
            }
            // Handle new profile picture upload
            elseif ($request->filled('profile_picture')) {
                $base64Image = $request->input('profile_picture');
                
                // Validate and process base64 image
                if ($this->isValidBase64Image($base64Image)) {
                    $validated['profile_picture'] = $base64Image;
                } else {
                    return back()
                        ->withErrors(['profile_picture' => 'Invalid image format. Only PNG, JPEG, and JPG are allowed.'])
                        ->withInput();
                }
            }

            // Remove helper fields that shouldn't be saved
            unset($validated['remove_profile_picture']);

            $user->update($validated);

            // Check if request expects JSON (AJAX)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User updated successfully.',
                    'user' => $user
                ]);
            }

            // FIXED: Always redirect to users index after update
            return redirect()->route('admin.users.index')
                ->with('success', 'User "' . $user->full_name . '" updated successfully.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('User update failed: ' . $e->getMessage());
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update user: ' . $e->getMessage()
                ], 500);
            }
            
            return back()
                ->with('error', 'Failed to update user: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(User $user)
    {
        try {
            // Prevent self-deletion
            if ($user->id === auth()->id()) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'You cannot delete your own account.');
            }

            $userName = $user->full_name;
            $user->delete();

            // FIXED: Always redirect to users index after deletion
            return redirect()->route('admin.users.index')
                ->with('success', "User '{$userName}' deleted successfully.");
                
        } catch (\Exception $e) {
            \Log::error('User deletion failed: ' . $e->getMessage());
            return redirect()->route('admin.users.index')
                ->with('error', 'Failed to delete user: ' . $e->getMessage());
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

        // Check file size (max 2MB in base64)
        $sizeInBytes = (strlen($imageData) * 3) / 4;
        $maxSize = 2 * 1024 * 1024; // 2MB
        
        if ($sizeInBytes > $maxSize) {
            return false;
        }

        return true;
    }
}
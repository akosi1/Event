<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

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
            // Validate the incoming request
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
            ], [
                // Custom error messages
                'id_number.required' => 'The ID number field is required.',
                'id_number.unique' => 'This ID number is already registered.',
                'first_name.required' => 'The first name field is required.',
                'first_name.regex' => 'The first name may only contain letters and spaces.',
                'last_name.required' => 'The last name field is required.',
                'last_name.regex' => 'The last name may only contain letters and spaces.',
                'email.required' => 'The email address field is required.',
                'email.email' => 'Please provide a valid email address.',
                'email.unique' => 'This email address is already registered.',
                'password.required' => 'The password field is required.',
                'password.min' => 'Password must be at least 8 characters long.',
                'password.confirmed' => 'Password confirmation does not match.',
                'department.required' => 'Please select a department.',
                'year_level.required' => 'Please select a year level.',
                'role.required' => 'Please select a role.',
                'status.required' => 'Please select a status.',
            ]);

            // Hash the password
            $validated['password'] = Hash::make($validated['password']);

            // Handle base64 profile picture
            if ($request->filled('profile_picture')) {
                $base64Image = $request->input('profile_picture');
                
                // Validate and process base64 image
                if ($this->isValidBase64Image($base64Image)) {
                    $validated['profile_picture'] = $base64Image;
                } else {
                    return redirect()
                        ->route('admin.users.create')
                        ->withErrors(['profile_picture' => 'Invalid image format. Only PNG, JPEG, and JPG are allowed. Maximum file size is 2MB.'])
                        ->withInput();
                }
            } else {
                unset($validated['profile_picture']);
            }

            // Create the user
            $user = User::create($validated);

            // Log successful creation
            Log::info('User created successfully', [
                'user_id' => $user->id,
                'created_by' => auth()->id()
            ]);

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User created successfully! Welcome ' . $user->full_name . '!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation errors
            Log::warning('User creation validation failed', [
                'errors' => $e->errors(),
                'input' => $request->except(['password', 'password_confirmation'])
            ]);

            // Return to create page with validation errors
            return redirect()
                ->route('admin.users.create')
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            // Log unexpected errors
            Log::error('User creation failed with exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return to create page with error message
            return redirect()
                ->route('admin.users.create')
                ->with('error', 'Failed to create user. Please try again.')
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
            ], [
                'id_number.required' => 'The ID number field is required.',
                'id_number.unique' => 'This ID number is already registered.',
                'first_name.required' => 'The first name field is required.',
                'first_name.regex' => 'The first name may only contain letters and spaces.',
                'last_name.required' => 'The last name field is required.',
                'last_name.regex' => 'The last name may only contain letters and spaces.',
                'email.required' => 'The email address field is required.',
                'email.email' => 'Please provide a valid email address.',
                'email.unique' => 'This email address is already registered.',
                'password.min' => 'Password must be at least 8 characters long.',
                'password.confirmed' => 'Password confirmation does not match.',
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
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid image format. Only PNG, JPEG, and JPG are allowed. Maximum file size is 2MB.',
                        ], 422);
                    }
                    return redirect()
                        ->route('admin.users.edit', $user)
                        ->withErrors(['profile_picture' => 'Invalid image format. Only PNG, JPEG, and JPG are allowed. Maximum file size is 2MB.'])
                        ->withInput();
                }
            }

            // Remove helper fields that shouldn't be saved
            unset($validated['remove_profile_picture']);

            $user->update($validated);

            // Log successful update
            Log::info('User updated successfully', [
                'user_id' => $user->id,
                'updated_by' => auth()->id()
            ]);

            // Check if request expects JSON (AJAX)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User updated successfully.',
                    'user' => $user
                ]);
            }

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User updated successfully.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('User update validation failed', [
                'user_id' => $user->id,
                'errors' => $e->errors()
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()
                ->route('admin.users.edit', $user)
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            Log::error('User update failed', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update user. Please try again.'
                ], 500);
            }
            
            return redirect()
                ->route('admin.users.edit', $user)
                ->with('error', 'Failed to update user. Please try again.')
                ->withInput();
        }
    }

    public function destroy(User $user)
    {
        try {
            // Prevent self-deletion
            if ($user->id === auth()->id()) {
                return redirect()
                    ->route('admin.users.index')
                    ->with('error', 'You cannot delete your own account.');
            }

            $userName = $user->full_name;
            $user->delete();

            Log::info('User deleted successfully', [
                'user_id' => $user->id,
                'deleted_by' => auth()->id()
            ]);

            return redirect()
                ->route('admin.users.index')
                ->with('success', "User '{$userName}' deleted successfully.");
                
        } catch (\Exception $e) {
            Log::error('User deletion failed', [
                'user_id' => $user->id,
                'message' => $e->getMessage()
            ]);
            
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Failed to delete user. Please try again.');
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
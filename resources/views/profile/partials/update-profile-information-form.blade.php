<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and profile picture.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Profile Picture -->
        <div>
            <x-input-label for="profile_picture" :value="__('Profile Picture')" />
            <div class="mt-2 flex items-center gap-4">
                <div class="relative">
                    @if($user->profile_picture)
                        <img id="profile-preview" src="{{ $user->profile_picture }}" alt="Profile" class="w-24 h-24 rounded-full object-cover border-2 border-gray-300">
                    @else
                        <div id="profile-preview" class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center border-2 border-gray-300">
                            <span class="text-gray-500 text-2xl font-semibold">
                                {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                            </span>
                        </div>
                    @endif
                </div>
                <div>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="hidden" onchange="previewImage(event)">
                    <input type="hidden" id="profile_picture_base64" name="profile_picture_base64">
                    <label for="profile_picture" class="cursor-pointer inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Change Picture') }}
                    </label>
                    @if($user->profile_picture)
                        <button type="button" onclick="removeProfilePicture()" class="ml-2 text-sm text-red-600 hover:text-red-800">
                            {{ __('Remove') }}
                        </button>
                        <input type="hidden" name="remove_profile_picture" id="remove_profile_picture" value="0">
                    @endif
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('profile_picture_base64')" />
            <p class="mt-1 text-xs text-gray-500">JPG, PNG or GIF (max. 2MB)</p>
        </div>

        <!-- First Name -->
        <div>
            <x-input-label for="first_name" :value="__('First Name')" />
            <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name', $user->first_name)" required autofocus autocomplete="given-name" />
            <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
        </div>

        <!-- Middle Name -->
        <div>
            <x-input-label for="middle_name" :value="__('Middle Name')" />
            <x-text-input id="middle_name" name="middle_name" type="text" class="mt-1 block w-full" :value="old('middle_name', $user->middle_name)" autocomplete="additional-name" />
            <x-input-error class="mt-2" :messages="$errors->get('middle_name')" />
            <p class="mt-1 text-xs text-gray-500">Optional</p>
        </div>

        <!-- Last Name -->
        <div>
            <x-input-label for="last_name" :value="__('Last Name')" />
            <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $user->last_name)" required autocomplete="family-name" />
            <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
        </div>

        <!-- Email (Read-only) -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-gray-100 cursor-not-allowed" :value="$user->email" readonly disabled />
            <p class="mt-1 text-xs text-gray-500">Email cannot be changed</p>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('profile-preview');
            const base64Input = document.getElementById('profile_picture_base64');
            const removeInput = document.getElementById('remove_profile_picture');
            
            if (file) {
                // Check file size (2MB = 2 * 1024 * 1024 bytes)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB');
                    event.target.value = '';
                    return;
                }

                // Check file type
                if (!file.type.match('image.*')) {
                    alert('Please select an image file');
                    event.target.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const base64String = e.target.result;
                    
                    // Update preview
                    if (preview.tagName === 'IMG') {
                        preview.src = base64String;
                    } else {
                        const img = document.createElement('img');
                        img.id = 'profile-preview';
                        img.src = base64String;
                        img.alt = 'Profile';
                        img.className = 'w-24 h-24 rounded-full object-cover border-2 border-gray-300';
                        preview.parentNode.replaceChild(img, preview);
                    }
                    
                    // Set base64 value
                    base64Input.value = base64String;
                    
                    // Reset remove flag
                    if (removeInput) {
                        removeInput.value = '0';
                    }
                };
                reader.readAsDataURL(file);
            }
        }

        function removeProfilePicture() {
            if (confirm('Are you sure you want to remove your profile picture?')) {
                const preview = document.getElementById('profile-preview');
                const base64Input = document.getElementById('profile_picture_base64');
                const removeInput = document.getElementById('remove_profile_picture');
                const fileInput = document.getElementById('profile_picture');
                
                // Get user initials from the form
                const firstName = document.getElementById('first_name').value;
                const lastName = document.getElementById('last_name').value;
                const initials = (firstName.charAt(0) + lastName.charAt(0)).toUpperCase();
                
                // Replace with initials div
                const initialsDiv = document.createElement('div');
                initialsDiv.id = 'profile-preview';
                initialsDiv.className = 'w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center border-2 border-gray-300';
                initialsDiv.innerHTML = `<span class="text-gray-500 text-2xl font-semibold">${initials}</span>`;
                preview.parentNode.replaceChild(initialsDiv, preview);
                
                // Set remove flag
                removeInput.value = '1';
                base64Input.value = '';
                fileInput.value = '';
            }
        }
    </script>
</section>
<x-guest-layout>
    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-header">
                <h1>Complete Registration</h1>
                <p>Create your E&amp;P-O account</p>
            </div>

            <div class="auth-form">
                <form method="POST" action="{{ route('register') }}" id="registrationForm">
                    @csrf

                    <div class="form-group">
                        <div class="input-wrapper">
                            <input 
                                id="id_number" 
                                type="text" 
                                name="id_number" 
                                value="{{ old('id_number') }}" 
                                placeholder=" " 
                                required 
                                autocomplete="off" 
                                class="form-control"
                                oninput="this.value = this.value.replace(/\s+/g, '')"
                                onblur="this.value = this.value.trim()"
                            >
                            <label class="input-label">Student ID Number</label>
                            <i class="fas fa-id-card input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('id_number')" class="error-msg" />
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <div class="input-wrapper">
                                <input 
                                    id="first_name" 
                                    type="text" 
                                    name="first_name" 
                                    value="{{ old('first_name') }}" 
                                    placeholder=" " 
                                    required 
                                    autofocus 
                                    autocomplete="given-name" 
                                    class="form-control"
                                    oninput="this.value = this.value.replace(/[0-9\s]/g, '')"
                                    onblur="this.value = this.value.trim()"
                                >
                                <label class="input-label">First Name</label>
                                <i class="fas fa-user input-icon"></i>
                            </div>
                            <x-input-error :messages="$errors->get('first_name')" class="error-msg" />
                        </div>

                        <div class="form-group">
                            <div class="input-wrapper">
                                <input 
                                    id="last_name" 
                                    type="text" 
                                    name="last_name" 
                                    value="{{ old('last_name') }}" 
                                    placeholder=" " 
                                    required 
                                    autocomplete="family-name" 
                                    class="form-control"
                                    oninput="this.value = this.value.replace(/[0-9\s]/g, '')"
                                    onblur="this.value = this.value.trim()"
                                >
                                <label class="input-label">Last Name</label>
                                <i class="fas fa-user input-icon"></i>
                            </div>
                            <x-input-error :messages="$errors->get('last_name')" class="error-msg" />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <input 
                                id="middle_name" 
                                type="text" 
                                name="middle_name" 
                                value="{{ old('middle_name') }}" 
                                placeholder=" " 
                                autocomplete="additional-name" 
                                class="form-control"
                                oninput="this.value = this.value.replace(/[0-9\s]/g, '')"
                                onblur="this.value = this.value.trim()"
                            >
                            <label class="input-label">Middle Name (Optional)</label>
                            <i class="fas fa-user-circle input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('middle_name')" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <input 
                                id="email" 
                                type="email" 
                                name="email" 
                                value="{{ old('email', session('verified_email')) }}" 
                                placeholder=" " 
                                required 
                                autocomplete="username"
                                readonly
                                class="form-control readonly-field"
                            >
                            <label class="input-label">McLawis College Email</label>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="error-msg" />
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <div class="dropdown-wrapper">
                                <div class="dropdown-btn" id="deptBtn">
                                    <div class="dropdown-content">
                                        <i class="fas fa-graduation-cap input-icon" id="deptIcon"></i>
                                        <label class="select-label" id="deptLabel">Select Department</label>
                                        <span id="deptText"></span>
                                    </div>
                                    <i class="fas fa-chevron-down arrow" id="deptArrow"></i>
                                </div>
                                <div class="menu" id="deptMenu">
                                    <div class="item" data-val="BSIT" data-full="Bachelor of Science in Information Technology" data-icon="fa-laptop-code">
                                        <i class="fas fa-laptop-code"></i><span>BSIT</span>
                                    </div>
                                    <div class="item" data-val="BSBA" data-full="Bachelor of Science in Business Administration" data-icon="fa-briefcase">
                                        <i class="fas fa-briefcase"></i><span>BSBA</span>
                                    </div>
                                    <div class="item" data-val="BSED" data-full="Bachelor of Science in Education" data-icon="fa-chalkboard-teacher">
                                        <i class="fas fa-chalkboard-teacher"></i><span>BSEd</span>
                                    </div>
                                    <div class="item" data-val="BEED" data-full="Bachelor of Elementary Education" data-icon="fa-school">
                                        <i class="fas fa-school"></i><span>BEEd</span>
                                    </div>
                                    <div class="item" data-val="BSHM" data-full="Bachelor of Science in Hospitality Management" data-icon="fa-hotel">
                                        <i class="fas fa-hotel"></i><span>BSHM</span>
                                    </div>
                                </div>
                                <input type="hidden" name="department" id="department" value="{{ old('department') }}" required>
                            </div>
                            <x-input-error :messages="$errors->get('department')" class="error-msg" />
                        </div>

                        <div class="form-group">
                            <div class="dropdown-wrapper">
                                <div class="dropdown-btn" id="yearBtn">
                                    <div class="dropdown-content">
                                        <i class="fas fa-calendar-alt input-icon" id="yearIcon"></i>
                                        <label class="select-label" id="yearLabel">Select Year Level</label>
                                        <span id="yearText"></span>
                                    </div>
                                    <i class="fas fa-chevron-down arrow" id="yearArrow"></i>
                                </div>
                                <div class="menu" id="yearMenu">
                                    <div class="item" data-val="1" data-full="1st Year" data-icon="fa-calendar-alt">
                                        <i class="fas fa-calendar-alt"></i><span>1st Year</span>
                                    </div>
                                    <div class="item" data-val="2" data-full="2nd Year" data-icon="fa-calendar-alt">
                                        <i class="fas fa-calendar-alt"></i><span>2nd Year</span>
                                    </div>
                                    <div class="item" data-val="3" data-full="3rd Year" data-icon="fa-calendar-alt">
                                        <i class="fas fa-calendar-alt"></i><span>3rd Year</span>
                                    </div>
                                    <div class="item" data-val="4" data-full="4th Year" data-icon="fa-calendar-alt">
                                        <i class="fas fa-calendar-alt"></i><span>4th Year</span>
                                    </div>
                                </div>
                                <input type="hidden" name="year_level" id="year_level" value="{{ old('year_level') }}" required>
                            </div>
                            <x-input-error :messages="$errors->get('year_level')" class="error-msg" />
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <div class="input-wrapper">
                                <input 
                                    id="password" 
                                    type="password" 
                                    name="password" 
                                    placeholder=" " 
                                    required 
                                    autocomplete="new-password" 
                                    class="form-control"
                                    oninput="this.value = this.value.replace(/\s+/g, '')"
                                    onblur="this.value = this.value.trim()"
                                >
                                <label class="input-label">Password</label>
                                <i class="fas fa-lock input-icon"></i>
                                <button type="button" class="password-toggle" onclick="togglePasswordVisibility('password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="error-msg" />
                        </div>

                        <div class="form-group">
                            <div class="input-wrapper">
                                <input 
                                    id="password_confirmation" 
                                    type="password" 
                                    name="password_confirmation" 
                                    placeholder=" " 
                                    required 
                                    autocomplete="new-password" 
                                    class="form-control"
                                    oninput="this.value = this.value.replace(/\s+/g, '')"
                                    onblur="this.value = this.value.trim()"
                                >
                                <label class="input-label">Confirm Password</label>
                                <i class="fas fa-check-circle input-icon"></i>
                                <button type="button" class="password-toggle" onclick="togglePasswordVisibility('password_confirmation')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="error-msg" />
                        </div>
                    </div>

                    <div class="password-policy">
                        Password must be at least 12 characters with uppercase, lowercase, number, and symbol.
                    </div>

                    <div class="form-group">
                        <div class="terms-checkbox-wrapper">
                            <input 
                                type="checkbox" 
                                id="terms_accepted" 
                                name="terms_accepted" 
                                value="1"
                                {{ old('terms_accepted') ? 'checked' : '' }}
                                required
                                class="terms-checkbox"
                            >
                            <label for="terms_accepted" class="terms-label">
                                I have read and agree to the 
                                <a href="javascript:void(0)" onclick="openTermsModal()" class="terms-link">
                                    Terms and Conditions
                                </a>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('terms_accepted')" class="error-msg" />
                    </div>

                    <input type="hidden" name="role" value="student">
                    <input type="hidden" name="status" value="active">

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-user-plus"></i>
                        Create Account
                    </button>

                    <div class="form-footer">
                        <div class="divider">
                            <span>or</span>
                        </div>
                        <div class="signup-link">
                            <p>Already registered?</p>
                            <a href="{{ route('login') }}" class="btn-signin">
                                Sign in here
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('layouts.term_condition')

    <link rel="stylesheet" href="{{ asset('user/register/register.css') }}">

    <script>
        function togglePasswordVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            const button = field.parentElement.querySelector('.password-toggle');
            const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
            field.setAttribute('type', type);
            button.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const deptBtn = document.getElementById('deptBtn');
            const deptMenu = document.getElementById('deptMenu');
            const deptText = document.getElementById('deptText');
            const deptInput = document.getElementById('department');
            const deptIcon = document.getElementById('deptIcon');
            const deptItems = deptMenu.querySelectorAll('.item');
            const yearBtn = document.getElementById('yearBtn');
            const yearMenu = document.getElementById('yearMenu');

            const oldDeptValue = deptInput.value;
            if (oldDeptValue) {
                deptItems.forEach(item => {
                    if (item.dataset.val === oldDeptValue || item.dataset.full === oldDeptValue) {
                        deptText.textContent = item.dataset.val;
                        deptInput.value = item.dataset.val;
                        deptIcon.className = 'fas ' + item.dataset.icon + ' input-icon';
                        deptBtn.classList.add('has-value');
                    }
                });
            }

            deptBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                deptMenu.classList.toggle('show');
                deptBtn.classList.toggle('active');
                yearMenu.classList.remove('show');
                yearBtn.classList.remove('active');
            });

            deptItems.forEach(item => {
                item.addEventListener('click', function() {
                    const val = this.dataset.val;
                    const icon = this.dataset.icon;
                    deptText.textContent = val;
                    deptInput.value = val;
                    deptIcon.className = 'fas ' + icon + ' input-icon';
                    deptBtn.classList.add('has-value');
                    deptMenu.classList.remove('show');
                    deptBtn.classList.remove('active');
                });
            });

            const yearText = document.getElementById('yearText');
            const yearInput = document.getElementById('year_level');
            const yearIcon = document.getElementById('yearIcon');
            const yearItems = yearMenu.querySelectorAll('.item');

            const oldYearValue = yearInput.value;
            if (oldYearValue) {
                yearItems.forEach(item => {
                    if (item.dataset.val === oldYearValue) {
                        yearText.textContent = item.dataset.full;
                        yearInput.value = item.dataset.val;
                        yearBtn.classList.add('has-value');
                    }
                });
            }

            yearBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                yearMenu.classList.toggle('show');
                yearBtn.classList.toggle('active');
                deptMenu.classList.remove('show');
                deptBtn.classList.remove('active');
            });

            yearItems.forEach(item => {
                item.addEventListener('click', function() {
                    const val = this.dataset.val;
                    const full = this.dataset.full;
                    yearText.textContent = full;
                    yearInput.value = val;
                    yearBtn.classList.add('has-value');
                    yearMenu.classList.remove('show');
                    yearBtn.classList.remove('active');
                });
            });

            document.addEventListener('click', function(e) {
                if (!deptBtn.contains(e.target)) {
                    deptMenu.classList.remove('show');
                    deptBtn.classList.remove('active');
                }
                if (!yearBtn.contains(e.target)) {
                    yearMenu.classList.remove('show');
                    yearBtn.classList.remove('active');
                }
            });

            const termsCheckbox = document.getElementById('terms_accepted');
            const termsWrapper = termsCheckbox.closest('.terms-checkbox-wrapper');
            
            termsCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    termsWrapper.classList.add('accepted');
                } else {
                    termsWrapper.classList.remove('accepted');
                }
            });

            if (termsCheckbox.checked) {
                termsWrapper.classList.add('accepted');
            }

            document.getElementById('registrationForm').addEventListener('submit', function(e) {
                const textFields = ['id_number', 'first_name', 'last_name', 'middle_name', 'password', 'password_confirmation'];
                for (const fieldId of textFields) {
                    const field = document.getElementById(fieldId);
                    if (field && /\s/.test(field.value)) {
                        e.preventDefault();
                        alert(`"${field.previousElementSibling?.textContent || fieldId}" must not contain spaces.`);
                        field.focus();
                        return false;
                    }
                }

                const nameFields = ['first_name', 'last_name', 'middle_name'];
                for (const fieldId of nameFields) {
                    const field = document.getElementById(fieldId);
                    if (field && field.value && /[0-9]/.test(field.value)) {
                        e.preventDefault();
                        alert(`"${field.previousElementSibling?.textContent || fieldId}" must not contain numbers.`);
                        field.focus();
                        return false;
                    }
                }

                const termsCheckbox = document.getElementById('terms_accepted');
                if (!termsCheckbox.checked) {
                    e.preventDefault();
                    alert('You must accept the Terms and Conditions to register.');
                    termsCheckbox.focus();
                    return false;
                }
            });
        });
    </script>
</x-guest-layout>
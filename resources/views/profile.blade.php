@extends('layouts.app')

@push('styles')
<style>
    /* Updated Color Scheme */
    :root {
        --primary-gradient: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        --sidebar-bg: #f1f5f9;
        --content-bg: #f8fafc;
        --card-bg: #ffffff;
        --primary-blue: #003B7B;
        --hover-blue: #003B7B;
        --text-primary: #1e293b;
        --text-secondary: #02275b;
        --border-color: #e2e8f0;
        --success-color: #10b981;
        --error-color: #ef4444;
    }

    /* Main Container Improvements */
    .main-wrapper {
        min-height: calc(100vh - 80px);
        background: var(--content-bg);
        padding: 2.5rem;
        background-image: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    }

    .profile-container {
        max-width: 1000px;
        margin: 0 auto;
        background: var(--card-bg);
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .profile-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px -10px rgba(0, 0, 0, 0.12);
    }

    /* Refined Sidebar */
    .sidebar {
        background: var(--primary-gradient);
        padding: 2.5rem 1.5rem;
        height: 100%;
        min-height: calc(100vh - 130px);
        position: relative;
        overflow: hidden;
    }

    .sidebar::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
        pointer-events: none;
    }

    .nav-link-custom {
        color: rgba(255, 255, 255, 0.9);
        padding: 0.875rem 1.25rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 0.875rem;
        transition: all 0.3s ease;
        text-decoration: none;
        margin-bottom: 0.75rem;
        position: relative;
        overflow: hidden;
    }

    .nav-link-custom i {
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s ease;
    }

    .nav-link-custom.active {
        background: rgba(255, 255, 255, 0.18);
        color: white;
        transform: scale(1.03);
    }

    .nav-link-custom:hover:not(.active) {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        transform: translateX(5px);
    }

    .nav-link-custom:hover i {
        transform: rotate(10deg);
    }

    /* Profile Header Refinements */
    .profile-header {
        text-align: center;
        margin-bottom: 2.5rem;
        padding: 2.5rem 1rem 1.5rem;
        position: relative;
    }

    .profile-picture-container {
        width: 150px;
        height: 150px;
        margin: 0 auto 1.5rem;
        border-radius: 50%;
        padding: 4px;
        background: linear-gradient(45deg, #3b82f6, #2563eb);
        position: relative;
    }

    .profile-picture {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
        transition: all 0.4s ease;
    }

    .profile-picture:hover {
        transform: scale(1.03);
    }

    .camera-icon {
        position: absolute;
        bottom: 8px;
        right: 8px;
        background: var(--primary-blue);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    }

    .camera-icon:hover {
        transform: scale(1.1) rotate(5deg);
        background: var(--hover-blue);
    }

    /* Content Wrapper */
    .content-wrapper {
        padding: 1rem 2.5rem 2.5rem;
    }

    /* Form Layout Improvements */
    .form-row {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-col {
        flex: 1;
    }

    /* Form Refinements */
    .form-section {
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }

    .form-label {
        color: var(--text-primary);
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        display: block;
        transition: color 0.3s ease;
    }

    .form-control {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        transition: all 0.3s ease;
        background-color: #f9fafb;
        color: var(--text-primary);
        font-size: 0.95rem;
    }

    .form-control:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        background-color: #ffffff;
        transform: translateY(-1px);
    }

    .form-control::placeholder {
        color: var(--text-secondary);
    }

    /* Form field with icon */
    .input-with-icon {
        position: relative;
    }

    .input-with-icon .form-control {
        padding-left: 2.5rem;
    }

    .input-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
    }

    /* Improved Save Button */
    .save-button {
        background: var(--primary-gradient);
        color: white;
        border: none;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        margin-top: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        max-width: 250px;
        margin-left: auto;
        margin-right: auto;
        position: relative;
        overflow: hidden;
    }

    .save-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }

    .save-button:active {
        transform: translateY(-1px);
    }

    /* Animation Classes */
    .fade-in {
        animation: fadeIn 0.5s ease-out;
    }

    .slide-up {
        animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .bounce-in {
        animation: bounceIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes bounceIn {
        0% {
            transform: scale(0.3);
            opacity: 0;
        }
        50% {
            transform: scale(1.05);
            opacity: 0.8;
        }
        70% { transform: scale(0.9); }
        100% { transform: scale(1); opacity: 1; }
    }

    /* Improved Success Notification */
    .save-success {
        position: fixed;
        top: 20px;
        right: 20px;
        background: var(--success-color);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        animation: slideInRight 0.5s ease-out;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .main-wrapper {
            padding: 1rem;
        }

        .profile-container {
            border-radius: 12px;
        }

        .sidebar {
            min-height: auto;
            padding: 1.5rem;
            border-radius: 12px 12px 0 0;
        }

        .content-wrapper {
            padding: 1rem 1.5rem 2rem;
        }

        .profile-picture-container {
            width: 130px;
            height: 130px;
        }

        .form-control {
            padding: 0.8rem 1rem;
        }

        .form-row {
            flex-direction: column;
            gap: 0;
        }
    }

    /* Error Handling Styles */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        20%, 60% { transform: translateX(-4px); }
        40%, 80% { transform: translateX(4px); }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    .error-message {
        color: var(--error-color);
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        opacity: 0;
        transform: translateY(-8px);
        animation: slideDown 0.3s ease forwards;
    }

    @keyframes slideDown {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form-control.error {
        border-color: var(--error-color);
        animation: shake 0.4s ease-in-out;
    }

    .form-control.success {
        border-color: var(--success-color);
    }

    .save-button.saving {
        background: var(--success-color);
        pointer-events: none;
    }

    .save-button.saving::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        animation: shimmer 1.5s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
</style>
@endpush

@section('content')
<div class="main-wrapper">
    <div class="profile-container">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="sidebar">
                    <nav>
                        <a href="#" class="nav-link-custom active">
                            <i class="fas fa-user"></i>
                            Profile Settings
                        </a>
                        <a href="#" class="nav-link-custom">
                            <i class="fas fa-lock"></i>
                            Password
                        </a>
                        <a href="#" class="nav-link-custom">
                            <i class="fas fa-check-circle"></i>
                            Verification
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <div class="content-wrapper">
                    <form id="profileForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Profile Picture Section -->
                        <div class="profile-header bounce-in">
                            <div class="profile-picture-container">
                                <img src="{{ $user->profile_picture ? Storage::url($user->profile_picture) : asset('images/default-avatar.png') }}"
                                     alt="Profile Picture"
                                     class="profile-picture">
                                <label for="profile_picture" class="camera-icon">
                                    <i class="fas fa-camera"></i>
                                </label>
                                <input type="file" id="profile_picture" name="profile_picture" class="d-none" accept="image/*">
                            </div>
                            <h3 class="mb-2">{{ $user->name }}</h3>
                            <p class="text-muted">{{ $user->email }}</p>
                        </div>

                        <!-- Form Fields - Better Layout -->
                        <div class="form-row fade-in">
                            <div class="form-col">
                                <div class="form-section">
                                    <label class="form-label">Full Name</label>
                                    <div class="input-with-icon">
                                        <span class="input-icon">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <input type="text" class="form-control" name="name"
                                               value="{{ old('name', $user->name) }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-row fade-in">
                            <div class="form-col">
                                <div class="form-section">
                                    <label class="form-label">Email Address</label>
                                    <div class="input-with-icon">
                                        <span class="input-icon">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                        <input type="email" class="form-control" name="email"
                                               value="{{ old('email', $user->email) }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-row fade-in">
                            <div class="form-col">
                                <div class="form-section">
                                    <label class="form-label">New Password</label>
                                    <div class="input-with-icon">
                                        <span class="input-icon">
                                            <i class="fas fa-key"></i>
                                        </span>
                                        <input type="password" class="form-control" name="password"
                                               placeholder="Leave blank to keep current password">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="save-button slide-up">
                            <i class="fas fa-save"></i>
                            <span>Save Changes</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Profile picture upload
    const profilePicInput = document.getElementById('profile_picture');
    const profilePicDisplay = document.querySelector('.profile-picture');

    if (profilePicInput) {
        profilePicInput.addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePicDisplay.style.transform = 'scale(0.5)';
                    profilePicDisplay.style.opacity = '0';
                    setTimeout(() => {
                        profilePicDisplay.src = e.target.result;
                        profilePicDisplay.style.transform = 'scale(1)';
                        profilePicDisplay.style.opacity = '1';
                    }, 200);
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    }

    // Form validation with enhanced animations
    const form = document.getElementById('profileForm');

    form.addEventListener('submit', function(e) {
        let hasError = false;
        const requiredFields = form.querySelectorAll('input[required]');

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                hasError = true;
                field.classList.add('error');
                field.style.animation = 'shake 0.5s ease-in-out';
                field.style.borderColor = 'var(--error-color)';

                // Create error message if it doesn't exist
                const formSection = field.closest('.form-section');
                if (!formSection.querySelector('.error-message')) {
                    const errorMsg = document.createElement('div');
                    errorMsg.className = 'error-message';
                    errorMsg.innerHTML = '<i class="fas fa-exclamation-circle"></i> This field is required';
                    formSection.appendChild(errorMsg);
                }

                setTimeout(() => {
                    field.style.animation = '';
                }, 500);

                field.addEventListener('input', function() {
                    this.classList.remove('error');
                    this.style.borderColor = '';

                    // Remove error message
                    const errorMsg = this.closest('.form-section').querySelector('.error-message');
                    if (errorMsg) {
                        errorMsg.remove();
                    }
                }, { once: true });
            }
        });

        if (hasError) {
            e.preventDefault();
            const firstError = form.querySelector('.error');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        } else {
            // Success animation
            const saveButton = form.querySelector('.save-button');
            if (saveButton) {
                saveButton.innerHTML = '<i class="fas fa-check-circle"></i> Saved!';
                saveButton.style.background = 'var(--success-color)';
                saveButton.classList.add('saving');

                // Create success notification
                const notification = document.createElement('div');
                notification.className = 'save-success';
                notification.innerHTML = '<i class="fas fa-check-circle"></i> Profile updated successfully!';
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.style.animation = 'slideOutRight 0.5s ease-out forwards';
                    setTimeout(() => {
                        notification.remove();
                    }, 500);
                }, 3000);
            }
        }
    });

    // Enhanced input field animations
    const formControls = document.querySelectorAll('.form-control');
    formControls.forEach(control => {
        const formSection = control.closest('.form-section');
        const label = formSection.querySelector('.form-label');
        const inputIcon = control.closest('.input-with-icon')?.querySelector('.input-icon i');

        control.addEventListener('focus', function() {
            formSection.style.transform = 'translateY(-2px)';
            label.style.color = 'var(--primary-blue)';
            this.style.borderColor = 'var(--primary-blue)';
            if (inputIcon) {
                inputIcon.style.color = 'var(--primary-blue)';
            }
        });

        control.addEventListener('blur', function() {
            formSection.style.transform = 'translateY(0)';
            label.style.color = '';
            if (!this.value) {
                this.style.borderColor = '';
            }
            if (inputIcon) {
                inputIcon.style.color = '';
            }
        });

        // Add floating effect on hover
        formSection.addEventListener('mouseenter', function() {
            if (document.activeElement !== control) {
                this.style.transform = 'translateY(-2px)';
            }
        });

        formSection.addEventListener('mouseleave', function() {
            if (document.activeElement !== control) {
                this.style.transform = 'translateY(0)';
            }
        });
    });

    // Sidebar link hover effects
    const navLinks = document.querySelectorAll('.nav-link-custom');
    navLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            if (!this.classList.contains('active')) {
                const icon = this.querySelector('i');
                icon.style.transform = 'rotate(10deg) scale(1.2)';
            }
        });

        link.addEventListener('mouseleave', function() {
            if (!this.classList.contains('active')) {
                const icon = this.querySelector('i');
                icon.style.transform = '';
            }
        });

        link.addEventListener('click', function(e) {
            e.preventDefault();
            navLinks.forEach(nav => {
                nav.classList.remove('active');
                nav.style.transform = '';
            });

            this.classList.add('active');
            this.style.transform = 'translateX(5px)';

            setTimeout(() => {
                this.style.transform = '';
            }, 300);
        });
    });

    // Profile picture container hover effect
    const pictureContainer = document.querySelector('.profile-picture-container');
    if (pictureContainer) {
        pictureContainer.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
        });

        pictureContainer.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    }
});
</script>
@endpush

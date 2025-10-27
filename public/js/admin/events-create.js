// Events Create JavaScript

document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    const cancelReasonRow = document.getElementById('cancelReasonRow');
    const imageInput = document.getElementById('image');
    
    // Show/hide cancel reason based on status
    function toggleCancelReason() {
        if (cancelReasonRow && statusSelect) {
            cancelReasonRow.style.display = ['postponed', 'cancelled'].includes(statusSelect.value) ? 'block' : 'none';
        }
    }
    
    // Image preview
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        const img = document.getElementById('previewImg');
        
        if (input.files && input.files[0]) {
            // Check file size (2MB = 2 * 1024 * 1024 bytes)
            if (input.files[0].size > 2 * 1024 * 1024) {
                Swal.fire({
                    title: 'File Too Large',
                    text: 'File size must be less than 2MB',
                    icon: 'error'
                });
                input.value = '';
                preview.style.display = 'none';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = e => {
                img.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
        }
    }
    
    // Remove preview
    window.removePreview = function() {
        document.getElementById('image').value = '';
        document.getElementById('imagePreview').style.display = 'none';
    };
    
    // Event listeners
    if (statusSelect) {
        statusSelect.addEventListener('change', toggleCancelReason);
    }
    
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            previewImage(this);
        });
    }
    
    // Form validation
    const eventForm = document.getElementById('eventForm');
    if (eventForm) {
        eventForm.addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const description = document.getElementById('description').value.trim();
            const date = document.getElementById('date').value;
            const location = document.getElementById('location').value.trim();
            
            if (!title || !description || !date || !location) {
                e.preventDefault();
                Swal.fire({
                    title: 'Validation Error',
                    text: 'Please fill in all required fields.',
                    icon: 'error'
                });
                return;
            }
            
            // Check if date is in the past
            const selectedDate = new Date(date);
            const now = new Date();
            if (selectedDate < now) {
                e.preventDefault();
                Swal.fire({
                    title: 'Invalid Date',
                    text: 'Event date cannot be in the past.',
                    icon: 'error'
                });
                return;
            }
        });
    }
    
    // Initialize
    toggleCancelReason();
    
    // Set minimum date to today
    const dateInput = document.getElementById('date');
    if (dateInput) {
        const now = new Date();
        const minDate = now.toISOString().slice(0, 16);
        dateInput.min = minDate;
    }
});

// Utility function to validate file size
function validateFileSize(file, maxSizeMB = 2) {
    const maxSize = maxSizeMB * 1024 * 1024; // Convert MB to bytes
    return file.size <= maxSize;
}

// Utility function to validate file type
function validateFileType(file, allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']) {
    return allowedTypes.includes(file.type);
}
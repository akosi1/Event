/**
 * Events Edit Form JavaScript
 * Handles form interactions, image previews, and dynamic field visibility
 */

// Initialize edit form functionality
document.addEventListener('DOMContentLoaded', function() {
    initializeEditForm();
});

/**
 * Initialize all edit form functionality
 */
function initializeEditForm() {
    const editStatusSelect = document.getElementById('edit_status');
    const editCancelReasonRow = document.getElementById('edit_cancelReasonRow');
    const editImageInput = document.getElementById('edit_image');
    
    // Initialize status change handler
    if (editStatusSelect && editCancelReasonRow) {
        editStatusSelect.addEventListener('change', handleStatusChange);
    }
    
    // Initialize image preview handler
    if (editImageInput) {
        editImageInput.addEventListener('change', function() {
            previewEditImage(this);
        });
    }
}

/**
 * Handle status dropdown changes to show/hide cancel reason field
 */
function handleStatusChange() {
    const editStatusSelect = document.getElementById('edit_status');
    const editCancelReasonRow = document.getElementById('edit_cancelReasonRow');
    
    if (editStatusSelect && editCancelReasonRow) {
        const shouldShow = ['postponed', 'cancelled'].includes(editStatusSelect.value);
        editCancelReasonRow.style.display = shouldShow ? 'block' : 'none';
        
        // Add smooth transition effect
        if (shouldShow) {
            editCancelReasonRow.style.opacity = '0';
            setTimeout(() => {
                editCancelReasonRow.style.opacity = '1';
            }, 10);
        }
    }
}

/**
 * Preview selected image in the edit form
 * @param {HTMLInputElement} input - The file input element
 */
function previewEditImage(input) {
    if (input.files && input.files[0]) {
        // Validate file size (2MB limit)
        const maxSize = 2 * 1024 * 1024; // 2MB in bytes
        if (input.files[0].size > maxSize) {
            alert('File size must be less than 2MB');
            input.value = '';
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(input.files[0].type)) {
            alert('Please select a valid image file (JPG, PNG, GIF, WebP)');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewImg = document.getElementById('edit_newPreviewImg');
            const previewContainer = document.getElementById('edit_newImagePreview');
            
            if (previewImg && previewContainer) {
                previewImg.src = e.target.result;
                previewContainer.style.display = 'block';
                
                // Add fade-in effect
                previewContainer.style.opacity = '0';
                setTimeout(() => {
                    previewContainer.style.opacity = '1';
                }, 10);
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

/**
 * Remove current image in edit form
 * Sets the remove_image hidden field and hides the current image container
 */
function removeEditCurrentImage() {
    const removeImageInput = document.getElementById('edit_removeImage');
    const currentImageContainer = document.getElementById('edit_currentImageContainer');
    
    if (removeImageInput && currentImageContainer) {
        removeImageInput.value = '1';
        
        // Add fade-out effect before hiding
        currentImageContainer.style.opacity = '0';
        setTimeout(() => {
            currentImageContainer.style.display = 'none';
        }, 300);
    }
}

/**
 * Remove new image preview in edit form
 * Clears the file input and hides the preview
 */
function removeEditNewPreview() {
    const imageInput = document.getElementById('edit_image');
    const previewContainer = document.getElementById('edit_newImagePreview');
    
    if (imageInput) {
        imageInput.value = '';
    }
    
    if (previewContainer) {
        // Add fade-out effect before hiding
        previewContainer.style.opacity = '0';
        setTimeout(() => {
            previewContainer.style.display = 'none';
        }, 300);
    }
}

/**
 * Validate form before submission
 * @param {Event} event - The form submission event
 */
function validateEditForm(event) {
    const form = document.getElementById('editEventForm');
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        event.preventDefault();
        alert('Please fill in all required fields');
    }
    
    return isValid;
}

// Optional: Add form validation on submit
document.addEventListener('DOMContentLoaded', function() {
    const editForm = document.getElementById('editEventForm');
    if (editForm) {
        editForm.addEventListener('submit', validateEditForm);
    }
});
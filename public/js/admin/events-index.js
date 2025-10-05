// Events Index JavaScript - Complete functionality

// View event function
function viewEvent(eventId) {
    fetch(`/admin/events/${eventId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('viewEventContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('viewEventModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error!', 'Failed to load event details.', 'error');
        });
}

// Success message handler
function showSuccessMessage(message) {
    Swal.fire({
        title: 'Success!',
        text: message,
        icon: 'success',
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
}

// Edit event function
function editEvent(eventId) {
    // Load edit form via AJAX
    fetch(`/admin/events/${eventId}/edit`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('editEventFormContainer').innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading edit form:', error);
            Swal.fire({
                title: 'Error',
                text: 'Failed to load edit form. Please try again.',
                icon: 'error'
            });
        });
}

// Load create form function
function loadCreateForm() {
    const container = document.getElementById('createEventFormContainer');
    const baseUrl = window.location.origin;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    
    container.innerHTML = `
        <form action="${baseUrl}/admin/events" method="POST" enctype="multipart/form-data" id="createEventForm">
            <input type="hidden" name="_token" value="${csrfToken}">
            
            <!-- Basic Info -->
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="create_title" class="form-label fw-semibold">Event Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="create_title" name="title" required placeholder="Enter event title">
                </div>
                
                <div class="col-md-12 mb-3">
                    <label for="create_description" class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="create_description" name="description" rows="4" required placeholder="Describe your event..."></textarea>
                </div>
            </div>

            <!-- Date & Location -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="create_date" class="form-label fw-semibold">Event Date <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control" id="create_date" name="date" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="create_location" class="form-label fw-semibold">Location <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="create_location" name="location" required placeholder="Event location">
                </div>
            </div>

            <!-- Department & Status -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="create_department" class="form-label fw-semibold">Department</label>
                    <select class="form-select" id="create_department" name="department">
                        <option value="">Select Department (Optional)</option>
                        <option value="BSIT">BSIT - Bachelor of Science in Information Technology</option>
                        <option value="BSBA">BSBA - Bachelor of Science in Business Administration</option>
                        <option value="BSED">BSED - Bachelor of Science in Education</option>
                        <option value="BEED">BEED - Bachelor of Elementary Education</option>
                        <option value="BSHM">BSHM - Bachelor of Science in Hospitality Management</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="create_status" class="form-label fw-semibold">Status</label>
                    <select class="form-select" id="create_status" name="status">
                        <option value="active" selected>Active</option>
                        <option value="postponed">Postponed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>

            <!-- Cancel Reason (hidden by default) -->
            <div class="row" id="create_cancelReasonRow" style="display: none;">
                <div class="col-md-12 mb-3">
                    <label for="create_cancel_reason" class="form-label fw-semibold">Reason for Postponement/Cancellation</label>
                    <textarea class="form-control" id="create_cancel_reason" name="cancel_reason" rows="2" placeholder="Provide reason..."></textarea>
                </div>
            </div>

            <!-- Image Upload -->
            <div class="mb-4">
                <label for="create_image" class="form-label fw-semibold">Event Image</label>
                <input type="file" class="form-control" id="create_image" name="image" accept="image/jpeg,image/png,image/jpg,image/gif">
                <div class="form-text">Supported: JPG, PNG, GIF. Max size: 2MB</div>
                
                <!-- Preview -->
                <div id="create_imagePreview" class="mt-3" style="display: none;">
                    <div class="border rounded p-2 bg-light">
                        <img id="create_previewImg" src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeCreatePreview()">
                                <i class="fas fa-times"></i> Remove
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Create Event
                </button>
            </div>
        </form>
    `;

    // Initialize create form functionality
    initializeCreateForm();
}

// Initialize create form
function initializeCreateForm() {
    const statusSelect = document.getElementById('create_status');
    const cancelReasonRow = document.getElementById('create_cancelReasonRow');
    const imageInput = document.getElementById('create_image');
    
    // Set minimum date to today
    const dateInput = document.getElementById('create_date');
    const now = new Date();
    const minDate = now.toISOString().slice(0, 16);
    dateInput.min = minDate;
    
    // Show/hide cancel reason based on status
    statusSelect.addEventListener('change', function() {
        cancelReasonRow.style.display = ['postponed', 'cancelled'].includes(this.value) ? 'block' : 'none';
    });
    
    // Image preview
    imageInput.addEventListener('change', function() {
        previewCreateImage(this);
    });
    
    // Form validation
    document.getElementById('createEventForm').addEventListener('submit', function(e) {
        const title = document.getElementById('create_title').value.trim();
        const description = document.getElementById('create_description').value.trim();
        const date = document.getElementById('create_date').value;
        const location = document.getElementById('create_location').value.trim();
        
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

// Image preview for create form
function previewCreateImage(input) {
    const preview = document.getElementById('create_imagePreview');
    const img = document.getElementById('create_previewImg');
    
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

// Remove create preview
function removeCreatePreview() {
    document.getElementById('create_image').value = '';
    document.getElementById('create_imagePreview').style.display = 'none';
}

// Live Search Implementation
function initializeLiveSearch() {
    const searchInput = document.getElementById('liveSearchInput');
    const eventsTableBody = document.getElementById('eventsTableBody');
    const searchSpinner = document.getElementById('searchSpinner');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const searchResultsInfo = document.getElementById('searchResultsInfo');
    const searchResultsText = document.getElementById('searchResultsText');
    const clearSearchResults = document.getElementById('clearSearchResults');
    const resetFiltersBtn = document.getElementById('resetFiltersBtn');
    const paginationSection = document.getElementById('paginationSection');
    
    if (!searchInput || !eventsTableBody) return;
    
    let searchTimeout;
    let originalRows = Array.from(eventsTableBody.querySelectorAll('.event-row'));
    let isSearching = false;
    
    // Search input event listener
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (searchTerm === '') {
            clearSearch();
            return;
        }
        
        // Show spinner and clear button
        showSearchSpinner();
        showClearButton();
        
        searchTimeout = setTimeout(() => {
            performLiveSearch(searchTerm);
        }, 200);
    });
    
    // Clear search button
    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        clearSearch();
        searchInput.focus();
    });
    
    // Clear search results button
    if (clearSearchResults) {
        clearSearchResults.addEventListener('click', function() {
            searchInput.value = '';
            clearSearch();
        });
    }
    
    // Reset filters button
    resetFiltersBtn.addEventListener('click', function() {
        // Reset all form inputs
        const form = document.getElementById('filtersForm');
        form.reset();
        searchInput.value = '';
        clearSearch();
        
        // Redirect to clean URL
        window.location.href = window.location.pathname;
    });
    
    function performLiveSearch(searchTerm) {
        isSearching = true;
        const searchTermLower = searchTerm.toLowerCase();
        let visibleCount = 0;
        let matchedEvents = [];
        
        originalRows.forEach(row => {
            const searchableText = row.dataset.searchable || '';
            const matches = searchableText.includes(searchTermLower);
            
            if (matches) {
                row.style.display = '';
                row.classList.add('highlight-match');
                visibleCount++;
                matchedEvents.push(row);
                
                // Highlight matching text
                highlightMatches(row, searchTerm);
            } else {
                row.style.display = 'none';
                row.classList.remove('highlight-match');
                removeHighlights(row);
            }
        });
        
        // Update search results info
        updateSearchResultsInfo(visibleCount, searchTerm);
        
        // Hide pagination during search
        if (paginationSection) {
            paginationSection.style.display = visibleCount === 0 ? 'none' : 'none';
        }
        
        hideSearchSpinner();
        
        // Show no results message if needed
        showNoResultsMessage(visibleCount, searchTerm);
    }
    
    function clearSearch() {
        isSearching = false;
        
        // Show all rows
        originalRows.forEach(row => {
            row.style.display = '';
            row.classList.remove('highlight-match');
            removeHighlights(row);
        });
        
        // Hide search UI elements
        hideSearchSpinner();
        hideClearButton();
        hideSearchResultsInfo();
        hideNoResultsMessage();
        
        // Show pagination
        if (paginationSection) {
            paginationSection.style.display = 'flex';
        }
    }
    
    function highlightMatches(row, searchTerm) {
        const elements = row.querySelectorAll('h6, small, .text-muted');
        const regex = new RegExp(`(${escapeRegex(searchTerm)})`, 'gi');
        
        elements.forEach(element => {
            if (!element.innerHTML.includes('<span class="search-match">')) {
                element.innerHTML = element.innerHTML.replace(regex, '<span class="search-match">$1</span>');
            }
        });
    }
    
    function removeHighlights(row) {
        const highlights = row.querySelectorAll('.search-match');
        highlights.forEach(highlight => {
            const parent = highlight.parentNode;
            parent.replaceChild(document.createTextNode(highlight.textContent), highlight);
            parent.normalize();
        });
    }
    
    function escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
    
    function updateSearchResultsInfo(count, searchTerm) {
        if (count > 0) {
            searchResultsText.textContent = `Found ${count} event${count !== 1 ? 's' : ''} matching "${searchTerm}"`;
            searchResultsInfo.style.display = 'block';
        } else {
            hideSearchResultsInfo();
        }
    }
    
    function showNoResultsMessage(visibleCount, searchTerm) {
        let noResultsRow = document.getElementById('noSearchResultsRow');
        
        if (visibleCount === 0 && searchTerm !== '') {
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.id = 'noSearchResultsRow';
                noResultsRow.innerHTML = `
                    <td colspan="9" class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted mb-2">No events found for "${searchTerm}"</h6>
                        <p class="text-muted mb-3">Try adjusting your search terms or check your spelling</p>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('liveSearchInput').value=''; clearSearch(); document.getElementById('liveSearchInput').focus();">
                            <i class="fas fa-times me-1"></i>Clear Search
                        </button>
                    </td>
                `;
                eventsTableBody.appendChild(noResultsRow);
            }
        }
    }
    
    function hideNoResultsMessage() {
        const noResultsRow = document.getElementById('noSearchResultsRow');
        if (noResultsRow) {
            noResultsRow.remove();
        }
    }
    
    function showSearchSpinner() {
        searchSpinner.style.display = 'block';
    }
    
    function hideSearchSpinner() {
        searchSpinner.style.display = 'none';
    }
    
    function showClearButton() {
        clearSearchBtn.style.display = 'block';
    }
    
    function hideClearButton() {
        clearSearchBtn.style.display = 'none';
    }
    
    function hideSearchResultsInfo() {
        searchResultsInfo.style.display = 'none';
    }
}

// Initialize delete buttons
function initializeDeleteButtons() {
    const deleteButtons = document.querySelectorAll('.delete-btn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const eventId = this.dataset.eventId;
            const title = this.dataset.title;
            const isRecurring = this.dataset.isRecurring === 'true';
            
            let confirmText = `Are you sure you want to delete "${title}"?`;
            let confirmButtonText = 'Yes, delete it!';
            
            if (isRecurring) {
                confirmText = `This is a recurring event. How would you like to proceed?`;
            }
            
            if (isRecurring) {
                // Special handling for recurring events
                Swal.fire({
                    title: 'Delete Recurring Event',
                    text: confirmText,
                    icon: 'warning',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Delete entire series',
                    denyButtonText: 'Delete only this event',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#d33',
                    denyButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteEvent(eventId, true); // Delete series
                    } else if (result.isDenied) {
                        deleteEvent(eventId, false); // Delete single
                    }
                });
            } else {
                // Regular delete confirmation
                Swal.fire({
                    title: 'Are you sure?',
                    text: confirmText,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: confirmButtonText
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteEvent(eventId, false);
                    }
                });
            }
        });
    });
}

function deleteEvent(eventId, deleteSeries = false) {
    const form = document.getElementById('deleteForm');
    form.action = `/admin/events/${eventId}`;
    
    // Add delete_series parameter if needed
    if (deleteSeries) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_series';
        input.value = '1';
        form.appendChild(input);
    }
    
    form.submit();
}

// Main DOMContentLoaded event listener
document.addEventListener('DOMContentLoaded', function() {
    initializeLiveSearch();
    initializeDeleteButtons();
    
    // Load create form when modal is shown
    const createModal = document.getElementById('createEventModal');
    if (createModal) {
        createModal.addEventListener('show.bs.modal', function () {
            loadCreateForm();
        });
    }

    // Search with debounce for regular filter
    let searchTimeout;
    const filterSearchInput = document.querySelector('input[name="search"]');
    if (filterSearchInput) {
        filterSearchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => this.form.submit(), 500);
        });
    }
});
jQuery(document).ready(function($) {
    // File upload handling
    const fileUpload = $('#verification_document');
    const fileUploadContainer = $('.file-upload-container');
    let dragCounter = 0;

    fileUploadContainer.on('dragenter', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dragCounter++;
        $(this).addClass('dragging');
    });

    fileUploadContainer.on('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dragCounter--;
        if (dragCounter === 0) {
            $(this).removeClass('dragging');
        }
    });

    fileUploadContainer.on('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dragCounter = 0;
        $(this).removeClass('dragging');

        const files = e.originalEvent.dataTransfer.files;
        fileUpload[0].files = files;
        handleFileSelection(files[0]);
    });

    fileUpload.on('change', function() {
        handleFileSelection(this.files[0]);
    });

    function handleFileSelection(file) {
        if (!file) return;

        // Validate file type
        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
        if (!allowedTypes.includes(file.type)) {
            showToast('Please upload a PDF or image file.', 'error');
            fileUpload.val('');
            return;
        }

        // Validate file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            showToast('File size must be less than 5MB.', 'error');
            fileUpload.val('');
            return;
        }

        // Update UI with file name
        $('.file-name').text(file.name);
        $('.file-upload-message').text('File selected');
    }

    // Form submission
    $('#claim-listing-form').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const submitButton = form.find('button[type="submit"]');

        // Validate required fields
        const requiredFields = form.find('[required]');
        let isValid = true;

        requiredFields.each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('error');
            } else {
                $(this).removeClass('error');
            }
        });

        if (!isValid) {
            showToast('Please fill in all required fields.', 'error');
            return;
        }

        // Disable submit button and show loading state
        submitButton.prop('disabled', true).addClass('loading');

        // Create FormData object
        const formData = new FormData(this);
        formData.append('action', 'process_claim_request');
        formData.append('nonce', directory_listings.nonce);

        // Submit form via AJAX
        $.ajax({
            url: directory_listings.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showToast('Claim request submitted successfully!', 'success');
                    setTimeout(() => {
                        window.location.href = directory_listings.redirect_url;
                    }, 2000);
                } else {
                    showToast(response.data || 'Error submitting claim request.', 'error');
                    submitButton.prop('disabled', false).removeClass('loading');
                }
            },
            error: function() {
                showToast('Error submitting claim request. Please try again.', 'error');
                submitButton.prop('disabled', false).removeClass('loading');
            }
        });
    });

    // Toast notification function
    function showToast(message, type = 'success') {
        const toast = $('<div>')
            .addClass('toast toast-' + type)
            .text(message)
            .appendTo('body');

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // Phone number formatting
    $('input[name="phone"]').on('input', function() {
        let phone = $(this).val().replace(/\D/g, '');
        if (phone.length >= 10) {
            phone = phone.match(/(\d{3})(\d{3})(\d{4})/);
            $(this).val('(' + phone[1] + ') ' + phone[2] + '-' + phone[3]);
        }
    });

    // Terms agreement toggle
    $('#terms-agreement').on('change', function() {
        const submitButton = $('button[type="submit"]');
        submitButton.prop('disabled', !this.checked);
    });

    // Initialize tooltips
    $('[data-tooltip]').each(function() {
        $(this).tooltip({
            position: { my: 'left center', at: 'right+10 center' }
        });
    });
});
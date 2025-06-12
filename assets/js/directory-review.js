// File: assets/js/directory-review.js

jQuery(document).ready(function($) {
    const reviewForm = $('#directory-review-form');
    const starButtons = $('.star-rating-input .star-button');
    let selectedRating = 0;

    // Star Rating System
    function updateStars(rating) {
        starButtons.each(function(index) {
            $(this).toggleClass('active', index < rating);
        });
    }

    starButtons.on('click', function() {
        selectedRating = $(this).data('rating');
        updateStars(selectedRating);
    });

    starButtons.hover(
        function() {
            updateStars($(this).data('rating'));
        },
        function() {
            updateStars(selectedRating);
        }
    );

    // Form Submission
    reviewForm.on('submit', function(e) {
        e.preventDefault();
        
        if (!selectedRating) {
            alert('Please select a rating');
            return;
        }

        const formData = new FormData(this);
        formData.append('action', 'submit_directory_review');
        formData.append('rating', selectedRating);
        formData.append('nonce', directory_review.nonce);

        $.ajax({
            url: directory_review.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                reviewForm.find('button[type="submit"]').prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    const successMessage = $('<div class="review-success-message">')
                        .text(response.data)
                        .insertBefore(reviewForm)
                        .fadeIn();

                    // Reset form
                    reviewForm[0].reset();
                    selectedRating = 0;
                    updateStars(0);

                    // Reload after delay
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    alert(response.data || 'Error submitting review');
                }
            },
            error: function() {
                alert('Error submitting review. Please try again.');
            },
            complete: function() {
                reviewForm.find('button[type="submit"]').prop('disabled', false)
                    .html('Submit Review');
            }
        });
    });

    // Handle helpful votes
    $('.helpful-button').on('click', function() {
        const button = $(this);
        const reviewId = button.data('review-id');

        $.ajax({
            url: directory_review.ajaxurl,
            type: 'POST',
            data: {
                action: 'mark_directory_review_helpful',
                review_id: reviewId,
                nonce: directory_review.helpful_nonce
            },
            beforeSend: function() {
                button.prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    button.html(`<i class="fas fa-thumbs-up"></i> Helpful (${response.data.new_count})`)
                        .addClass('voted');
                }
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });

    // Date validation
    const dateInput = $('input[name="date_of_service"]');
    if (dateInput.length) {
        dateInput.attr('max', new Date().toISOString().split('T')[0]);
    }
});
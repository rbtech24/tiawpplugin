jQuery(document).ready(function($) {
    // Star rating functionality
    $('.star-rating-input i').on('click', function() {
        var $this = $(this);
        var rating = $this.data('rating');
        var $container = $this.parent();
        var inputId = $container.data('rating-input');
        
        $container.find('i').removeClass('fas').addClass('far');
        $container.find('i').each(function(index) {
            if (index < rating) {
                $(this).removeClass('far').addClass('fas');
            }
        });
        
        $('#' + inputId).val(rating);
    });

    // Form submission
    $('#submit-restaurant-review-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: restaurant_review_ajax.ajax_url,
            type: 'POST',
            data: formData + '&action=submit_restaurant_review&nonce=' + restaurant_review_ajax.nonce,
            success: function(response) {
                if (response.success) {
                    alert('Review submitted successfully!');
                    location.reload();
                } else {
                    alert('Error submitting review. Please try again.');
                }
            }
        });
    });
});
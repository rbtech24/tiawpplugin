//sidebar
jQuery(document).ready(function($) {
    let offset = 0; // Track current offset for loaded categories
    const categoriesPerPage = 10; // Number of categories to load each time
    let currentLetter = 'A'; // Default letter

    // Function to load more categories
    function loadMoreCategories(letter) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'load_more_categories',
                letter: letter,
                offset: offset
            },
            success: function(response) {
                if (response.success) {
                    $('.business-category-list').append(response.data.html);
                    offset += categoriesPerPage;

                    // Hide "Load More" button if no more categories
                    if (!response.data.has_more) {
                        $('.load-more-button').hide();
                    }
                } else {
                    alert(response.data);
                }
            },
            error: function() {
                alert('Error loading categories.');
            }
        });
    }

    // Alphabet filter click event
    $(document).on('click', '.alphabet-link', function(e) {
        e.preventDefault();
        currentLetter = $(this).text();
        offset = 0; // Reset offset for new letter
        $('.business-category-list').empty(); // Clear previous categories
        $('.load-more-button').show(); // Show "Load More" button
        loadMoreCategories(currentLetter);
    });

    // "Load More" button click event
    $(document).on('click', '.load-more-button', function(e) {
        e.preventDefault();
        loadMoreCategories(currentLetter);
    });
});


jQuery(document).ready(function($) {
    // Search input keyup event for live search
    $('#category-search-input').on('keyup', function() {
        const searchTerm = $(this).val();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'search_categories',
                term: searchTerm
            },
            success: function(response) {
                if (response.success) {
                    $('.business-category-list').html(response.data); // Display results
                } else {
                    $('.business-category-list').html('<li>' + response.data + '</li>');
                }
            },
            error: function() {
                $('.business-category-list').html('<li>Error searching categories.</li>');
            }
        });
    });
});

//sidebar ends


jQuery(document).ready(function($) {
    // Question submission
    $('#question-form').on('submit', function(e) {
        e.preventDefault();
        var title = $('#question-title').val();
        var content = $('#question-content').val();
        var category = $('#question-category').val();

        $.ajax({
            url: qa_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'submit_question',
                title: title,
                content: content,
                category: category,
                nonce: qa_ajax.submit_nonce
            },
            beforeSend: function() {
                $('#question-form button').prop('disabled', true).text('Submitting...');
            },
            success: function(response) {
                if (response.success) {
                    alert('Your question has been submitted successfully!');
                    $('#question-title').val('');
                    $('#question-content').val('');
                    $('#question-category').val('');
                    location.reload(); // Reload to show the new question
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            },
            complete: function() {
                $('#question-form button').prop('disabled', false).text('Submit Question');
            }
        });
    });

    // Voting functionality
    $('.vote-btn').on('click', function() {
       // alert('hi');
        var $btn = $(this);
        var postId = $btn.data('post-id');
        var voteType = $btn.data('vote-type');

        $.ajax({
            url: qa_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'handle_vote',
                post_id: postId,
                vote_type: voteType,
                nonce: qa_ajax.vote_nonce
            },
            beforeSend: function() {
                $btn.prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                     var score=$('.score').text();
                   // var $voteCount = $btn.siblings('.' + voteType);
                 
                   // alert(voteCount);
                    if(voteType=='Upvote'){
                          var voteCount=$('.Upvote-count').text();
                    $('.Upvote-count').text(parseInt(voteCount) + 1);
                    $('.score').text(parseInt(score) + 1);
                    }
                    if(voteType=='Downvote'){
                          var voteCount=$('.Downvote-count').text();
                    $('.Downvote-count').text(parseInt(voteCount) + 1);
                     $('.score').text(parseInt(score) - 1);
                    }
                    
                    
                } else {
                   //  var score=$('.score').text();
                    //  $('.score').text(parseInt(score) + 1);
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });

    // Mark question as resolved
    $('.resolve-btn').on('click', function() {
        var $btn = $(this);
        var postId = $btn.data('post-id');

        $.ajax({
            url: qa_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mark_question_resolved',
                post_id: postId,
                nonce: qa_ajax.resolve_nonce
            },
            beforeSend: function() {
                $btn.prop('disabled', true).text('Marking as resolved...');
            },
            success: function(response) {
                if (response.success) {
                    $btn.replaceWith('<span class="resolved-badge">Resolved</span>');
                } else {
                    alert('Error: ' + response.data);
                    $btn.prop('disabled', false).text('Mark as Resolved');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
                $btn.prop('disabled', false).text('Mark as Resolved');
            }
        });
    });

    // Optional: Add smooth scrolling to the "Ask a Question" section
    $('.ask-question-link').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: $("#question-form").offset().top
        }, 1000);
    });

    // Optional: Add character count for question content
    $('#question-content').on('input', function() {
        var maxLength = 1000; // Set your desired max length
        var currentLength = $(this).val().length;
        var remainingChars = maxLength - currentLength;
        
        if ($('#char-count').length === 0) {
            $(this).after('<div id="char-count"></div>');
        }
        
        $('#char-count').text(remainingChars + ' characters remaining');
        
        if (remainingChars < 0) {
            $('#char-count').css('color', 'red');
        } else {
            $('#char-count').css('color', '');
        }
    });
});
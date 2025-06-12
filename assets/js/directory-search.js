jQuery(document).ready(function($) {
    // Initialize variables
    const searchForm = $('#directory-search-form');
    const searchInput = $('#search-input');
    const categorySelect = $('#category-select');
    const stateSelect = $('#state-select');
    const searchBtn = $('#search-btn');
    const resultsContainer = $('#search-results');
    
    // Handle form submission
    searchForm.on('submit', function(e) {
        e.preventDefault();
        performSearch();
    });

    // Function to perform the search
    function performSearch() {
        // Show loading indicator
        resultsContainer.html('<div class="loading">Loading results...</div>');
        resultsContainer.show();

        // Collect data
        const data = {
            action: 'filter_listings',
            nonce: directory_search_data.nonce,
            search: searchInput.val(),
            category_id: categorySelect.val(),
            state: stateSelect.val()
        };

        // Send AJAX request
        $.ajax({
            url: directory_search_data.ajax_url,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    resultsContainer.html(response.data.html);
                    
                    // Add result summary
                    if (response.data.found > 0) {
                        resultsContainer.prepend('<div class="search-summary">Found ' + response.data.found + ' listings</div>');
                    }
                    
                    // Scroll to results
                    $('html, body').animate({
                        scrollTop: resultsContainer.offset().top - 100
                    }, 500);
                } else {
                    resultsContainer.html('<p class="error">Error loading results.</p>');
                }
            },
            error: function() {
                resultsContainer.html('<p class="error">Error connecting to server.</p>');
            }
        });
    }
});
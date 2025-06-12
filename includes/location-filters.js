jQuery(document).ready(function($) {
    const stateFilter = $('#state-filter');
    const cityFilter = $('#city-filter');
    const categoryId = $('.location-filters').data('category-id');
    const listingContainer = $('.listings-grid'); // Assuming this is where your listings are displayed

    stateFilter.on('change', function() {
        const state = $(this).val();
        cityFilter.prop('disabled', !state);
        
        // Reset city filter
        cityFilter.html('<option value="">All Cities</option>');
        
        if (state) {
            // Fetch cities for selected state
            $.ajax({
                url: directory_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_cities',
                    state: state,
                    category_id: categoryId,
                    nonce: directory_ajax.nonce
                },
                success: function(response) {
                    if (response.success && response.data.cities) {
                        let options = '<option value="">All Cities</option>';
                        response.data.cities.forEach(function(city) {
                            options += `<option value="${city}">${city}</option>`;
                        });
                        cityFilter.html(options);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch cities:', error);
                }
            });
        }
        
        // Use AJAX to update listings without full page reload
        updateListings();
    });

    cityFilter.on('change', updateListings);

    function updateListings() {
        const state = stateFilter.val();
        const city = cityFilter.val();
        
        $.ajax({
            url: directory_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'filter_listings',
                state: state,
                city: city,
                category_id: categoryId,
                nonce: directory_ajax.nonce
            },
            success: function(response) {
                if (response.success && response.data.html) {
                    listingContainer.html(response.data.html);
                    
                    // Update URL without reloading page
                    const newUrl = updateUrlParams(state, city);
                    history.pushState({}, '', newUrl);
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to filter listings:', error);
                listingContainer.html('<p class="no-results">An error occurred while fetching listings.</p>');
            }
        });
    }

    function updateUrlParams(state, city) {
        let url = new URL(window.location);
        if (state) url.searchParams.set('state', state);
        else url.searchParams.delete('state');
        if (city) url.searchParams.set('city', city);
        else url.searchParams.delete('city');
        return url.toString();
    }
});
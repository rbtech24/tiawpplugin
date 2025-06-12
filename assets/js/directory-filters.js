jQuery(document).ready(function($) {
    // Check for required global variables
    window.directoryAjax = window.directoryAjax || {
        ajaxurl: '/wp-admin/admin-ajax.php',
        nonce: '',
        category_id: 0
    };

    // Initialize variables
    const stateFilter = $('#state-filter');
    const cityFilter = $('#city-filter');
    const sortFilter = $('select[name="sort"]');
    const searchInput = $('.search-input input');
    const listingsGrid = $('.listings-grid');
    const searchBtn = $('.search-btn');
    let searchTimeout;

    // Handle state changes
    stateFilter.on('change', function() {
        const selectedState = $(this).val();
        cityFilter.prop('disabled', !selectedState);
        
        if (selectedState) {
            $.ajax({
                url: window.directoryAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_cities',
                    state: selectedState,
                    category_id: window.directoryAjax.category_id,
                    nonce: window.directoryAjax.nonce
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
                    console.error('Error fetching cities:', error);
                }
            });
        } else {
            cityFilter.html('<option value="">All Cities</option>');
        }
        
        updateListings();
    });

    // Handle city and sort changes
    cityFilter.add(sortFilter).on('change', function() {
        updateListings();
    });

    // Handle search input
    searchInput.on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            updateListings();
        }, 500);
    });

    // Handle search button click
    searchBtn.on('click', function() {
        updateListings();
    });

    function updateListings(page = 1) {
        const data = {
            action: 'filter_listings',
            nonce: window.directoryAjax.nonce,
            state: stateFilter.val(),
            city: cityFilter.val(),
            sort: sortFilter.val(),
            search: searchInput.val(),
            category_id: window.directoryAjax.category_id,
            paged: page
        };

        listingsGrid.addClass('loading');
        listingsGrid.html('<div class="loading-spinner">Loading...</div>');

        $.ajax({
            url: window.directoryAjax.ajaxurl,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    listingsGrid.html(response.data.html);
                    updateURL();
                    if (response.data.found_posts !== undefined) {
                        updateResultCount(response.data.found_posts);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error updating listings:', error);
                listingsGrid.html('<div class="error">Error loading listings. Please try again.</div>');
            },
            complete: function() {
                listingsGrid.removeClass('loading');
            }
        });
    }

    function updateURL() {
        const params = new URLSearchParams(window.location.search);
        const filters = {
            'state': stateFilter.val(),
            'city': cityFilter.val(),
            'sort': sortFilter.val(),
            'search': searchInput.val()
        };

        Object.entries(filters).forEach(([key, value]) => {
            if (value) {
                params.set(key, value);
            } else {
                params.delete(key);
            }
        });

        window.history.replaceState(
            {}, 
            '', 
            `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`
        );
    }

    function updateResultCount(count) {
        const countElement = $('.results-count');
        if (countElement.length) {
            countElement.text(`${count} listings found`);
        }
    }

    // Initialize filters from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('state')) {
        stateFilter.val(urlParams.get('state')).trigger('change');
    }
    if (urlParams.has('sort')) {
        sortFilter.val(urlParams.get('sort'));
    }
    if (urlParams.has('search')) {
        searchInput.val(urlParams.get('search'));
    }
});
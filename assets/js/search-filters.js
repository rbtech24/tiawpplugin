// assets/js/search-filters.js

(function($) {
    'use strict';

    class DirectorySearch {
        constructor() {
            this.searchForm = $('#directory-filter-form');
            this.stateSelect = $('#state-filter');
            this.citySelect = $('#city-filter');
            this.filtersModal = $('#filters-modal');
            this.mapModal = $('#map-modal');
            this.resultsContainer = $('.listings-grid');
            this.loadingClass = 'loading';
            
            this.initializeSearchHandlers();
            this.initializeMapView();
            this.initializeQuickView();
        }

        initializeSearchHandlers() {
            // State Change Handler
            this.stateSelect.on('change', (e) => this.handleStateChange(e));

            // Form Submit
            this.searchForm.on('submit', (e) => this.handleFormSubmit(e));

            // Live Search
            let searchTimeout;
            $('#search-input').on('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => this.handleLiveSearch(e), 300);
            });

            // Filter Toggle
            $('.toggle-filters').on('click', () => this.filtersModal.show());
            $('.modal-close').on('click', () => $('.search-modal').hide());

            // Active Filter Tags
            $('.filter-tag .remove').on('click', (e) => this.removeFilter(e));

            // Sort Change
            $('#sort-filter').on('change', () => this.searchForm.submit());

            // Rating Range
            $('#rating-range').on('input', (e) => {
                $('.current-rating').text($(e.target).val());
            });
        }

        async handleStateChange(e) {
            const stateSlug = $(e.target).val();
            this.citySelect.prop('disabled', !stateSlug);
            this.citySelect.html('<option value="">All Cities</option>');
            
            if (!stateSlug) return;

            try {
                this.citySelect.prop('disabled', true);
                const response = await $.ajax({
                    url: directorySearchParams.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'get_cities_by_state',
                        state: stateSlug,
                        nonce: directorySearchParams.nonce
                    }
                });

                if (response.success && response.data.cities) {
                    response.data.cities.forEach(city => {
                        this.citySelect.append(new Option(city, city));
                    });
                }
            } catch (error) {
                console.error('Error loading cities:', error);
            } finally {
                this.citySelect.prop('disabled', false);
            }
        }

        async handleLiveSearch(e) {
            const searchTerm = $(e.target).val();
            if (searchTerm.length < 2) return;

            try {
                const response = await $.ajax({
                    url: directorySearchParams.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'live_search_listings',
                        term: searchTerm,
                        state: this.stateSelect.val(),
                        city: this.citySelect.val(),
                        nonce: directorySearchParams.nonce
                    }
                });

                if (response.success) {
                    this.updateLiveSearchResults(response.data);
                }
            } catch (error) {
                console.error('Search error:', error);
            }
        }

        updateLiveSearchResults(results) {
            const container = $('.live-search-results');
            container.empty();

            if (results.length === 0) {
                container.append(`<div class="no-results">${directorySearchParams.noResults}</div>`);
                return;
            }

            const list = $('<ul class="search-suggestions"></ul>');
            results.forEach(result => {
                list.append(`
                    <li>
                        <a href="${result.permalink}">
                            <div class="suggestion-content">
                                <h4>${result.title}</h4>
                                <span>${result.city}, ${result.state}</span>
                                ${result.rating ? `<div class="rating">${this.getStarRating(result.rating)}</div>` : ''}
                            </div>
                        </a>
                    </li>
                `);
            });
            container.append(list);
            container.show();
        }

        handleFormSubmit(e) {
            e.preventDefault();
            this.resultsContainer.addClass(this.loadingClass);

            const formData = new FormData(e.target);
            const searchParams = new URLSearchParams(formData);
            
            // Update URL without reload
            window.history.pushState(
                {}, 
                '', 
                `${window.location.pathname}?${searchParams.toString()}`
            );

            this.fetchResults(searchParams);
        }

        async fetchResults(params) {
            try {
                const response = await $.ajax({
                    url: window.location.href,
                    type: 'GET',
                    data: params
                });

                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = response;
                
                const newResults = $(tempDiv).find('.listings-grid').html();
                this.resultsContainer.html(newResults);

                // Update pagination if exists
                const newPagination = $(tempDiv).find('.pagination-wrapper').html();
                if (newPagination) {
                    $('.pagination-wrapper').html(newPagination);
                }

            } catch (error) {
                console.error('Error fetching results:', error);
                this.resultsContainer.html('<div class="error-message">Error loading results</div>');
            } finally {
                this.resultsContainer.removeClass(this.loadingClass);
            }
        }

        removeFilter(e) {
            e.preventDefault();
            const filter = $(e.target).data('filter');
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.delete(filter);
            
            window.location.search = urlParams.toString();
        }

        getStarRating(rating) {
            const fullStars = Math.floor(rating);
            const halfStar = rating % 1 >= 0.5;
            const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);

            return `
                ${'★'.repeat(fullStars)}
                ${halfStar ? '⯨' : ''}
                ${'☆'.repeat(emptyStars)}
                <span class="rating-number">${rating}</span>
            `;
        }

        initializeMapView() {
            if (!window.google || !directorySearchParams.mapApiKey) return;

            const mapButton = $('.view-map-btn');
            if (mapButton.length === 0) return;

            mapButton.on('click', () => {
                this.mapModal.show();
                this.initializeMap();
            });
        }

        initializeMap() {
            if (this.map) return;

            const mapElement = document.getElementById('listings-map');
            if (!mapElement) return;

            this.map = new google.maps.Map(mapElement, {
                zoom: 12,
                center: { lat: -34.397, lng: 150.644 }
            });

            this.loadMapMarkers();
        }

        async loadMapMarkers() {
            try {
                const response = await $.ajax({
                    url: directorySearchParams.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'get_listings_locations',
                        nonce: directorySearchParams.nonce
                    }
                });

                if (response.success) {
                    this.addMarkersToMap(response.data);
                }
            } catch (error) {
                console.error('Error loading map markers:', error);
            }
        }

        addMarkersToMap(locations) {
            const bounds = new google.maps.LatLngBounds();

            locations.forEach(location => {
                const marker = new google.maps.Marker({
                    position: { 
                        lat: parseFloat(location.lat), 
                        lng: parseFloat(location.lng) 
                    },
                    map: this.map,
                    title: location.title
                });

                bounds.extend(marker.getPosition());

                const infoWindow = new google.maps.InfoWindow({
                    content: this.getInfoWindowContent(location)
                });

                marker.addListener('click', () => {
                    infoWindow.open(this.map, marker);
                });
            });

            this.map.fitBounds(bounds);
        }

        getInfoWindowContent(location) {
            return `
                <div class="map-info-window">
                    <h3>${location.title}</h3>
                    <p>${location.address}</p>
                    ${location.rating ? `
                        <div class="rating">
                            ${this.getStarRating(location.rating)}
                        </div>
                    ` : ''}
                    <a href="${location.url}" class="btn-primary">View Details</a>
                </div>
            `;
        }

        initializeQuickView() {
            $('.quick-view-btn').on('click', async (e) => {
                e.preventDefault();
                const listingId = $(e.target).data('listing-id');
                
                try {
                    const response = await $.ajax({
                        url: directorySearchParams.ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'get_listing_quick_view',
                            listing_id: listingId,
                            nonce: directorySearchParams.nonce
                        }
                    });

                    if (response.success) {
                        this.showQuickViewModal(response.data);
                    }
                } catch (error) {
                    console.error('Error loading quick view:', error);
                }
            });
        }

        showQuickViewModal(data) {
            const modal = $('#quick-view-modal');
            modal.find('.modal-body').html(data.html);
            modal.show();
        }
    }

    // Initialize on document ready
    $(document).ready(() => {
        new DirectorySearch();
    });

})(jQuery);
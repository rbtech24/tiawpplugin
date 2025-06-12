// templates/search/filter-form.php

<div class="directory-search-wrapper">
    <div class="search-filters">
        <form id="directory-filter-form" method="GET">
            <div class="filter-grid">
                <!-- State Filter -->
                <div class="filter-group">
                    <label for="state-filter">State</label>
                    <select name="state" id="state-filter" class="filter-select">
                        <option value="">All States</option>
                        <?php
                        $states = get_terms(array(
                            'taxonomy' => 'state',
                            'hide_empty' => true
                        ));
                        
                        foreach($states as $state) {
                            $selected = isset($_GET['state']) && $_GET['state'] === $state->slug ? 'selected' : '';
                            printf(
                                '<option value="%s" %s>%s</option>',
                                esc_attr($state->slug),
                                $selected,
                                esc_html($state->name)
                            );
                        }
                        ?>
                    </select>
                </div>

                <!-- City Filter -->
                <div class="filter-group">
                    <label for="city-filter">City</label>
                    <select name="city" id="city-filter" class="filter-select" <?php echo empty($_GET['state']) ? 'disabled' : ''; ?>>
                        <option value="">All Cities</option>
                        <?php
                        if (!empty($_GET['state'])) {
                            $cities = Directory_Search_Filter::get_instance()->get_state_cities($_GET['state']);
                            foreach($cities as $city) {
                                $selected = isset($_GET['city']) && $_GET['city'] === $city ? 'selected' : '';
                                printf(
                                    '<option value="%s" %s>%s</option>',
                                    esc_attr($city),
                                    $selected,
                                    esc_html($city)
                                );
                            }
                        }
                        ?>
                    </select>
                </div>

                <!-- Rating Filter -->
                <div class="filter-group">
                    <label for="rating-filter">Rating</label>
                    <select name="rating" id="rating-filter" class="filter-select">
                        <option value="">Any Rating</option>
                        <?php
                        $ratings = range(5, 1);
                        foreach($ratings as $rating) {
                            $selected = isset($_GET['rating']) && $_GET['rating'] == $rating ? 'selected' : '';
                            printf(
                                '<option value="%d" %s>%d+ Stars</option>',
                                $rating,
                                $selected,
                                $rating
                            );
                        }
                        ?>
                    </select>
                </div>

                <!-- Sort -->
                <div class="filter-group">
                    <label for="sort-filter">Sort By</label>
                    <select name="sort" id="sort-filter" class="filter-select">
                        <option value="">Most Relevant</option>
                        <option value="rating" <?php selected($_GET['sort'] ?? '', 'rating'); ?>>Highest Rated</option>
                        <option value="reviews" <?php selected($_GET['sort'] ?? '', 'reviews'); ?>>Most Reviews</option>
                    </select>
                </div>

                <button type="submit" class="filter-submit">Apply Filters</button>
            </div>
        </form>
    </div>
</div>
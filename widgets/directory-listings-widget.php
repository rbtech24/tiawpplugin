<?php
class Directory_Listings_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'directory_listings';
    }

    public function get_title() {
        return __('Directory Listings', 'directory-listings');
    }

    public function get_icon() {
        return 'eicon-posts-grid';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'directory-listings'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'filter_type',
            [
                'label' => __('Filter Type', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'auto',
                'options' => [
                    'auto' => __('Automatic (by location/category)', 'directory-listings'),
                    'manual' => __('Manual Selection', 'directory-listings'),
                ],
            ]
        );

        $this->add_control(
            'state',
            [
                'label' => __('State', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $this->get_states(),
                'condition' => [
                    'filter_type' => 'auto',
                ],
            ]
        );

        $this->add_control(
            'city',
            [
                'label' => __('City', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [],
                'condition' => [
                    'filter_type' => 'auto',
                    'state!' => '',
                ],
            ]
        );

        $this->add_control(
            'category',
            [
                'label' => __('Category', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $this->get_categories(),
                'condition' => [
                    'filter_type' => 'auto',
                ],
            ]
        );

        $this->add_control(
            'subcategory',
            [
                'label' => __('Subcategory', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [],
                'condition' => [
                    'filter_type' => 'auto',
                    'category!' => '',
                ],
            ]
        );

        $this->add_control(
            'manual_listings',
            [
                'label' => __('Select Listings', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => $this->get_all_listings(),
                'multiple' => true,
                'condition' => [
                    'filter_type' => 'manual',
                ],
            ]
        );

        $this->add_control(
            'number_of_listings',
            [
                'label' => __('Number of Listings', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 100,
                'step' => 1,
                'default' => 6,
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        // Fetch and display listings based on settings
        $args = [
            'post_type' => 'directory_listing',
            'posts_per_page' => $settings['number_of_listings'],
        ];

        if ($settings['filter_type'] === 'auto') {
            $tax_query = [];

            if (!empty($settings['state'])) {
                $tax_query[] = [
                    'taxonomy' => 'state',
                    'field' => 'term_id',
                    'terms' => $settings['state'],
                ];
            }

            if (!empty($settings['city'])) {
                $tax_query[] = [
                    'taxonomy' => 'state',
                    'field' => 'term_id',
                    'terms' => $settings['city'],
                ];
            }

            if (!empty($settings['category'])) {
                $tax_query[] = [
                    'taxonomy' => 'business_category',
                    'field' => 'term_id',
                    'terms' => $settings['category'],
                ];
            }

            if (!empty($settings['subcategory'])) {
                $tax_query[] = [
                    'taxonomy' => 'business_category',
                    'field' => 'term_id',
                    'terms' => $settings['subcategory'],
                ];
            }

            if (!empty($tax_query)) {
                $args['tax_query'] = $tax_query;
            }
        } else {
            $args['post__in'] = $settings['manual_listings'];
        }

        $listings = new WP_Query($args);

        if ($listings->have_posts()) {
            echo '<div class="directory-listings-grid">';
            while ($listings->have_posts()) {
                $listings->the_post();
                $this->render_listing(get_the_ID());
            }
            echo '</div>';
        } else {
            echo __('No listings found', 'directory-listings');
        }

        wp_reset_postdata();

        // Output the inline styles
        echo '<style>' . $this->get_widget_css() . '</style>';

        // Output the inline script
        echo '<script>' . $this->get_widget_js() . '</script>';
    }

    private function render_listing($post_id) {
        $title = get_the_title($post_id);
        $logo = get_field('logo', $post_id);
        $rating = get_field('overall_rating', $post_id);
        $review_count = get_field('review_count', $post_id);
        $description = get_field('business_description', $post_id);
        $phone = get_field('phone_number', $post_id);

        echo '<div class="listing-card">';
        if ($logo) {
            echo '<div class="listing-image"><img src="' . esc_url($logo['url']) . '" alt="' . esc_attr($title) . '"></div>';
        }
        echo '<div class="listing-content">';
        echo '<h2 class="listing-title">' . esc_html($title) . '</h2>';
        if ($rating) {
            echo '<div class="listing-rating">★' . esc_html($rating) . ' (' . esc_html($review_count) . ' reviews)</div>';
        }
        echo '<p class="listing-description">' . wp_trim_words($description, 20) . '</p>';
        echo '<div class="listing-cta">';
        echo '<a href="' . get_permalink($post_id) . '" class="btn btn-primary">View Profile</a>';
        if ($phone) {
            echo '<a href="tel:' . esc_attr($phone) . '" class="btn btn-secondary">Call Now</a>';
        }
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    private function get_states() {
        $states = get_terms([
            'taxonomy' => 'state',
            'hide_empty' => false,
            'parent' => 0,
        ]);

        $options = ['' => __('Select a State', 'directory-listings')];
        foreach ($states as $state) {
            $options[$state->term_id] = $state->name;
        }

        return $options;
    }

    private function get_categories() {
        $categories = get_terms([
            'taxonomy' => 'business_category',
            'hide_empty' => false,
            'parent' => 0,
        ]);

        $options = ['' => __('Select a Category', 'directory-listings')];
        foreach ($categories as $category) {
            $options[$category->term_id] = $category->name;
        }

        return $options;
    }

    private function get_all_listings() {
        $listings = get_posts([
            'post_type' => 'directory_listing',
            'posts_per_page' => -1,
        ]);

        $options = [];
        foreach ($listings as $listing) {
            $options[$listing->ID] = $listing->post_title;
        }

        return $options;
    }

    private function get_widget_css() {
        return "
        .directory-listings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .listing-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            transition: box-shadow 0.3s ease;
        }
        .listing-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .listing-image {
            height: 200px;
            overflow: hidden;
        }
        .listing-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .listing-content {
            padding: 15px;
        }
        .listing-title {
            font-size: 1.2em;
            margin-bottom: 10px;
        }
        .listing-rating {
            color: #ffc107;
            margin-bottom: 10px;
        }
        .listing-description {
            font-size: 0.9em;
            margin-bottom: 15px;
        }
        .listing-cta {
            display: flex;
            justify-content: space-between;
        }
        .btn {
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        ";
    }

    private function get_widget_js() {
        return "
        jQuery(document).ready(function($) {
            $('.directory-listings-widget').each(function() {
                var widget = $(this);
                var stateSelect = widget.find('.state-select');
                var citySelect = widget.find('.city-select');
                var categorySelect = widget.find('.category-select');
                var subcategorySelect = widget.find('.subcategory-select');

                stateSelect.on('change', function() {
                    var stateId = $(this).val();
                    if (stateId) {
                        $.ajax({
                            url: directory_listings_ajax.ajax_url,
                            type: 'POST',
                            data: {
                                action: 'load_cities',
                                state_id: stateId,
                                nonce: directory_listings_ajax.nonce
                            },
                            success: function(response) {
                                citySelect.html('<option value=\"\">' + directory_listings_ajax.select_city + '</option>');
                                $.each(response, function(index, city) {
                                    citySelect.append($('<option></option>').attr('value', city.id).text(city.name));
                                });
                            }
                        });
                    } else {
                        citySelect.html('<option value=\"\">' + directory_listings_ajax.select_city + '</option>');
                    }
                });

                categorySelect.on('change', function() {
                    var categoryId = $(this).val();
                    if (categoryId) {
                        $.ajax({
                            url: directory_listings_ajax.ajax_url,
                            type: 'POST',
                            data: {
                                action: 'load_subcategories',
                                category_id: categoryId,
                                nonce: directory_listings_ajax.nonce
                            },
                            success: function(response) {
                                subcategorySelect.html('<option value=\"\">' + directory_listings_ajax.select_subcategory + '</option>');
                                $.each(response, function(index, subcategory) {
                                    subcategorySelect.append($('<option></option>').attr('value', subcategory.id).text(subcategory.name));
                                });
                            }
                        });
                    } else {
                        subcategorySelect.html('<option value=\"\">' + directory_listings_ajax.select_subcategory + '</option>');
                    }
                });
            });
        });
        ";
    }
}

// AJAX handlers (add these outside the class)
function load_cities_callback() {
    check_ajax_referer('directory_listings_nonce', 'nonce');
    
    $state_id = intval($_POST['state_id']);
    $cities = get_terms(array(
        'taxonomy' => 'state',
        'hide_empty' => false,
        'parent' => $state_id
    ));

    $city_options = array();
    foreach ($cities as $city) {
        $city_options[] = array(
            'id' => $city->term_id,
            'name' => $city->name
        );
    }

    wp_send_json($city_options);
}
add_action('wp_ajax_load_cities', 'load_cities_callback');
add_action('wp_ajax_nopriv_load_cities', 'load_cities_callback');

function load_subcategories_callback() {
    check_ajax_referer('directory_listings_nonce', 'nonce');
    
    $category_id = intval($_POST['category_id']);
    $subcategories = get_terms(array(
        'taxonomy' => 'business_category',
        'hide_empty' => false,
        'parent' => $category_id
    ));

    $subcategory_options = array();
    foreach ($subcategories as $subcategory) {
        $subcategory_options[] = array(
            'id' => $subcategory->term_id,
            'name' => $subcategory->name
        );
    }

    wp_send_json($subcategory_options);
}
add_action('wp_ajax_load_subcategories', 'load_subcategories_callback');
add_action('wp_ajax_nopriv_load_subcategories', 'load_subcategories_callback');

// Register the widget
function register_directory_listings_widget( $widgets_manager ) {
    $widgets_manager->register( new \Directory_Listings_Widget() );
}
add_action( 'elementor/widgets/register', 'register_directory_listings_widget' );

        
      // Localize script (add this to your main plugin file or a function that runs on init)
function directory_listings_localize_script() {
    wp_localize_script('elementor-frontend', 'directory_listings_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('directory_listings_nonce'),
        'select_city' => __('Select a City', 'directory-listings'),
        'select_subcategory' => __('Select a Subcategory', 'directory-listings')
    ));
}
add_action('wp_enqueue_scripts', 'directory_listings_localize_script');

/**
 * Explanation of the Directory Listings Widget:
 * 
 * 1. Widget Structure:
 *    - The widget extends Elementor's Widget_Base class.
 *    - It provides controls for filtering listings by state, city, category, and subcategory.
 *    - Users can choose between automatic filtering or manual selection of listings.
 * 
 * 2. Rendering:
 *    - The widget fetches listings based on the selected filters.
 *    - It displays listings in a responsive grid layout.
 *    - Each listing card shows the business logo, title, rating, description, and call-to-action buttons.
 * 
 * 3. Styling:
 *    - CSS is included inline for easy customization.
 *    - The layout uses CSS Grid for responsiveness.
 *    - Hover effects are added to listing cards for better user interaction.
 * 
 * 4. JavaScript Functionality:
 *    - The widget uses AJAX to dynamically load cities when a state is selected.
 *    - It also loads subcategories when a main category is selected.
 *    - The script is included inline and uses jQuery for DOM manipulation and AJAX requests.
 * 
 * 5. AJAX Handlers:
 *    - Two AJAX handlers are included: one for loading cities and another for loading subcategories.
 *    - These handlers are hooked to both logged-in and logged-out user actions.
 * 
 * 6. Localization:
 *    - The widget supports internationalization.
 *    - Translatable strings are passed to the JavaScript via wp_localize_script.
 * 
 * 7. Integration:
 *    - The widget is registered with Elementor using the 'elementor/widgets/register' hook.
 *    - It can be easily added to any Elementor-powered page.
 * 
 * Usage:
 * 1. Include this file in your theme or plugin.
 * 2. Ensure that the necessary taxonomies (state, business_category) and custom post type (directory_listing) are set up.
 * 3. The widget will appear in the Elementor editor under the 'general' category.
 * 4. Customize the appearance by modifying the inline CSS in the get_widget_css() method.
 * 5. Adjust the JavaScript functionality in the get_widget_js() method if needed.
 * 
 * Note: This widget assumes the existence of certain ACF fields (logo, overall_rating, review_count, business_description, phone_number).
 * Ensure these fields are set up in your ACF configuration for the directory_listing post type.
 */  
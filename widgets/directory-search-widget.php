<?php
class Directory_Search_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'directory_search';
    }

    public function get_title() {
        return __('Directory Search', 'directory-listings');
    }

    public function get_icon() {
        return 'eicon-search';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function _register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'directory-listings'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_keyword',
            [
                'label' => __('Show Keyword Search', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'directory-listings'),
                'label_off' => __('Hide', 'directory-listings'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_state',
            [
                'label' => __('Show State Search', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'directory-listings'),
                'label_off' => __('Hide', 'directory-listings'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_city',
            [
                'label' => __('Show City Search', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'directory-listings'),
                'label_off' => __('Hide', 'directory-listings'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_category',
            [
                'label' => __('Show Category Search', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'directory-listings'),
                'label_off' => __('Hide', 'directory-listings'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_subcategory',
            [
                'label' => __('Show Subcategory Search', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'directory-listings'),
                'label_off' => __('Hide', 'directory-listings'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_rating',
            [
                'label' => __('Show Rating Filter', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'directory-listings'),
                'label_off' => __('Hide', 'directory-listings'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'directory-listings'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'background_color',
            [
                'label' => __('Background Color', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#f0f0f0',
                'selectors' => [
                    '{{WRAPPER}} .directory-search-form' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Text Color', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .directory-search-form' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'border_color',
            [
                'label' => __('Border Color', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#bf0a30',
                'selectors' => [
                    '{{WRAPPER}} .directory-search-form' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'button_background_color',
            [
                'label' => __('Button Background Color', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#bf0a30',
                'selectors' => [
                    '{{WRAPPER}} .directory-search-form input[type="submit"]' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => __('Button Text Color', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .directory-search-form input[type="submit"]' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'selector' => '{{WRAPPER}} .directory-search-form',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <style>
            .directory-search-form {
                font-family: 'Arial', sans-serif;
                line-height: 1.6;
                padding: 30px;
                border-radius: 15px;
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                border: 3px solid <?php echo $settings['border_color']; ?>;
                position: relative;
                overflow: hidden;
                margin-bottom: 40px;
            }
            .directory-search-form::before {
                content: '';
                position: absolute;
                top: -50px;
                left: -50px;
                width: 100px;
                height: 100px;
                background-color: <?php echo $settings['border_color']; ?>;
                transform: rotate(45deg);
                z-index: 0;
            }
            .directory-search-form > * {
                position: relative;
                z-index: 1;
            }
            .directory-search-form h2 {
                color: #002868;
                text-align: center;
                margin-bottom: 30px;
                font-size: 32px;
                text-transform: uppercase;
                letter-spacing: 2px;
                border-bottom: 3px solid <?php echo $settings['border_color']; ?>;
                padding-bottom: 10px;
            }
            .directory-search-form label {
                display: block;
                margin-bottom: 10px;
                font-weight: bold;
            }
            .directory-search-form input[type="text"],
            .directory-search-form select {
                width: 100%;
                padding: 12px;
                margin-bottom: 20px;
                border: 2px solid #002868;
                border-radius: 5px;
                font-size: 16px;
            }
            .directory-search-form input[type="submit"] {
                width: 100%;
                padding: 15px 30px;
                border: none;
                border-radius: 50px;
                font-weight: bold;
                text-transform: uppercase;
                letter-spacing: 1px;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            .directory-search-form input[type="submit"]:hover {
                opacity: 0.9;
                transform: translateY(-3px);
                box-shadow: 0 6px 8px rgba(191, 10, 48, 0.3);
            }
            @media (max-width: 768px) {
                .directory-search-form h2 {
                    font-size: 28px;
                }
            }
            @media (max-width: 480px) {
                .directory-search-form h2 {
                    font-size: 24px;
                }
            }
        </style>
        <form role="search" method="get" class="directory-search-form" action="<?php echo esc_url(home_url('/')); ?>">
            <h2><?php _e('Find Local Businesses', 'directory-listings'); ?></h2>
            <input type="hidden" name="post_type" value="directory_listing" />
            
            <?php if ($settings['show_keyword'] === 'yes') : ?>
                <label for="keyword"><?php _e('Keyword:', 'directory-listings'); ?></label>
                <input type="text" id="keyword" name="s" value="<?php echo get_search_query(); ?>" placeholder="<?php _e('Search listings...', 'directory-listings'); ?>">
            <?php endif; ?>
            
            <?php if ($settings['show_state'] === 'yes') : ?>
                <label for="state"><?php _e('State:', 'directory-listings'); ?></label>
                <?php
                wp_dropdown_categories(array(
                    'show_option_all' => __('All States', 'directory-listings'),
                    'taxonomy'        => 'state',
                    'name'            => 'state',
                    'id'              => 'state',
                    'orderby'         => 'name',
                    'selected'        => isset($_GET['state']) ? $_GET['state'] : '',
                    'hierarchical'    => true,
                    'depth'           => 1,
                    'show_count'      => false,
                    'hide_empty'      => false,
                ));
                ?>
            <?php endif; ?>
            
            <?php if ($settings['show_city'] === 'yes') : ?>
                <label for="city"><?php _e('City:', 'directory-listings'); ?></label>
                <select id="city" name="city">
                    <option value=""><?php _e('Select a State first', 'directory-listings'); ?></option>
                </select>
            <?php endif; ?>
            
            <?php if ($settings['show_category'] === 'yes') : ?>
                <label for="business_category"><?php _e('Business Category:', 'directory-listings'); ?></label>
                <?php
                wp_dropdown_categories(array(
                    'show_option_all' => __('All Categories', 'directory-listings'),
                    'taxonomy'        => 'business_category',
                    'name'            => 'business_category',
                    'id'              => 'business_category',
                    'orderby'         => 'name',
                    'selected'        => isset($_GET['business_category']) ? $_GET['business_category'] : '',
                    'hierarchical'    => true,
                    'depth'           => 1,
                    'show_count'      => false,
                    'hide_empty'      => false,
                ));
                ?>
            <?php endif; ?>
            
            <?php if ($settings['show_subcategory'] === 'yes') : ?>
                <label for="business_subcategory"><?php _e('Business Subcategory:', 'directory-listings'); ?></label>
                <select id="business_subcategory" name="business_subcategory">
                    <option value=""><?php _e('Select a Category first', 'directory-listings'); ?></option>
                </select>
            <?php endif; ?>
            
            <?php if ($settings['show_rating'] === 'yes') : ?>
                <label for="min_rating"><?php _e('Minimum Rating:', 'directory-listings'); ?></label>
                <select id="min_rating" name="min_rating">
                    <option value=""><?php _e('Any Rating', 'directory-listings'); ?></option>
                    <option value="1" <?php selected(isset($_GET['min_rating']) ? $_GET['min_rating'] : '', '1'); ?>><?php _e('1 Star & Up', 'directory-listings'); ?></option>
                    <option value="2" <?php selected(isset($_GET['min_rating']) ? $_GET['min_rating'] : '', '2'); ?>><?php _e('2 Stars & Up', 'directory-listings'); ?></option>
                    <option value="3" <?php selected(isset($_GET['min_rating']) ? $_GET['min_rating'] : '', '3'); ?>><?php _e('3 Stars & Up', 'directory-listings'); ?></option>
                    <option value="4" <?php selected(isset($_GET['min_rating']) ? $_GET['min_rating'] : '', '4'); ?>><?php _e('4 Stars & Up', 'directory-listings'); ?></option>
                    <option value="5" <?php selected(isset($_GET['min_rating']) ? $_GET['min_rating'] : '', '5'); ?>><?php _e('5 Stars', 'directory-listings'); ?></option>
                </select>
            <?php endif; ?>
            
            <input type="submit" value="<?php _e('Search', 'directory-listings'); ?>">
        </form>
        <script>
        jQuery(document).ready(function($) {
            $('#state').change(function() {
                var state_id = $(this).val();
                $.ajax({
                    url: directory_search_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'load_cities',
                        state_id: state_id,
                        nonce: directory_search_ajax.nonce
                    },
                    success: function(response) {
                        $('#city').html(response);
                    }
                });
            });

            $('#business_category').change(function() {
                var category_id = $(this).val();
                $.ajax({
                    url: directory_search_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'load_search_subcategories',
                        category_id: category_id,
                        nonce: directory_search_ajax.nonce
                    },
                    success: function(response) {
                        $('#business_subcategory').html(response);
                    }
                });
            });
        });
        </script>
        <?php
    }
}
?>
<?php
/**
 * State Categories Widget
 */
class State_Categories_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'state_categories_widget';
    }

    public function get_title() {
        return esc_html__('State Categories', 'directory-listings');
    }

    public function get_icon() {
        return 'eicon-folder-o';
    }

    public function get_categories() {
        return ['directory-listings'];
    }

    public function get_script_depends() {
        return [];
    }

    public function get_style_depends() {
        return [];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Content Settings', 'directory-listings'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        // Layout Control
        $this->add_control(
            'layout',
            [
                'label' => esc_html__('Layout', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'vertical',
                'options' => [
                    'vertical' => esc_html__('Vertical', 'directory-listings'),
                    'horizontal' => esc_html__('Horizontal', 'directory-listings'),
                ],
            ]
        );

        // Columns for horizontal layout
        $this->add_responsive_control(
            'columns',
            [
                'label' => esc_html__('Columns', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                ],
                'condition' => [
                    'layout' => 'horizontal',
                ],
                'selectors' => [
                    '{{WRAPPER}} .layout-horizontal' => '--columns: {{VALUE}};',
                ],
            ]
        );

        $states = get_terms([
            'taxonomy' => 'state',
            'hide_empty' => false,
            'parent' => 0,
        ]);

        $state_options = [];
        foreach ($states as $state) {
            $state_options[$state->term_id] = $state->name;
        }

        $this->add_control(
            'state',
            [
                'label' => esc_html__('State', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $state_options,
            ]
        );

        $this->add_control(
            'num_categories',
            [
                'label' => esc_html__('Number of Categories', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 50,
                'default' => 10,
            ]
        );

        // Style Controls
        $this->add_control(
            'card_background',
            [
                'label' => esc_html__('Card Background', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .category-link' => 'background: linear-gradient(145deg, {{VALUE}}, #f0f0f0);',
                ],
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => esc_html__('Text Color', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#0a3161',
                'selectors' => [
                    '{{WRAPPER}} .category-name' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'badge_background',
            [
                'label' => esc_html__('Badge Background', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#c41230',
                'selectors' => [
                    '{{WRAPPER}} .category-count' => 'background: linear-gradient(145deg, {{VALUE}}, #a80f29);',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $layout = $settings['layout'];
        
        $container_class = 'categories-grid layout-' . $layout;

        echo '<style>
            .categories-grid {
                display: flex;
                flex-wrap: wrap;
                gap: 1rem;
                padding: 1rem;
                background: #ffffff;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            }

            .layout-vertical {
                flex-direction: column;
            }

            .layout-horizontal {
                flex-direction: row;
            }

            .layout-horizontal .category-item {
                flex: 1 1 calc((100% / var(--columns)) - 1rem);
                min-width: 200px;
            }

            .layout-vertical .category-item {
                width: 100%;
            }

            .category-item {
                transition: all 0.3s ease;
            }

            .category-link {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 1rem 1.5rem;
                background: linear-gradient(145deg, #ffffff, #f0f0f0);
                border-radius: 10px;
                box-shadow: 5px 5px 10px #d9d9d9, -5px -5px 10px #ffffff;
                text-decoration: none;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            .category-link::before {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(
                    45deg,
                    rgba(255,255,255,0.1) 0%,
                    rgba(255,255,255,0.05) 100%
                );
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .category-link:hover {
                transform: translateY(-3px);
                box-shadow: 7px 7px 15px #d1d1d1, -7px -7px 15px #ffffff;
            }

            .category-link:hover::before {
                opacity: 1;
            }

            .category-name {
                color: #0a3161;
                font-size: 1rem;
                font-weight: 600;
                margin-right: 1rem;
            }

            .category-count {
                background: linear-gradient(145deg, #c41230, #a80f29);
                color: #ffffff;
                padding: 0.4rem 0.8rem;
                border-radius: 20px;
                font-size: 0.85rem;
                font-weight: 500;
                min-width: 80px;
                text-align: center;
                box-shadow: inset 2px 2px 5px rgba(0,0,0,0.2);
            }

            .show-more-wrapper {
                width: 100%;
                text-align: center;
                padding: 2rem 0 1rem;
                position: relative;
            }

            .show-more-wrapper::before {
                content: "";
                position: absolute;
                top: 50%;
                left: 0;
                right: 0;
                height: 1px;
                background: #e0e0e0;
                z-index: 1;
            }

            .show-more-link {
                position: relative;
                z-index: 2;
                display: inline-block;
                padding: 0.8rem 2rem;
                background: linear-gradient(145deg, #0a3161, #082648);
                color: #ffffff;
                text-decoration: none;
                border-radius: 25px;
                font-weight: 500;
                transition: all 0.3s ease;
                box-shadow: 5px 5px 10px #d9d9d9;
            }

            .show-more-link:hover {
                transform: translateY(-2px);
                box-shadow: 7px 7px 15px #d1d1d1;
                background: linear-gradient(145deg, #082648, #0a3161);
            }

            @media (max-width: 1024px) {
                .layout-horizontal {
                    --columns: 2;
                }

                .category-name {
                    font-size: 0.95rem;
                }
            }

            @media (max-width: 767px) {
                .layout-horizontal {
                    --columns: 1;
                }
                
                .category-item {
                    min-width: 100%;
                }

                .category-link {
                    padding: 0.8rem 1.2rem;
                }

                .category-name {
                    font-size: 0.9rem;
                }

                .category-count {
                    font-size: 0.8rem;
                    min-width: 70px;
                    padding: 0.3rem 0.6rem;
                }

                .show-more-link {
                    padding: 0.7rem 1.5rem;
                    font-size: 0.9rem;
                }
            }

            @media (hover: none) {
                .category-link:hover {
                    transform: none;
                    box-shadow: 5px 5px 10px #d9d9d9, -5px -5px 10px #ffffff;
                }

                .show-more-link:hover {
                    transform: none;
                    box-shadow: 5px 5px 10px #d9d9d9;
                }
            }
        </style>

        <div class="' . esc_attr($container_class) . '">';
        
        $categories = get_terms([
            'taxonomy' => 'business_category',
            'hide_empty' => true,
            'number' => $settings['num_categories']
        ]);

        foreach ($categories as $category) {
            $count = $this->get_category_state_count($category->term_id, $settings['state']);
            $link = get_term_link($category);
            
            if ($settings['state']) {
                $state = get_term($settings['state'], 'state');
                $link = add_query_arg('state', $state->slug, $link);
            }

            echo '<div class="category-item">';
            echo '<a href="' . esc_url($link) . '" class="category-link">';
            echo '<span class="category-name">' . esc_html($category->name) . '</span>';
            echo '<span class="category-count">' . $count . ' listings</span>';
            echo '</a>';
            echo '</div>';
        }

        echo '</div>';
        
        echo '<div class="show-more-wrapper">';
        $archive_link = get_post_type_archive_link('directory_listing');
        $all_categories_url = $archive_link . '#categories';
        if ($settings['state']) {
            $state = get_term($settings['state'], 'state');
            $all_categories_url = add_query_arg('state', $state->slug, $all_categories_url);
        }
        echo '<a href="' . esc_url($all_categories_url) . '" class="show-more-link">View All Categories</a>';
        echo '</div>';
    }

    private function get_category_state_count($category_id, $state_id) {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT COUNT(DISTINCT p.ID) 
             FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->term_relationships} tr_cat ON p.ID = tr_cat.object_id
             INNER JOIN {$wpdb->term_taxonomy} tt_cat ON tr_cat.term_taxonomy_id = tt_cat.term_taxonomy_id
             WHERE p.post_type = 'directory_listing'
             AND p.post_status = 'publish'
             AND tt_cat.taxonomy = 'business_category'
             AND tt_cat.term_id = %d",
            $category_id
        );

        if ($state_id) {
            $query .= $wpdb->prepare(
                " AND p.ID IN (
                    SELECT object_id 
                    FROM {$wpdb->term_relationships} tr_state
                    INNER JOIN {$wpdb->term_taxonomy} tt_state ON tr_state.term_taxonomy_id = tt_state.term_taxonomy_id
                    WHERE tt_state.taxonomy = 'state'
                    AND tt_state.term_id = %d
                )",
                $state_id
            );
        }

        return (int) $wpdb->get_var($query);
    }
}
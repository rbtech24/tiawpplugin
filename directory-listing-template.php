<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Function to create the template
function create_directory_listing_template() {
    // Check if Elementor is installed and activated
    if (!did_action('elementor/loaded')) {
        return;
    }

    // Include Elementor files
    require_once ELEMENTOR_PATH . 'includes/base/controls-stack.php';
    require_once ELEMENTOR_PATH . 'includes/base/element-base.php';

    // Create the template
    $template_id = \Elementor\Plugin::$instance->templates_manager->save_template([
        'title' => 'Directory Listing Template',
        'type' => 'page',
        'content' => [
            [
                'id' => 'main-section',
                'elType' => 'section',
                'settings' => [
                    'stretch_section' => 'section-stretched',
                    'background_color' => '#F0F0F0',
                ],
                'elements' => [
                    [
                        'id' => 'main-column',
                        'elType' => 'column',
                        'settings' => [
                            '_column_size' => 100,
                        ],
                        'elements' => [
                            [
                                'id' => 'listing-card',
                                'elType' => 'section',
                                'settings' => [
                                    'structure' => '10',
                                    'background_color' => '#FFFFFF',
                                    'border_radius' => [
                                        'top' => '15',
                                        'right' => '15',
                                        'bottom' => '15',
                                        'left' => '15',
                                        'unit' => 'px',
                                    ],
                                    'box_shadow_box_shadow_type' => 'yes',
                                    'box_shadow_box_shadow' => [
                                        'horizontal' => 0,
                                        'vertical' => 5,
                                        'blur' => 20,
                                        'spread' => 0,
                                        'color' => 'rgba(0,0,0,0.1)',
                                    ],
                                ],
                                'elements' => [
                                    [
                                        'id' => 'listing-content-column',
                                        'elType' => 'column',
                                        'settings' => [
                                            '_column_size' => 100,
                                        ],
                                        'elements' => [
                                            [
                                                'id' => 'listing-header',
                                                'elType' => 'section',
                                                'settings' => [
                                                    'structure' => '20',
                                                    'background_color' => '#002868',
                                                    'custom_css' => '.elementor-element-listing-header { background-image: linear-gradient(45deg, #002868 25%, transparent 25%), linear-gradient(-45deg, #002868 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #002868 75%), linear-gradient(-45deg, transparent 75%, #002868 75%); background-size: 20px 20px; background-position: 0 0, 0 10px, 10px -10px, -10px 0px; }',
                                                ],
                                                'elements' => [
                                                    [
                                                        'id' => 'logo-column',
                                                        'elType' => 'column',
                                                        'settings' => [
                                                            '_column_size' => 30,
                                                        ],
                                                        'elements' => [
                                                            [
                                                                'id' => 'company-logo',
                                                                'elType' => 'widget',
                                                                'widgetType' => 'image',
                                                                'settings' => [
                                                                    'image' => [
                                                                        'url' => '{{logo_url}}',
                                                                        'id' => '{{logo_id}}',
                                                                    ],
                                                                    '_css_classes' => 'company-logo',
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                    [
                                                        'id' => 'company-info-column',
                                                        'elType' => 'column',
                                                        'settings' => [
                                                            '_column_size' => 70,
                                                        ],
                                                        'elements' => [
                                                            [
                                                                'id' => 'company-name',
                                                                'elType' => 'widget',
                                                                'widgetType' => 'heading',
                                                                'settings' => [
                                                                    'title' => '{{company_name}}',
                                                                    'header_size' => 'h1',
                                                                    '_css_classes' => 'company-info',
                                                                ],
                                                            ],
                                                            [
                                                                'id' => 'rating-badges',
                                                                'elType' => 'widget',
                                                                'widgetType' => 'text-editor',
                                                                'settings' => [
                                                                    'editor' => '{{rating_and_badges}}',
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                            // ... Add more elements for listing body, contact info, etc.
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'id' => 'sticky-cta',
                'elType' => 'section',
                'settings' => [
                    'structure' => '10',
                    '_css_classes' => 'sticky-cta',
                    'hide_desktop' => 'yes',
                ],
                'elements' => [
                    [
                        'id' => 'cta-column',
                        'elType' => 'column',
                        'settings' => [
                            '_column_size' => 100,
                        ],
                        'elements' => [
                            [
                                'id' => 'cta-button',
                                'elType' => 'widget',
                                'widgetType' => 'button',
                                'settings' => [
                                    'text' => 'Call Now (832) 895-9155',
                                    'link' => [
                                        'url' => 'tel:8328959155',
                                    ],
                                    '_css_classes' => 'sticky-cta',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]);

    // Set the template for your custom post type
    if ($template_id) {
        update_option('elementor_directory_listing_template', $template_id);
    }
}

// Hook to run after Elementor is loaded
add_action('elementor/init', 'create_directory_listing_template');

// Function to assign the template to your custom post type
function assign_directory_listing_template($single_template) {
    if (get_post_type() === 'directory_listing') { // Replace with your actual post type
        $template_id = get_option('elementor_directory_listing_template');
        if ($template_id) {
            $single_template = ELEMENTOR_PATH . 'modules/page-templates/templates/canvas.php';
            update_post_meta(get_the_ID(), '_wp_page_template', 'elementor_canvas');
            update_post_meta(get_the_ID(), '_elementor_template_type', 'page');
            update_post_meta(get_the_ID(), '_elementor_edit_mode', 'builder');
            update_post_meta(get_the_ID(), '_elementor_data', get_post_meta($template_id, '_elementor_data', true));
        }
    }
    return $single_template;
}
add_filter('single_template', 'assign_directory_listing_template');
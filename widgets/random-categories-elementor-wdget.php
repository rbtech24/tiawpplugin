<?php
class Random_Categories_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'random_categories';
    }

    public function get_title() {
        return __('Random Categories', 'directory-listings');
    }

    public function get_icon() {
        return 'eicon-posts-grid';
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
            'number_of_categories',
            [
                'label' => __('Number of Categories', 'directory-listings'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 20,
                'step' => 1,
                'default' => 8,
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $number_of_categories = $settings['number_of_categories'];

        $random_categories = get_random_business_categories($number_of_categories);

        if (!empty($random_categories)) {
            echo '<div class="random-categories">';
            echo '<h2>' . __('Popular Categories', 'directory-listings') . '</h2>';
            echo '<div class="categories">';
            foreach ($random_categories as $category) {
                echo '<a href="' . esc_url(get_term_link($category)) . '" class="category-button">';
                echo esc_html($category->name);
                echo '</a>';
            }
            echo '</div>';
            echo '</div>';
        }
    }
}
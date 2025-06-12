<?php
/*
 * Template Name: Directory State Page
 */
 get_header(); // This will include the theme's header
?>
<div class="state-directory-template">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Playfair+Display:wght@700&display=swap');

        :root {
            --navy-blue: #002868;
            --true-red: #BF0A30;
            --pure-white: #FFFFFF;
            --off-white: #F0F0F0;
            --gold: #FFD700;
            --verified-green: #4CAF50;
        }

        .state-directory-template {
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
            color: var(--navy-blue);
            background-color: var(--off-white);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .state-header {
            background-color: var(--navy-blue);
            color: var(--pure-white);
            padding: 40px 0;
            text-align: center;
            background-image: 
                linear-gradient(45deg, var(--navy-blue) 25%, transparent 25%),
                linear-gradient(-45deg, var(--navy-blue) 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, var(--navy-blue) 75%),
                linear-gradient(-45deg, transparent 75%, var(--navy-blue) 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
        }

        .state-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .state-description {
            max-width: 800px;
            margin: 0 auto;
            font-size: 1.1rem;
        }

        .filters {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            margin: 30px 0;
        }

        .filter-btn {
            background-color: var(--navy-blue);
            color: var(--pure-white);
            border: 2px solid var(--navy-blue);
            padding: 12px 24px;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }

        .filter-btn:hover, .filter-btn.active {
            background-color: var(--pure-white);
            color: var(--navy-blue);
            transform: translateY(-3px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .listings-count {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--navy-blue);
        }

        .listings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        @media (min-width: 768px) {
            .listings-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .listing-card {
            background: var(--pure-white);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .listing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .featured-listing {
            border: 3px solid var(--gold);
        }

        .top-rated-listing {
            box-shadow: 0 0 0 3px var(--gold), 0 5px 20px rgba(0,0,0,0.1);
        }

        .top-rated-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background-color: var(--gold);
            color: var(--navy-blue);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
            z-index: 1;
        }

        .listing-image {
            height: 200px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f0f0;
            padding: 20px;
            overflow: hidden;
        }

        .listing-image img {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .listing-card:hover .listing-image img {
            transform: scale(1.05);
        }

        .no-logo-text {
            font-weight: bold;
            text-align: center;
            color: #333;
        }

        .listing-content {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .listing-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: var(--navy-blue);
            margin: 15px 0;
            width: 100%;
            text-align: center;
        }

        .verified-badge {
            display: inline-block;
            background-color: var(--verified-green);
            color: var(--pure-white);
            font-size: 0.8rem;
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 12px;
            margin-top: 5px;
        }

        .verified-badge::before {
            content: '✓ ';
            font-weight: bold;
        }

        .listing-rating {
            color: var(--gold);
            margin: 10px 0;
            font-size: 1rem;
        }

        .listing-votes {
            margin: 10px 0;
            font-size: 0.9rem;
            color: var(--navy-blue);
        }

        .vote-score {
            font-weight: bold;
        }

        .vote-details {
            font-size: 0.8rem;
            color: #666;
        }

        .listing-services {
            font-size: 0.9rem;
            margin-bottom: 20px;
            flex-grow: 1;
        }

        .listing-location {
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .listing-cta {
            display: flex;
            justify-content: space-between;
            margin-top: auto;
            width: 100%;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            text-align: center;
            flex-basis: 48%;
        }

        .btn-primary {
            background-color: var(--true-red);
            color: var(--pure-white);
        }

        .btn-secondary {
            background-color: var(--off-white);
            color: var(--navy-blue);
            border: 2px solid var(--navy-blue);
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-3px);
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 40px;
        }

        .page-numbers {
            padding: 10px 20px;
            margin: 0 5px;
            background-color: var(--pure-white);
            color: var(--navy-blue);
            border: 2px solid var(--navy-blue);
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
        }

        .page-numbers:hover, .page-numbers.current {
            background-color: var(--navy-blue);
            color: var(--pure-white);
        }

        @media (max-width: 768px) {
            .state-header h1 {
                font-size: 2rem;
            }
            .state-description {
                font-size: 1rem;
            }
            .listings-grid {
                grid-template-columns: 1fr;
            }
            .filter-btn {
                padding: 8px 15px;
                font-size: 0.8rem;
            }
        }
    </style>

    <header class="state-header">
        <div class="container">
            <h1>Top Companies in <?php echo esc_html(get_queried_object()->name); ?></h1>
            <p class="state-description">Find expert service providers in <?php echo esc_html(get_queried_object()->name); ?>. Compare top-rated companies and get quotes for your needs.</p>
        </div>
    </header>

    <main class="container">
        <div class="filters">
            <?php
            $state_term = get_queried_object();
            ?>
            <a href="<?php echo esc_url(get_term_link($state_term)); ?>" class="filter-btn <?php echo !isset($_GET['city']) ? 'active' : ''; ?>">All Cities</a>
            <?php
            $cities = get_terms(array('taxonomy' => 'city', 'parent' => 0, 'hide_empty' => true));
            foreach ($cities as $city) {
                if (is_object($city)) {
                    $active = isset($_GET['city']) && $_GET['city'] === $city->slug ? 'active' : '';
                    echo '<a href="' . esc_url(add_query_arg('city', $city->slug, get_term_link($state_term))) . '" class="filter-btn ' . $active . '">' . esc_html($city->name) . '</a>';
                }
            }
            ?>
        </div>

        <?php
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

        $args = array(
            'post_type' => 'directory_listing',
            'posts_per_page' => 6,
            'paged' => $paged,
            'tax_query' => array(
                array(
                    'taxonomy' => 'state',
                    'field' => 'term_id',
                    'terms' => $state_term->term_id,
                ),
            ),
        );

        // Check if a city is selected
        if (isset($_GET['city']) && !empty($_GET['city'])) {
            $args['tax_query'][] = array(
                'taxonomy' => 'city',
                'field' => 'slug',
                'terms' => sanitize_text_field($_GET['city']),
            );
        }

        $listings = new WP_Query($args);

        if ($listings->have_posts()) :
            echo '<div class="listings-count">Found ' . $listings->found_posts . ' listings</div>';
        ?>
        <div class="listings-grid">
            <?php
            while ($listings->have_posts()) : $listings->the_post();
                $featured = get_field('is_featured') ?: false;
                $verified = get_field('is_verified') ?: false;
                $top_rated = get_field('top_rated') ?: false;
                $rating = get_field('overall_rating');
                $rating = is_numeric($rating) ? floatval($rating) : 0;
                $review_count = get_field('review_count');
                $review_count = is_numeric($review_count) ? intval($review_count) : 0;
                $services = get_field('services_offered') ?: array();
                $phone = get_field('phone_number') ?: '';
                $city = get_field('city') ?: '';
                $upvotes = get_post_meta(get_the_ID(), 'upvotes', true) ?: 0;
                $downvotes = get_post_meta(get_the_ID(), 'downvotes', true) ?: 0;
                $vote_score = get_post_meta(get_the_ID(), 'vote_score', true) ?: 0;
            ?>
            <div class="listing-card <?php echo $featured ? 'featured-listing' : ''; ?> <?php echo $top_rated ? 'top-rated-listing' : ''; ?>">
                <?php if ($top_rated) : ?>
                    <div class="top-rated-badge">★ Top Rated</div>
                <?php endif; ?>
                <div class="listing-image">
                    <?php 
                    $logo = get_field('logo');
                    $listing_type = get_field('listing_type');
                    $placeholder_logo_url = plugin_dir_url(dirname(__FILE__)) . 'images/placeholder.png';
                    $placeholder_file_path = plugin_dir_path(dirname(__FILE__)) . 'images/placeholder.png';
                    $placeholder_exists = file_exists($placeholder_file_path);

                    if ($listing_type === 'free' || empty($logo)) {
                        if ($placeholder_exists) {
                            echo '<img src="' . esc_url($placeholder_logo_url) . '" alt="' . esc_attr(get_the_title()) . ' Placeholder Logo" class="listing-logo">';
                        } else {
                            echo '<div class="no-logo-text">No Logo Available</div>';
                        }
                    } else {
                        if (is_numeric($logo)) {
                            echo wp_get_attachment_image($logo, 'medium', false, array('class' => 'listing-logo'));
                        } elseif (is_array($logo) && isset($logo['url'])) {
                            echo '<img src="' . esc_url($logo['url']) . '" alt="' . esc_attr(get_the_title()) . '" class="listing-logo">';
                        } elseif (is_string($logo)) {
                            echo '<img src="' . esc_url($logo) . '" alt="' . esc_attr(get_the_title()) . '" class="listing-logo">';
                        }
                    }
                    ?>
                </div>
                <div class="listing-content">
                    <h2 class="listing-title"><?php the_title(); ?></h2>
                    <?php if ($verified) : ?>
                        <div class="verified-badge">Verified Business</div>
                    <?php endif; ?>
                    <div class="listing-rating">
                        <?php
                        if ($rating > 0) {
                            $stars = str_repeat('★', round($rating)) . str_repeat('☆', 5 - round($rating));
                            echo $stars . ' ' . number_format($rating, 1) . ' (' . $review_count . ' reviews)';
                        } else {
                            echo 'No ratings yet';
                        }
                        ?>
                    </div>
                    <div class="listing-votes">
                        <span class="vote-score">Vote Score: <?php echo esc_html($vote_score); ?></span>
                        <span class="vote-details">(<?php echo esc_html($upvotes); ?> 👍 / <?php echo esc_html($downvotes); ?> 👎)</span>
                    </div>
                    <p class="listing-services">
                        <?php
                        if (!empty($services) && is_array($services)) {
                            $service_names = array_column($services, 'service_name');
                            echo esc_html(implode(', ', array_slice($service_names, 0, 3)));
                        } else {
                            echo 'Services not specified';
                        }
                        ?>
                    </p>
                    <p class="listing-location"><?php echo esc_html($city ?: 'City not specified'); ?>, <?php echo esc_html(get_queried_object()->name); ?></p>
                    <div class="listing-cta">
                        <a href="<?php the_permalink(); ?>" class="btn btn-primary">View Profile</a>
                        <?php if ($phone) : ?>
                            <a href="tel:<?php echo esc_attr($phone); ?>" class="btn btn-secondary">Call Now</a>
                        <?php else : ?>
                            <span class="btn btn-secondary disabled">No Phone</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
        <?php
        else :
            echo '<p>No listings found in this state.</p>';
        endif;
        ?>

        <div class="pagination">
            <?php
            echo paginate_links(array(
                'total' => $listings->max_num_pages,
                'current' => $paged,
                'prev_text' => __('Previous'),
                'next_text' => __('Next'),
            ));
            ?>
        </div>
    </main>
</div>

<?php get_footer(); ?>
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
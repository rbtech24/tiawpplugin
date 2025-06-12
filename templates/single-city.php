/**
 * Template Name: Single City
 * Template for displaying individual city pages
 */

get_header();

// Get city data
$city_name = get_the_title();
$state_id = get_post_meta(get_the_ID(), 'state_id', true);
$state = get_post($state_id);
$state_name = $state ? $state->post_title : '';

// Get listings for this city
$args = array(
    'post_type' => array('directory_listing', 'restaurant'),
    'posts_per_page' => -1,
    'meta_query' => array(
        array(
            'key' => 'city',
            'value' => $city_name,
            'compare' => '='
        )
    )
);

$listings = new WP_Query($args);
?>

<div class="city-page-wrapper">
    <!-- City Header Section -->
    <div class="city-header">
        <div class="container">
            <h1><?php echo esc_html($city_name); ?>, <?php echo esc_html($state_name); ?></h1>
            
            <!-- City Stats -->
            <div class="city-stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo $listings->found_posts; ?></span>
                    <span class="stat-label">Local Businesses</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="city-content">
        <div class="container">
            <!-- Business Categories -->
            <section class="business-categories">
                <h2>Browse Businesses by Category</h2>
                <?php
                $categories = array();
                if ($listings->have_posts()) {
                    while ($listings->have_posts()) {
                        $listings->the_post();
                        $terms = get_the_terms(get_the_ID(), 'business_category');
                        if ($terms && !is_wp_error($terms)) {
                            foreach ($terms as $term) {
                                if (!isset($categories[$term->term_id])) {
                                    $categories[$term->term_id] = array(
                                        'name' => $term->name,
                                        'slug' => $term->slug,
                                        'count' => 1
                                    );
                                } else {
                                    $categories[$term->term_id]['count']++;
                                }
                            }
                        }
                    }
                }
                wp_reset_postdata();
                
                if (!empty($categories)) {
                    echo '<div class="category-grid">';
                    foreach ($categories as $category) {
                        printf(
                            '<a href="%s" class="category-item">
                                <h3>%s</h3>
                                <span class="count">%d businesses</span>
                            </a>',
                            esc_url(add_query_arg(array(
                                'city' => urlencode($city_name),
                                'category' => $category['slug']
                            ), home_url('/listings/'))),
                            esc_html($category['name']),
                            $category['count']
                        );
                    }
                    echo '</div>';
                }
                ?>
            </section>

            <!-- Featured Listings -->
            <section class="featured-listings">
                <h2>Featured Businesses in <?php echo esc_html($city_name); ?></h2>
                <div class="listings-grid">
                    <?php
                    // Reset and modify query for featured listings
                    $featured_args = $args;
                    $featured_args['posts_per_page'] = 6;
                    $featured_args['meta_query'][] = array(
                        'key' => 'is_featured',
                        'value' => '1',
                        'compare' => '='
                    );
                    
                    $featured_listings = new WP_Query($featured_args);
                    
                    if ($featured_listings->have_posts()) {
                        while ($featured_listings->have_posts()) {
                            $featured_listings->the_post();
                            get_template_part('template-parts/content', 'listing-card');
                        }
                    } else {
                        echo '<p>No featured listings available.</p>';
                    }
                    wp_reset_postdata();
                    ?>
                </div>
            </section>

            <!-- Recent Listings -->
            <section class="recent-listings">
                <h2>Recently Added Businesses</h2>
                <div class="listings-grid">
                    <?php
                    // Reset and modify query for recent listings
                    $recent_args = $args;
                    $recent_args['posts_per_page'] = 6;
                    $recent_args['orderby'] = 'date';
                    $recent_args['order'] = 'DESC';
                    
                    $recent_listings = new WP_Query($recent_args);
                    
                    if ($recent_listings->have_posts()) {
                        while ($recent_listings->have_posts()) {
                            $recent_listings->the_post();
                            get_template_part('template-parts/content', 'listing-card');
                        }
                    } else {
                        echo '<p>No recent listings available.</p>';
                    }
                    wp_reset_postdata();
                    ?>
                </div>
            </section>

            <?php
            // Get related FAQs
            $city_faqs = new WP_Query(array(
                'post_type' => 'faq',
                'posts_per_page' => 5,
                's' => $city_name,
                'orderby' => 'date',
                'order' => 'DESC'
            ));

            if ($city_faqs->have_posts()) : ?>
                <section class="city-faqs">
                    <div class="container">
                        <h2>Frequently Asked Questions about <?php echo esc_html($city_name); ?></h2>
                        <div class="faq-list">
                            <?php while ($city_faqs->have_posts()) : $city_faqs->the_post(); ?>
                                <div class="faq-item">
                                    <h3 class="faq-question"><?php the_title(); ?></h3>
                                    <div class="faq-answer">
                                        <?php the_content(); ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        <a href="<?php echo esc_url(get_post_type_archive_link('faq')); ?>" class="view-all-link">
                            View All FAQs
                        </a>
                    </div>
                </section>
            <?php 
            endif;
            wp_reset_postdata();

            // Get related blog posts
            $blog_args = array(
                'post_type' => 'post',
                'posts_per_page' => 3,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'category',
                        'field' => 'name',
                        'terms' => $city_name
                    )
                )
            );

            $city_blogs = new WP_Query($blog_args);

            if ($city_blogs->have_posts()) : ?>
                <section class="city-blogs">
                    <div class="container">
                        <h2>Latest News from <?php echo esc_html($city_name); ?></h2>
                        <div class="blog-grid">
                            <?php while ($city_blogs->have_posts()) : $city_blogs->the_post(); ?>
                                <article class="blog-card">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="blog-image">
                                            <?php the_post_thumbnail('medium'); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="blog-content">
                                        <h3 class="blog-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h3>
                                        <div class="blog-meta">
                                            <?php echo get_the_date(); ?> | 
                                            <?php echo get_the_author(); ?>
                                        </div>
                                        <div class="blog-excerpt">
                                            <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                        </div>
                                        <a href="<?php the_permalink(); ?>" class="read-more">
                                            Read More
                                        </a>
                                    </div>
                                </article>
                            <?php endwhile; ?>
                        </div>
                        <a href="<?php echo esc_url(get_category_link(get_cat_ID($city_name))); ?>" class="view-all-link">
                            View All News
                        </a>
                    </div>
                </section>
            <?php 
            endif;
            wp_reset_postdata();
            ?>
        </div>
    </div>
</div>

<style>
    /* Base Styles */
    .city-page-wrapper {
        width: 100%;
        max-width: 100%;
        margin: 0;
        padding: 0;
        background-color: #f8fafc;
    }

    .container {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* Header Styles */
    .city-header {
        background-color: #1e3a8a;
        color: white;
        padding: 60px 0;
        margin-bottom: 40px;
    }

    .city-header h1 {
        font-size: 2.5em;
        margin: 0;
        line-height: 1.2;
    }

    .city-stats {
        display: flex;
        gap: 30px;
        margin-top: 20px;
    }

    .stat-item {
        text-align: center;
        background: rgba(255, 255, 255, 0.1);
        padding: 15px 25px;
        border-radius: 8px;
    }

    .stat-number {
        display: block;
        font-size: 24px;
        font-weight: bold;
    }

    .stat-label {
        font-size: 14px;
        opacity: 0.8;
    }

    /* Section Styles */
    .city-content section {
        margin-bottom: 60px;
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .city-content h2 {
        color: #1e3a8a;
        font-size: 28px;
        margin-bottom: 30px;
    }

    /* Category Grid */
    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }

    .category-item {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        text-decoration: none;
        color: #333;
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }

    .category-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border-color: #1e3a8a;
    }

    .category-item h3 {
        margin: 0 0 10px 0;
        color: #1e3a8a;
    }

    .category-item .count {
        color: #666;
        font-size: 14px;
    }

    /* Listings Grid */
    .listings-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 30px;
    }

    /* FAQ Styles */
    .city-faqs {
        padding: 60px 0;
        background-color: #f8fafc;
    }

    .faq-list {
        margin-top: 30px;
    }

    .faq-item {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: transform 0.2s ease;
    }

    .faq-item:hover {
        transform: translateY(-2px);
    }

    .faq-question {
        font-size: 18px;
        color: #1e3a8a;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .faq-answer {
        color: #4a5568;
        line-height: 1.6;
    }

    /* Blog Styles */
    .blog-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 30px;
    }

    .blog-card {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }

    .blog-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border-color: #1e3a8a;
    }

    .blog-image {
        width: 100%;
        height: 200px;
        overflow: hidden;
    }

    .blog-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .blog-card:hover .blog-image img {
        transform: scale(1.05);
    }

    .blog-content {
        padding: 20px;
    }

    .blog-title {
        margin: 0 0 10px 0;
        font-size: 20px;
    }

    .blog-title a {
        color: #1e3a8a;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .blog-title a:hover {
        color: #e63946;
    }

    .blog-meta {
        font-size: 14px;
        color: #666;
        margin-bottom: 10px;
    }

    .blog-excerpt {
        color: #4a5568;
        margin-bottom: 15px;
        line-height: 1.6;
    }

    .read-more {
        display: inline-block;
        color: #1e3a8a;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s ease;
    }

    .read-more:hover {
        color: #e63946;
    }

 .view-all-link {
        display: inline-block;
        margin-top: 30px;
        padding: 12px 24px;
        background-color: #1e3a8a;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .view-all-link:hover {
        background-color: #1c3175;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    /* Listing Card Styles */
    .listing-card {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .listing-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border-color: #1e3a8a;
    }

    .listing-image {
        width: 100%;
        height: 200px;
        overflow: hidden;
    }

    .listing-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .listing-card:hover .listing-image img {
        transform: scale(1.05);
    }

    .listing-content {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .listing-title {
        margin: 0 0 10px 0;
        font-size: 18px;
    }

    .listing-title a {
        color: #1e3a8a;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .listing-title a:hover {
        color: #e63946;
    }

    /* Responsive Styles */
    @media (max-width: 1024px) {
        .category-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }

        .listings-grid,
        .blog-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .city-header {
            padding: 40px 0;
        }

        .city-header h1 {
            font-size: 2em;
        }

        .city-stats {
            flex-wrap: wrap;
        }

        .stat-item {
            flex: 1 1 calc(50% - 15px);
        }

        .category-grid {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        }

        .listings-grid,
        .blog-grid {
            grid-template-columns: 1fr;
        }

        .city-content section {
            padding: 20px;
            margin-bottom: 30px;
        }
    }

    @media (max-width: 480px) {
        .stat-item {
            flex: 1 1 100%;
        }

        .category-grid {
            grid-template-columns: 1fr;
        }

        .city-content h2 {
            font-size: 24px;
        }

        .blog-image {
            height: 180px;
        }
    }
</style>

<?php get_footer(); ?>
<?php
get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <header class="page-header">
            <h1 class="page-title">Restaurants</h1>
        </header>

        <?php if (have_posts()) : ?>
            <div class="restaurant-grid">
                <?php
                while (have_posts()) :
                    the_post();
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('restaurant-card'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="restaurant-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <header class="entry-header">
                            <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        </header>
                        <div class="entry-summary">
                            <?php the_excerpt(); ?>
                        </div>
                        <?php
                        // Display average rating and review count
                        $overall_rating = get_post_meta(get_the_ID(), 'overall_rating', true);
                        $review_count = get_post_meta(get_the_ID(), 'review_count', true);

                        if ($overall_rating && $review_count) :
                        ?>
                            <div class="restaurant-rating">
                                <div class="star-rating" title="<?php echo esc_attr($overall_rating); ?> out of 5 stars">
                                    <?php echo display_stars($overall_rating); ?>
                                </div>
                                <span class="review-count">(<?php echo esc_html($review_count); ?> reviews)</span>
                            </div>
                        <?php endif; ?>
                        <?php
                        $cuisines = get_the_terms(get_the_ID(), 'cuisine');
                        if ($cuisines && !is_wp_error($cuisines)) :
                            ?>
                            <div class="restaurant-cuisines">
                                <?php foreach ($cuisines as $cuisine) : ?>
                                    <span class="cuisine-tag"><?php echo esc_html($cuisine->name); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php
                endwhile;
                ?>
            </div>
            <?php
            the_posts_navigation();
        else :
            echo '<p>No restaurants found.</p>';
        endif;
        ?>
    </main>
</div>

<?php
get_sidebar();
get_footer();
?>
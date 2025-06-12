<?php
/**
 * Template for displaying state taxonomy archives
 */

get_header(); ?>

<div class="state-template-wrapper">
    <div class="state-header">
        <div class="container">
            <h1 class="state-title"><?php single_term_title(); ?></h1>
            <?php
            $term_description = term_description();
            if (!empty($term_description)) :
                echo '<div class="state-description">' . $term_description . '</div>';
            endif;
            ?>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <main class="state-content">
                    <?php if (have_posts()) : ?>
                        <div class="state-listings">
                            <?php
                            while (have_posts()) :
                                the_post();
                                ?>
                                <article id="post-<?php the_ID(); ?>" <?php post_class('listing-card'); ?>>
                                    <div class="listing-content">
                                        <h2 class="listing-title">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_title(); ?>
                                            </a>
                                        </h2>
                                        
                                        <?php if (function_exists('get_field')) : ?>
                                            <div class="listing-meta">
                                                <?php
                                                $city = get_field('city');
                                                if ($city) :
                                                    ?>
                                                    <span class="listing-city"><?php echo esc_html($city); ?></span>
                                                <?php endif; ?>
                                                
                                                <?php
                                                $phone = get_field('phone_number');
                                                if ($phone) :
                                                    ?>
                                                    <span class="listing-phone"><?php echo esc_html($phone); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="listing-excerpt">
                                            <?php the_excerpt(); ?>
                                        </div>
                                    </div>
                                </article>
                            <?php endwhile; ?>

                            <?php
                            the_posts_pagination(array(
                                'prev_text' => '&larr;',
                                'next_text' => '&rarr;',
                                'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Page', 'directory-listings') . ' </span>',
                            ));
                            ?>

                        </div>
                    <?php else : ?>
                        <p><?php _e('No listings found in this state.', 'directory-listings'); ?></p>
                    <?php endif; ?>
                </main>
            </div>

            <div class="col-md-4">
                <aside class="state-sidebar">
                    <?php
                    // Get cities in this state
                    $state_term = get_queried_object();
                    $cities = get_terms(array(
                        'taxonomy' => 'city',
                        'meta_query' => array(
                            array(
                                'key' => 'state_id',
                                'value' => $state_term->term_id,
                            ),
                        ),
                    ));

                    if (!empty($cities) && !is_wp_error($cities)) :
                        ?>
                        <div class="state-cities">
                            <h3><?php _e('Cities', 'directory-listings'); ?></h3>
                            <ul>
                                <?php foreach ($cities as $city) : ?>
                                    <li>
                                        <a href="<?php echo get_term_link($city); ?>">
                                            <?php echo esc_html($city->name); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php
                    // Get business categories in this state
                    $categories = get_terms(array(
                        'taxonomy' => 'business_category',
                        'hide_empty' => true,
                    ));

                    if (!empty($categories) && !is_wp_error($categories)) :
                        ?>
                        <div class="state-categories">
                            <h3><?php _e('Business Categories', 'directory-listings'); ?></h3>
                            <ul>
                                <?php foreach ($categories as $category) : ?>
                                    <li>
                                        <a href="<?php echo get_term_link($category); ?>">
                                            <?php echo esc_html($category->name); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </aside>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
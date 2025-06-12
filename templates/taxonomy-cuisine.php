<?php
get_header();

$term = get_queried_object();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <header class="page-header">
            <h1 class="page-title"><?php echo esc_html($term->name); ?> Restaurants</h1>
            <?php if ($term->description) : ?>
                <div class="archive-description"><?php echo esc_html($term->description); ?></div>
            <?php endif; ?>
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
                                <?php the_post_thumbnail('medium'); ?>
                            </div>
                        <?php endif; ?>
                        <header class="entry-header">
                            <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        </header>
                        <div class="entry-summary">
                            <?php the_excerpt(); ?>
                        </div>
                    </article>
                <?php
                endwhile;
                ?>
            </div>

            <?php
            the_posts_navigation();

        else :
            echo '<p>No restaurants found in this category.</p>';
        endif;
        ?>

    </main>
</div>

<?php
get_sidebar();
get_footer();
?>
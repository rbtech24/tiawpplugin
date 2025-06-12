<?php
/**
 * Template part for displaying the listing header
 */
?>

<header class="listing-header">
    <div class="header-content">
        <div class="business-logo">
            <?php if ($logo && is_array($logo)): ?>
                <img src="<?php echo esc_url($logo['url']); ?>" 
                     alt="<?php echo esc_attr(get_the_title()); ?> Logo"
                     style="max-width: 100%; height: auto;">
            <?php else: ?>
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/placeholder.png" 
                     alt="<?php echo esc_attr(get_the_title()); ?> Logo"
                     style="max-width: 100%; height: auto;">
            <?php endif; ?>
        </div>
        
        <div class="business-info">
            <h1><?php the_title(); ?></h1>
            
            <?php if (!empty($category_names)): ?>
            <div class="category-tags">
                <?php foreach ($category_names as $category): ?>
                    <span class="category-tag"><?php echo esc_html($category); ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <div class="business-meta">
                <?php if ($is_verified): ?>
                    <span class="badge badge-verified">
                        <i class="fas fa-check-circle"></i>
                        Verified
                    </span>
                <?php endif; ?>
                
                <?php if ($is_top_rated): ?>
                    <span class="badge badge-premium">
                        <i class="fas fa-award"></i>
                        Top Rated
                    </span>
                <?php endif; ?>
            </div>
            
            <?php if ($rating > 0): ?>
            <div class="rating-stars">
                <?php
                echo str_repeat('<i class="fas fa-star"></i>', floor($rating));
                if ($rating - floor($rating) >= 0.5) {
                    echo '<i class="fas fa-star-half-alt"></i>';
                }
                ?>
                <span>(<?php echo number_format($rating, 1); ?>/5 from <?php echo $review_count; ?> reviews)</span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</header>
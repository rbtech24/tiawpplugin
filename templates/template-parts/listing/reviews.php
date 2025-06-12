<?php
/**
 * Template part for displaying reviews section
 */
?>

<section class="reviews-section" id="reviews">
    <h2 class="section-title">Customer Reviews</h2>
    
    <div class="review-stats">
        <div class="overall-rating">
            <div class="rating-number"><?php echo number_format($rating, 1); ?></div>
            <div class="rating-stars">
                <?php echo str_repeat('<i class="fas fa-star"></i>', floor($rating)); ?>
                <?php if ($rating - floor($rating) >= 0.5) echo '<i class="fas fa-star-half-alt"></i>'; ?>
            </div>
            <div>Based on <?php echo $review_count; ?> reviews</div>
        </div>
        
        <div class="rating-distribution">
            <?php
            $ratings = get_rating_distribution(get_the_ID());
            for ($i = 5; $i >= 1; $i--):
                $count = $ratings[$i] ?? 0;
                $percentage = $review_count > 0 ? ($count / $review_count) * 100 : 0;
            ?>
            <div class="rating-bar">
                <span><?php echo $i; ?> ★</span>
                <div class="rating-progress">
                    <div class="rating-progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                </div>
                <span><?php echo $count; ?></span>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <div class="review-cards">
        <?php display_listing_reviews(); ?>
    </div>

    <!-- Add Review Form -->
    <div class="add-review-form">
        <h3>Write a Review</h3>
        <?php add_review_form(); ?>
    </div>
</section>
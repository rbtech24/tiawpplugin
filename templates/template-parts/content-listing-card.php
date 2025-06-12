<div class="listing-card">
    <?php if (has_post_thumbnail()): ?>
        <div class="listing-image">
            <?php the_post_thumbnail('medium'); ?>
        </div>
    <?php endif; ?>
    
    <div class="listing-content">
        <h3 class="listing-title">
            <a href="<?php the_permalink(); ?>">
                <?php the_title(); ?>
            </a>
        </h3>
        
        <?php 
        $rating = get_field('overall_rating');
        if ($rating): ?>
            <div class="listing-rating">
                <?php 
                for ($i = 1; $i <= 5; $i++) {
                    echo '<span class="star ' . ($i <= $rating ? 'filled' : '') . '">★</span>';
                }
                ?>
                <span class="rating-number"><?php echo number_format($rating, 1); ?></span>
            </div>
        <?php endif; ?>

        <div class="listing-meta">
            <?php 
            $categories = get_the_terms(get_the_ID(), 'business_category');
            if ($categories && !is_wp_error($categories)) {
                echo '<span class="listing-category">' . esc_html($categories[0]->name) . '</span>';
            }
            ?>
        </div>

        <div class="listing-address">
            <?php 
            $address = get_field('street_address');
            if ($address) {
                echo '<span>' . esc_html($address) . '</span>';
            }
            ?>
        </div>
    </div>
</div>

<style>
    .listing-card {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
    }

    .listing-card:hover {
        transform: translateY(-2px);
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
    }

    .listing-content {
        padding: 20px;
    }

    .listing-title {
        margin: 0 0 10px 0;
        font-size: 18px;
    }

    .listing-title a {
        color: #1e3a8a;
        text-decoration: none;
    }

    .listing-rating {
        margin-bottom: 10px;
    }

    .star {
        color: #ffd700;
    }

    .star.filled {
        color: #ffd700;
    }

    .listing-meta {
        font-size: 14px;
        color: #666;
        margin-bottom: 10px;
    }

    .listing-category {
        background: #f0f0f0;
        padding: 4px 8px;
        border-radius: 4px;
    }

    .listing-address {
        font-size: 14px;
        color: #666;
    }
</style>
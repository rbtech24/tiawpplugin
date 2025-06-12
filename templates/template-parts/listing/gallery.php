<?php
/**
 * Template part for displaying gallery section
 */

if (empty($gallery) || $listingtype == 'free') return;
?>

<section class="gallery-section" id="gallery">
    <h2 class="section-title">
        <i class="fas fa-images"></i>
        Photo Gallery
    </h2>
    
    <div class="gallery-categories">
        <button class="category-btn active" data-category="all">All Photos</button>
        <button class="category-btn" data-category="work">Completed Work</button>
        <button class="category-btn" data-category="facility">Facility</button>
    </div>

    <div class="gallery-grid">
        <?php foreach ($gallery as $image): ?>
            <div class="gallery-item" data-category="<?php echo esc_attr($image['category'] ?? 'work'); ?>">
                <img src="<?php echo esc_url($image['url']); ?>" 
                     alt="<?php echo esc_attr($image['alt']); ?>"
                     loading="lazy">
                <div class="gallery-overlay">
                    <div class="overlay-content">
                        <h4><?php echo esc_html($image['title'] ?? ''); ?></h4>
                        <p><?php echo esc_html($image['caption'] ?? ''); ?></p>
                    </div>
                    <button class="gallery-zoom">
                        <i class="fas fa-search-plus"></i>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="photo-modal">
        <div class="modal-content">
            <button class="modal-close">
                <i class="fas fa-times"></i>
            </button>
            <button class="modal-prev">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="modal-next">
                <i class="fas fa-chevron-right"></i>
            </button>
            <img src="" alt="Gallery Image">
            <div class="modal-caption">
                <h3></h3>
                <p></p>
            </div>
        </div>
    </div>
</section>
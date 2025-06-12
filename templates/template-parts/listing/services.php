<?php
/**
 * Template part for displaying services section
 */
?>

<?php if (!empty($services)): ?>
<section class="services-section">
    <h2 class="section-title">Services Offered</h2>
    <div class="services-grid">
        <?php foreach ($services as $service): ?>
            <div class="service-card">
                <?php 
                // Get icon based on service name
                $icon_class = get_service_icon($service['service_name']);
                ?>
                <i class="fas <?php echo esc_attr($icon_class); ?>"></i>
                <h3><?php echo esc_html($service['service_name']); ?></h3>
                <?php if (!empty($service['description'])): ?>
                    <p><?php echo esc_html($service['description']); ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($listingtype !== 'free'): ?>
    <!-- Stats Section -->
    <div class="company-stats">
        <div class="stat-item">
            <div class="stat-number">25+</div>
            <div class="stat-label">Years Experience</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">15K+</div>
            <div class="stat-label">Satisfied Customers</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">24/7</div>
            <div class="stat-label">Emergency Service</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">100%</div>
            <div class="stat-label">Satisfaction Guarantee</div>
        </div>
    </div>
    <?php endif; ?>
</section>
<?php endif; ?>

<?php
/**
 * Helper function to get service icon
 */
function get_service_icon($service_name) {
    $service_icons = array(
        'emergency' => 'fa-exclamation-circle',
        'repair' => 'fa-wrench',
        'installation' => 'fa-tools',
        'maintenance' => 'fa-cogs',
        'plumbing' => 'fa-faucet',
        'heating' => 'fa-temperature-high',
        'cooling' => 'fa-snowflake',
        'electrical' => 'fa-bolt',
        'default' => 'fa-tools'
    );

    foreach ($service_icons as $key => $icon) {
        if (stripos($service_name, $key) !== false) {
            return $icon;
        }
    }

    return $service_icons['default'];
}
?>
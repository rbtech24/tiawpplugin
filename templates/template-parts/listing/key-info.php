<?php
/**
 * Template part for displaying the key info section
 */
?>

<div class="key-info-bar">
    <div class="key-info-grid">
        <?php if ($phone): ?>
        <div class="key-info-item">
            <div class="key-info-icon">
                <i class="fas fa-phone"></i>
            </div>
            <div>
                <strong>Contact</strong>
                <div><?php echo esc_html($phone); ?></div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($hours_today = get_current_business_hours($business_hours)): ?>
        <div class="key-info-item">
            <div class="key-info-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <strong>Hours</strong>
                <div class="open-status">
                    <?php if ($hours_today['is_open']): ?>
                        <span class="status-badge open">
                            <i class="fas fa-circle"></i> Currently Open
                        </span>
                        <span class="next-hours">Closes at <?php echo esc_html($hours_today['close']); ?></span>
                    <?php else: ?>
                        <span class="status-badge closed">
                            <i class="fas fa-circle"></i> Currently Closed
                        </span>
                        <span class="next-hours">Opens <?php echo esc_html($hours_today['next_open']); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($website): ?>
        <div class="key-info-item">
            <div class="key-info-icon">
                <i class="fas fa-globe"></i>
            </div>
            <div>
                <strong>Website</strong>
                <div><a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($website); ?></a></div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($service_area): ?>
<div class="service-area-section">
    <h2 class="section-title">Service Area</h2>
    <div class="service-area-content">
        <div class="service-area-list">
            <h3>Areas We Serve</h3>
            <ul>
                <?php
                $areas = is_array($service_area) && isset($service_area['areas']) 
                    ? array_map('trim', explode(',', $service_area['areas'])) 
                    : array_map('trim', explode(',', $service_area));
                
                foreach ($areas as $area):
                ?>
                    <li><?php echo esc_html($area); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div id="service-area-map" class="map-container"></div>
    </div>
</div>
<?php endif; ?>

<?php
/**
 * Helper function to get current business hours status
 */
function get_current_business_hours($business_hours) {
    if (empty($business_hours)) return false;
    
    $current_day = strtolower(date('l'));
    $current_time = date('H:i');
    
    foreach ($business_hours as $hours) {
        if (strtolower($hours['day_of_week']) === $current_day) {
            if (empty($hours['opening_time']) || empty($hours['closing_time'])) {
                return array(
                    'is_open' => false,
                    'next_open' => get_next_open_time($business_hours)
                );
            }

            $is_open = strtotime($current_time) >= strtotime($hours['opening_time']) && 
                      strtotime($current_time) < strtotime($hours['closing_time']);

            return array(
                'is_open' => $is_open,
                'close' => $hours['closing_time'],
                'next_open' => $is_open ? null : get_next_open_time($business_hours)
            );
        }
    }
    
    return false;
}

function get_next_open_time($business_hours) {
    // Implementation for finding next open time
    // Add logic here to find the next opening time
    return 'tomorrow at 8:00 AM'; // Placeholder
}
?>
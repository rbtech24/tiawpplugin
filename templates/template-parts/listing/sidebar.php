<?php
/**
 * Template part for displaying the sidebar
 */
?>

<aside class="sidebar">
    <!-- Business Hours Widget -->
    <div class="sidebar-widget">
        <h3 class="widget-title">Business Hours</h3>
        <ul class="hours-list">
            <?php
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            $current_day = date('l');
            
            foreach ($days as $day):
                $hours = array_values(array_filter($business_hours, function($h) use ($day) {
                    return strtolower($h['day_of_week']) === strtolower($day);
                }));
                $day_hours = !empty($hours) ? $hours[0] : null;
            ?>
            <li class="hours-item <?php echo ($day === $current_day) ? 'current-day' : ''; ?>">
                <span class="day"><?php echo $day; ?></span>
                <span class="hours">
                    <?php
                    if (!empty($day_hours) && !empty($day_hours['opening_time']) && !empty($day_hours['closing_time'])) {
                        echo esc_html($day_hours['opening_time'] . ' - ' . $day_hours['closing_time']);
                    } else {
                        echo 'Closed';
                    }
                    ?>
                </span>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Voting Widget -->
    <div class="voting-widget">
        <h3 class="voting-title">Support This Business</h3>
        <?php display_voting_buttons(get_the_ID()); ?>
    </div>

    <!-- Quick Contact Form -->
    <div class="sidebar-widget">
        <h3 class="widget-title">Request Quote</h3>
        <form class="quick-contact-form">
            <input type="text" placeholder="Your Name" required>
            <input type="email" placeholder="Email Address" required>
            <input type="tel" placeholder="Phone Number">
            <select required>
                <option value="">Select Service</option>
                <?php foreach ($services as $service): ?>
                    <option value="<?php echo esc_attr($service['service_name']); ?>">
                        <?php echo esc_html($service['service_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <textarea placeholder="Describe your needs..." rows="4"></textarea>
            <button type="submit" class="submit-button">Get Quote</button>
        </form>
    </div>

    <!-- Social Links -->
    <?php if (!empty($social_links)): ?>
    <div class="sidebar-widget">
        <h3 class="widget-title">Follow Us</h3>
        <div class="social-links">
            <?php
            $social_platforms = [
                'facebook' => 'fab fa-facebook',
                'twitter' => 'fab fa-twitter',
                'instagram' => 'fab fa-instagram',
                'linkedin' => 'fab fa-linkedin',
                'google' => 'fab fa-google',
                'yelp' => 'fab fa-yelp'
            ];
            
            foreach ($social_platforms as $platform => $icon):
                if (!empty($social_links[$platform])):
            ?>
            <a href="<?php echo esc_url($social_links[$platform]); ?>" 
               class="social-link" 
               target="_blank" 
               rel="noopener">
                <i class="<?php echo $icon; ?>"></i>
            </a>
            <?php 
                endif;
            endforeach;
            ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Similar Businesses -->
    <div class="sidebar-widget">
        <h3 class="widget-title">Similar Businesses Nearby</h3>
        <?php display_similar_businesses(); ?>
    </div>
</aside>
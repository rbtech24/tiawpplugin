<?php
function directory_listings_admin_dashboard() {
    ?>
    <div class="wrap">
        <h1>Directory Listings Dashboard</h1>
        
        <?php
        // Show pending listings notification
        $pending_listings = wp_count_posts('directory_listing')->pending;
        if ($pending_listings > 0) {
            echo '<div class="notice notice-warning">';
            echo '<p>You have ' . $pending_listings . ' pending listings that need to be verified. ';
            echo '<a href="edit.php?post_type=directory_listing&post_status=pending">View Pending Listings</a></p>';
            echo '</div>';
        }
        ?>

        <div class="dashboard-container">
            <!-- Listings Overview Widget -->
            <div class="dashboard-widget">
                <h2 class="widget-title">Listings Overview</h2>
                <?php
                $total_listings = wp_count_posts('directory_listing')->publish;
                ?>
                <div class="stats-container">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $total_listings; ?></span>
                        <span class="stat-label">Total Listings</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $pending_listings; ?></span>
                        <span class="stat-label">Pending Listings</span>
                    </div>
                </div>
                <div class="widget-actions">
                    <a href="edit.php?post_type=directory_listing" class="button button-primary">Manage Listings</a>
                    <a href="post-new.php?post_type=directory_listing" class="button">Add New Listing</a>
                </div>
            </div>

            <!-- FAQ Section Widget -->
            <div class="dashboard-widget">
                <h2 class="widget-title">FAQ Section</h2>
                <?php
                $total_faqs = wp_count_posts('faq')->publish;
                $pending_faqs = wp_count_posts('faq')->pending;
                $faq_topics = wp_count_terms('faq_topic');
                ?>
                <div class="stats-container">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $total_faqs; ?></span>
                        <span class="stat-label">Total FAQs</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $pending_faqs; ?></span>
                        <span class="stat-label">Pending FAQs</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $faq_topics; ?></span>
                        <span class="stat-label">FAQ Topics</span>
                    </div>
                </div>
                <div class="widget-actions">
                    <a href="edit.php?post_type=faq" class="button button-primary">Manage FAQs</a>
                    <a href="post-new.php?post_type=faq" class="button">Add New FAQ</a>
                    <a href="edit-tags.php?taxonomy=faq_topic&post_type=faq" class="button">Manage Topics</a>
                </div>
            </div>

            <!-- Reviews Widget -->
            <div class="dashboard-widget">
                <h2 class="widget-title">Reviews</h2>
                <?php
                global $wpdb;
                $reviews_table = $wpdb->prefix . 'directory_reviews';
                $total_reviews = $wpdb->get_var("SELECT COUNT(*) FROM $reviews_table");
                $pending_reviews = $wpdb->get_var("SELECT COUNT(*) FROM $reviews_table WHERE status = 'pending'");
                ?>
                <div class="stats-container">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $total_reviews; ?></span>
                        <span class="stat-label">Total Reviews</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $pending_reviews; ?></span>
                        <span class="stat-label">Pending Reviews</span>
                    </div>
                </div>
                <div class="widget-actions">
                    <a href="admin.php?page=manage-reviews" class="button button-primary">Manage Reviews</a>
                </div>
            </div>
        </div>
    </div>
    <style>
        .dashboard-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .dashboard-widget {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .widget-title {
            margin: 0 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            color: #1e3a8a;
        }
        .stats-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-item {
            flex: 1;
            text-align: center;
            min-width: 100px;
        }
        .stat-number {
            display: block;
            font-size: 24px;
            font-weight: bold;
            color: #1e3a8a;
        }
        .stat-label {
            display: block;
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        .widget-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .notice {
            margin: 20px 0;
        }
    </style>
    <?php
}
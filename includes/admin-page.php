<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function get_listing_locations() {
    global $wpdb;
    $locations = $wpdb->get_results("
        SELECT p.ID, p.post_title, pm1.meta_value as lat, pm2.meta_value as long
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = 'lat'
        LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = 'long'
        WHERE p.post_type = 'directory_listing' AND p.post_status = 'publish'
    ");
    return $locations;
}

function directory_listings_admin_page() {
    global $wpdb;
    $locations = get_listing_locations();
    $pending_listings = wp_count_posts('directory_listing')->pending;
    ?>
    <div class="wrap directory-listings-dashboard">
        <h1>Directory Listings Dashboard</h1>
        
        <?php if ($pending_listings > 0): ?>
        <div class="alert-message">
            <p>You have <strong><?php echo $pending_listings; ?></strong> pending listings that need to be verified.</p>
            <a href="edit.php?post_type=directory_listing&post_status=pending" class="button">View Pending Listings</a>
        </div>
        <?php endif; ?>

        <div id="dashboard-grid" class="dashboard-grid">
            <div class="grid-item listings">
                <h2>Listings Overview</h2>
                <?php
                $total_listings = wp_count_posts('directory_listing')->publish;
                ?>
                <p>Total Listings: <span class="highlight"><?php echo $total_listings; ?></span></p>
                <p>Pending Listings: <span class="highlight"><?php echo $pending_listings; ?></span></p>
                <a href="edit.php?post_type=directory_listing" class="button">Manage Listings</a>
            </div>

            <div class="grid-item categories">
                <h2>Business Categories</h2>
                <?php $categories = wp_count_terms('business_category'); ?>
                <p>Total Categories: <span class="highlight"><?php echo $categories; ?></span></p>
                <a href="edit-tags.php?taxonomy=business_category&post_type=directory_listing" class="button">Manage Categories</a>
            </div>

            <div class="grid-item states">
                <h2>States</h2>
                <?php $states = wp_count_terms('state'); ?>
                <p>Total States: <span class="highlight"><?php echo $states; ?></span></p>
                <a href="edit-tags.php?taxonomy=state&post_type=directory_listing" class="button">Manage States</a>
            </div>

            <div class="grid-item qa">
                <h2>Q&A Section</h2>
                <?php $total_questions = wp_count_posts('qa_post')->publish; ?>
                <p>Total Questions: <span class="highlight"><?php echo $total_questions; ?></span></p>
                <a href="edit.php?post_type=qa_post" class="button">Manage Q&A</a>
            </div>

            <div class="grid-item reviews">
                <h2>Reviews</h2>
                <?php
                $review_table = $wpdb->prefix . 'directory_reviews';
                $total_reviews = $wpdb->get_var("SELECT COUNT(*) FROM $review_table");
                ?>
                <p>Total Reviews: <span class="highlight"><?php echo $total_reviews; ?></span></p>
                <a href="admin.php?page=manage-reviews" class="button">Manage Reviews</a>
            </div>

            <div class="grid-item voting">
                <h2>Voting Section</h2>
                <?php
                $votes_table = $wpdb->prefix . 'votes';
                $total_votes = $wpdb->get_var("SELECT COUNT(*) FROM $votes_table");
                
$postmeta_table = $wpdb->prefix . 'postmeta';

// Query to get total Upvote count
$total_upvotes = $wpdb->get_var("
    SELECT SUM(CAST(meta_value AS UNSIGNED)) 
    FROM $postmeta_table
    WHERE meta_key = 'Upvote'
");

$total_downvotes = $wpdb->get_var("
    SELECT SUM(CAST(meta_value AS UNSIGNED)) 
    FROM $postmeta_table
    WHERE meta_key = 'Downvote'
");
      
      $total_votes=        $total_upvotes + $total_downvotes;
                ?>
                <p>Total Votes: <span class="highlight"><?php echo $total_votes; ?></span></p>
                <a href="admin.php?page=manage-votes" class="button">Manage Votes</a>
            </div>

            <div class="grid-item related">
                <h2>Related Listings</h2>
                <p>Configure related listings settings</p>
                <a href="admin.php?page=related-listings-settings" class="button">Settings</a>
            </div>

            <div class="grid-item restaurants">
                <h2>Restaurant Listings</h2>
                <?php
                $total_restaurants = wp_count_posts('restaurant')->publish;
                $pending_restaurants = wp_count_posts('restaurant')->pending;
                ?>
                <p>Total Restaurants: <span class="highlight"><?php echo $total_restaurants; ?></span></p>
                <p>Pending Restaurants: <span class="highlight"><?php echo $pending_restaurants; ?></span></p>
                <a href="edit.php?post_type=restaurant" class="button">Manage Restaurants</a>
            </div>

            <div class="grid-item cuisines">
                <h2>Cuisine Types</h2>
                <?php $cuisines = wp_count_terms('cuisine'); ?>
                <p>Total Cuisine Types: <span class="highlight"><?php echo $cuisines; ?></span></p>
                <a href="edit-tags.php?taxonomy=cuisine&post_type=restaurant" class="button">Manage Cuisines</a>
            </div>

            <div class="grid-item price-ranges">
                <h2>Price Ranges</h2>
                <?php $price_ranges = wp_count_terms('price_range'); ?>
                <p>Total Price Ranges: <span class="highlight"><?php echo $price_ranges; ?></span></p>
                <a href="edit-tags.php?taxonomy=price_range&post_type=restaurant" class="button">Manage Price Ranges</a>
            </div>

            <div class="grid-item restaurant-reviews">
                <h2>Restaurant Reviews</h2>
                <?php
                $restaurant_review_table = $wpdb->prefix . 'restaurant_reviews';
                $total_restaurant_reviews = $wpdb->get_var("SELECT COUNT(*) FROM $restaurant_review_table");
                ?>
                <p>Total Restaurant Reviews: <span class="highlight"><?php echo $total_restaurant_reviews; ?></span></p>
                <a href="admin.php?page=manage-restaurant-reviews" class="button">Manage Restaurant Reviews</a>
            </div>

            <div class="grid-item quick-actions">
                <h2>Quick Actions</h2>
                <a href="post-new.php?post_type=directory_listing" class="button">Add New Listing</a>
                <a href="post-new.php?post_type=restaurant" class="button">Add New Restaurant</a>
            </div>

            <div class="grid-item map">
                <h2>Listing Locations</h2>
                <div id="listingsMap" style="height: 400px;"></div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <style>
        .directory-listings-dashboard {
            background-color: #f0f0f0;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .directory-listings-dashboard h1 {
            color: #1e3a8a;
            text-align: center;
            margin-bottom: 30px;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .grid-item {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }
        .grid-item h2 {
            color: #1e3a8a;
            margin-top: 0;
            margin-bottom: 15px;
            border-bottom: 2px solid #e63946;
            padding-bottom: 10px;
        }
        .grid-item p {
            margin: 10px 0;
            color: #333;
        }
        .highlight {
            font-weight: bold;
            color: #e63946;
        }
        .button {
            display: inline-block;
            background-color: #1e3a8a;
            color: #ffffff;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            margin-top: auto;
            align-self: flex-start;
        }
        .button:hover {
            background-color: #e63946;
        }
        .grid-item.map {
            grid-column: span 3;
        }
        .alert-message {
            background-color: #ffe5e5;
            border: 1px solid #ff9999;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .alert-message p {
            margin: 0 0 10px 0;
            color: #cc0000;
        }
        .alert-message .button {
            background-color: #cc0000;
        }
        .alert-message .button:hover {
            background-color: #990000;
        }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Leaflet map initialization
        var map = L.map('listingsMap').setView([0, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var locations = <?php echo json_encode($locations); ?>;
        var bounds = L.latLngBounds();
        var markersAdded = 0;

        console.log("Locations data:", locations); // Debug log

        locations.forEach(function(location) {
            console.log("Processing location:", location); // Debug log
            if (location.lat && location.long) {
                var lat = parseFloat(location.lat);
                var long = parseFloat(location.long);
                console.log("Parsed coordinates:", lat, long); // Debug log
                if (!isNaN(lat) && !isNaN(long)) {
                    var marker = L.marker([lat, long]).addTo(map);
                    marker.bindPopup(location.post_title);
                    bounds.extend([lat, long]);
                    markersAdded++;
                    console.log("Marker added for:", location.post_title); // Debug log
                } else {
                    console.log("Invalid coordinates for:", location.post_title); // Debug log
                }
            } else {
                console.log("Missing coordinates for:", location.post_title); // Debug log
            }
        });

        console.log("Total markers added:", markersAdded); // Debug log

        if (markersAdded > 0) {
            map.fitBounds(bounds);
        } else {
            map.setView([0, 0], 2);
        }
    });
    </script>
    <?php
}
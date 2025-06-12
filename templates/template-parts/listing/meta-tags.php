<?php
/**
 * Template part for meta tags
 */

// Get the services list for meta description
$services_list = implode(', ', array_column($services, 'service_name'));
?>

<!-- Meta Tags -->
<meta name="description" content="<?php echo esc_attr(wp_trim_words(get_the_excerpt(), 20)); ?> - Serving <?php echo esc_attr($service_area); ?>. Services: <?php echo esc_attr($services_list); ?>">

<!-- Open Graph -->
<meta property="og:type" content="business.business">
<meta property="og:title" content="<?php echo esc_attr(get_the_title()); ?> | Directory">
<meta property="og:description" content="<?php echo esc_attr(wp_trim_words(get_the_excerpt(), 20)); ?> - Serving <?php echo esc_attr($service_area); ?>">
<meta property="og:url" content="<?php echo esc_url(get_permalink()); ?>">
<meta property="og:image" content="<?php echo esc_url($logo ? $logo['url'] : get_template_directory_uri() . '/assets/images/placeholder.png'); ?>">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo esc_attr(get_the_title()); ?> | Directory">
<meta name="twitter:description" content="<?php echo esc_attr(wp_trim_words(get_the_excerpt(), 20)); ?> - Serving <?php echo esc_attr($service_area); ?>">
<meta name="twitter:image" content="<?php echo esc_url($logo ? $logo['url'] : get_template_directory_uri() . '/assets/images/placeholder.png'); ?>">

<!-- Schema.org -->
<script type="application/ld+json">
<?php 
$schema = array(
    "@context" => "http://schema.org",
    "@type" => "LocalBusiness",
    "name" => get_the_title(),
    "image" => $logo ? $logo['url'] : '',
    "telephone" => $phone,
    "email" => $email,
    "url" => $website,
    "address" => array(
        "@type" => "PostalAddress",
        "addressRegion" => $state,
        "addressCountry" => "US"
    ),
    "aggregateRating" => array(
        "@type" => "AggregateRating",
        "ratingValue" => $rating,
        "reviewCount" => $review_count
    ),
    "openingHours" => array(),
    "makesOffer" => array_map(function($service) {
        return array(
            "@type" => "Offer",
            "itemOffered" => array(
                "@type" => "Service",
                "name" => $service['service_name']
            )
        );
    }, $services)
);

foreach ($business_hours as $hours) {
    if (!empty($hours['day_of_week']) && !empty($hours['opening_time']) && !empty($hours['closing_time'])) {
        $schema['openingHours'][] = ucfirst(substr($hours['day_of_week'], 0, 2)) . 
            " " . $hours['opening_time'] . "-" . $hours['closing_time'];
    }
}

echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
?>
</script>
<?php
if (!defined('ABSPATH')) exit;

class Directory_Listings_Leads_Manager {
    public static function init() {
        add_action('wp_ajax_handle_quick_contact', array(__CLASS__, 'handle_form_submission'));
        add_action('wp_ajax_nopriv_handle_quick_contact', array(__CLASS__, 'handle_form_submission'));
    }

    public static function handle_form_submission() {
        check_ajax_referer('quick_contact_nonce', 'nonce');
        
        $listing_id = intval($_POST['listing_id']);
        $listing_type = get_field('listing_type', $listing_id);
        $listing_email = get_field('email', $listing_id);

        // Sanitize form data
        $form_data = array(
            'name' => sanitize_text_field($_POST['name']),
            'email' => sanitize_email($_POST['email']),
            'phone' => sanitize_text_field($_POST['phone']),
            'service_requested' => sanitize_text_field($_POST['service']),
            'message' => sanitize_textarea_field($_POST['message'])
        );

        // Store lead in database
        global $wpdb;
        $table_name = $wpdb->prefix . 'directory_leads';
        
        $lead_data = array(
            'listing_id' => $listing_id,
            'name' => $form_data['name'],
            'email' => $form_data['email'],
            'phone' => $form_data['phone'],
            'service_requested' => $form_data['service_requested'],
            'message' => $form_data['message'],
            'status' => 'new'
        );

        $wpdb->insert($table_name, $lead_data);
        $lead_id = $wpdb->insert_id;

        if ($listing_type === 'paid') {
            self::send_paid_listing_notification($listing_email, $form_data, $listing_id);
            wp_send_json_success(array(
                'message' => 'Your message has been sent directly to the business.',
                'lead_id' => $lead_id
            ));
        } else {
            self::handle_free_listing_lead($lead_id, $form_data, $listing_id);
            wp_send_json_success(array(
                'message' => 'Thanks for your interest! A representative will contact you soon.',
                'lead_id' => $lead_id
            ));
        }
    }

    private static function send_paid_listing_notification($to_email, $form_data, $listing_id) {
        $subject = 'New Lead from ' . get_bloginfo('name');
        $message = "A new lead has been received:\n\n";
        $message .= "Name: " . $form_data['name'] . "\n";
        $message .= "Email: " . $form_data['email'] . "\n";
        $message .= "Phone: " . $form_data['phone'] . "\n";
        $message .= "Service Requested: " . $form_data['service_requested'] . "\n";
        $message .= "Message: " . $form_data['message'] . "\n\n";
        
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail($to_email, $subject, $message, $headers);
    }

    private static function handle_free_listing_lead($lead_id, $form_data, $listing_id) {
        $admin_email = get_option('admin_email');
        $subject = 'New Free Listing Lead';
        $message = "A new lead has been received for listing #" . $listing_id . "\n\n";
        $message .= "Lead ID: " . $lead_id . "\n";
        $message .= "View lead: " . admin_url("admin.php?page=manage-leads&lead=" . $lead_id);
        
        wp_mail($admin_email, $subject, $message);
    }
}
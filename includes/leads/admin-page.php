<?php
if (!defined('ABSPATH')) exit;

function add_leads_management_page() {
    add_submenu_page(
        'directory-listings-dashboard',
        'Manage Leads',
        'Leads',
        'manage_options',
        'manage-leads',
        'render_leads_management_page'
    );
}
add_action('admin_menu', 'add_leads_management_page');

function render_leads_management_page() {
    if (isset($_GET['lead'])) {
        render_single_lead_page(intval($_GET['lead']));
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'directory_leads';
    
    // Get leads with pagination
    $page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $per_page = 20;
    $offset = ($page - 1) * $per_page;
    
    $leads = $wpdb->get_results($wpdb->prepare(
        "SELECT l.*, p.post_title as business_name 
         FROM $table_name l 
         LEFT JOIN {$wpdb->posts} p ON l.listing_id = p.ID 
         ORDER BY l.created_at DESC 
         LIMIT %d OFFSET %d",
        $per_page,
        $offset
    ));

    $total_leads = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    ?>
    <div class="wrap">
        <h1>Manage Leads</h1>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Business</th>
                    <th>Lead Name</th>
                    <th>Service</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($leads as $lead) : ?>
                    <tr>
                        <td><?php echo esc_html($lead->business_name); ?></td>
                        <td><?php echo esc_html($lead->name); ?></td>
                        <td><?php echo esc_html($lead->service_requested); ?></td>
                        <td><?php echo esc_html(ucfirst($lead->status)); ?></td>
                        <td><?php echo esc_html(date('M j, Y', strtotime($lead->created_at))); ?></td>
                        <td>
                            <a href="?page=manage-leads&lead=<?php echo $lead->id; ?>" 
                               class="button button-small">View Details</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php
        echo paginate_links(array(
            'base' => add_query_arg('paged', '%#%'),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => ceil($total_leads / $per_page),
            'current' => $page
        ));
        ?>
    </div>
    <?php
}

function render_single_lead_page($lead_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'directory_leads';
    
    $lead = $wpdb->get_row($wpdb->prepare(
        "SELECT l.*, p.post_title as business_name 
         FROM $table_name l 
         LEFT JOIN {$wpdb->posts} p ON l.listing_id = p.ID 
         WHERE l.id = %d",
        $lead_id
    ));

    if (!$lead) {
        wp_die('Lead not found');
    }

    ?>
    <div class="wrap">
        <h1>Lead Details</h1>
        
        <div class="card">
            <h2>Contact Information</h2>
            <p><strong>Name:</strong> <?php echo esc_html($lead->name); ?></p>
            <p><strong>Email:</strong> <?php echo esc_html($lead->email); ?></p>
            <p><strong>Phone:</strong> <?php echo esc_html($lead->phone); ?></p>
            <p><strong>Business:</strong> <?php echo esc_html($lead->business_name); ?></p>
            <p><strong>Service:</strong> <?php echo esc_html($lead->service_requested); ?></p>
            <p><strong>Message:</strong></p>
            <blockquote><?php echo esc_html($lead->message); ?></blockquote>
        </div>

        <div class="card">
            <h2>Lead Status</h2>
            <form method="post">
                <?php wp_nonce_field('update_lead_status'); ?>
                <select name="status">
                    <option value="new" <?php selected($lead->status, 'new'); ?>>New</option>
                    <option value="contacted" <?php selected($lead->status, 'contacted'); ?>>Contacted</option>
                    <option value="qualified" <?php selected($lead->status, 'qualified'); ?>>Qualified</option>
                    <option value="converted" <?php selected($lead->status, 'converted'); ?>>Converted</option>
                    <option value="lost" <?php selected($lead->status, 'lost'); ?>>Lost</option>
                </select>
                <button type="submit" class="button button-primary">Update Status</button>
            </form>
        </div>
    </div>
    <?php
}
<?php
if (!defined('ABSPATH')) exit;
?>
<div class="wrap">
    <h1 class="wp-heading-inline">Leads Management</h1>
    
    <!-- Filters -->
    <div class="tablenav top">
        <div class="alignleft actions">
            <form method="get">
                <input type="hidden" name="page" value="manage-leads">
                <select name="status">
                    <option value=""><?php _e('All Statuses', 'directory-listings'); ?></option>
                    <option value="new" <?php selected(isset($_GET['status']) ? $_GET['status'] : '', 'new'); ?>><?php _e('New', 'directory-listings'); ?></option>
                    <option value="contacted" <?php selected(isset($_GET['status']) ? $_GET['status'] : '', 'contacted'); ?>><?php _e('Contacted', 'directory-listings'); ?></option>
                    <option value="qualified" <?php selected(isset($_GET['status']) ? $_GET['status'] : '', 'qualified'); ?>><?php _e('Qualified', 'directory-listings'); ?></option>
                    <option value="converted" <?php selected(isset($_GET['status']) ? $_GET['status'] : '', 'converted'); ?>><?php _e('Converted', 'directory-listings'); ?></option>
                </select>
                <input type="submit" class="button" value="<?php _e('Filter', 'directory-listings'); ?>">
            </form>
        </div>
    </div>

    <!-- Leads Table -->
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th class="manage-column"><?php _e('Business', 'directory-listings'); ?></th>
                <th class="manage-column"><?php _e('Lead Name', 'directory-listings'); ?></th>
                <th class="manage-column"><?php _e('Email', 'directory-listings'); ?></th>
                <th class="manage-column"><?php _e('Phone', 'directory-listings'); ?></th>
                <th class="manage-column"><?php _e('Service', 'directory-listings'); ?></th>
                <th class="manage-column"><?php _e('Status', 'directory-listings'); ?></th>
                <th class="manage-column"><?php _e('Created', 'directory-listings'); ?></th>
                <th class="manage-column"><?php _e('Actions', 'directory-listings'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($leads as $lead) : ?>
                <tr>
                    <td><?php echo esc_html($lead->business_name); ?></td>
                    <td><?php echo esc_html($lead->name); ?></td>
                    <td>
                        <a href="mailto:<?php echo esc_attr($lead->email); ?>">
                            <?php echo esc_html($lead->email); ?>
                        </a>
                    </td>
                    <td>
                        <?php if ($lead->phone) : ?>
                            <a href="tel:<?php echo esc_attr($lead->phone); ?>">
                                <?php echo esc_html($lead->phone); ?>
                            </a>
                        <?php endif; ?>
                    </td>
                    <td><?php echo esc_html($lead->service_requested); ?></td>
                    <td>
                        <span class="lead-status status-<?php echo esc_attr($lead->status); ?>">
                            <?php echo esc_html(ucfirst($lead->status)); ?>
                        </span>
                    </td>
                    <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($lead->created_at))); ?></td>
                    <td>
                        <a href="?page=manage-leads&action=view&lead=<?php echo $lead->id; ?>" 
                           class="button button-small">
                            <?php _e('View Details', 'directory-listings'); ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php echo $pagination; ?>
</div>
<?php
if (!defined('ABSPATH')) exit;
?>
<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php _e('Lead Details', 'directory-listings'); ?>
        <a href="?page=manage-leads" class="page-title-action"><?php _e('Back to Leads', 'directory-listings'); ?></a>
    </h1>

    <div class="lead-details-container">
        <!-- Lead Information -->
        <div class="postbox">
            <h2 class="hndle"><?php _e('Lead Information', 'directory-listings'); ?></h2>
            <div class="inside">
                <table class="form-table">
                    <tr>
                        <th><?php _e('Business', 'directory-listings'); ?></th>
                        <td>
                            <a href="<?php echo get_edit_post_link($lead->listing_id); ?>" target="_blank">
                                <?php echo esc_html($lead->business_name); ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Name', 'directory-listings'); ?></th>
                        <td><?php echo esc_html($lead->name); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e('Email', 'directory-listings'); ?></th>
                        <td>
                            <a href="mailto:<?php echo esc_attr($lead->email); ?>">
                                <?php echo esc_html($lead->email); ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Phone', 'directory-listings'); ?></th>
                        <td>
                            <?php if ($lead->phone) : ?>
                                <a href="tel:<?php echo esc_attr($lead->phone); ?>">
                                    <?php echo esc_html($lead->phone); ?>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Service Requested', 'directory-listings'); ?></th>
                        <td><?php echo esc_html($lead->service_requested); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e('Message', 'directory-listings'); ?></th>
                        <td><?php echo wp_kses_post(nl2br($lead->message)); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e('Created', 'directory-listings'); ?></th>
                        <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($lead->created_at))); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Lead Status -->
        <div class="postbox">
            <h2 class="hndle"><?php _e('Lead Status', 'directory-listings'); ?></h2>
            <div class="inside">
                <form method="post" class="lead-status-form">
                    <?php wp_nonce_field('update_lead_status', 'lead_status_nonce'); ?>
                    <input type="hidden" name="lead_id" value="<?php echo esc_attr($lead->id); ?>">
                    
                    <select name="lead_status" id="lead_status">
                        <option value="new" <?php selected($lead->status, 'new'); ?>><?php _e('New', 'directory-listings'); ?></option>
                        <option value="contacted" <?php selected($lead->status, 'contacted'); ?>><?php _e('Contacted', 'directory-listings'); ?></option>
                        <option value="qualified" <?php selected($lead->status, 'qualified'); ?>><?php _e('Qualified', 'directory-listings'); ?></option>
                        <option value="converted" <?php selected($lead->status, 'converted'); ?>><?php _e('Converted', 'directory-listings'); ?></option>
                    </select>

                    <textarea name="lead_notes" id="lead_notes" rows="4" placeholder="<?php esc_attr_e('Add notes about this lead...', 'directory-listings'); ?>"><?php echo esc_textarea($lead->notes); ?></textarea>

                    <button type="submit" class="button button-primary">
                        <?php _e('Update Lead', 'directory-listings'); ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.lead-details-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-top: 20px;
}
.lead-status-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}
#lead_notes {
    margin: 10px 0;
}
.form-table th {
    width: 150px;
}
</style>
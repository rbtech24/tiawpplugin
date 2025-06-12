<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="faq-import-instructions">
        <h3>Import FAQs from CSV</h3>
        <p>Upload a CSV file with the following columns:</p>
        <ul>
            <li><strong>question</strong> (required) - The FAQ question</li>
            <li><strong>answer</strong> (required) - The full answer</li>
            <li><strong>short_answer</strong> - Brief answer (max 200 characters)</li>
            <li><strong>topic</strong> - FAQ topic (comma-separated for multiple)</li>
            <li><strong>state</strong> - State name</li>
            <li><strong>city</strong> - City name</li>
            <li><strong>order</strong> - Display order (numeric)</li>
            <li><strong>featured</strong> - Set to "yes" for featured FAQs</li>
        </ul>
        
        <a href="<?php echo esc_url(DIRECTORY_LISTINGS_PLUGIN_URL . 'templates/samples/faq-sample.csv'); ?>" 
           class="button">Download Sample CSV</a>
    </div>

    <form method="post" enctype="multipart/form-data" class="faq-import-form">
        <?php wp_nonce_field('faq_import_nonce', 'faq_import_nonce'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row"><label for="csv_file">Choose CSV File</label></th>
                <td>
                    <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
                </td>
            </tr>
            <tr>
                <th scope="row">Import Options</th>
                <td>
                    <fieldset>
                        <label>
                            <input type="checkbox" name="update_existing" value="1">
                            Update existing FAQs if question matches
                        </label>
                        <br>
                        <label>
                            <input type="checkbox" name="skip_empty" value="1" checked>
                            Skip rows with empty required fields
                        </label>
                    </fieldset>
                </td>
            </tr>
        </table>

        <p class="submit">
            <input type="submit" name="submit_faq_import" class="button button-primary" 
                   value="Import FAQs">
        </p>
    </form>
</div>
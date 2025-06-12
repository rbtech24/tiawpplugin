<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Directory_FAQ_Importer {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_import_page'));
        add_action('admin_init', array($this, 'handle_import'));
        add_action('admin_notices', array($this, 'display_import_messages'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_import_assets'));
    }

    public function add_import_page() {
        add_submenu_page(
            'edit.php?post_type=faq',
            'Import FAQs',
            'Import FAQs',
            'manage_options',
            'faq-import',
            array($this, 'render_import_page')
        );
    }

    public function render_import_page() {
        include(DIRECTORY_LISTINGS_PLUGIN_PATH . 'templates/admin/faq-import-page.php');
    }

    public function handle_import() {
        if (!isset($_POST['submit_faq_import'])) {
            return;
        }

        if (!check_admin_referer('faq_import_nonce', 'faq_import_nonce')) {
            wp_die('Security check failed');
        }

        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            add_settings_error(
                'faq_import',
                'file_upload',
                'File upload failed. Please try again.',
                'error'
            );
            return;
        }

        $results = $this->process_csv_file($_FILES['csv_file']['tmp_name']);
        $this->set_import_results($results);
    }

    private function process_csv_file($file_path) {
        $results = array(
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => array()
        );

        $file = fopen($file_path, 'r');
        if (!$file) {
            $results['errors'][] = 'Could not open CSV file.';
            return $results;
        }

        $headers = fgetcsv($file);
        $required_columns = array('question', 'answer');
        $missing_columns = array_diff($required_columns, $headers);

        if (!empty($missing_columns)) {
            $results['errors'][] = 'Missing required columns: ' . implode(', ', $missing_columns);
            fclose($file);
            return $results;
        }

        $update_existing = isset($_POST['update_existing']);
        $skip_empty = isset($_POST['skip_empty']);
        $row = 2;

        while (($data = fgetcsv($file)) !== FALSE) {
            // Ensure headers and data have the same number of elements
            if (count($headers) !== count($data)) {
                $results['errors'][] = "Row {$row}: Column count mismatch.";
                $row++;
                continue;
            }

            try {
                $faq_data = array_combine($headers, $data);

                // Skip empty required fields if option is set
                if ($skip_empty && (empty($faq_data['question']) || empty($faq_data['answer']))) {
                    $results['skipped']++;
                    continue;
                }

                $existing_faq = null;
                $query = new WP_Query(array(
                    'post_type' => 'faq',
                    'title' => $faq_data['question'],
                    'posts_per_page' => 1,
                ));
                if ($query->have_posts()) {
                    $existing_faq = $query->post;
                }

                if ($existing_faq && !$update_existing) {
                    $results['skipped']++;
                    continue;
                }

                $faq_id = $this->import_faq_row($faq_data, $existing_faq);

                if ($existing_faq) {
                    $results['updated']++;
                } else {
                    $results['imported']++;
                }
            } catch (Exception $e) {
                $results['errors'][] = "Row {$row}: " . $e->getMessage();
            }
            $row++;
        }

        fclose($file);
        return $results;
    }

    private function import_faq_row($data, $existing_faq = null) {
        $post_data = array(
            'post_title' => wp_strip_all_tags($data['question']),
            'post_content' => wp_kses_post($data['answer']),
            'post_type' => 'faq',
            'post_status' => 'publish'
        );

        if ($existing_faq) {
            $post_data['ID'] = $existing_faq->ID;
            $faq_id = wp_update_post($post_data);
        } else {
            $faq_id = wp_insert_post($post_data);
        }

        if (is_wp_error($faq_id)) {
            throw new Exception($faq_id->get_error_message());
        }

        // Set taxonomies and custom fields
        $this->set_faq_taxonomies($faq_id, $data);
        $this->set_faq_custom_fields($faq_id, $data);

        return $faq_id;
    }

    private function set_faq_taxonomies($faq_id, $data) {
        if (!empty($data['topic'])) {
            $topics = array_map('trim', explode(',', $data['topic']));
            wp_set_object_terms($faq_id, $topics, 'faq_topic');
        }
    }

    private function set_faq_custom_fields($faq_id, $data) {
        if (!function_exists('update_field')) {
            return;
        }

        $fields = array(
            'short_answer' => array('type' => 'text', 'sanitize' => 'sanitize_textarea_field'),
            'faq_order' => array('type' => 'number', 'sanitize' => 'intval'),
            'is_featured' => array('type' => 'boolean', 'sanitize' => function($value) {
                return strtolower($value) === 'yes';
            }),
            'last_reviewed' => array('type' => 'date', 'value' => current_time('Y-m-d'))
        );

        foreach ($fields as $field_name => $field_info) {
            $value = isset($data[$field_name]) ? $data[$field_name] : '';
            if ($value !== '' || $field_info['type'] === 'date') {
                if (isset($field_info['sanitize'])) {
                    $value = $field_info['sanitize']($value);
                }
                update_field($field_name, $value, $faq_id);
            }
        }
    }

    private function set_import_results($results) {
        $message = sprintf(
            'Import complete. Imported: %d, Updated: %d, Skipped: %d',
            $results['imported'],
            $results['updated'],
            $results['skipped']
        );

        add_settings_error(
            'faq_import',
            'import_complete',
            $message,
            'success'
        );

        if (!empty($results['errors'])) {
            add_settings_error(
                'faq_import',
                'import_errors',
                'Errors occurred during import:<br>' . implode('<br>', $results['errors']),
                'error'
            );
        }
    }

    public function display_import_messages() {
        settings_errors('faq_import');
    }

    public function enqueue_import_assets($hook) {
        if ('faq_page_faq-import' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'faq-import-styles',
            plugins_url('assets/css/faq-import.css', dirname(__FILE__)),
            array(),
            '1.0.0'
        );
    }
}

// Initialize the importer
new Directory_FAQ_Importer();

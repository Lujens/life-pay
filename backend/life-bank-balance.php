<?php
/*
Plugin Name: LIFE Bank Balance Checker
Description: A plugin to store and check balances for LIFE Bank users.
Version: 1.1
*/

// Activation hook to create the database table
function create_balance_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_balances';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        account_number varchar(50) NOT NULL,
        name varchar(100) NOT NULL,
        pin varchar(255) NOT NULL,
        balance decimal(10,2) NOT NULL,
        display varchar(100) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Hook to run the table creation on plugin activation
register_activation_hook(__FILE__, 'create_balance_table');

// Add admin menu for CSV upload
function life_bank_admin_menu() {
    add_menu_page('LIFE Bank CSV Upload', 'Balance Uploader', 'manage_options', 'life-bank-uploader', 'life_bank_csv_uploader');
}
add_action('admin_menu', 'life_bank_admin_menu');

// Display the upload form
function life_bank_csv_uploader() {
    ?>
    <h2>Upload CSV to Update Balances</h2>
    <form enctype="multipart/form-data" method="post">
        <input type="file" name="csv_file" accept=".csv">
        <input type="submit" name="upload_csv" value="Upload">
    </form>
    <?php

    if (isset($_POST['upload_csv']) && !empty($_FILES['csv_file']['tmp_name'])) {
        handle_csv_upload($_FILES['csv_file']);
    }
}

// Function to process CSV and insert/update balances (without formatting for DB)
function handle_csv_upload($file) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_balances';

    // Truncate the table to remove all existing records
    $wpdb->query("TRUNCATE TABLE $table_name");

    // Open the CSV file
    if (($handle = fopen($file['tmp_name'], 'r')) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
            
            $account_number = sanitize_text_field($data[0]);
            $name = sanitize_text_field($data[1]);
            $pin = password_hash(sanitize_text_field($data[2]), PASSWORD_DEFAULT); // Hash PIN for security
            $balance = floatval($data[3]); // Store balance as raw decimal value
            $display = sanitize_text_field($data[4]);

            // Insert new record into the table
            $wpdb->insert(
                $table_name,
                array(
                    'account_number' => $account_number,
                    'name'           => $name,
                    'pin'            => $pin,
                    'balance'        => $balance, // Store raw balance (no formatting)
                    'display'       => $display // Display name
                )
            );
        }
        fclose($handle);

        echo '<p>CSV uploaded and data processed successfully.</p>';
    } else {
        echo '<p>Error uploading the CSV file.</p>';
    }
}

// Function to display balances with thousands separator
function display_balance($balance) {
    return number_format($balance, 2, '.', ','); // Formats balance with two decimal places and thousands separator
}

// Example of how to use display_balance when showing a balance
function show_user_balance($account_number) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_balances';
    
    // Get the balance for a specific user based on their account number
    $balance = $wpdb->get_var($wpdb->prepare("SELECT balance FROM $table_name WHERE account_number = %s", $account_number));

    if ($balance !== null) {
        // Return the formatted balance for display
        return display_balance($balance);
    } else {
        return 'Balance not found';
    }
}

// Example usage (for display in template):
// echo show_user_balance('123456789');
?>

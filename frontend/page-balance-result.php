<?php
/* Template Name: Balance Result */
get_header();

global $wpdb;
$table_name = $wpdb->prefix . 'user_balances';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $account_number = sanitize_text_field($_POST['account-number']);
    $pin = sanitize_text_field($_POST['pin']);

    // Query the database for the account
    $user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE account_number = %s OR name = %s", $account_number, $account_number));

    if ($user && password_verify($pin, $user->pin)) {
        $message = "Salut, <strong>" . esc_html($user->display) . "</strong>! 👋<br><br>"
                 . "Ton solde actuel est de <strong>" . esc_html($user->balance) . " G</strong> 💸<br><br>"
                 . "📲 Contacte-nous sur WhatsApp pour un p'tit dépôt, retrait, ou tout ce que tu veux ! 😉💬";
        // Add a button that links to WhatsApp
        $message .= '<br><br><a href="https://lbhaiti.com/wa" target="_blank">
<button type="button" id="whatsapp-btn" style="padding: 10px 15px; background-color: #25D366; color: white; border: none; border-radius: 5px; cursor: pointer;" class="whatsapp-btn">Contacte-nous sur WhatsApp</button></a>';

$message .= '
<style>
    /* Change background color when button is pressed (active state) */
    .whatsapp-btn:active {
        background-color: #34a853; /* Slightly darker green on press */
        transform: scale(0.98); /* Slightly shrink button for feedback */
    }
</style>';

    } else {
        $message = "❌ Identifiant ou code PIN invalide. Merci de vérifier et réessayer ! 🔄";
    }
} else {
    // If no POST data, redirect back to the form page
    wp_redirect('/check-balance'); // Ensure this matches your Check Balance page URL
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Balance</title>
    <style>
        /* Reset some basic styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            height: 100vh;
            color: #333;
        }

        .container {
            background-image: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            margin: 20px auto;
            text-align: left; /* Center text content */
        }

        h1 {
            text-align: left;
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 16px;
            background-color: #f9f9f9;
            color: #333;
        }

        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 8px rgba(76, 175, 80, 0.2);
        }

        .btn {
            display: inline-block;
            width: 100%;
            padding: 14px;
            background-color: #4CAF50;
            color: white;
            font-size: 18px;
            text-align: center;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #45a049;
        }

        .message {
            font-size: 18px;
            margin-top: 20px;
            padding: 10px;
            color: #333;
            line-height: 1.6; /* Add spacing between lines */
        }

        .whatsapp-btn {
            padding: 12px 20px;
            background-color: #25D366; /* WhatsApp green */
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            display: inline-block;
            margin-top: 20px;
        }

        .whatsapp-btn:hover {
            background-color: #1EBE57;
        }
    </style>
</head>

<div class="container">
    <h1></h1>
    <?php if (isset($message)): ?>
        <p><?php echo $message; // Do not escape here so the button can be displayed correctly ?></p>
    <?php endif; ?>
</div>

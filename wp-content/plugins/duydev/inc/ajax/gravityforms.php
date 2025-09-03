<?php 


function add_form_entry($form) {

    $entry = [
        'form_id' => 2,
        '1' => $form['sample'],
        '2' => $form['sample'],
        '3' => $form['sample'],
        '4' => $form['sample'],
        '5' => $form['sample'],
        '13' => $form['sample'],
        '6' => $form['sample'],
        '7' => $form['sample'],
        '8' => $form['sample'],
        '10' => $form['sample'],
        '9' => $form['sample'],
        "21" => $form['sample'],
        "23" => $form['sample'],
    ];

    $entry_id = GFAPI::add_entry($entry);
    return $entry_id;
}

function update_field($entry_id, $field_id, $message) {
    GFAPI::update_entry_field($entry_id, $field_id, $message);
}


// Include email template files
require_once dirname(__DIR__) . '/emails/admin-notification.php';
require_once dirname(__DIR__) . '/emails/customer-confirmation.php';

function send_admin_notification($entry_id) {
    try {
        // Get the entry data
        $entry = GFAPI::get_entry($entry_id);
        if (is_wp_error($entry)) {
            error_log('Failed to get entry data: ' . $entry->get_error_message());
            return false;
        }

        $email_franchise = $entry[23];

        // Get customer details
        $customer_name = rgar($entry, '1');  // Field ID 1 is name
        
        // Prepare email content
        $site_name = get_bloginfo('name');
        $site_url = get_bloginfo('url');
        $logo_url = get_theme_mod('custom_logo') ? wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full') : '';

        // CC recipients for admin email
        $cc_emails = array(
            ///'lam@duydev.com',
            'duy@duydev.com'
        );
        
        // Admin notification email
        $admin_email = $email_franchise;
        $admin_subject = sprintf('New Quote Request - %s - Entry #%s', $customer_name, $entry_id);
        
        // Get HTML email template for admin
        $admin_message = get_admin_notification_template($entry_id, $entry, $logo_url);

        // Admin email headers with CC
        $admin_headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $site_name . ' <' . get_option('admin_email') . '>',
            'Reply-To: ' . $email_franchise
        );

        // Add CC headers for admin email only
        if (!empty($cc_emails)) {
            $admin_headers[] = 'Cc: ' . implode(', ', $cc_emails);
        }

        // Send admin notification
        $admin_mail_sent = wp_mail($admin_email, $admin_subject, $admin_message, $admin_headers);
        if (!$admin_mail_sent) {
            error_log('Failed to send admin notification email for entry ' . $entry_id);
            return false;
        }

        return true;

    } catch (Exception $e) {
        error_log('Error sending admin notification email: ' . $e->getMessage());
        return false;
    }
}

function send_customer_confirmation($entry_id, $payment_status = '') {
    try {
        // Debug log
        error_log('Sending customer confirmation - Entry ID: ' . $entry_id . ', Payment Status: ' . var_export($payment_status, true));
        
        // Get the entry data
        $entry = GFAPI::get_entry($entry_id);
        
        if (is_wp_error($entry)) {
            error_log('Failed to get entry data: ' . $entry->get_error_message());
            return false;
        }

        $email_franchise = $entry[23];

        // Get customer email from entry
        $customer_email = rgar($entry, '2'); // Field ID 2 is email
        if (empty($customer_email)) {
            error_log('No customer email found for entry ' . $entry_id);
            return false;
        }

        // Prepare common email content
        $site_name = get_bloginfo('name');
        $logo_url = get_theme_mod('custom_logo') ? wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full') : '';
        $logo_img = $logo_url ? '<img src="' . esc_url($logo_url) . '" style="max-width: 200px; margin-bottom: 20px;">' : '';

        $cc_emails = array(
            $email_franchise,
            'duy@duydev.com'
        );
        
        // Get email template
        $email_template = get_customer_confirmation_template($entry_id, $entry, $site_name, $logo_img, $payment_status);
        
        // Debug log the email template details
        error_log('Email template subject: ' . $email_template['subject']);
        error_log('Customer email: ' . $customer_email);
        
        // Send the email
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $site_name . ' <' . get_option('admin_email') . '>',
            'Reply-To: ' . $customer_email
        );

        // Add CC headers for admin email only
        if (!empty($cc_emails)) {
            $headers[] = 'Cc: ' . implode(', ', $cc_emails);
        }

        $mail_sent = wp_mail($customer_email, $email_template['subject'], $email_template['message'], $headers);
        
        // Enhanced debugging
        error_log('Mail sent result: ' . var_export($mail_sent, true));
        if (!$mail_sent) {
            // Check if there are any mail errors
            global $phpmailer;
            if (isset($phpmailer) && !empty($phpmailer->ErrorInfo)) {
                error_log('PHPMailer Error: ' . $phpmailer->ErrorInfo);
            }
            error_log('Failed to send customer confirmation email for entry ' . $entry_id);
            return false;
        }

        error_log('Customer confirmation email sent successfully for entry ' . $entry_id);

        return true;

    } catch (Exception $e) {
        error_log('Error sending customer confirmation email: ' . $e->getMessage());
        return false;
    }
}


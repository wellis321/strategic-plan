<?php
// Email sending functions using PHP's mail() function with SMTP support
// For Outlook/Office365 SMTP

function sendEmail($to, $subject, $body, $isHTML = true) {
    $fromEmail = SMTP_FROM_EMAIL;
    $fromName = SMTP_FROM_NAME;

    // If SMTP credentials are configured, use SMTP
    if (!empty(SMTP_USER) && !empty(SMTP_PASS)) {
        return sendEmailSMTP($to, $subject, $body, $isHTML, $fromEmail, $fromName);
    } else {
        // Fallback to PHP mail() function
        return sendEmailPHP($to, $subject, $body, $isHTML, $fromEmail, $fromName);
    }
}

function sendEmailSMTP($to, $subject, $body, $isHTML, $fromEmail, $fromName) {
    // Use PHPMailer if available, otherwise use socket-based SMTP
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        return sendEmailPHPMailer($to, $subject, $body, $isHTML, $fromEmail, $fromName);
    } else {
        return sendEmailSocket($to, $subject, $body, $isHTML, $fromEmail, $fromName);
    }
}

function sendEmailPHPMailer($to, $subject, $body, $isHTML, $fromEmail, $fromName) {
    // Check if PHPMailer is available via Composer
    $phpmailerPath = __DIR__ . '/../vendor/autoload.php';
    if (file_exists($phpmailerPath)) {
        require_once $phpmailerPath;
    } else {
        // Try alternative path
        $phpmailerPath = __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
        if (!file_exists($phpmailerPath)) {
            return false; // PHPMailer not available
        }
        require_once $phpmailerPath;
        require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
        require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';
    }

    // Use fully qualified class names instead of use statements
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';

        // Recipients
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($to);

        // Content
        $mail->isHTML($isHTML);
        $mail->Subject = $subject;
        $mail->Body = $body;
        if (!$isHTML) {
            $mail->AltBody = strip_tags($body);
        }

        $mail->send();
        return true;
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error_log("Email sending failed: {$mail->ErrorInfo}");
        return false;
    }
}

function sendEmailSocket($to, $subject, $body, $isHTML, $fromEmail, $fromName) {
    // Simple socket-based SMTP implementation for Outlook
    $host = SMTP_HOST;
    $port = SMTP_PORT;
    $username = SMTP_USER;
    $password = SMTP_PASS;

    $socket = @fsockopen($host, $port, $errno, $errstr, 30);
    if (!$socket) {
        error_log("SMTP connection failed: $errstr ($errno)");
        return false;
    }

    $response = fgets($socket, 515);
    if (substr($response, 0, 3) != '220') {
        error_log("SMTP error: $response");
        fclose($socket);
        return false;
    }

    // EHLO
    fputs($socket, "EHLO $host\r\n");
    $response = fgets($socket, 515);

    // STARTTLS
    fputs($socket, "STARTTLS\r\n");
    $response = fgets($socket, 515);
    if (substr($response, 0, 3) != '220') {
        error_log("STARTTLS failed: $response");
        fclose($socket);
        return false;
    }

    // Enable crypto
    stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

    // EHLO again after TLS
    fputs($socket, "EHLO $host\r\n");
    $response = fgets($socket, 515);

    // AUTH LOGIN
    fputs($socket, "AUTH LOGIN\r\n");
    $response = fgets($socket, 515);

    fputs($socket, base64_encode($username) . "\r\n");
    $response = fgets($socket, 515);

    fputs($socket, base64_encode($password) . "\r\n");
    $response = fgets($socket, 515);
    if (substr($response, 0, 3) != '235') {
        error_log("SMTP authentication failed: $response");
        fclose($socket);
        return false;
    }

    // MAIL FROM
    fputs($socket, "MAIL FROM: <$fromEmail>\r\n");
    $response = fgets($socket, 515);

    // RCPT TO
    fputs($socket, "RCPT TO: <$to>\r\n");
    $response = fgets($socket, 515);

    // DATA
    fputs($socket, "DATA\r\n");
    $response = fgets($socket, 515);

    // Email headers and body
    $headers = "From: $fromName <$fromEmail>\r\n";
    $headers .= "To: <$to>\r\n";
    $headers .= "Subject: $subject\r\n";
    if ($isHTML) {
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    }
    $headers .= "\r\n";

    fputs($socket, $headers . $body . "\r\n.\r\n");
    $response = fgets($socket, 515);

    // QUIT
    fputs($socket, "QUIT\r\n");
    fclose($socket);

    return substr($response, 0, 3) == '250';
}

function sendEmailPHP($to, $subject, $body, $isHTML, $fromEmail, $fromName) {
    $headers = "From: $fromName <$fromEmail>\r\n";
    $headers .= "Reply-To: $fromEmail\r\n";
    if ($isHTML) {
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    }

    return mail($to, $subject, $body, $headers);
}

function getEmailTemplate($templateName, $variables = []) {
    $templatePath = __DIR__ . '/../templates/emails/' . $templateName . '.php';
    if (!file_exists($templatePath)) {
        return false;
    }

    // Make h() function available in template
    if (!function_exists('h')) {
        require_once __DIR__ . '/functions.php';
    }

    extract($variables);
    ob_start();
    include $templatePath;
    return ob_get_clean();
}
?>

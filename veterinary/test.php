<?php
require "../cn/vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$mail = new PHPMailer(true);
try {
    $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->Username = "menyachannel@gmail.com";
    $mail->Password = "lrscbasxxmvqlxuq";
    $mail->setFrom("menyachannel@gmail.com", "FAO Assistance Platform");
    $mail->addAddress("tesipatience15@gmail.com", "Test Farmer");
    $mail->isHTML(false);
    $mail->Subject = "Test Email";
    $mail->Body = "This is a test email from FAO Platform.";
    $mail->send();
    echo "Test email sent successfully.";
} catch (Exception $e) {
    echo "Test email failed: " . $e->getMessage();
}
?>
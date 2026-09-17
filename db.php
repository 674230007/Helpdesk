<?php
require_once 'phpmailer/Exception.php';
require_once 'phpmailer/PHPMailer.php';
require_once 'phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'helpdesk';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("เชื่อมต่อฐานข้อมูลไม่สำเร็จ: " . $e->getMessage());
}

// 📧 ฟังก์ชันส่งอีเมลจริงผ่าน Gmail SMTP
function sendRealEmail($to_email, $to_name, $subject, $html_body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        
        // 🛑 ตรงนี้ต้องใส่อีเมล Gmail ของคุณจริงๆ
        $mail->Username   = 'yotwadee49824982@gmail.com'; 
        
        // 🛑 ตรงนี้ต้องใส่ App Password 16 หลัก (ติดกัน ไม่เว้นวรรค)
        $mail->Password   = 'vywaynqelhdbbfsg'; 
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom('yotwadee49824982@gmail.com', 'IT Helpdesk System');
        $mail->addAddress($to_email, $to_name);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $html_body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        // หากส่งไม่ผ่าน สามารถเปิดบรรทัดด้านล่างนี้ชั่วคราวเพื่อดู Error ที่เกิดขึ้นได้ครับ
        // echo "Mailer Error: " . $mail->ErrorInfo;
        return false;
    }
}
?>
<?php

require_once '/usr/share/php/libphp-phpmailer/src/PHPMailer.php';
require_once '/usr/share/php/libphp-phpmailer/src/SMTP.php';
require_once '/usr/share/php/libphp-phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// SMTP server 정의
define('GMAIL_SMTP', 'smtp.gmail.com');
define('NAVER_SMTP', 'smtp.naver.com');

// 발신자 정보 정의
define('SITE_NAME', 'put_your_site_name');
define('STUDYHARDWORKOUT_GMAIL', 'put_your_gmail_address');
define('STUDYHARDWORKOUT_GMAIL_PW', 'put_your_gmail_pw');
define('CHOPOKEUM96_NAVER', 'put_your_naver_id');
define('CHOPOKEUM96_NAVER_PW', 'put_your_naver_pw');


function sendMail(
    string $smtp,                   // SMTP server 
    string $from_email,             // 발신 이메일
    string $from_email_pw,          // 발신 이메일 계정 비밀번호 
    string $from_name,              // 발신인 이름 
    string $to_email,               // 수신 이메일 
    string $to_name,                // 수신인 이름 
    string $subject,                // 제목
    array $body) {                  // [ 'html' => (html 본문), 'alt' => (non-html 본문) ]

    // 본인 인증 이메일을 전송한다.
    $mail = new PHPMailer();
    try {
        /* Server settings */
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;            // Enable verbose debug output
        $mail->isSMTP();                                    // Send using SMTP
        $mail->Host       = $smtp;                          // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                           // Enable SMTP authentication
        $mail->Username   = $from_email;                    // SMTP username
        $mail->Password   = $from_email_pw;                 // SMTP password
        $mail->SMTPSecure = "ssl";
        $mail->Port = 465;                                  // TCP port to connect to; 
        
        /* Encoding settings */
        $mail->CharSet = 'utf-8';
        $mail->Encoding = "base64";

        /* Recipients */
        $mail->setFrom($from_email, $from_name);
        $mail->addAddress($to_email, $to_name);             // Add a recipient
      

        /* Content */
        $mail->isHTML(true);            // Set email format to HTML
        $mail->Subject = $subject;	    // 제목
        // 내용
        if (isset($body['html'])) {         
            $mail->Body = $body['html'];
        }
        if (isset($body['alt'])) {
            $mail->AltBody = $body['alt'];
        }

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

?>

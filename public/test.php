<?php

require("libphp-phpmailer/class.phpmailer.php");

// instantiate mailer
$mail = new PHPMailer();

// use your ISP's SMTP server (e.g., smtp.fas.harvard.edu if on campus or smtp.comcast.net if off campus and your ISP is Comcast)
$mail->IsSMTP();
$mail->Host = "smtp.fas.harvard.edu";
$mail->SetFrom("sender@example.com");
$mail->AddAddress("ariannabenson@gmail.com");
$mail->Subject = "hello, world";
$mail->Body = "<html><body>hello, world</body></html>";
$mail->AltBody = "hello, world";

if ($mail->Send() === false)
    die("So the error is: " . $mail->ErrorInfo . "\n");
    
?>

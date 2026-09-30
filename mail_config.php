<?php
/*
|--------------------------------------------------------------------------
| SETTINGS PARA SA FORGOT PASSWORD
|--------------------------------------------------------------------------
*/

// TRUE  = hindi talaga magpapadala; ipapakita ang code sa screen (para sa testing sa localhost)
// FALSE = totoong magpapadala ng email/SMS
define('DEV_MODE', true);

// ---- EMAIL (Gmail SMTP gamit ang PHPMailer) ----
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'youremail@gmail.com');      // Gmail mo
define('SMTP_PASS', 'xxxx xxxx xxxx xxxx');      // Gmail APP PASSWORD (hindi ang normal password)
define('MAIL_FROM_NAME', 'GAD VPA System');

// ---- SMS (Semaphore, para sa PH numbers) ----
define('SEMAPHORE_API_KEY', '');                 // API key mula sa semaphore.co
define('SEMAPHORE_SENDER', '');                  // optional sender name


/*
|--------------------------------------------------------------------------
| MAGPADALA NG OTP SA EMAIL
|--------------------------------------------------------------------------
*/
function sendEmailOtp($to, $code){

    require_once __DIR__ . '/PHPMailer/src/Exception.php';
    require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/PHPMailer/src/SMTP.php';

    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

    try{

        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        $mail->setFrom(SMTP_USER, MAIL_FROM_NAME);
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = 'GAD VPA - Password Reset Code';
        $mail->Body    =
            "<p>Your password reset code is:</p>" .
            "<h2 style='letter-spacing:4px;'>$code</h2>" .
            "<p>This code expires in 10 minutes. If you didn't request this, ignore this email.</p>";
        $mail->AltBody = "Your password reset code is $code. It expires in 10 minutes.";

        $mail->send();
        return true;

    }catch(\Exception $e){

        error_log("Mail error: " . $mail->ErrorInfo);
        return false;

    }

}


/*
|--------------------------------------------------------------------------
| MAGPADALA NG OTP SA SMS (Semaphore)
|--------------------------------------------------------------------------
*/
function sendSmsOtp($number, $code){

    $data = [
        'apikey'  => SEMAPHORE_API_KEY,
        'number'  => $number,
        'message' => "Your GAD VPA password reset code is $code. It expires in 10 minutes."
    ];

    if(SEMAPHORE_SENDER !== ''){
        $data['sendername'] = SEMAPHORE_SENDER;
    }

    $ch = curl_init('https://api.semaphore.co/api/v4/messages');

    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15
    ]);

    $response = curl_exec($ch);
    $http     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if($response === false || $http < 200 || $http >= 300){
        error_log("SMS error ($http): " . $response);
        return false;
    }

    return true;

}
<?php
session_start();
include 'db.php';
require_once 'mail_config.php';

/* ---------- HELPERS ---------- */

function maskEmail($email){
    $parts = explode('@', $email, 2);
    $name  = $parts[0];
    $domain = $parts[1] ?? '';
    return substr($name, 0, 1) . str_repeat('*', max(strlen($name) - 1, 2)) . '@' . $domain;
}

function maskPhone($phone){
    return str_repeat('*', max(strlen($phone) - 3, 0)) . substr($phone, -3);
}

function getUser($conn, $username){
    $stmt = $conn->prepare("SELECT username, email, phone FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/* ---------- STATE ---------- */

$error = "";
$done  = false;
$fp    = $_SESSION['fp'] ?? null;   // laman: username, stage, otp_hash, expires, attempts, last_sent

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $action = $_POST['action'] ?? '';

    // Cancel / start over
    if($action === 'cancel'){
        unset($_SESSION['fp']);
        header("Location: login.php");
        exit();
    }

    // Back to choose method (resend)
    elseif($action === 'back' && $fp){
        $fp['stage'] = 'choose';
        $_SESSION['fp'] = $fp;
    }

    // STEP 1: hanapin ang user
    elseif($action === 'find'){

        $u = getUser($conn, trim($_POST['username'] ?? ''));

        if(!$u || (empty($u['email']) && empty($u['phone']))){
            $error = "Account not found, or no email/phone number is registered. Please contact the admin.";
        }else{
            $fp = ['username' => $u['username'], 'stage' => 'choose'];
            $_SESSION['fp'] = $fp;
        }

    }

    // STEP 2: magpadala ng code
    elseif($action === 'send' && $fp){

        $u      = getUser($conn, $fp['username']);
        $method = $_POST['method'] ?? '';
        $dest   = '';

        if($u && $method === 'email'){ $dest = $u['email'] ?? ''; }
        if($u && $method === 'sms'){   $dest = $u['phone'] ?? ''; }

        if($dest === ''){

            $error = "Invalid method.";

        }elseif(!empty($fp['last_sent']) && (time() - $fp['last_sent']) < 60){

            $wait  = 60 - (time() - $fp['last_sent']);
            $error = "Please wait $wait seconds before requesting another code.";

        }else{

            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $ok   = true;

            if(!DEV_MODE){
                $ok = ($method === 'email') ? sendEmailOtp($dest, $code) : sendSmsOtp($dest, $code);
            }

            if($ok){

                $fp['method']    = $method;
                $fp['masked']    = ($method === 'email') ? maskEmail($dest) : maskPhone($dest);
                $fp['otp_hash']  = password_hash($code, PASSWORD_DEFAULT);
                $fp['expires']   = time() + 600;   // 10 minutes
                $fp['attempts']  = 0;
                $fp['last_sent'] = time();
                $fp['stage']     = 'verify';

                if(DEV_MODE){ $fp['dev_code'] = $code; }

                $_SESSION['fp'] = $fp;

            }else{

                $error = "Failed to send the code. Please try again later.";

            }

        }

    }

    // STEP 3: i-verify ang code
    elseif($action === 'verify' && $fp && ($fp['stage'] ?? '') === 'verify'){

        $otp = trim($_POST['otp'] ?? '');

        if(empty($fp['otp_hash']) || time() > $fp['expires']){

            $error = "Code expired. Please request a new one.";
            $fp['stage'] = 'choose';
            unset($fp['otp_hash'], $fp['dev_code']);

        }else{

            $fp['attempts']++;

            if(password_verify($otp, $fp['otp_hash'])){

                $fp['stage'] = 'reset';
                unset($fp['otp_hash'], $fp['dev_code']);

            }elseif($fp['attempts'] >= 5){

                $error = "Too many wrong attempts. Please request a new code.";
                $fp['stage'] = 'choose';
                unset($fp['otp_hash'], $fp['dev_code']);

            }else{

                $error = "Incorrect code. " . (5 - $fp['attempts']) . " attempt(s) left.";

            }

        }

        $_SESSION['fp'] = $fp;

    }

    // STEP 4: palitan ang password
    elseif($action === 'reset' && $fp && ($fp['stage'] ?? '') === 'reset'){

        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if($new !== $confirm){

            $error = "Passwords do not match.";

        }elseif(strlen($new) < 6){

            $error = "Password must be at least 6 characters.";

        }else{

            $hash = password_hash($new, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE users SET password=? WHERE username=?");
            $stmt->bind_param("ss", $hash, $fp['username']);
            $stmt->execute();

            unset($_SESSION['fp']);
            $fp   = null;
            $done = true;

        }

    }

}

$stage = $done ? 'done' : ($fp['stage'] ?? 'start');

// para sa "choose" step
$user = ($stage === 'choose' && $fp) ? getUser($conn, $fp['username']) : null;
?>

<!DOCTYPE html>
<html>

<head>

<title>Forgot Password - GAD VPA</title>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width,initial-scale=1">

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700;900&display=swap"
rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
}

body{
min-height:100vh;
display:flex;
justify-content:center;
align-items:center;
padding:20px;
font-family:'Poppins',sans-serif;
background:
radial-gradient(circle at 20% 20%, rgba(160,0,255,.35), transparent 30%),
radial-gradient(circle at 80% 20%, rgba(80,0,255,.35), transparent 30%),
radial-gradient(circle at 50% 90%, rgba(90,0,255,.35), transparent 40%),
linear-gradient(-45deg,#12001f,#240046,#3a0ca3,#4a00e0);
background-size:400% 400%;
animation:bgmove 12s infinite ease;
}

@keyframes bgmove{
0%{background-position:0% 50%;}
50%{background-position:100% 50%;}
100%{background-position:0% 50%;}
}

.card-box{
width:420px;
max-width:100%;
padding:40px;
border-radius:30px;
background:rgba(255,255,255,.93);
border:1px solid rgba(255,255,255,.15);
box-shadow:0 0 60px rgba(142,45,226,.35), 0 25px 80px rgba(0,0,0,.5);
text-align:center;
}

.logo{
width:120px;
margin-bottom:15px;
filter:drop-shadow(0 0 15px #8e2de2);
}

.title{
font-size:28px;
font-weight:900;
margin-bottom:8px;
background:linear-gradient(90deg,#6a11cb,#8e2de2,#b44cff);
-webkit-background-clip:text;
-webkit-text-fill-color:transparent;
}

.subtitle{
font-size:14px;
color:#555;
margin-bottom:25px;
}

.form-control{
padding:12px;
border-radius:12px;
}

.otp-input{
text-align:center;
font-size:26px;
letter-spacing:10px;
font-weight:700;
}

.btn-login{
background:linear-gradient(135deg,#8e2de2,#4a00e0);
border:none;
color:white;
font-weight:700;
border-radius:12px;
padding:10px;
transition:.3s;
}

.btn-login:hover{
color:white;
transform:translateY(-3px);
box-shadow:0 0 30px rgba(142,45,226,.6);
}

.btn-method{
border:2px solid #8e2de2;
color:#6a11cb;
font-weight:600;
border-radius:12px;
padding:10px;
background:white;
transition:.3s;
}

.btn-method:hover{
background:#f3e9ff;
color:#4a00e0;
}

.link-btn{
background:none;
border:none;
font-size:14px;
color:#6a11cb;
font-weight:500;
margin-top:18px;
cursor:pointer;
text-decoration:none;
}

.link-btn:hover{
color:#8e2de2;
text-decoration:underline;
}

.error{
color:#dc3545;
margin-bottom:15px;
font-size:14px;
}

.success{
color:#198754;
font-size:15px;
margin-bottom:10px;
}

.dev-box{
background:#fff3cd;
color:#664d03;
border-radius:12px;
padding:10px;
font-size:13px;
margin-bottom:15px;
}

</style>

</head>

<body>

<div class="card-box">

<img src="Images/gad logo.jpg" class="logo">

<h3 class="title">Forgot Password</h3>

<?php if($error): ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>


<?php if($stage === 'start'): ?>

    <p class="subtitle">Enter your username. We'll send a verification code to your registered email or phone number.</p>

    <form method="POST">

        <input type="hidden" name="action" value="find">

        <input
        type="text"
        name="username"
        class="form-control mb-3"
        placeholder="Username"
        required>

        <button type="submit" class="btn btn-login w-100">
            Continue
        </button>

    </form>


<?php elseif($stage === 'choose'): ?>

    <p class="subtitle">Where should we send the verification code?</p>

    <?php if($user && !empty($user['email'])): ?>
        <form method="POST" class="mb-3">
            <input type="hidden" name="action" value="send">
            <input type="hidden" name="method" value="email">
            <button type="submit" class="btn btn-method w-100">
                Email &mdash; <?php echo htmlspecialchars(maskEmail($user['email'])); ?>
            </button>
        </form>
    <?php endif; ?>

    <?php if($user && !empty($user['phone'])): ?>
        <form method="POST" class="mb-3">
            <input type="hidden" name="action" value="send">
            <input type="hidden" name="method" value="sms">
            <button type="submit" class="btn btn-method w-100">
                SMS &mdash; <?php echo htmlspecialchars(maskPhone($user['phone'])); ?>
            </button>
        </form>
    <?php endif; ?>


<?php elseif($stage === 'verify'): ?>

    <p class="subtitle">
        We sent a 6-digit code to <b><?php echo htmlspecialchars($fp['masked']); ?></b>.
        It expires in 10 minutes.
    </p>

    <?php if(DEV_MODE && !empty($fp['dev_code'])): ?>
        <div class="dev-box">
            <b>DEV MODE:</b> walang totoong ipinadala. Ang code mo ay
            <b><?php echo htmlspecialchars($fp['dev_code']); ?></b>
        </div>
    <?php endif; ?>

    <form method="POST">

        <input type="hidden" name="action" value="verify">

        <input
        type="text"
        name="otp"
        class="form-control otp-input mb-3"
        placeholder="000000"
        maxlength="6"
        inputmode="numeric"
        autocomplete="one-time-code"
        required>

        <button type="submit" class="btn btn-login w-100">
            Verify Code
        </button>

    </form>

    <form method="POST">
        <input type="hidden" name="action" value="back">
        <button type="submit" class="link-btn">Didn't get it? Send again</button>
    </form>


<?php elseif($stage === 'reset'): ?>

    <p class="subtitle">Verified! Set your new password.</p>

    <form method="POST">

        <input type="hidden" name="action" value="reset">

        <input
        type="password"
        name="new_password"
        class="form-control mb-3"
        placeholder="New password"
        required>

        <input
        type="password"
        name="confirm_password"
        class="form-control mb-3"
        placeholder="Confirm new password"
        required>

        <button type="submit" class="btn btn-login w-100">
            Change Password
        </button>

    </form>


<?php else: ?>

    <div class="success">Your password has been changed successfully!</div>

    <a href="login.php" class="btn btn-login w-100 mt-2">Back to Login</a>

<?php endif; ?>


<?php if($stage !== 'done'): ?>
    <form method="POST">
        <input type="hidden" name="action" value="cancel">
        <button type="submit" class="link-btn">← Back to Login</button>
    </form>
<?php endif; ?>

</div>

</body>

</html>
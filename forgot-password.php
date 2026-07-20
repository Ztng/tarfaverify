<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$conn = new mysqli("localhost", "root", "", "tarfaverify");
if ($conn->connect_error) die("Database Connection Failed: " . $conn->connect_error);
$message = ""; $msgType = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    if (empty($email)) { $message = "Please enter your email."; $msgType = "error"; }
    else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
        $stmt->bind_param("s", $email); $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $token = bin2hex(random_bytes(32));
            $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));
            $update = $conn->prepare("UPDATE users SET reset_token=?, reset_expires=? WHERE email=?");
            $update->bind_param("sss", $token, $expires, $email); $update->execute();
            header("Location: reset-password.php?token=" . $token); exit;
        } else { $message = "Email not found in our system."; $msgType = "error"; }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password - Tarfaverify</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<style>
:root{--green:#008751;--green-dark:#005c38;--green-light:#00b368;--gold:#FFBF00;--gold-dark:#e6ac00;}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Lato',sans-serif;min-height:100vh;display:flex;overflow-x:hidden;overflow-y:auto;}
.left-panel{flex:1;background:linear-gradient(145deg,var(--green-dark) 0%,var(--green) 55%,var(--green-light) 100%);display:flex;flex-direction:column;justify-content:center;align-items:center;padding:60px 50px;position:relative;overflow:hidden;}
.left-panel::before{content:'';position:absolute;width:480px;height:480px;border-radius:50%;border:2px solid rgba(255,255,255,0.07);top:-110px;left:-110px;animation:rotate 22s linear infinite;}
.left-panel::after{content:'';position:absolute;width:300px;height:300px;border-radius:50%;border:2px solid rgba(255,191,0,0.1);bottom:-80px;right:-80px;animation:rotate 16s linear infinite reverse;}
@keyframes rotate{from{transform:rotate(0deg);}to{transform:rotate(360deg);}}
.brand-area{position:relative;z-index:2;text-align:center;}
.brand-area img{width:180px;margin-bottom:32px;filter:drop-shadow(0 4px 20px rgba(0,0,0,0.3));animation:float 4s ease-in-out infinite;}
@keyframes float{0%,100%{transform:translateY(0);}50%{transform:translateY(-10px);}}
.brand-area h1{font-family:'Montserrat',sans-serif;font-size:34px;font-weight:800;color:#fff;line-height:1.25;margin-bottom:14px;}
.brand-area h1 span{color:var(--gold);}
.brand-area p{color:rgba(255,255,255,0.72);font-size:14px;line-height:1.7;max-width:310px;margin:0 auto 36px;}
.steps{display:flex;flex-direction:column;gap:14px;width:100%;max-width:310px;}
.step{display:flex;align-items:center;gap:14px;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);border-radius:12px;padding:14px 18px;backdrop-filter:blur(4px);}
.step-num{background:var(--gold);color:#000;font-weight:800;font-size:13px;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.step-text{color:rgba(255,255,255,0.85);font-size:13px;}

.right-panel{width:480px;flex-shrink:0;background:#f8faf9;display:flex;flex-direction:column;justify-content:center;padding:60px 50px;position:relative;overflow-y:auto;}
.right-panel::before{content:'';position:absolute;top:0;left:0;right:0;height:5px;background:linear-gradient(90deg,var(--green),var(--gold),var(--green));}
.form-header{margin-bottom:32px;}
.form-header .tag{font-size:11px;font-weight:700;color:var(--gold-dark);text-transform:uppercase;letter-spacing:2.5px;margin-bottom:6px;}
.form-header h2{font-family:'Montserrat',sans-serif;font-size:28px;font-weight:800;color:#1a1a1a;margin-bottom:6px;}
.form-header p{font-size:13px;color:#777;}
.lock-icon{width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,var(--green),var(--green-light));display:flex;align-items:center;justify-content:center;margin-bottom:28px;box-shadow:0 6px 20px rgba(0,135,81,0.25);}
.lock-icon i{color:white;font-size:28px;}
.input-group{margin-bottom:20px;}
.input-group label{display:block;font-size:11px;font-weight:700;color:#444;text-transform:uppercase;letter-spacing:1px;margin-bottom:7px;}
.input-wrap{position:relative;}
.input-wrap i.fi{position:absolute;left:15px;top:50%;transform:translateY(-50%);color:#bbb;font-size:14px;transition:0.2s;}
.input-wrap input{width:100%;padding:14px 14px 14px 42px;border:1.5px solid #e0e0e0;border-radius:10px;font-size:14px;font-family:'Lato',sans-serif;background:white;color:#333;transition:0.2s;outline:none;}
.input-wrap input:focus{border-color:var(--green);box-shadow:0 0 0 3px rgba(0,135,81,0.1);}
.input-wrap:focus-within i.fi{color:var(--green);}
.btn{width:100%;padding:15px;background:linear-gradient(135deg,var(--green) 0%,var(--green-dark) 100%);color:white;border:none;border-radius:10px;font-family:'Montserrat',sans-serif;font-size:14px;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:0.3s;position:relative;overflow:hidden;}
.btn::after{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.15),transparent);transition:0.5s;}
.btn:hover::after{left:100%;}
.btn:hover{background:linear-gradient(135deg,var(--green-light),var(--green));box-shadow:0 8px 20px rgba(0,135,81,0.35);transform:translateY(-1px);}
.msg{padding:12px 15px;border-radius:8px;font-size:13px;font-weight:600;margin-bottom:20px;display:flex;align-items:center;gap:9px;}
.msg.error{background:#fef2f2;color:#c0392b;border:1px solid #fbc4c4;}
.msg.success{background:#e8f7f0;color:#006b3c;border:1px solid #b3e0c8;}
.back-link{display:flex;align-items:center;gap:8px;margin-top:22px;font-size:13px;color:var(--green);font-weight:700;text-decoration:none;justify-content:center;}
.back-link:hover{color:var(--green-dark);}
.footer-note{margin-top:28px;text-align:center;font-size:11px;color:#bbb;border-top:1px solid #eee;padding-top:14px;}

/* ===== COMPREHENSIVE MOBILE FIX ===== */
@media(max-width:1100px){
    .right-panel{width:100%;padding:50px 30px;}
}
@media(max-width:960px){
    body{
        flex-direction:column;
        overflow-y:auto;
        overflow-x:hidden;
        height:auto;
        min-height:100vh;
    }
    .left-panel{display:none !important; width:0 !important; flex:0 !important; padding:0 !important; overflow:hidden !important;}
    .right-panel{
        width:100% !important;
        min-height:100vh;
        padding:40px 28px !important;
        overflow-y:visible;
        justify-content:flex-start;
        padding-top:50px !important;
        flex-shrink:0;
    }
}
@media(max-width:600px){
    .right-panel{
        padding:36px 18px !important;
    }
    .form-header h2, .form-header h2{
        font-size:24px !important;
    }
    .form-header p{
        font-size:13px;
    }
    .lock-icon, .shield-icon, .wallet-icon{
        width:58px !important;
        height:58px !important;
        margin-bottom:18px !important;
    }
    .lock-icon i, .shield-icon i, .wallet-icon i{
        font-size:22px !important;
    }
    .input-wrap input{
        font-size:14px !important;
        padding:13px 13px 13px 40px !important;
    }
    .btn, .btn-login, .btn-signup{
        font-size:13px !important;
        padding:14px !important;
        letter-spacing:0.5px !important;
    }
    .tabs{gap:8px;}
    .tab-btn{font-size:11px !important; padding:10px 6px !important;}
    .row-2{grid-template-columns:1fr !important;}
    .quick-amounts{flex-wrap:wrap;}
    .qa-btn{min-width:calc(50% - 6px) !important; flex:none !important;}
    .back-link{font-size:13px;}
    .footer-note{font-size:10px;}
    .perks, .steps, .tips, .payment-methods{display:none;}
}
@media(max-width:380px){
    .right-panel{padding:28px 14px !important;}
    .form-header h2{font-size:22px !important;}
    .btn, .btn-login, .btn-signup{padding:13px !important;}
}

</style>
</head>
<body>
<div class="left-panel">
    <div class="brand-area">
        <img src="images/tarfalogo.png" alt="Tarfaverify">
        <h1>Reset Your<br><span>Password</span></h1>
        <p>Don't worry! It happens. Follow these quick steps to regain access to your account.</p>
        <div class="steps">
            <div class="step"><div class="step-num">1</div><div class="step-text">Enter your registered email address</div></div>
            <div class="step"><div class="step-num">2</div><div class="step-text">You'll be redirected to reset your password</div></div>
            <div class="step"><div class="step-num">3</div><div class="step-text">Log in with your new password</div></div>
        </div>
    </div>
</div>
<div class="right-panel">
    <div class="lock-icon"><i class="fa-solid fa-lock"></i></div>
    <div class="form-header">
        <div class="tag">Account Recovery</div>
        <h2>Forgot Password?</h2>
        <p>Enter your email and we'll get you back in.</p>
    </div>
    <?php if($message): ?>
    <div class="msg <?php echo $msgType; ?>">
        <i class="fa-solid fa-<?php echo $msgType === 'error' ? 'circle-exclamation' : 'circle-check'; ?>"></i>
        <?php echo htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>
    <form method="POST">
        <div class="input-group">
            <label>Email Address</label>
            <div class="input-wrap">
                <i class="fa-solid fa-envelope fi"></i>
                <input type="email" name="email" placeholder="you@example.com" required>
            </div>
        </div>
        <button type="submit" class="btn"><i class="fa-solid fa-paper-plane"></i> &nbsp; Send Reset Link</button>
    </form>
    <a href="login.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Login</a>
    <div class="footer-note">&copy; <?php echo date('Y'); ?> Tarfaverify &mdash; Powered by ZeeTech Solutions</div>
</div>
</body>
</html>

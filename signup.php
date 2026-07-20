<?php
include('config.php');
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            $error = "Email or username already registered.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, email, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $first_name, $last_name, $username, $email, $hashed_password);
            if($stmt->execute()) {
                header("Location: login.php?signup=success");
                exit();
            } else {
                $error = "Error: " . $stmt->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign Up - Tarfaverify</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<style>
:root{
    --green:#008751;--green-dark:#005c38;--green-light:#00b368;
    --gold:#FFBF00;--gold-dark:#e6ac00;--white:#ffffff;
}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Lato',sans-serif;min-height:100vh;display:flex;overflow-x:hidden;}

/* LEFT PANEL */
.left-panel{
    flex:1;
    background:linear-gradient(145deg,var(--green-dark) 0%,var(--green) 50%,var(--green-light) 100%);
    display:flex;flex-direction:column;justify-content:center;align-items:center;
    padding:60px 50px;position:relative;overflow:hidden;
}
.left-panel::before{
    content:'';position:absolute;width:500px;height:500px;border-radius:50%;
    border:2px solid rgba(255,255,255,0.07);top:-120px;left:-120px;
    animation:rotate 22s linear infinite;
}
.left-panel::after{
    content:'';position:absolute;width:320px;height:320px;border-radius:50%;
    border:2px solid rgba(255,191,0,0.1);bottom:-80px;right:-80px;
    animation:rotate 16s linear infinite reverse;
}
.circle-deco{position:absolute;border-radius:50%;border:1.5px solid rgba(255,255,255,0.05);}
.c1{width:240px;height:240px;top:38%;left:8%;}
.c2{width:170px;height:170px;top:12%;right:8%;border-color:rgba(255,191,0,0.09);}
.c3{width:110px;height:110px;bottom:18%;left:22%;}
@keyframes rotate{from{transform:rotate(0deg);}to{transform:rotate(360deg);}}

.brand-area{position:relative;z-index:2;text-align:center;}
.brand-area img{width:180px;margin-bottom:32px;filter:drop-shadow(0 4px 20px rgba(0,0,0,0.3));animation:float 4s ease-in-out infinite;}
@keyframes float{0%,100%{transform:translateY(0);}50%{transform:translateY(-10px);}}
.brand-area h1{font-family:'Montserrat',sans-serif;font-size:34px;font-weight:800;color:var(--white);line-height:1.25;margin-bottom:14px;text-shadow:0 2px 10px rgba(0,0,0,0.2);}
.brand-area h1 span{color:var(--gold);}
.brand-area p{color:rgba(255,255,255,0.75);font-size:14px;line-height:1.7;max-width:320px;margin:0 auto 36px;}

.perks{display:flex;flex-direction:column;gap:14px;width:100%;max-width:320px;}
.perk{display:flex;align-items:center;gap:14px;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);border-radius:12px;padding:14px 18px;backdrop-filter:blur(4px);}
.perk i{color:var(--gold);font-size:18px;width:22px;text-align:center;}
.perk-text .title{color:white;font-weight:700;font-size:13px;}
.perk-text .desc{color:rgba(255,255,255,0.65);font-size:11px;margin-top:2px;}

/* RIGHT PANEL */
.right-panel{
    width:500px;flex-shrink:0;background:#f8faf9;
    display:flex;flex-direction:column;justify-content:center;
    padding:50px 50px;position:relative;overflow-y:auto;
}
.right-panel::before{
    content:'';position:absolute;top:0;left:0;right:0;height:5px;
    background:linear-gradient(90deg,var(--green),var(--gold),var(--green));
}
.form-header{margin-bottom:28px;}
.form-header .tag{font-size:11px;font-weight:700;color:var(--gold-dark);text-transform:uppercase;letter-spacing:2.5px;margin-bottom:6px;}
.form-header h2{font-family:'Montserrat',sans-serif;font-size:28px;font-weight:800;color:#1a1a1a;margin-bottom:6px;}
.form-header p{font-size:13px;color:#777;}

.row-2{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.input-group{margin-bottom:14px;}
.input-group label{display:block;font-size:11px;font-weight:700;color:#444;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;}
.input-wrap{position:relative;}
.input-wrap i.fi{position:absolute;left:15px;top:50%;transform:translateY(-50%);color:#bbb;font-size:13px;transition:0.2s;}
.input-wrap input{
    width:100%;padding:13px 13px 13px 40px;border:1.5px solid #e0e0e0;
    border-radius:10px;font-size:13px;font-family:'Lato',sans-serif;
    background:white;color:#333;transition:0.2s;outline:none;
}
.input-wrap input:focus{border-color:var(--green);box-shadow:0 0 0 3px rgba(0,135,81,0.1);}
.input-wrap:focus-within i.fi{color:var(--green);}
.toggle-pw{position:absolute;right:13px;top:50%;transform:translateY(-50%);cursor:pointer;color:#bbb;font-size:13px;transition:0.2s;z-index:2;}
.toggle-pw:hover{color:var(--green);}

.btn-signup{
    width:100%;padding:15px;margin-top:6px;
    background:linear-gradient(135deg,var(--green) 0%,var(--green-dark) 100%);
    color:white;border:none;border-radius:10px;
    font-family:'Montserrat',sans-serif;font-size:14px;font-weight:700;
    letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:0.3s;
    position:relative;overflow:hidden;
}
.btn-signup::after{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.15),transparent);transition:0.5s;}
.btn-signup:hover::after{left:100%;}
.btn-signup:hover{background:linear-gradient(135deg,var(--green-light) 0%,var(--green) 100%);box-shadow:0 8px 20px rgba(0,135,81,0.35);transform:translateY(-1px);}

.error-msg{background:#fef2f2;color:#c0392b;border:1px solid #fbc4c4;padding:11px 15px;border-radius:8px;font-size:13px;font-weight:600;margin-bottom:16px;display:flex;align-items:center;gap:9px;}
.divider{display:flex;align-items:center;gap:10px;margin:18px 0 14px;color:#bbb;font-size:12px;}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:#e8e8e8;}
.form-links{text-align:center;font-size:13px;color:#777;}
.form-links a{color:var(--green);font-weight:700;text-decoration:none;}
.form-links a:hover{color:var(--green-dark);text-decoration:underline;}
.footer-note{margin-top:24px;text-align:center;font-size:11px;color:#bbb;border-top:1px solid #eee;padding-top:14px;}

/* ===== COMPREHENSIVE MOBILE FIX ===== */
@media(max-width:1100px){
    .right-panel{width:100%;padding:50px 30px;}
    .row-2{grid-template-columns:1fr;gap:0;}
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
    <div class="circle-deco c1"></div>
    <div class="circle-deco c2"></div>
    <div class="circle-deco c3"></div>
    <div class="brand-area">
        <img src="images/tarfalogo.png" alt="Tarfaverify Logo">
        <h1>Join <span>Tarfaverify</span><br>Today</h1>
        <p>Create your free account and start verifying identities instantly with Nigeria's most trusted platform.</p>
        <div class="perks">
            <div class="perk"><i class="fa-solid fa-id-card"></i><div class="perk-text"><div class="title">NIN & BVN Verification</div><div class="desc">Instant identity lookups in seconds</div></div></div>
            <div class="perk"><i class="fa-solid fa-building-columns"></i><div class="perk-text"><div class="title">CAC Registration</div><div class="desc">Register your business with ease</div></div></div>
            <div class="perk"><i class="fa-solid fa-wallet"></i><div class="perk-text"><div class="title">Wallet Powered</div><div class="desc">Fund once, use anytime</div></div></div>
        </div>
    </div>
</div>

<div class="right-panel">
    <div class="form-header">
        <div class="tag">Get Started</div>
        <h2>Create Account</h2>
        <p>Fill in your details to register</p>
    </div>

    <?php if($error): ?>
    <div class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="row-2">
            <div class="input-group">
                <label>First Name</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-user fi"></i>
                    <input type="text" name="first_name" placeholder="John" required>
                </div>
            </div>
            <div class="input-group">
                <label>Last Name</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-user fi"></i>
                    <input type="text" name="last_name" placeholder="Doe" required>
                </div>
            </div>
        </div>

        <div class="input-group">
            <label>Username</label>
            <div class="input-wrap">
                <i class="fa-solid fa-at fi"></i>
                <input type="text" name="username" placeholder="johndoe" required>
            </div>
        </div>

        <div class="input-group">
            <label>Email Address</label>
            <div class="input-wrap">
                <i class="fa-solid fa-envelope fi"></i>
                <input type="email" name="email" placeholder="you@example.com" required>
            </div>
        </div>

        <div class="input-group">
            <label>Password</label>
            <div class="input-wrap">
                <i class="fa-solid fa-lock fi"></i>
                <input type="password" name="password" id="password" placeholder="Create a password" required>
                <i class="fa-solid fa-eye toggle-pw" onclick="togglePw('password',this)"></i>
            </div>
        </div>

        <div class="input-group">
            <label>Confirm Password</label>
            <div class="input-wrap">
                <i class="fa-solid fa-lock fi"></i>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Repeat your password" required>
                <i class="fa-solid fa-eye toggle-pw" onclick="togglePw('confirm_password',this)"></i>
            </div>
        </div>

        <button type="submit" class="btn-signup"><i class="fa-solid fa-user-plus"></i> &nbsp; Create Account</button>
    </form>

    <div class="divider">or</div>
    <div class="form-links">Already have an account? <a href="login.php">Sign in here &rarr;</a></div>
    <div class="footer-note">&copy; <?php echo date('Y'); ?> Tarfaverify &mdash; Powered by ZeeTech Solutions</div>
</div>

<script>
function togglePw(id, icon){
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
    icon.classList.toggle('fa-eye'); icon.classList.toggle('fa-eye-slash');
}
</script>
</body>
</html>

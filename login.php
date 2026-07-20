<?php
session_start();
include('config.php');

$error = "";
$signupSuccess = isset($_GET['signup']) && $_GET['signup'] === 'success';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        if(password_verify($password, $row['password'])){
            $_SESSION['user_firstname'] = $row['first_name'];
            $_SESSION['user_lastname'] = $row['last_name'];
            $_SESSION['user_email'] = $row['email'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "Email not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Tarfaverify</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<style>
:root{
    --green:#008751;
    --green-dark:#005c38;
    --green-light:#00b368;
    --gold:#FFBF00;
    --gold-dark:#e6ac00;
    --white:#ffffff;
}

*{margin:0;padding:0;box-sizing:border-box;}

body{
    font-family:'Lato', sans-serif;
    min-height:100vh;
    display:flex;
    overflow-x:hidden;
    position:relative;
}

/* ===== LEFT PANEL - Decorative ===== */
.left-panel{
    flex:1;
    background:linear-gradient(145deg, var(--green-dark) 0%, var(--green) 50%, var(--green-light) 100%);
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    padding:60px 50px;
    position:relative;
    overflow:hidden;
}

/* Animated circles */
.left-panel::before{
    content:'';
    position:absolute;
    width:500px;
    height:500px;
    border-radius:50%;
    border:2px solid rgba(255,255,255,0.08);
    top:-100px;
    left:-100px;
    animation:rotate 20s linear infinite;
}

.left-panel::after{
    content:'';
    position:absolute;
    width:350px;
    height:350px;
    border-radius:50%;
    border:2px solid rgba(255,191,0,0.12);
    bottom:-80px;
    right:-80px;
    animation:rotate 15s linear infinite reverse;
}

.circle-deco{
    position:absolute;
    border-radius:50%;
    border:1.5px solid rgba(255,255,255,0.06);
}
.circle-deco.c1{width:250px;height:250px;top:40%;left:10%;}
.circle-deco.c2{width:180px;height:180px;top:15%;right:10%;border-color:rgba(255,191,0,0.1);}
.circle-deco.c3{width:120px;height:120px;bottom:20%;left:25%;}

@keyframes rotate{
    from{transform:rotate(0deg);}
    to{transform:rotate(360deg);}
}

.brand-area{
    position:relative;
    z-index:2;
    text-align:center;
}

.brand-area img{
    width:200px;
    margin-bottom:40px;
    filter:drop-shadow(0 4px 20px rgba(0,0,0,0.3));
    animation:floatLogo 4s ease-in-out infinite;
}

@keyframes floatLogo{
    0%,100%{transform:translateY(0);}
    50%{transform:translateY(-10px);}
}

.brand-area h1{
    font-family:'Montserrat', sans-serif;
    font-size:38px;
    font-weight:800;
    color:var(--white);
    line-height:1.2;
    margin-bottom:16px;
    text-shadow:0 2px 10px rgba(0,0,0,0.2);
}

.brand-area h1 span{
    color:var(--gold);
}

.brand-area p{
    color:rgba(255,255,255,0.75);
    font-size:15px;
    line-height:1.7;
    max-width:340px;
    margin:0 auto 40px;
}

/* Stats row */
.stats{
    display:flex;
    gap:30px;
    justify-content:center;
    flex-wrap:wrap;
}

.stat-item{
    text-align:center;
    background:rgba(255,255,255,0.08);
    border:1px solid rgba(255,255,255,0.12);
    border-radius:12px;
    padding:16px 24px;
    backdrop-filter:blur(4px);
}

.stat-item .num{
    font-family:'Montserrat',sans-serif;
    font-size:26px;
    font-weight:800;
    color:var(--gold);
}

.stat-item .lbl{
    font-size:11px;
    color:rgba(255,255,255,0.7);
    text-transform:uppercase;
    letter-spacing:1px;
    margin-top:2px;
}

/* Trust badges */
.trust-badges{
    display:flex;
    gap:16px;
    margin-top:36px;
    flex-wrap:wrap;
    justify-content:center;
}

.badge{
    display:flex;
    align-items:center;
    gap:8px;
    background:rgba(255,255,255,0.1);
    border:1px solid rgba(255,255,255,0.15);
    border-radius:30px;
    padding:8px 16px;
    font-size:12px;
    color:rgba(255,255,255,0.85);
    backdrop-filter:blur(4px);
}

.badge i{color:var(--gold);}

/* ===== RIGHT PANEL - Login Form ===== */
.right-panel{
    width:480px;
    flex-shrink:0;
    background:#f8faf9;
    display:flex;
    flex-direction:column;
    justify-content:center;
    padding:60px 50px;
    position:relative;
    overflow-y:auto;
}

/* Green top accent bar */
.right-panel::before{
    content:'';
    position:absolute;
    top:0;left:0;right:0;
    height:5px;
    background:linear-gradient(90deg, var(--green), var(--gold), var(--green));
}

.form-header{
    margin-bottom:36px;
}

.form-header .welcome{
    font-size:12px;
    font-weight:700;
    color:var(--gold-dark);
    text-transform:uppercase;
    letter-spacing:2.5px;
    margin-bottom:8px;
}

.form-header h2{
    font-family:'Montserrat', sans-serif;
    font-size:32px;
    font-weight:800;
    color:#1a1a1a;
    margin-bottom:8px;
}

.form-header p{
    font-size:14px;
    color:#666;
}

/* Input group */
.input-group{
    margin-bottom:18px;
}

.input-group label{
    display:block;
    font-size:12px;
    font-weight:700;
    color:#444;
    text-transform:uppercase;
    letter-spacing:1px;
    margin-bottom:8px;
}

.input-wrap{
    position:relative;
}

.input-wrap i.field-icon{
    position:absolute;
    left:16px;
    top:50%;
    transform:translateY(-50%);
    color:#aaa;
    font-size:15px;
    transition:0.2s;
}

.input-wrap input{
    width:100%;
    padding:14px 14px 14px 44px;
    border:1.5px solid #e0e0e0;
    border-radius:10px;
    font-size:14px;
    font-family:'Lato',sans-serif;
    background:white;
    color:#333;
    transition:0.2s;
    outline:none;
}

.input-wrap input:focus{
    border-color:var(--green);
    box-shadow:0 0 0 3px rgba(0,135,81,0.1);
}

.input-wrap input:focus + .field-icon,
.input-wrap:focus-within i.field-icon{
    color:var(--green);
}

.toggle-pw{
    position:absolute;
    right:14px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
    color:#aaa;
    font-size:15px;
    transition:0.2s;
    z-index:2;
}

.toggle-pw:hover{color:var(--green);}

/* Submit button */
.btn-login{
    width:100%;
    padding:16px;
    margin-top:10px;
    background:linear-gradient(135deg, var(--green) 0%, var(--green-dark) 100%);
    color:white;
    border:none;
    border-radius:10px;
    font-family:'Montserrat',sans-serif;
    font-size:15px;
    font-weight:700;
    letter-spacing:1px;
    text-transform:uppercase;
    cursor:pointer;
    transition:0.3s;
    position:relative;
    overflow:hidden;
}

.btn-login::after{
    content:'';
    position:absolute;
    top:0;left:-100%;
    width:100%;height:100%;
    background:linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
    transition:0.5s;
}

.btn-login:hover::after{left:100%;}

.btn-login:hover{
    background:linear-gradient(135deg, var(--green-light) 0%, var(--green) 100%);
    box-shadow:0 8px 20px rgba(0,135,81,0.35);
    transform:translateY(-1px);
}

/* Or divider */
.divider{
    display:flex;
    align-items:center;
    gap:12px;
    margin:22px 0 18px;
    color:#bbb;
    font-size:12px;
}

.divider::before,.divider::after{
    content:'';
    flex:1;
    height:1px;
    background:#e8e8e8;
}

/* Links */
.form-links{
    text-align:center;
    margin-top:4px;
}

.form-links p{
    font-size:13px;
    color:#777;
    margin-bottom:10px;
}

.form-links a{
    color:var(--green);
    font-weight:700;
    text-decoration:none;
    transition:0.2s;
}

.form-links a:hover{color:var(--green-dark); text-decoration:underline;}

.forgot-link{
    display:inline-block;
    font-size:12px;
    color:#999;
    font-weight:600;
    text-decoration:none;
    transition:0.2s;
    margin-top:6px;
}

.forgot-link:hover{color:var(--green);}

/* Messages */
.message{
    padding:12px 16px;
    border-radius:8px;
    font-size:13px;
    font-weight:600;
    margin-bottom:20px;
    display:flex;
    align-items:center;
    gap:10px;
}

.success{background:#e8f7f0;color:#006b3c;border:1px solid #b3e0c8;}
.error-msg{background:#fef2f2;color:#c0392b;border:1px solid #fbc4c4;}

/* Footer note */
.footer-note{
    margin-top:36px;
    text-align:center;
    font-size:11px;
    color:#bbb;
    border-top:1px solid #eee;
    padding-top:16px;
}

/* ===== RESPONSIVE ===== */
@media(max-width:1100px){
    .right-panel{width:100%; padding:50px 30px;}
    .form-header h2{font-size:26px;}
    .form-header p{font-size:13px;}
    .input-wrap input{font-size:13px; padding:13px 13px 13px 42px;}
    .btn-login{font-size:14px; padding:14px;}
    .stat-item .num{font-size:20px;}
    .brand-area h1{font-size:28px;}
    .trust-badges{gap:8px;}
    .badge{font-size:11px; padding:6px 12px;}
    .footer-note{font-size:10px;}
}

/* ===== MOBILE FIX ===== */
@media(max-width:900px){
    body{flex-direction:column; overflow-y:auto; overflow-x:hidden; height:auto; min-height:100vh;}
    .left-panel{display:none !important; width:0 !important; flex:0 !important; padding:0 !important; overflow:hidden !important;}
    .right-panel{width:100% !important; min-height:100vh; padding:50px 28px !important; justify-content:flex-start; padding-top:60px !important; flex-shrink:0;}
}
@media(max-width:600px){
    .right-panel{padding:40px 18px !important;}
    .form-header h2{font-size:26px !important;}
    .input-wrap input{font-size:14px; padding:12px 12px 12px 40px;}
    .btn-login{font-size:13px; padding:14px;}
    .footer-note{font-size:10px;}
}
@media(max-width:380px){
    .right-panel{padding:30px 14px !important;}
    .form-header h2{font-size:22px !important;}
}

</style>
</head>
<body>

<!-- ===== LEFT PANEL ===== -->
<div class="left-panel">
    <div class="circle-deco c1"></div>
    <div class="circle-deco c2"></div>
    <div class="circle-deco c3"></div>

    <div class="brand-area">
        <img src="images/tarfalogo.png" alt="Tarfaverify Logo">

        <h1>Welcome to<br><span>Tarfaverify</span></h1>
        <p>Nigeria's trusted identity verification platform. Fast, secure, and reliable verification services at your fingertips.</p>

        <div class="stats">
            <div class="stat-item">
                <div class="num">NIN</div>
                <div class="lbl">Search</div>
            </div>
            <div class="stat-item">
                <div class="num">BVN</div>
                <div class="lbl">Lookup</div>
            </div>
            <div class="stat-item">
                <div class="num">CAC</div>
                <div class="lbl">Registration</div>
            </div>
        </div>

        <div class="trust-badges">
            <div class="badge"><i class="fa-solid fa-shield-halved"></i> Secure & Encrypted</div>
            <div class="badge"><i class="fa-solid fa-bolt"></i> Instant Results</div>
            <div class="badge"><i class="fa-solid fa-certificate"></i> NIMC Verified</div>
        </div>
    </div>
</div>

<!-- ===== RIGHT PANEL ===== -->
<div class="right-panel">
    <div class="form-header">
        <div class="welcome">Welcome Back</div>
        <h2>Sign In</h2>
        <p>Enter your credentials to access your dashboard</p>
    </div>

    <?php if($signupSuccess): ?>
    <div class="message success">
        <i class="fa-solid fa-circle-check"></i> Signup successful! Please login.
    </div>
    <?php endif; ?>

    <?php if($error): ?>
    <div class="message error-msg">
        <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
    </div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <label>Email Address</label>
            <div class="input-wrap">
                <i class="fa-solid fa-envelope field-icon"></i>
                <input type="email" name="email" placeholder="you@example.com" required>
            </div>
        </div>

        <div class="input-group">
            <label>Password</label>
            <div class="input-wrap">
                <i class="fa-solid fa-lock field-icon"></i>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
                <i class="fa-solid fa-eye toggle-pw" onclick="togglePassword('password', this)"></i>
            </div>
        </div>

        <div style="text-align:right; margin-top:-6px; margin-bottom:20px;">
            <a href="forgot-password.php" class="forgot-link"><i class="fa-solid fa-key"></i> Forgot Password?</a>
        </div>

        <button type="submit" class="btn-login">
            <i class="fa-solid fa-right-to-bracket"></i> &nbsp; Login to Dashboard
        </button>
    </form>

    <div class="divider">or</div>

    <div class="form-links">
        <p>Don't have an account? <a href="signup.php">Create one here &rarr;</a></p>
    </div>

    <div class="footer-note">
        &copy; <?php echo date('Y'); ?> Tarfaverify &mdash; Powered by ZeeTech Solutions<br>
        <a href="mailto:zeetecsolutions@gmail.com" style="color:#bbb; font-size:11px;">zeetecsolutions@gmail.com</a>
    </div>
</div>

<script>
function togglePassword(id, icon) {
    const input = document.getElementById(id);
    if(input.type === "password"){
        input.type = "text";
        icon.classList.replace('fa-eye','fa-eye-slash');
    } else {
        input.type = "password";
        icon.classList.replace('fa-eye-slash','fa-eye');
    }
}
</script>
</body>
</html>

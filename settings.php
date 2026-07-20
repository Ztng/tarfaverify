<?php
session_start();
include('config.php');
if(!isset($_SESSION['user_email'])){ header("Location: index.php"); exit(); }
$message = ""; $msgType = "";
$stmt = $conn->prepare("SELECT first_name, last_name, email, password FROM users WHERE email=?");
$stmt->bind_param("s", $_SESSION['user_email']); $stmt->execute();
$result = $stmt->get_result(); $user = $result->fetch_assoc();
if(isset($_POST['update_profile'])){
    $first_name = $_POST['first_name'] ?? ''; $last_name = $_POST['last_name'] ?? ''; $email = $_POST['email'] ?? '';
    $update = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=? WHERE email=?");
    $update->bind_param("ssss", $first_name, $last_name, $email, $_SESSION['user_email']);
    if($update->execute()){ $_SESSION['user_email'] = $email; $message = "Profile updated successfully!"; $msgType = "success";
        echo "<script>setTimeout(function(){ window.location.href='dashboard.php'; }, 2000);</script>";
    } else { $message = "Profile update failed."; $msgType = "error"; }
}
if(isset($_POST['change_password'])){
    $old_password = $_POST['old_password'] ?? ''; $new_password = $_POST['new_password'] ?? ''; $confirm_password = $_POST['confirm_password'] ?? '';
    if(!password_verify($old_password, $user['password'])){ $message = "Old password is incorrect."; $msgType = "error"; }
    elseif($new_password !== $confirm_password){ $message = "New passwords do not match."; $msgType = "error"; }
    else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password=? WHERE email=?");
        $update->bind_param("ss", $hashed, $_SESSION['user_email']);
        if($update->execute()){ $message = "Password updated successfully!"; $msgType = "success";
            echo "<script>setTimeout(function(){ window.location.href='dashboard.php'; }, 2000);</script>";
        } else { $message = "Password update failed."; $msgType = "error"; }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings - Tarfaverify</title>
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
.brand-area h1{font-family:'Montserrat',sans-serif;font-size:32px;font-weight:800;color:#fff;line-height:1.25;margin-bottom:14px;}
.brand-area h1 span{color:var(--gold);}
.brand-area p{color:rgba(255,255,255,0.72);font-size:14px;line-height:1.7;max-width:310px;margin:0 auto 32px;}
.user-card{background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.15);border-radius:16px;padding:22px 28px;width:100%;max-width:310px;backdrop-filter:blur(4px);}
.user-card .avatar{width:56px;height:56px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-family:'Montserrat',sans-serif;font-size:22px;font-weight:800;color:#000;}
.user-card .name{color:white;font-family:'Montserrat',sans-serif;font-weight:700;font-size:16px;text-align:center;}
.user-card .email{color:rgba(255,255,255,0.65);font-size:12px;text-align:center;margin-top:4px;}
.user-card .divider{height:1px;background:rgba(255,255,255,0.15);margin:16px 0;}
.user-card .info-row{display:flex;align-items:center;gap:10px;color:rgba(255,255,255,0.8);font-size:12px;margin-bottom:8px;}
.user-card .info-row i{color:var(--gold);width:16px;}

.right-panel{width:520px;flex-shrink:0;background:#f8faf9;display:flex;flex-direction:column;justify-content:center;padding:50px 50px;position:relative;overflow-y:auto;}
.right-panel::before{content:'';position:absolute;top:0;left:0;right:0;height:5px;background:linear-gradient(90deg,var(--green),var(--gold),var(--green));}
.form-header{margin-bottom:24px;}
.form-header .tag{font-size:11px;font-weight:700;color:var(--gold-dark);text-transform:uppercase;letter-spacing:2.5px;margin-bottom:6px;}
.form-header h2{font-family:'Montserrat',sans-serif;font-size:26px;font-weight:800;color:#1a1a1a;margin-bottom:6px;}
.form-header p{font-size:13px;color:#777;}

.tabs{display:flex;gap:10px;margin-bottom:26px;}
.tab-btn{flex:1;padding:11px 10px;border:1.5px solid #e0e0e0;border-radius:10px;background:white;font-family:'Montserrat',sans-serif;font-size:12px;font-weight:700;color:#888;cursor:pointer;transition:0.2s;text-transform:uppercase;letter-spacing:0.5px;}
.tab-btn.active{background:var(--green);color:white;border-color:var(--green);}
.tab-btn:not(.active):hover{border-color:var(--green);color:var(--green);}

.tab-content{display:none;}
.tab-content.active{display:block;}

.row-2{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.input-group{margin-bottom:14px;}
.input-group label{display:block;font-size:11px;font-weight:700;color:#444;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;}
.input-wrap{position:relative;}
.input-wrap i.fi{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#bbb;font-size:13px;transition:0.2s;}
.input-wrap input{width:100%;padding:13px 13px 13px 40px;border:1.5px solid #e0e0e0;border-radius:10px;font-size:13px;font-family:'Lato',sans-serif;background:white;color:#333;transition:0.2s;outline:none;}
.input-wrap input:focus{border-color:var(--green);box-shadow:0 0 0 3px rgba(0,135,81,0.1);}
.input-wrap:focus-within i.fi{color:var(--green);}
.toggle-pw{position:absolute;right:12px;top:50%;transform:translateY(-50%);cursor:pointer;color:#bbb;font-size:13px;z-index:2;transition:0.2s;}
.toggle-pw:hover{color:var(--green);}
.btn{width:100%;padding:14px;background:linear-gradient(135deg,var(--green) 0%,var(--green-dark) 100%);color:white;border:none;border-radius:10px;font-family:'Montserrat',sans-serif;font-size:14px;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:0.3s;position:relative;overflow:hidden;margin-top:6px;}
.btn::after{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.15),transparent);transition:0.5s;}
.btn:hover::after{left:100%;}
.btn:hover{box-shadow:0 8px 20px rgba(0,135,81,0.35);transform:translateY(-1px);}
.msg{padding:11px 14px;border-radius:8px;font-size:13px;font-weight:600;margin-bottom:16px;display:flex;align-items:center;gap:9px;}
.msg.error{background:#fef2f2;color:#c0392b;border:1px solid #fbc4c4;}
.msg.success{background:#e8f7f0;color:#006b3c;border:1px solid #b3e0c8;}
.back-link{display:flex;align-items:center;gap:8px;margin-top:20px;font-size:13px;color:var(--green);font-weight:700;text-decoration:none;justify-content:center;}
.back-link:hover{color:var(--green-dark);}
.footer-note{margin-top:20px;text-align:center;font-size:11px;color:#bbb;border-top:1px solid #eee;padding-top:14px;}

/* ===== COMPREHENSIVE MOBILE FIX ===== */
@media(max-width:1100px){
    .right-panel{width:100%;padding:50px 30px;}
    .row-2{grid-template-columns:1fr;}
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
        flex-shrink:0;
        padding-top:50px !important;
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
        <h1>Account<br><span>Settings</span></h1>
        <p>Manage your profile and keep your account secure with a strong password.</p>
        <div class="user-card">
            <div class="avatar"><?php echo strtoupper(substr($user['first_name'],0,1).substr($user['last_name'],0,1)); ?></div>
            <div class="name"><?php echo htmlspecialchars($user['first_name'].' '.$user['last_name']); ?></div>
            <div class="email"><?php echo htmlspecialchars($user['email']); ?></div>
            <div class="divider"></div>
            <div class="info-row"><i class="fa-solid fa-circle-check"></i> Verified Account</div>
            <div class="info-row"><i class="fa-solid fa-shield-halved"></i> Secured with Password</div>
            <div class="info-row"><i class="fa-solid fa-wallet"></i> Wallet Active</div>
        </div>
    </div>
</div>
<div class="right-panel">
    <div class="form-header">
        <div class="tag">My Account</div>
        <h2>Account Settings</h2>
        <p>Update your profile or change your password</p>
    </div>
    <?php if($message): ?>
    <div class="msg <?php echo $msgType; ?>">
        <i class="fa-solid fa-<?php echo $msgType==='error'?'circle-exclamation':'circle-check'; ?>"></i>
        <?php echo $message; ?>
    </div>
    <?php endif; ?>
    <div class="tabs">
        <button class="tab-btn active" id="tab-profile" onclick="openTab('profile')"><i class="fa-solid fa-user"></i> Profile</button>
        <button class="tab-btn" id="tab-password" onclick="openTab('password')"><i class="fa-solid fa-lock"></i> Password</button>
    </div>

    <!-- PROFILE TAB -->
    <div id="profile" class="tab-content active">
        <form method="POST">
            <div class="row-2">
                <div class="input-group">
                    <label>First Name</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-user fi"></i>
                        <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                    </div>
                </div>
                <div class="input-group">
                    <label>Last Name</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-user fi"></i>
                        <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                    </div>
                </div>
            </div>
            <div class="input-group">
                <label>Email Address</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-envelope fi"></i>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
            </div>
            <button type="submit" name="update_profile" class="btn"><i class="fa-solid fa-floppy-disk"></i> &nbsp; Save Profile</button>
        </form>
    </div>

    <!-- PASSWORD TAB -->
    <div id="password" class="tab-content">
        <form method="POST">
            <div class="input-group">
                <label>Current Password</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock fi"></i>
                    <input type="password" name="old_password" id="old_pw" placeholder="Enter current password" required>
                    <i class="fa-solid fa-eye toggle-pw" onclick="togglePw('old_pw',this)"></i>
                </div>
            </div>
            <div class="input-group">
                <label>New Password</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock fi"></i>
                    <input type="password" name="new_password" id="new_pw" placeholder="Enter new password" required>
                    <i class="fa-solid fa-eye toggle-pw" onclick="togglePw('new_pw',this)"></i>
                </div>
            </div>
            <div class="input-group">
                <label>Confirm New Password</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock fi"></i>
                    <input type="password" name="confirm_password" id="conf_pw" placeholder="Repeat new password" required>
                    <i class="fa-solid fa-eye toggle-pw" onclick="togglePw('conf_pw',this)"></i>
                </div>
            </div>
            <button type="submit" name="change_password" class="btn"><i class="fa-solid fa-key"></i> &nbsp; Update Password</button>
        </form>
    </div>

    <a href="dashboard.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
    <div class="footer-note">&copy; <?php echo date('Y'); ?> Tarfaverify &mdash; Powered by ZeeTech Solutions</div>
</div>
<script>
function openTab(name){
    document.querySelectorAll('.tab-content').forEach(t=>t.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
    document.getElementById(name).classList.add('active');
    document.getElementById('tab-'+name).classList.add('active');
}
function togglePw(id,icon){
    const i=document.getElementById(id);
    i.type=i.type==='password'?'text':'password';
    icon.classList.toggle('fa-eye');icon.classList.toggle('fa-eye-slash');
}
window.onload=function(){ openTab('profile'); }
</script>
</body>
</html>

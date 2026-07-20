<?php
$conn = new mysqli("localhost", "root", "", "tarfaverify");
if($conn->connect_error){ die("Connection failed: " . $conn->connect_error); }

$message = ""; $msgType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email); $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $token = bin2hex(random_bytes(50));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));
        $update = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
        $update->bind_param("sss", $token, $expires, $email); $update->execute();
        $reset_link = "http://localhost/Tarfaverify/reset-password.php?token=" . $token;
        $message = "Reset link generated! <a href='$reset_link' style='color:var(--green);font-weight:700;'>Click here to reset</a>";
        $msgType = "success";
    } else {
        $message = "Email not found in our system.";
        $msgType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password (Dev) - Tarfaverify</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<style>
:root{--green:#008751;--green-dark:#005c38;--green-light:#00b368;--gold:#FFBF00;--gold-dark:#e6ac00;}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Lato',sans-serif;min-height:100vh;background:linear-gradient(145deg,var(--green-dark),var(--green),var(--green-light));display:flex;justify-content:center;align-items:center;padding:20px;overflow-x:hidden;}

.card{background:white;border-radius:18px;padding:40px 36px;width:100%;max-width:440px;box-shadow:0 20px 50px rgba(0,0,0,0.25);position:relative;}
.card::before{content:'';position:absolute;top:0;left:0;right:0;height:5px;background:linear-gradient(90deg,var(--green),var(--gold),var(--green));border-radius:18px 18px 0 0;}

.dev-badge{display:inline-flex;align-items:center;gap:7px;background:#fff3cd;border:1px solid #ffc107;border-radius:20px;padding:5px 14px;font-size:11px;font-weight:700;color:#856404;margin-bottom:20px;text-transform:uppercase;letter-spacing:1px;}
.dev-badge i{font-size:11px;}

.icon-wrap{width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,var(--green),var(--green-light));display:flex;align-items:center;justify-content:center;margin-bottom:20px;box-shadow:0 6px 20px rgba(0,135,81,0.25);}
.icon-wrap i{color:white;font-size:24px;}
.tag{font-size:11px;font-weight:700;color:var(--gold-dark);text-transform:uppercase;letter-spacing:2.5px;margin-bottom:6px;}
h2{font-family:'Montserrat',sans-serif;font-size:24px;font-weight:800;color:#1a1a1a;margin-bottom:6px;}
.subtitle{font-size:13px;color:#777;margin-bottom:24px;}

.input-group{margin-bottom:18px;}
.input-group label{display:block;font-size:11px;font-weight:700;color:#444;text-transform:uppercase;letter-spacing:1px;margin-bottom:7px;}
.input-wrap{position:relative;}
.input-wrap i.fi{position:absolute;left:15px;top:50%;transform:translateY(-50%);color:#bbb;font-size:14px;}
.input-wrap input{width:100%;padding:14px 14px 14px 42px;border:1.5px solid #e0e0e0;border-radius:10px;font-size:14px;font-family:'Lato',sans-serif;background:white;color:#333;outline:none;transition:0.2s;}
.input-wrap input:focus{border-color:var(--green);box-shadow:0 0 0 3px rgba(0,135,81,0.1);}
.input-wrap:focus-within i.fi{color:var(--green);}

.btn{width:100%;padding:15px;background:linear-gradient(135deg,var(--green),var(--green-dark));color:white;border:none;border-radius:10px;font-family:'Montserrat',sans-serif;font-size:14px;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:0.3s;margin-top:4px;}
.btn:hover{box-shadow:0 8px 20px rgba(0,135,81,0.35);transform:translateY(-1px);}

.msg{padding:12px 15px;border-radius:8px;font-size:13px;font-weight:600;margin-bottom:16px;display:flex;align-items:flex-start;gap:9px;line-height:1.5;}
.msg.error{background:#fef2f2;color:#c0392b;border:1px solid #fbc4c4;}
.msg.success{background:#e8f7f0;color:#006b3c;border:1px solid #b3e0c8;}
.msg i{margin-top:1px;flex-shrink:0;}

.back-link{display:flex;align-items:center;gap:8px;margin-top:20px;font-size:13px;color:var(--green);font-weight:700;text-decoration:none;justify-content:center;}
.back-link:hover{color:var(--green-dark);}
.note{margin-top:18px;padding:12px;background:#f8f9fa;border-radius:8px;font-size:11px;color:#888;line-height:1.6;border-left:3px solid var(--gold);}
.note strong{color:#555;}

@media(max-width:480px){
    body{padding:14px;}
    .card{padding:28px 18px;}
    h2{font-size:20px;}
}
</style>
</head>
<body>
<div class="card">
    <div class="dev-badge"><i class="fa-solid fa-code"></i> Developer / Local Mode</div>
    <div class="icon-wrap"><i class="fa-solid fa-lock"></i></div>
    <div class="tag">Password Recovery</div>
    <h2>Forgot Password</h2>
    <p class="subtitle">Enter your email to generate a reset link</p>

    <?php if($message): ?>
    <div class="msg <?php echo $msgType; ?>">
        <i class="fa-solid fa-<?php echo $msgType==='error'?'circle-exclamation':'circle-check'; ?>"></i>
        <span><?php echo $message; ?></span>
    </div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <label>Email Address</label>
            <div class="input-wrap">
                <i class="fa-solid fa-envelope fi"></i>
                <input type="email" name="email" placeholder="Enter your registered email" required>
            </div>
        </div>
        <button type="submit" class="btn"><i class="fa-solid fa-paper-plane"></i> &nbsp; Generate Reset Link</button>
    </form>

    <div class="note">
        <strong>Dev Note:</strong> Since email sending is not configured, the reset link is displayed directly on screen. Copy the link and open it in your browser to reset the password.
    </div>

    <a href="login.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Login</a>
</div>
</body>
</html>

<?php
session_start();
include('../config.php');

if(!isset($_SESSION['user_email'])){
    header("Location: ../index.php");
    exit();
}

// Get current wallet balance
$stmt = $conn->prepare("SELECT wallet_balance FROM users WHERE email = ?");
$stmt->bind_param("s", $_SESSION['user_email']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$wallet_balance = $user['wallet_balance'] ?? 0;

$user_email = $_SESSION['user_email'];
$error = "";
$unique_ref = 'TARFA_' . uniqid() . '_' . time();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $amount = floatval($_POST['amount']);
    if($amount <= 0){ $error = "Enter a valid amount"; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Fund Wallet - Tarfaverify</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<style>
:root{--green:#008751;--green-dark:#005c38;--green-light:#00b368;--gold:#FFBF00;--gold-dark:#e6ac00;}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Lato',sans-serif;min-height:100vh;display:flex;overflow-x:hidden;background:#f4f6f9;}

/* TOP BAR */
.topbar{display:none;}

/* MAIN LAYOUT */
.page-wrap{width:100%;display:flex;flex-direction:column;min-height:100vh;}

/* HEADER */
.header{background:var(--green);padding:14px 30px;display:flex;justify-content:space-between;align-items:center;color:white;}
.header .logo{font-family:'Montserrat',sans-serif;font-size:16px;font-weight:800;color:white;text-decoration:none;}
.header .balance{background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);border-radius:20px;padding:6px 16px;font-size:13px;font-weight:700;}
.header .back-btn{display:flex;align-items:center;gap:6px;color:white;text-decoration:none;font-size:13px;font-weight:600;opacity:0.85;}
.header .back-btn:hover{opacity:1;}

/* CONTENT */
.content{flex:1;display:flex;align-items:flex-start;justify-content:center;padding:40px 20px;gap:30px;flex-wrap:wrap;}

/* LEFT INFO PANEL */
.info-panel{width:320px;flex-shrink:0;}
.wallet-card{background:linear-gradient(135deg,var(--green),var(--green-dark));border-radius:18px;padding:28px;color:white;margin-bottom:20px;position:relative;overflow:hidden;}
.wallet-card::before{content:'';position:absolute;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,0.05);top:-60px;right:-60px;}
.wallet-card .wc-label{font-size:11px;text-transform:uppercase;letter-spacing:2px;opacity:0.7;margin-bottom:8px;}
.wallet-card .wc-amount{font-family:'Montserrat',sans-serif;font-size:36px;font-weight:800;margin-bottom:4px;}
.wallet-card .wc-email{font-size:12px;opacity:0.65;}
.info-cards{display:flex;flex-direction:column;gap:12px;}
.info-card{background:white;border-radius:12px;padding:16px 18px;display:flex;align-items:center;gap:14px;box-shadow:0 2px 8px rgba(0,0,0,0.06);}
.info-card i{color:var(--gold);font-size:20px;width:24px;text-align:center;}
.info-card .ic-text .title{font-size:13px;font-weight:700;color:#333;}
.info-card .ic-text .desc{font-size:11px;color:#999;margin-top:2px;}

/* RIGHT FORM PANEL */
.form-panel{flex:1;max-width:500px;min-width:280px;}
.form-card{background:white;border-radius:18px;padding:36px;box-shadow:0 4px 20px rgba(0,0,0,0.08);position:relative;}
.form-card::before{content:'';position:absolute;top:0;left:0;right:0;height:5px;background:linear-gradient(90deg,var(--green),var(--gold),var(--green));border-radius:18px 18px 0 0;}

.wallet-icon{width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,var(--gold),var(--gold-dark));display:flex;align-items:center;justify-content:center;margin-bottom:20px;box-shadow:0 6px 20px rgba(255,191,0,0.3);}
.wallet-icon i{color:white;font-size:24px;}
.form-header .tag{font-size:11px;font-weight:700;color:var(--gold-dark);text-transform:uppercase;letter-spacing:2.5px;margin-bottom:6px;}
.form-header h2{font-family:'Montserrat',sans-serif;font-size:24px;font-weight:800;color:#1a1a1a;margin-bottom:4px;}
.form-header p{font-size:13px;color:#777;margin-bottom:24px;}

.quick-amounts{display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;}
.qa-btn{flex:1;min-width:70px;padding:9px 6px;border:1.5px solid #e0e0e0;border-radius:8px;background:white;font-family:'Montserrat',sans-serif;font-size:12px;font-weight:700;color:#555;cursor:pointer;transition:0.2s;text-align:center;}
.qa-btn:hover,.qa-btn.active{border-color:var(--green);background:#e8f7f0;color:var(--green);}

.input-group{margin-bottom:20px;}
.input-group label{display:block;font-size:11px;font-weight:700;color:#444;text-transform:uppercase;letter-spacing:1px;margin-bottom:7px;}
.input-wrap{position:relative;}
.input-wrap .currency{position:absolute;left:15px;top:50%;transform:translateY(-50%);color:#555;font-size:16px;font-weight:700;font-family:'Montserrat',sans-serif;}
.input-wrap input{width:100%;padding:14px 14px 14px 40px;border:1.5px solid #e0e0e0;border-radius:10px;font-size:18px;font-family:'Montserrat',sans-serif;font-weight:700;background:white;color:#333;transition:0.2s;outline:none;}
.input-wrap input:focus{border-color:var(--green);box-shadow:0 0 0 3px rgba(0,135,81,0.1);}

.btn{width:100%;padding:15px;background:linear-gradient(135deg,var(--green) 0%,var(--green-dark) 100%);color:white;border:none;border-radius:10px;font-family:'Montserrat',sans-serif;font-size:14px;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:0.3s;position:relative;overflow:hidden;}
.btn::after{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.15),transparent);transition:0.5s;}
.btn:hover::after{left:100%;}
.btn:hover{box-shadow:0 8px 20px rgba(0,135,81,0.35);transform:translateY(-1px);}

.error-msg{background:#fef2f2;color:#c0392b;border:1px solid #fbc4c4;padding:11px 15px;border-radius:8px;font-size:13px;font-weight:600;margin-bottom:16px;display:flex;align-items:center;gap:9px;}
.paystack-note{margin-top:14px;text-align:center;font-size:11px;color:#bbb;display:flex;align-items:center;justify-content:center;gap:6px;}

/* RESPONSIVE */
@media(max-width:768px){
    .header{padding:12px 16px;}
    .header .logo{font-size:14px;}
    .content{padding:20px 14px;gap:16px;}
    .info-panel{width:100%;}
    .wallet-card .wc-amount{font-size:28px;}
    .info-cards{flex-direction:row;flex-wrap:wrap;}
    .info-card{flex:1;min-width:130px;}
    .form-card{padding:28px 20px;}
    .qa-btn{min-width:60px;font-size:11px;}
}
@media(max-width:480px){
    .header{padding:10px 12px;}
    .content{padding:14px 10px;}
    .info-cards{flex-direction:column;}
    .wallet-card{padding:20px;}
    .wallet-card .wc-amount{font-size:24px;}
    .form-card{padding:22px 16px;}
    .quick-amounts{gap:6px;}
    .qa-btn{min-width:calc(50% - 3px);flex:none;}
    .btn{font-size:13px;padding:14px;}
}
</style>
</head>
<body>
<div class="page-wrap">

    <!-- HEADER -->
    <div class="header">
        <a href="../dashboard.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
        <a href="../index.php" class="logo">Tarfaverify</a>
        <div class="balance">&#8358;<?php echo number_format($wallet_balance, 2); ?></div>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <!-- LEFT INFO -->
        <div class="info-panel">
            <div class="wallet-card">
                <div class="wc-label">Current Balance</div>
                <div class="wc-amount">&#8358;<?php echo number_format($wallet_balance, 2); ?></div>
                <div class="wc-email"><?php echo htmlspecialchars($user_email); ?></div>
            </div>
            <div class="info-cards">
                <div class="info-card"><i class="fa-solid fa-credit-card"></i><div class="ic-text"><div class="title">Card Payment</div><div class="desc">Visa & Mastercard</div></div></div>
                <div class="info-card"><i class="fa-solid fa-bolt"></i><div class="ic-text"><div class="title">Instant Credit</div><div class="desc">Funded immediately</div></div></div>
                <div class="info-card"><i class="fa-solid fa-shield-halved"></i><div class="ic-text"><div class="title">SSL Secured</div><div class="desc">100% safe payments</div></div></div>
            </div>
        </div>

        <!-- RIGHT FORM -->
        <div class="form-panel">
            <div class="form-card">
                <div class="wallet-icon"><i class="fa-solid fa-wallet"></i></div>
                <div class="form-header">
                    <div class="tag">Wallet Top-Up</div>
                    <h2>Add Funds</h2>
                    <p>Choose an amount or enter a custom value</p>
                </div>

                <?php if($error): ?>
                <div class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?></div>
                <?php endif; ?>

                <div class="quick-amounts">
                    <button class="qa-btn" onclick="setAmount(500)">&#8358;500</button>
                    <button class="qa-btn" onclick="setAmount(1000)">&#8358;1,000</button>
                    <button class="qa-btn" onclick="setAmount(2000)">&#8358;2,000</button>
                    <button class="qa-btn" onclick="setAmount(5000)">&#8358;5,000</button>
                </div>

                <form id="paystack-form" method="POST">
                    <div class="input-group">
                        <label>Amount (NGN)</label>
                        <div class="input-wrap">
                            <span class="currency">&#8358;</span>
                            <input type="number" step="0.01" name="amount" id="amount-input" placeholder="0.00" min="100" required>
                        </div>
                    </div>
                    <button type="submit" class="btn"><i class="fa-solid fa-lock"></i> &nbsp; Pay Securely</button>
                </form>
                <div class="paystack-note"><i class="fa-solid fa-circle-info"></i> Powered by Paystack &mdash; 100% Secure</div>
            </div>
        </div>

    </div>
</div>

<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
function setAmount(val){
    document.getElementById('amount-input').value = val;
    document.querySelectorAll('.qa-btn').forEach(b=>b.classList.remove('active'));
    event.target.classList.add('active');
}
document.getElementById('paystack-form').addEventListener('submit', function(e){
    e.preventDefault();
    var amount = parseFloat(document.getElementById('amount-input').value);
    if(!amount || amount <= 0){ alert("Enter a valid amount"); return; }
    var handler = PaystackPop.setup({
        key: 'pk_test_ae876f30b42318ae0870492ec728e042c3d35757',
        email: '<?php echo $user_email; ?>',
        amount: amount * 100,
        currency: 'NGN',
        ref: '<?php echo $unique_ref; ?>',
        channels: ['card'],
        callback: function(response){
            window.location.href = '../wallet-success.php?reference=' + response.reference + '&amount=' + amount;
        },
        onClose: function(){ alert('Payment window closed'); }
    });
    handler.openIframe();
});
</script>
</body>
</html>

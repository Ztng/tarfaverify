<?php
session_start();
include('config.php');
if(!isset($_SESSION['user_email'])){ header("Location: login.php"); exit(); }
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
.payment-methods{display:flex;flex-direction:column;gap:12px;width:100%;max-width:310px;}
.pm{display:flex;align-items:center;gap:14px;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);border-radius:12px;padding:14px 18px;backdrop-filter:blur(4px);}
.pm i{color:var(--gold);font-size:18px;width:22px;text-align:center;}
.pm-text .title{color:white;font-weight:700;font-size:13px;}
.pm-text .desc{color:rgba(255,255,255,0.6);font-size:11px;margin-top:2px;}
.secure-badge{display:flex;align-items:center;gap:8px;margin-top:24px;background:rgba(255,191,0,0.12);border:1px solid rgba(255,191,0,0.25);border-radius:30px;padding:9px 18px;color:rgba(255,255,255,0.85);font-size:12px;}
.secure-badge i{color:var(--gold);}

.right-panel{width:500px;flex-shrink:0;background:#f8faf9;display:flex;flex-direction:column;justify-content:center;padding:60px 50px;position:relative;overflow-y:auto;}
.right-panel::before{content:'';position:absolute;top:0;left:0;right:0;height:5px;background:linear-gradient(90deg,var(--green),var(--gold),var(--green));}
.wallet-icon{width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,var(--gold),var(--gold-dark));display:flex;align-items:center;justify-content:center;margin-bottom:26px;box-shadow:0 6px 20px rgba(255,191,0,0.3);}
.wallet-icon i{color:white;font-size:28px;}
.form-header{margin-bottom:28px;}
.form-header .tag{font-size:11px;font-weight:700;color:var(--gold-dark);text-transform:uppercase;letter-spacing:2.5px;margin-bottom:6px;}
.form-header h2{font-family:'Montserrat',sans-serif;font-size:28px;font-weight:800;color:#1a1a1a;margin-bottom:6px;}
.form-header p{font-size:13px;color:#777;}

.quick-amounts{display:flex;gap:10px;margin-bottom:22px;flex-wrap:wrap;}
.qa-btn{flex:1;min-width:80px;padding:10px 8px;border:1.5px solid #e0e0e0;border-radius:8px;background:white;font-family:'Montserrat',sans-serif;font-size:13px;font-weight:700;color:#555;cursor:pointer;transition:0.2s;text-align:center;}
.qa-btn:hover,.qa-btn.active{border-color:var(--green);background:#e8f7f0;color:var(--green);}

.input-group{margin-bottom:22px;}
.input-group label{display:block;font-size:11px;font-weight:700;color:#444;text-transform:uppercase;letter-spacing:1px;margin-bottom:7px;}
.input-wrap{position:relative;}
.input-wrap .currency{position:absolute;left:15px;top:50%;transform:translateY(-50%);color:#666;font-size:16px;font-weight:700;font-family:'Montserrat',sans-serif;}
.input-wrap input{width:100%;padding:14px 14px 14px 42px;border:1.5px solid #e0e0e0;border-radius:10px;font-size:18px;font-family:'Montserrat',sans-serif;font-weight:700;background:white;color:#333;transition:0.2s;outline:none;}
.input-wrap input:focus{border-color:var(--green);box-shadow:0 0 0 3px rgba(0,135,81,0.1);}

.btn{width:100%;padding:16px;background:linear-gradient(135deg,var(--green) 0%,var(--green-dark) 100%);color:white;border:none;border-radius:10px;font-family:'Montserrat',sans-serif;font-size:15px;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:0.3s;position:relative;overflow:hidden;}
.btn::after{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.15),transparent);transition:0.5s;}
.btn:hover::after{left:100%;}
.btn:hover{box-shadow:0 8px 20px rgba(0,135,81,0.35);transform:translateY(-1px);}
.error-msg{background:#fef2f2;color:#c0392b;border:1px solid #fbc4c4;padding:11px 15px;border-radius:8px;font-size:13px;font-weight:600;margin-bottom:18px;display:flex;align-items:center;gap:9px;}
.back-link{display:flex;align-items:center;gap:8px;margin-top:20px;font-size:13px;color:var(--green);font-weight:700;text-decoration:none;justify-content:center;}
.back-link:hover{color:var(--green-dark);}
.paystack-note{margin-top:18px;text-align:center;font-size:11px;color:#bbb;display:flex;align-items:center;justify-content:center;gap:6px;}
.paystack-note i{color:#00c3f7;}
.footer-note{margin-top:20px;text-align:center;font-size:11px;color:#bbb;border-top:1px solid #eee;padding-top:14px;}

/* ===== COMPREHENSIVE MOBILE FIX ===== */
@media(max-width:1100px){
    .right-panel{width:100%;padding:50px 30px;}
    .quick-amounts{gap:6px;}.qa-btn{min-width:60px;font-size:12px;}
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
        <h1>Fund Your<br><span>Wallet</span></h1>
        <p>Top up your Tarfaverify wallet securely using your debit or credit card via Paystack.</p>
        <div class="payment-methods">
            <div class="pm"><i class="fa-solid fa-credit-card"></i><div class="pm-text"><div class="title">Debit / Credit Card</div><div class="desc">Visa, Mastercard accepted</div></div></div>
            <div class="pm"><i class="fa-solid fa-bolt"></i><div class="pm-text"><div class="title">Instant Credit</div><div class="desc">Wallet funded immediately</div></div></div>
            <div class="pm"><i class="fa-solid fa-shield-halved"></i><div class="pm-text"><div class="title">SSL Secured</div><div class="desc">Your payment data is safe</div></div></div>
        </div>
        <div class="secure-badge"><i class="fa-solid fa-lock"></i> Powered by Paystack &mdash; 100% Secure</div>
    </div>
</div>
<div class="right-panel">
    <div class="wallet-icon"><i class="fa-solid fa-wallet"></i></div>
    <div class="form-header">
        <div class="tag">Wallet Top-Up</div>
        <h2>Add Funds</h2>
        <p>Choose an amount or enter a custom value below</p>
    </div>
    <?php if($error): ?>
    <div class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?></div>
    <?php endif; ?>
    <div class="quick-amounts">
        <button class="qa-btn" onclick="setAmount(500)">₦500</button>
        <button class="qa-btn" onclick="setAmount(1000)">₦1,000</button>
        <button class="qa-btn" onclick="setAmount(2000)">₦2,000</button>
        <button class="qa-btn" onclick="setAmount(5000)">₦5,000</button>
    </div>
    <form id="paystack-form" method="POST">
        <div class="input-group">
            <label>Amount (NGN)</label>
            <div class="input-wrap">
                <span class="currency">₦</span>
                <input type="number" step="0.01" name="amount" id="amount-input" placeholder="0.00" min="100" required>
            </div>
        </div>
        <button type="submit" class="btn"><i class="fa-solid fa-lock"></i> &nbsp; Pay Securely</button>
    </form>
    <div class="paystack-note"><i class="fa-solid fa-circle-info"></i> Transactions are processed securely via Paystack</div>
    <a href="dashboard.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
    <div class="footer-note">&copy; <?php echo date('Y'); ?> Tarfaverify &mdash; Powered by ZeeTech Solutions</div>
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
            window.location.href = 'wallet-success.php?reference=' + response.reference + '&amount=' + amount;
        },
        onClose: function(){ alert('Payment window closed'); }
    });
    handler.openIframe();
});
</script>
</body>
</html>

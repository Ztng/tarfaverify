<?php
session_start();
include('config.php');

if(!isset($_SESSION['user_email'])){
    header("Location: login.php");
    exit();
}

$reference = $_GET['reference'] ?? '';
$amount = floatval($_GET['amount'] ?? 0);
$email = $_SESSION['user_email'];

if(!$reference || $amount <= 0){
    header("Location: fund-wallet.php");
    exit();
}

// Update wallet balance directly from URL amount
$stmt = $conn->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE email = ?");
$stmt->bind_param("ds", $amount, $email);
$stmt->execute();

// Get new wallet balance
$bal = $conn->prepare("SELECT wallet_balance FROM users WHERE email = ?");
$bal->bind_param("s", $email);
$bal->execute();
$result = $bal->get_result();
$user = $result->fetch_assoc();
$new_balance = $user['wallet_balance'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Successful - Tarfaverify</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after {
    margin: 0; padding: 0; box-sizing: border-box;
  }

  body {
    font-family: 'Poppins', sans-serif;
    background: #008751;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 20px;
    position: relative;
    overflow-x: hidden;
  }

  /* Background decorative circles */
  body::before {
    content: '';
    position: fixed;
    width: 500px; height: 500px;
    border-radius: 50%;
    background: rgba(255,255,255,0.04);
    top: -150px; right: -150px;
    pointer-events: none;
  }
  body::after {
    content: '';
    position: fixed;
    width: 350px; height: 350px;
    border-radius: 50%;
    background: rgba(255,255,255,0.04);
    bottom: -100px; left: -100px;
    pointer-events: none;
  }

  .card {
    background: #ffffff;
    border-radius: 24px;
    padding: 50px 40px 45px;
    width: 100%;
    max-width: 460px;
    text-align: center;
    box-shadow: 0 25px 60px rgba(0,0,0,0.25);
    animation: slideUp 0.5s ease forwards;
    position: relative;
    z-index: 1;
  }

  @keyframes slideUp {
    from { opacity: 0; transform: translateY(40px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  .logo {
    width: 140px;
    margin-bottom: 28px;
  }

  /* Animated checkmark circle */
  .check-circle {
    width: 90px; height: 90px;
    border-radius: 50%;
    background: linear-gradient(135deg, #008751, #00b368);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 22px;
    box-shadow: 0 8px 25px rgba(0,135,81,0.35);
    animation: popIn 0.4s 0.3s ease both;
  }

  @keyframes popIn {
    from { transform: scale(0); opacity: 0; }
    to   { transform: scale(1); opacity: 1; }
  }

  .check-circle svg {
    width: 44px; height: 44px;
    stroke: #fff;
    stroke-width: 3;
    fill: none;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-dasharray: 60;
    stroke-dashoffset: 60;
    animation: drawCheck 0.5s 0.7s ease forwards;
  }

  @keyframes drawCheck {
    to { stroke-dashoffset: 0; }
  }

  h2 {
    color: #008751;
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 6px;
  }

  .subtitle {
    color: #999;
    font-size: 13px;
    margin-bottom: 28px;
  }

  /* Amount display */
  .amount-display {
    background: linear-gradient(135deg, #008751, #00a862);
    border-radius: 14px;
    padding: 22px;
    margin-bottom: 20px;
    color: white;
  }

  .amount-display .label {
    font-size: 12px;
    opacity: 0.85;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    margin-bottom: 6px;
  }

  .amount-display .naira {
    font-size: 38px;
    font-weight: 800;
    letter-spacing: -1px;
  }

  /* Info rows */
  .info-box {
    background: #f7f9fc;
    border-radius: 12px;
    padding: 18px 20px;
    margin-bottom: 28px;
    text-align: left;
  }

  .info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
    font-size: 13px;
  }

  .info-row:last-child { border-bottom: none; }

  .info-row .key {
    color: #888;
    font-weight: 500;
  }

  .info-row .val {
    color: #333;
    font-weight: 600;
    max-width: 230px;
    text-align: right;
    word-break: break-all;
    font-size: 12px;
  }

  .val.green { color: #008751; }
  .val.gold  { color: #FFBF00; font-size: 13px; }

  /* Button */
  .btn {
    display: block;
    background: #FFBF00;
    color: #fff;
    padding: 16px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    text-decoration: none;
    transition: background 0.25s, transform 0.2s;
    letter-spacing: 0.3px;
  }

  .btn:hover {
    background: #e6ac00;
    transform: translateY(-2px);
  }

  @media(max-width: 600px){
    .card{padding:36px 22px 32px; border-radius:18px;}
    .logo{width:110px; margin-bottom:20px;}
    .check-circle{width:72px; height:72px; margin-bottom:16px;}
    .check-circle svg{width:36px; height:36px;}
    h2{font-size:18px;}
    .subtitle{font-size:12px; margin-bottom:20px;}
    .amount-display{padding:16px; margin-bottom:14px; border-radius:10px;}
    .amount-display .label{font-size:11px;}
    .amount-display .naira{font-size:28px;}
    .info-box{padding:14px 16px; margin-bottom:20px;}
    .info-row{font-size:12px; padding:7px 0;}
    .info-row .val{font-size:11px;}
    .btn{padding:14px; font-size:14px; border-radius:10px;}
  }
  @media(max-width:380px){
    body{padding:12px;}
    .card{padding:28px 16px 24px;}
    .amount-display .naira{font-size:24px;}
    h2{font-size:16px;}
  }
</style>
</head>
<body>

<div class="card">

  <img src="images/tarfalogo.png" alt="Tarfaverify" class="logo">

  <div class="check-circle">
    <svg viewBox="0 0 52 52">
      <polyline points="14,27 22,35 38,18"/>
    </svg>
  </div>

  <h2>Payment Successful!</h2>
  <p class="subtitle">Your wallet has been credited</p>

  <div class="amount-display">
    <div class="label">Amount Funded</div>
    <div class="naira">₦<?php echo number_format($amount, 2); ?></div>
  </div>

  <div class="info-box">
    <div class="info-row">
      <span class="key">Status</span>
      <span class="val green">✔ Success</span>
    </div>
    <div class="info-row">
      <span class="key">New Balance</span>
      <span class="val gold">₦<?php echo number_format($new_balance, 2); ?></span>
    </div>
    <div class="info-row">
      <span class="key">Reference</span>
      <span class="val"><?php echo htmlspecialchars($reference); ?></span>
    </div>
  </div>

  <a href="dashboard.php" class="btn">Go to Dashboard</a>

</div>

</body>
</html>

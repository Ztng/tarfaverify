<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_email'])) {
    header("Location: index.php");
    exit();
}

$stmt = $conn->prepare("SELECT wallet_balance FROM users WHERE email = ?");
$stmt->bind_param("s", $_SESSION['user_email']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$wallet_balance = $user['wallet_balance'] ?? 0;

// Admin email configuration
$admin_email = 'zeeboykd@gmail.com'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - Tarfaverify</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
/* RESTORING YOUR ORIGINAL CSS EXACTLY */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

body{
    background:#f4f6f9;
    min-height:100vh;
    display:flex;
    flex-direction:column;
    overflow-x:hidden;
}

.topbar{
    background:#008751;
    padding:15px 60px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    color:white;
}

.left-top a{
    background:#FFBF00;
    padding:8px 18px;
    border-radius:6px;
    text-decoration:none;
    color:black;
    font-weight:bold;
    font-size:14px;
}

.right-top{
    display:flex;
    gap:25px;
    align-items:center;
    font-size:14px;
}

.right-top a{
    color:white;
    text-decoration:none;
    font-weight:500;
}

.wallet{
    font-weight:bold;
}

.services{
    flex:1;
    padding:50px 80px;
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:24px;
}

.service-box{
    background:white;
    border-radius:10px;
    text-decoration:none;
    color:#333;
    border:1px solid #e0e0e0;
    transition:0.2s ease;
    display:flex;
    flex-direction:row;
    align-items:stretch;
    overflow:hidden;
    min-height:90px;
    box-shadow:0 1px 4px rgba(0,0,0,0.06);
}

.service-box .img-wrap{
    width:55%;
    flex-shrink:0;
    overflow:hidden;
}

.service-box .img-wrap img{
    width:100%;
    height:100%;
    object-fit:cover;
    object-position:center center;
    display:block;
    transition:0.2s ease;
}

.service-box .label{
    flex:1;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:10px 12px;
    font-size:13px;
    font-weight:700;
    text-align:center;
    line-height:1.4;
    background:white;
    transition:0.2s ease;
}

.service-box:hover{
    transform:translateY(-3px);
    box-shadow:0 6px 18px rgba(0,135,81,0.18);
    border-color:#008751;
}

.service-box:hover .label{
    background:#008751;
    color:white;
}

/* ADDING THE ADMIN LINK WITHOUT BREAKING THE GRID */
.admin-link-area {
    text-align: center;
    padding: 20px;
    background: #fff;
    border-top: 1px solid #eee;
}

.admin-portal-btn {
    display: inline-block;
    padding: 12px 30px;
    background: #ff0000;
    color: white;
    text-decoration: none;
    font-weight: bold;
    border-radius: 50px;
    font-size: 14px;
}

.footer-logos{
    background:white;
    padding:35px 100px;
    display:flex;
    justify-content:space-evenly;
    align-items:center;
    flex-wrap:wrap;
    border-top:1px solid #ddd;
}

.footer-logos img{
    height:90px;
    max-width:200px;
    object-fit:contain;
    transition:0.3s ease;
}

.dev-footer{
    background:linear-gradient(135deg,#008751 0%,#005c38 100%);
    padding:28px 60px;
    display:flex;
    justify-content:center;
    align-items:center;
    gap:50px;
    flex-wrap:wrap;
}

.dev-copy{
    color:rgba(255,255,255,0.45);
    font-size:11px;
    text-align:center;
    padding:10px;
    background:#004d2e;
}

@media(max-width:900px){
    .topbar{padding:12px 16px; flex-wrap:wrap; gap:8px;}
    .services{grid-template-columns:repeat(2,1fr); padding:20px 16px; gap:14px;}
    .footer-logos img{height:50px; max-width:90px;}
}
</style>
</head>
<body>

<div class="topbar">
    <div class="left-top"><a href="fund-wallet.php">Fund Wallet</a></div>
    <div class="right-top">
        <div class="wallet">Wallet: &#8358;<?php echo number_format($wallet_balance,2); ?></div>
        <a href="settings.php">Settings</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="services">
    <a href="service_pages/nin-search.php" class="service-box">
        <div class="img-wrap"><img src="images/nin.png" alt="NIN Search"></div>
        <div class="label">NIN Search</div>
    </a>
    <a href="service_pages/bvn-search.php" class="service-box">
        <div class="img-wrap"><img src="images/bvn.png" alt="BVN Search"></div>
        <div class="label">BVN Search</div>
    </a>
    <a href="#" class="service-box">
        <div class="img-wrap"><img src="images/nin-modification.png" alt="NIN Modification"></div>
        <div class="label">NIN Modification</div>
    </a>
    <a href="service_pages/nin-validation.php" class="service-box">
        <div class="img-wrap"><img src="images/nin-validation.png" alt="NIN Validation"></div>
        <div class="label">NIN Validation</div>
    </a>
    <a href="#" class="service-box">
        <div class="img-wrap"><img src="images/nin-ipe-clearance.png" alt="NIN IPE Clearance"></div>
        <div class="label">NIN IPE Clearance</div>
    </a>
    <a href="#" class="service-box">
        <div class="img-wrap"><img src="images/nin-personalization.png" alt="NIN Personalization"></div>
        <div class="label">NIN Personalization</div>
    </a>
    <a href="#" class="service-box">
        <div class="img-wrap"><img src="images/cac2.jpg" alt="CAC Registration"></div>
        <div class="label">CAC Registration</div>
    </a>
    <a href="#" class="service-box">
        <div class="img-wrap"><img src="images/tin.png" alt="NRS TIN REQUEST"></div>
        <div class="label">NRS TIN REQUEST</div>
    </a>
</div>

<?php if (trim($_SESSION['user_email']) === $admin_email): ?>
<div class="admin-link-area">
    <a href="service_pages/admin-validation.php" class="admin-portal-btn">
        <i class="fas fa-lock"></i> OPEN ADMIN PORTAL
    </a>
</div>
<?php endif; ?>

<div class="footer-logos">
    <img src="images/nimc.png" alt="NIMC">
    <img src="images/Bvn-1024x430.jpeg" alt="BVN">
    <img src="images/CAC.jpg" alt="CAC">
    <img src="images/scuml.jpg" alt="SCUML">
    <img src="images/board.jpg" alt="Board">
</div>

<div class="dev-copy">
    &copy; <?php echo date('Y'); ?> Tarfaverify &mdash; Powered by ZeeTech Solutions
</div>

</body>
</html>
<?php
session_start();
include('../config.php');

if (!isset($_SESSION['user_email'])) {
    header("Location: ../login.php");
    exit();
}

/**
 * ── PRICING & CONFIGURATION ──
 */
define('PRICE_BVN_API', 300);    
define('PRICE_BVN_PHONE', 1000); 

$user_email = $_SESSION['user_email'];
$error      = '';
$success    = false;
$current_tab = $_POST['search_type'] ?? 'bvn'; 

// ── GET WALLET BALANCE ──
$stmt = $conn->prepare("SELECT wallet_balance FROM users WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$bal_res = $stmt->get_result()->fetch_assoc();
$current_balance = $bal_res['wallet_balance'] ?? 0;

// ── PROCESS ACTION ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_action'])) {
    $input_val = trim($_POST['number'] ?? '');

    $check = $conn->prepare("SELECT id, status, searched_at, amount_charged FROM bvn_searches WHERE user_email = ? AND bvn = ? LIMIT 1");
    $check->bind_param("ss", $user_email, $input_val);
    $check->execute();
    $existing = $check->get_result()->fetch_assoc();

    if ($existing) {
        $success = true;
        $manual_status = $existing['status'];
        $record_id = $existing['id'];
        $search_time = $existing['searched_at'];
    } else {
        $price = ($current_tab === 'phone') ? PRICE_BVN_PHONE : PRICE_BVN_API;
        if ($current_balance >= $price) {
            $status = ($current_tab === 'phone') ? 'Pending' : 'Success';
            $log = $conn->prepare("INSERT INTO bvn_searches (user_email, bvn, amount_charged, status, searched_at) VALUES (?, ?, ?, ?, NOW())");
            $log->bind_param("ssds", $user_email, $input_val, $price, $status);
            $log->execute();

            $conn->query("UPDATE users SET wallet_balance = wallet_balance - $price WHERE email = '$user_email'");
            
            $success = true;
            $manual_status = $status;
            $current_balance -= $price;
            $search_time = date("Y-m-d H:i:s");
        } else {
            $error = "Insufficient wallet balance.";
        }
    }
}

$history_res = $conn->query("SELECT * FROM bvn_searches WHERE user_email = '$user_email' ORDER BY searched_at DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BVN Details - TarfaVerify</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #008751; --bg: #f4f7f6; }
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; color: #333; }
        
        .topbar { background: var(--primary); color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; position: sticky; top:0; z-index: 1000; }
        .back-btn { color: white; text-decoration: none; font-weight: bold; font-size: 13px; border: 1px solid rgba(255,255,255,0.4); padding: 8px 12px; border-radius: 8px; }

        .container { width: 100%; max-width: 800px; margin: 20px auto; padding: 0 15px; }
        .card { background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow: hidden; }
        
        .banner-box { width: 100%; height: auto; min-height: 100px; max-height: 160px; background: #fff; border-bottom: 3px solid var(--primary); display: flex; justify-content: center; }
        .banner-box img { width: 100%; height: auto; object-fit: contain; display: block; }

        .tabs { display: flex; background: #f1f1f1; }
        .tab { flex: 1; padding: 15px 5px; text-align: center; cursor: pointer; font-weight: bold; color: #777; font-size: 13px; }
        .tab.active { background: white; color: var(--primary); border-bottom: 4px solid var(--primary); }
        
        .search-area { padding: 25px 20px; }
        .input-group label { display: block; font-weight: bold; margin-bottom: 10px; color: var(--primary); font-size: 14px; }
        .input-group input { width: 100%; padding: 15px; border: 2px solid #eee; border-radius: 12px; font-size: 16px; outline: none; }

        .btn-gen { background: var(--primary); color: white; border: none; width: 100%; padding: 18px; border-radius: 12px; font-size: 16px; font-weight: 800; cursor: pointer; margin-top: 20px; display: block; text-decoration: none; text-align: center; }

        .notice { background: #f0fdf4; border-left: 5px solid var(--primary); padding: 15px; border-radius: 8px; margin-bottom: 20px; color: #166534; font-size: 13px; }
        .hidden-notice { display: none; }

        /* RESULT VIEW STYLING */
        .result-view { text-align: center; background: white; padding: 30px 20px; border-radius: 15px; border: 3px solid var(--primary); }
        .status-badge { padding: 6px 15px; border-radius: 20px; font-weight: bold; font-size: 11px; text-transform: uppercase; margin-bottom: 15px; display: inline-block; }
        .status-Pending { background: #fff8e1; color: #856404; }
        
        .countdown { font-size: 32px; font-weight: bold; color: #856404; font-family: monospace; margin: 15px 0; }
        .nav-links { margin-top: 25px; display: flex; flex-direction: column; gap: 15px; }
        .link-back { color: var(--primary); text-decoration: none; font-weight: bold; font-size: 14px; }

        .history-section { background: white; padding: 20px; border-radius: 15px; margin-top: 25px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 500px; }
        td, th { padding: 15px 12px; border-bottom: 1px solid #f8f9fa; font-size: 13px; text-align: left; }

        .hidden { display: none; }

        @media (min-width: 768px) {
            .topbar { padding: 18px 60px; }
            .container { margin: 35px auto; }
            .nav-links { flex-direction: row; justify-content: center; gap: 30px; }
        }
    </style>
</head>
<body>

<div class="topbar">
    <a href="../dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> GO BACK TO DASHBOARD</a>
    <div style="font-weight: 800;">₦<?php echo number_format($current_balance, 2); ?></div>
</div>

<div class="container">
    <div class="card <?php echo ($success) ? 'hidden' : ''; ?>">
        <div class="banner-box"><img src="../images/Bvn-1024x430.jpeg"></div>
        <div class="tabs">
            <div class="tab <?php echo ($current_tab == 'bvn') ? 'active' : ''; ?>" onclick="setTab('bvn')">BVN SLIP (₦300)</div>
            <div class="tab <?php echo ($current_tab == 'phone') ? 'active' : ''; ?>" onclick="setTab('phone')">SEARCH BY PHONE (₦1000)</div>
        </div>
        <div class="search-area">
            <div id="refund-notice" class="notice <?php echo ($current_tab == 'bvn') ? 'hidden-notice' : ''; ?>">
                <i class="fas fa-shield-alt"></i> <strong>Refund Policy:</strong> Failed manual searches are instantly refunded. <span style="color:red;">(Unavailable on weekends)</span>
            </div>
            <form method="POST">
                <input type="hidden" name="search_type" id="search_type" value="<?php echo $current_tab; ?>">
                <div class="input-group">
                    <label id="input_label"><?php echo ($current_tab == 'phone' ? 'Enter Phone Number' : 'Enter 11-Digit BVN'); ?></label>
                    <input type="text" name="number" id="main_number" maxlength="11" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="00000000000" required>
                </div>
                <button type="submit" name="verify_action" class="btn-gen">GENERATE SLIP</button>
            </form>
        </div>
    </div>

    <?php if($success): ?>
    <div class="result-view">
        <div class="status-badge status-<?php echo $manual_status; ?>"><?php echo $manual_status; ?></div>
        
        <?php if($manual_status === 'Pending'): ?>
            <h3 style="color:var(--primary); margin-bottom:10px;">Processing Request...</h3>
            <p style="font-size: 14px; color: #666;">Wait Time (Working Days Only):</p>
            <div id="timer" class="countdown">--:--:--</div>
        <?php else: ?>
            <img src="../images/bvn-slip.png" style="width:100%; max-width:400px; border-radius:10px; margin-bottom:20px;">
            <a href="../images/bvn-slip.png" download="My_BVN_Slip" class="btn-gen" style="background:#333; width:auto; padding:15px 40px; display:inline-block;">DOWNLOAD SLIP</a>
        <?php endif; ?>

        <div class="nav-links">
            <a href="bvn-search.php" class="link-back"><i class="fas fa-arrow-left"></i> NEW SEARCH</a>
            <a href="../dashboard.php" class="link-back"><i class="fas fa-home"></i> BACK TO DASHBOARD</a>
        </div>
    </div>
    <?php endif; ?>

    <div class="history-section <?php echo ($success) ? 'hidden' : ''; ?>">
        <h4 style="margin-top:0; color:var(--primary);">History</h4>
        <table>
            <thead><tr><th>Reference</th><th>Status</th><th>Fee</th><th>Action</th></tr></thead>
            <tbody>
            <?php while($row = $history_res->fetch_assoc()): ?>
                <tr>
                    <td><strong><?php echo $row['bvn']; ?></strong></td>
                    <td><span class="status-badge status-<?php echo $row['status']; ?>" style="margin:0;"><?php echo $row['status']; ?></span></td>
                    <td>₦<?php echo $row['amount_charged']; ?></td>
                    <td><form method="POST"><input type="hidden" name="number" value="<?php echo $row['bvn']; ?>"><button type="submit" name="verify_action" style="cursor:pointer; background:#f0fdf4; border:1px solid #bbf7d0; color:var(--primary); padding:6px 12px; border-radius:6px; font-weight:bold;">View</button></form></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function setTab(type) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    event.currentTarget.classList.add('active');
    document.getElementById('search_type').value = type;
    document.getElementById('input_label').innerText = (type === 'phone' ? 'Enter Phone Number' : 'Enter 11-Digit BVN');
    const notice = document.getElementById('refund-notice');
    if (type === 'phone') { notice.classList.remove('hidden-notice'); } else { notice.classList.add('hidden-notice'); }
}

function startWorkingDayTimer(dbStartTime) {
    const timerElement = document.getElementById('timer');
    if (!timerElement) return;
    const startDate = new Date(dbStartTime.replace(/-/g, "/"));
    let deadline = new Date(startDate.getTime() + (24 * 60 * 60 * 1000));
    const startDay = startDate.getDay();
    if (startDay === 5 || startDay === 6) { deadline = new Date(deadline.getTime() + (48 * 60 * 60 * 1000)); }
    else if (startDay === 0) { deadline = new Date(deadline.getTime() + (24 * 60 * 60 * 1000)); }

    setInterval(function() {
        const distance = deadline - new Date().getTime();
        if (distance < 0) { timerElement.innerHTML = "DUE"; return; }
        const h = Math.floor((distance % 86400000) / 3600000);
        const m = Math.floor((distance % 3600000) / 60000);
        const s = Math.floor((distance % 60000) / 1000);
        timerElement.innerHTML = (h<10?'0':'')+h+"h "+(m<10?'0':'')+m+"m "+(s<10?'0':'')+s+"s ";
    }, 1000);
}

<?php if($success && $manual_status === 'Pending'): ?>
    startWorkingDayTimer('<?php echo $search_time; ?>');
<?php endif; ?>
</script>
</body>
</html>
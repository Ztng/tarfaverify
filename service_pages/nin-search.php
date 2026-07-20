<?php
session_start();
include('../config.php');

if (!isset($_SESSION['user_email'])) {
    header("Location: ../login.php");
    exit();
}

/**
 * ── DYNAMIC PRICING CONFIGURATION ──
 */
$doc_prices = [
    'NIN Slip'          => 150, 
    'VNIN Slip'         => 200, 
    'Standard Slip'     => 170, 
    'Premium Slip'      => 180, 
    'Basic Information' => 150  
];

$user_email = $_SESSION['user_email'];
$error      = '';
$success    = false;
$current_tab = $_POST['doc_type'] ?? 'NIN Slip';
$current_price = $doc_prices[$current_tab] ?? 150;

// ── GET WALLET BALANCE ──
$stmt = $conn->prepare("SELECT wallet_balance FROM users WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$bal_res = $stmt->get_result()->fetch_assoc();
$current_balance = $bal_res['wallet_balance'] ?? 0;

// ── PROCESS ACTION ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_action'])) {
    $nin_input = trim($_POST['number'] ?? '');

    $check = $conn->prepare("SELECT id FROM nin_searches WHERE user_email = ? AND nin = ? LIMIT 1");
    $check->bind_param("ss", $user_email, $nin_input);
    $check->execute();
    $already_exists = $check->get_result()->num_rows > 0;

    if ($already_exists) {
        $success = true;
    } else {
        if (strlen($nin_input) !== 11) {
            $error = "Error: NIN must be exactly 11 digits.";
        } elseif ($current_balance < $current_price) {
            $error = "Insufficient funds. Required: ₦" . $current_price;
        } else {
            // LOGIC: 0 Naira if no record found (Simulated)
            $record_found = true; 

            if ($record_found) {
                $log = $conn->prepare("INSERT INTO nin_searches (user_email, nin, amount_charged, response_json, searched_at) VALUES (?, ?, ?, 'Success', NOW())");
                $log->bind_param("ssd", $user_email, $nin_input, $current_price);
                $log->execute();
                
                $conn->query("UPDATE users SET wallet_balance = wallet_balance - $current_price WHERE email = '$user_email'");
                $success = true;
                $current_balance -= $current_price;
            } else {
                $error = "No record found. You have been charged ₦0.";
            }
        }
    }
}

$history_res = $conn->query("SELECT nin, amount_charged, searched_at FROM nin_searches WHERE user_email = '$user_email' ORDER BY searched_at DESC LIMIT 10");

$doc_images = [
    'NIN Slip'          => 'nin-slip.png',
    'VNIN Slip'         => 'v-nin.png',
    'Standard Slip'     => 'standard-slip.png',
    'Premium Slip'      => 'premium-slip.png',
    'Basic Information' => 'basic-info.png'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NIN Premium Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #008751; --bg: #f0f2f5; }
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; color: #1a1a1a; }
        
        .topbar { background: var(--primary); color: white; padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; position: sticky; top:0; z-index: 1000; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .back-btn { color: white; text-decoration: none; font-weight: 800; font-size: 15px; border: 2px solid rgba(255,255,255,0.4); padding: 10px 20px; border-radius: 10px; transition: 0.3s; }
        .back-btn:hover { background: white; color: var(--primary); }

        /* ULTRA WIDE CONTAINER */
        .container { width: 100%; max-width: 1400px; margin: 40px auto; padding: 0 25px; }
        
        /* ENHANCED DOC GRID */
        .doc-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 25px; margin-bottom: 50px; }
        .doc-card { background: white; border: 3px solid #eee; border-radius: 20px; padding: 25px; text-align: center; cursor: pointer; transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); position: relative; box-shadow: 0 10px 20px rgba(0,0,0,0.02); }
        .doc-card.active { border-color: var(--primary); background: #f0fdf4; transform: scale(1.05); box-shadow: 0 20px 40px rgba(0,135,81,0.15); }
        .doc-card img { width: 100%; height: 140px; object-fit: contain; margin-bottom: 15px; }
        .price-tag { position: absolute; top: 15px; right: 15px; background: var(--primary); color: white; padding: 6px 14px; border-radius: 25px; font-size: 13px; font-weight: 900; }

        .search-area { background: white; padding: 60px; border-radius: 25px; box-shadow: 0 20px 60px rgba(0,0,0,0.06); }
        .input-group label { display: block; font-weight: 800; margin-bottom: 15px; color: var(--primary); font-size: 18px; }
        .input-group input { width: 100%; padding: 22px; border: 3px solid #f0f0f0; border-radius: 15px; font-size: 20px; outline: none; transition: 0.3s; background: #fafafa; }
        .input-group input:focus { border-color: var(--primary); background: #fff; }

        .btn-gen { background: var(--primary); color: white; border: none; width: 100%; padding: 22px; border-radius: 15px; font-size: 20px; font-weight: 900; cursor: pointer; margin-top: 30px; transition: 0.3s; }
        .btn-gen:hover { filter: brightness(1.1); transform: translateY(-3px); }

        /* RESULT VIEW */
        .result-view { text-align: center; background: white; padding: 60px; border-radius: 25px; border: 5px solid var(--primary); margin-top: 40px; }
        .result-view img { width: 100%; max-width: 800px; border-radius: 15px; box-shadow: 0 30px 70px rgba(0,0,0,0.2); margin: 30px 0; }
        
        .action-btns { display: flex; gap: 20px; justify-content: center; margin-top: 40px; flex-wrap: wrap; }
        .btn-action { padding: 22px 60px; border-radius: 15px; font-weight: 900; text-decoration: none; display: inline-block; font-size: 18px; transition: 0.3s; border: none; cursor: pointer; }

        /* HISTORY SECTION */
        .history-section { background: white; padding: 40px; border-radius: 25px; margin-top: 50px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 700px; }
        th { text-align: left; padding: 20px; color: #999; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; border-bottom: 3px solid #f8f9fa; }
        td { padding: 25px 20px; border-bottom: 1px solid #f8f9fa; font-size: 16px; }

        .hidden { display: none; }

        /* MOBILE FIXES */
        @media (max-width: 768px) {
            .topbar { padding: 15px; }
            .container { margin: 20px auto; }
            .search-area, .result-view { padding: 30px 20px; }
            .btn-action { width: 100%; }
            .doc-grid { grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 15px; }
        }
    </style>
</head>
<body>

<div class="topbar">
    <a href="../dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> GO BACK TO DASHBOARD</a>
    <div style="font-weight: 900; font-size: 20px;">WALLET: ₦<?php echo number_format($current_balance, 2); ?></div>
</div>

<div class="container">
    <?php if($error): ?><div style="color:#fff; background:#e74c3c; padding:20px; border-radius:15px; margin-bottom:30px; font-weight: 800; text-align:center;"><?php echo $error; ?></div><?php endif; ?>

    <div class="search-area <?php echo ($success) ? 'hidden' : ''; ?>">
        <h2 style="color:var(--primary); font-size: 24px; margin-bottom: 35px; text-align:center;">STEP 1: SELECT YOUR DOCUMENT STYLE</h2>
        
        <div class="doc-grid">
            <?php foreach($doc_images as $label => $img): ?>
            <div class="doc-card <?php echo ($current_tab == $label) ? 'active' : ''; ?>" onclick="selectDoc('<?php echo $label; ?>', '<?php echo $doc_prices[$label]; ?>', this)">
                <span class="price-tag">₦<?php echo $doc_prices[$label]; ?></span>
                <img src="../images/<?php echo $img; ?>">
                <div style="font-weight: 900; font-size: 15px; color: #444;"><?php echo $label; ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <form method="POST">
            <input type="hidden" name="doc_type" id="doc_type" value="<?php echo $current_tab; ?>">
            <div class="input-group">
                <label>STEP 2: ENTER YOUR 11-DIGIT NIN</label>
                <input type="text" name="number" maxlength="11" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="00000000000" required>
            </div>
            <button type="submit" name="verify_action" id="gen_btn" class="btn-gen">GENERATE NOW (₦<?php echo $current_price; ?>)</button>
        </form>
    </div>

    <?php if($success): ?>
    <div class="result-view">
        <h2 style="color:var(--primary); font-size: 30px; margin-bottom: 10px;">SUCCESS! DOCUMENT GENERATED</h2>
        <p style="color: #666; font-weight: bold;">Your file is ready for download or printing.</p>
        
        <img src="../images/<?php echo $doc_images[$current_tab]; ?>">
        
        <div class="action-btns">
            <a href="../images/<?php echo $doc_images[$current_tab]; ?>" download="My_NIN_Document" class="btn-action" style="background:#1a1a1a; color:white;"><i class="fas fa-download"></i> DOWNLOAD NOW</a>
            <button onclick="window.print()" class="btn-action" style="background:var(--primary); color:white;"><i class="fas fa-print"></i> PRINT DOCUMENT</button>
        </div>
        
        <p style="margin-top:40px;"><a href="nin-search.php" style="color:var(--primary); text-decoration:none; font-weight:900; font-size: 18px;">← START ANOTHER SEARCH</a></p>
    </div>
    <?php endif; ?>

    <div class="history-section <?php echo ($success) ? 'hidden' : ''; ?>">
        <h3 style="margin-top:0; color:var(--primary); font-size: 22px; margin-bottom: 30px;">YOUR RECENT NIN SEARCH HISTORY</h3>
        <table>
            <thead><tr><th>NIN REFERENCE</th><th>FEE PAID</th><th>DATE COMPLETED</th><th>ACTION</th></tr></thead>
            <tbody>
            <?php while($row = $history_res->fetch_assoc()): ?>
                <tr>
                    <td><strong><?php echo $row['nin']; ?></strong></td>
                    <td style="color: var(--primary); font-weight: 800;">₦<?php echo number_format($row['amount_charged'], 2); ?></td>
                    <td><?php echo date('F d, Y', strtotime($row['searched_at'])); ?></td>
                    <td>
                        <form method="POST" style="margin:0;">
                            <input type="hidden" name="number" value="<?php echo $row['nin']; ?>">
                            <button type="submit" name="verify_action" style="background:var(--primary); color:white; border:none; padding:12px 25px; border-radius:10px; cursor:pointer; font-weight:900; font-size: 14px;">VIEW AGAIN</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function selectDoc(val, price, el) {
    document.querySelectorAll('.doc-card').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('doc_type').value = val;
    document.getElementById('gen_btn').innerText = "GENERATE NOW (₦" + price + ")";
}
</script>
</body>
</html>
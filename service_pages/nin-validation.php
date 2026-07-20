<?php
session_start();
include('../config.php');

if (!isset($_SESSION['user_email'])) {
    header("Location: ../login.php");
    exit();
}

$user_email = $_SESSION['user_email'];
$error = ''; $success = false;

// ── GET WALLET BALANCE ──
$stmt = $conn->prepare("SELECT wallet_balance FROM users WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$current_balance = $stmt->get_result()->fetch_assoc()['wallet_balance'] ?? 0;

// ── WEEKEND STATUS ──
$is_weekend = (date('N') >= 6); 

// ── PROCESS VALIDATION ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_validation'])) {
    $nin = trim($_POST['nin_number']);
    $cat = $_POST['category'];
    $purpose = $_POST['purpose'];
    
    $prices = ['No Record' => 1000, 'mod' => 1500, 'V-nin' => 1500];
    $fee = $prices[$cat] ?? 1000;

    if (strlen($nin) !== 11) {
        $error = "NIN must be exactly 11 digits.";
    } elseif ($current_balance < $fee) {
        $error = "Insufficient balance.";
    } else {
        $conn->query("UPDATE users SET wallet_balance = wallet_balance - $fee WHERE email = '$user_email'");
        $ins = $conn->prepare("INSERT INTO validation_requests (user_email, nin, category, purpose, amount_charged, status, created_at) VALUES (?, ?, ?, ?, ?, 'Pending', NOW())");
        $ins->bind_param("ssssd", $user_email, $nin, $cat, $purpose, $fee);
        $ins->execute();
        header("Location: nin-validation.php?success=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NIN Validation - Tarfaverify</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #008751; --bg: #f4f6f9; }
        * { box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: var(--bg); margin: 0; padding: 0; }

        .top-nav { background: white; padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--primary); }
        .back-link { text-decoration: none; color: var(--primary); font-weight: 800; font-size: 14px; }
        
        .main-container { max-width: 1200px; margin: 20px auto; padding: 0 15px; }
        .grid { display: grid; grid-template-columns: 380px 1fr; gap: 20px; }

        .card { background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 25px; }
        .card-h { color: var(--primary); margin-bottom: 20px; font-weight: 800; font-size: 18px; text-transform: uppercase; border-left: 4px solid var(--primary); padding-left: 10px; }

        .weekend-alert { background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-size: 13px; font-weight: 700; display: <?php echo $is_weekend ? 'block' : 'none'; ?>; }

        label { display: block; margin-bottom: 5px; font-weight: 700; color: #555; font-size: 12px; }
        select, input { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px; }
        
        .btn-submit { background: var(--primary); color: white; border: none; padding: 15px; border-radius: 8px; width: 100%; font-weight: 800; font-size: 16px; cursor: pointer; }
        .btn-submit:disabled { background: #ccc; cursor: not-allowed; }

        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th { text-align: left; padding: 10px; background: #f8fafc; color: #64748b; font-size: 11px; text-transform: uppercase; }
        td { padding: 10px; border-bottom: 1px solid #f1f5f9; font-size: 13px; vertical-align: middle; }

        .timer-hms { font-family: monospace; font-weight: 700; color: var(--primary); background: #e8f5e9; padding: 3px 6px; border-radius: 4px; font-size: 12px; }
        .time-taken-badge { background: var(--primary); color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 900; }
        .admin-response { font-weight: 600; color: #333; font-size: 12px; line-height: 1.3; }

        @media (max-width: 950px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="top-nav">
    <a href="../dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> DASHBOARD</a>
    <div style="font-weight: 800; color: var(--primary);">WALLET: ₦<?php echo number_format($current_balance, 2); ?></div>
</div>

<div class="main-container">
    <div class="weekend-alert">
        <i class="fas fa-clock"></i> Service Unavailable on Weekends. Processing starts Monday.
    </div>

    <div class="grid">
        <div class="card">
            <h2 class="card-h">New Request</h2>
            <form method="POST">
                <label>CATEGORY</label>
                <select name="category" required>
                    <option value="No Record">No Record Found (₦1,000)</option>
                    <option value="mod">Modification (₦1,500)</option>
                    <option value="V-nin">V-NIN Validation (₦1,500)</option>
                </select>

                <label>11-DIGIT NIN</label>
                <input type="number" name="nin_number" placeholder="Enter NIN" oninput="if(this.value.length > 11) this.value = this.value.slice(0, 11);" required>

                <label>PURPOSE</label>
                <input type="text" name="purpose" placeholder="e.g. Bank" required>

                <button type="submit" name="submit_validation" class="btn-submit" <?php echo $is_weekend ? 'disabled' : ''; ?>>
                    <?php echo $is_weekend ? 'CLOSED FOR WEEKEND' : 'SUBMIT REQUEST'; ?>
                </button>
            </form>
        </div>

        <div class="card">
            <h2 class="card-h">Recent History</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>NIN / Type</th>
                            <th>Status / Time Taken</th>
                            <th>Response</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = $conn->query("SELECT * FROM validation_requests WHERE user_email = '$user_email' ORDER BY id DESC LIMIT 10");
                        while($row = $res->fetch_assoc()): 
                            
                            // CALCULATE TIME TAKEN (If Status is Completed)
                            $time_display = "";
                            if ($row['status'] == 'Completed' && !empty($row['completed_at'])) {
                                $start = new DateTime($row['created_at']);
                                $end = new DateTime($row['completed_at']);
                                $interval = $start->diff($end);
                                
                                // Format: 1h 20m
                                if ($interval->h > 0) {
                                    $time_display = $interval->format('%hh %im');
                                } else {
                                    $time_display = $interval->format('%im %ss');
                                }
                            }
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo $row['nin']; ?></strong><br>
                                <span style="font-size:11px; color:#777;"><?php echo $row['category']; ?></span>
                            </td>
                            <td>
                                <?php if($row['status'] == 'Completed'): ?>
                                    <span class="time-taken-badge">VALIDATED IN: <?php echo $time_display; ?></span>
                                <?php else: ?>
                                    <span class="timer-hms" data-start="<?php echo $row['created_at']; ?>">--:--:--</span>
                                <?php endif; ?>
                            </td>
                            <td><div class="admin-response"><?php echo $row['response'] ? $row['response'] : 'Pending...'; ?></div></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function runCountdowns() {
    document.querySelectorAll('.timer-hms').forEach(el => {
        const start = new Date(el.getAttribute('data-start').replace(/-/g, "/"));
        const now = new Date();
        const day = start.getDay(); 
        let limit = 48;
        if (day === 5) limit = 96; else if (day === 6) limit = 84; else if (day === 0) limit = 72;
        const deadline = new Date(start.getTime() + (limit * 60 * 60 * 1000));
        const diff = deadline - now;
        
        if (diff <= 0) {
            el.innerText = "PROCESSING...";
        } else {
            const h = Math.floor(diff / 3600000);
            const m = Math.floor((diff % 3600000) / 60000);
            const s = Math.floor((diff % 60000) / 1000);
            el.innerText = h + "h " + m + "m " + s + "s";
        }
    });
}
setInterval(runCountdowns, 1000);
runCountdowns();
</script>
</body>
</html>
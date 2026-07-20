<?php
session_start();
include('../config.php');

// 1. SECURITY CHECK - Combined and streamlined
$admin_email = 'zeeboykd@gmail.com'; 

if (!isset($_SESSION['user_email']) || $_SESSION['user_email'] !== $admin_email || $_SESSION['user_role'] !== 'admin') {
    echo "<div style='text-align:center; margin-top:50px; font-family:Arial;'>
            <h2 style='color:red;'>Access Denied</h2>
            <p>You must be logged in as Admin to view this page.</p>
            <a href='../index.php'>Go to Login Page</a>
          </div>";
    exit();
}

// 2. UPDATE LOGIC: Records the completion time
if (isset($_POST['update_req'])) {
    $id = $_POST['req_id'];
    $resp = trim($_POST['admin_response']);
    
    // Updates status, response, and completion timestamp
    $stmt = $conn->prepare("UPDATE validation_requests SET status = 'Completed', response = ?, completed_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $resp, $id);
    
    if($stmt->execute()){
        echo "<script>alert('Response sent successfully!'); window.location='admin-validation.php';</script>";
    } else {
        echo "<script>alert('Error updating record');</script>";
    }
}

// 3. SEARCH LOGIC
$search_filter = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    // Escaping the search string for security
    $s = "%" . mysqli_real_escape_string($conn, $_GET['search']) . "%";
    $search_filter = " AND (nin LIKE '$s' OR user_email LIKE '$s')";
}

$requests = $conn->query("SELECT * FROM validation_requests WHERE status = 'Pending' $search_filter ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Tarfaverify</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #008751; --bg: #f4f6f9; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); margin: 0; padding: 20px; }
        .container { max-width: 1100px; margin: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        
        .search-box { display: flex; gap: 10px; margin-bottom: 25px; }
        .search-box input { flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 8px; outline: none; }
        .btn-search { background: var(--primary); color: white; border: none; padding: 0 25px; border-radius: 8px; cursor: pointer; font-weight: bold; }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px; background: #f8f9fa; color: #666; font-size: 12px; text-transform: uppercase; }
        td { padding: 15px 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        
        textarea { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; height: 50px; resize: none; font-size: 13px; }
        .btn-update { background: var(--primary); color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-weight: bold; margin-top: 5px; width: 100%; }
        
        .user-info { font-size: 12px; color: #888; margin-bottom: 4px; }
        .nin-text { font-weight: 800; color: #333; font-size: 16px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2 style="color: var(--primary);">Staff Validation Manager</h2>
        <a href="../dashboard.php" style="text-decoration:none; color:#666; font-size:14px;"><i class="fas fa-home"></i> Dashboard</a>
    </div>

    <div class="card">
        <form method="GET" class="search-box">
            <input type="text" name="search" placeholder="Find by NIN or Email..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            <button type="submit" class="btn-search">Search</button>
            <?php if(isset($_GET['search'])): ?>
                <a href="admin-validation.php" style="padding:12px; color:#ff4444; text-decoration:none;">Reset</a>
            <?php endif; ?>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Customer & NIN</th>
                    <th>Category</th>
                    <th>Type Your Response</th>
                </tr>
            </thead>
            <tbody>
                <?php if($requests && $requests->num_rows > 0): ?>
                    <?php while($row = $requests->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div class="user-info"><?php echo htmlspecialchars($row['user_email']); ?></div>
                            <div class="nin-text"><?php echo htmlspecialchars($row['nin']); ?></div>
                        </td>
                        <td><strong><?php echo htmlspecialchars($row['category']); ?></strong></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="req_id" value="<?php echo $row['id']; ?>">
                                <textarea name="admin_response" placeholder="Enter result..." required></textarea>
                                <button type="submit" name="update_req" class="btn-update">Update Client</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3" style="text-align:center; padding:50px; color:#999;">No pending requests found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
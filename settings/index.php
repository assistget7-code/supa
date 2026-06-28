<?php
// ============================================
// SETTINGS - Client configures bot
// ============================================

session_start();

$admin_password = 'admin123'; // ← CHANGE THIS!

if (isset($_POST['login_password'])) {
    if ($_POST['login_password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: index.php');
        exit;
    } else {
        $login_error = "❌ Incorrect password!";
    }
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Show login form
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Settings Login</title>
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
            <h1>🔐 Settings</h1>
            <form method="POST">
                <input type="password" name="login_password" placeholder="Enter password" required>
                <button type="submit">Access Settings</button>
                <?php if (isset($login_error)) echo '<p class="error">' . $login_error . '</p>'; ?>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Load settings
$settingsFile = __DIR__ . '/../data/settings.json';
$settings = [];
if (file_exists($settingsFile)) {
    $settings = json_decode(file_get_contents($settingsFile), true) ?? [];
}
$botToken = $settings['bot_token'] ?? '';
$chatId = $settings['chat_id'] ?? '';

if (isset($_POST['save_settings'])) {
    $newBotToken = trim($_POST['bot_token'] ?? '');
    $newChatId = trim($_POST['chat_id'] ?? '');
    if ($newBotToken && $newChatId) {
        $settings = ['bot_token' => $newBotToken, 'chat_id' => $newChatId];
        file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT));
        $save_success = "✅ Settings saved!";
        $botToken = $newBotToken;
        $chatId = $newChatId;
    } else {
        $save_error = "❌ Both fields required.";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Settings</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        .card { background: #fff; padding: 30px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ddd; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { font-size: 28px; }
        .header-actions a { padding: 10px 20px; background: #0067b8; color: #fff; text-decoration: none; border-radius: 4px; }
        .header-actions a:hover { background: #005da6; }
        input { width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; font-family: monospace; }
        .btn { width: 100%; padding: 12px; background: #0067b8; color: #fff; border: none; border-radius: 4px; font-weight: 600; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #005da6; }
        .message-success { background: #e8f5e9; color: #2e7d32; padding: 12px; border-radius: 4px; margin-bottom: 15px; }
        .message-error { background: #fce4ec; color: #c62828; padding: 12px; border-radius: 4px; margin-bottom: 15px; }
        .current { background: #f8f9fa; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .current .label { color: #666; font-size: 12px; }
        .current .value { font-family: monospace; font-size: 14px; }
        .status-active { background: #e8f5e9; color: #2e7d32; padding: 3px 12px; border-radius: 20px; display: inline-block; font-size: 12px; font-weight: 600; }
        .status-inactive { background: #fce4ec; color: #c62828; padding: 3px 12px; border-radius: 20px; display: inline-block; font-size: 12px; font-weight: 600; }
        .help-text { font-size: 13px; color: #666; line-height: 1.8; }
        .help-text code { background: #f0f0f0; padding: 2px 6px; border-radius: 4px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚙️ #1 Bot Settings</h1>
            <div class="header-actions">
                <a href="../admin/dashboard.php">← Dashboard</a>
                <a href="index.php?logout=1">🚪 Logout</a>
            </div>
        </div>

        <?php if (isset($save_success)) echo '<div class="message-success">' . $save_success . '</div>'; ?>
        <?php if (isset($save_error)) echo '<div class="message-error">' . $save_error . '</div>'; ?>

        <div class="card">
            <h2>📊 Current Settings</h2>
            <div class="current">
                <div class="label">🤖 Bot Token</div>
                <div class="value"><?php echo $botToken ? substr($botToken, 0, 10) . '...' . substr($botToken, -5) : 'Not configured'; ?></div>
                <div class="label" style="margin-top:10px;">📱 Chat ID</div>
                <div class="value"><?php echo $chatId ?: 'Not configured'; ?></div>
                <div style="margin-top:10px;">
                    <span class="<?php echo ($botToken && $chatId) ? 'status-active' : 'status-inactive'; ?>">
                        <?php echo ($botToken && $chatId) ? '✅ Connected' : '❌ Not Configured'; ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>⚙️ Configure Bot</h2>
            <form method="POST">
                <label style="display:block;margin-bottom:5px;font-weight:600;">🤖 Bot Token</label>
                <input type="text" name="bot_token" placeholder="1234567890:ABCdef..." value="<?php echo htmlspecialchars($botToken); ?>">
                
                <label style="display:block;margin-bottom:5px;font-weight:600;margin-top:15px;">📱 Chat ID</label>
                <input type="text" name="chat_id" placeholder="123456789" value="<?php echo htmlspecialchars($chatId); ?>">
                
                <button type="submit" name="save_settings" class="btn" style="margin-top:15px;">💾 Save Settings</button>
            </form>
        </div>

        <div class="card">
            <h2>📖 How to Get Your Bot Token & Chat ID</h2>
            <div class="help-text">
                <p><strong>🤖 Bot Token:</strong></p>
                <ol style="padding-left:20px;">
                    <li>Open Telegram and search for <code>@BotFather</code></li>
                    <li>Send <code>/newbot</code> and follow instructions</li>
                    <li>Copy the token you receive</li>
                </ol>
                <br>
                <p><strong>📱 Chat ID:</strong></p>
                <ol style="padding-left:20px;">
                    <li>Open Telegram and search for <code>@userinfobot</code></li>
                    <li>Send <code>/start</code></li>
                    <li>Copy your user ID</li>
                </ol>
            </div>
        </div>
    </div>
</body>
</html>

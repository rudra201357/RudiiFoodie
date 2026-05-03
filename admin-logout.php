<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging out...</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
</head>
<body>
    <script>
        localStorage.clear();
        sessionStorage.clear();

        if ('caches' in window) {
            caches.keys().then(function (cacheNames) {
                return Promise.all(cacheNames.map(function (cacheName) {
                    return caches.delete(cacheName);
                }));
            }).finally(function () {
                window.location.replace('admin.php');
            });
        } else {
            window.location.replace('admin.php');
        }
    </script>

    <noscript>
        <meta http-equiv="refresh" content="0;url=admin.php">
        <p>You have been logged out. <a href="admin.php">Go to home</a></p>
    </noscript>
</body>
</html>

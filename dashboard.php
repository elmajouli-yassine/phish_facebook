<?php
session_start();

// Example credentials
$valid_username = 'admin';
$valid_password = 'admin123';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";

    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION["admin_logged_in"] = true;
        header("Location: login.php");
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Admin Login</title></head>
<body>
<style>
:root{--bg:#f3f7fb;--card:#ffffff;--accent:#2563eb}
*{box-sizing:border-box;font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif}
body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#eef6ff,#f8fbff)}
.wrapper{width:100%;max-width:420px;padding:24px}
.card{background:var(--card);border-radius:12px;padding:28px;box-shadow:0 10px 30px rgba(2,6,23,0.08)}
.brand{display:block;text-align:center;font-weight:700;color:var(--accent);font-size:20px;margin-bottom:8px}
h2{margin:6px 0 18px;text-align:center;color:#0f172a;font-size:18px}
.form-group{margin-bottom:14px}
label{display:block;font-size:13px;color:#475569;margin-bottom:6px}
input[type="text"],input[type="password"]{width:100%;padding:10px 12px;border:1px solid #e6eef8;border-radius:8px;background:#fbfdff;font-size:14px;color:#0f172a}
input:focus{outline:none;border-color:var(--accent);box-shadow:0 6px 18px rgba(37,99,235,0.08)}
button{width:100%;padding:11px;border:none;border-radius:10px;background:var(--accent);color:#fff;font-weight:600;cursor:pointer;font-size:15px}
button:hover{filter:brightness(.96)}
.error{background:#fee2e2;color:#991b1b;padding:10px;border-radius:8px;margin-bottom:12px;text-align:center;font-size:14px}
.footer{margin-top:14px;text-align:center;color:#64748b;font-size:13px}
.small{font-size:13px;color:#64748b}
</style>

<div class="wrapper">
    <div class="card">
        <span class="brand">Admin Portal</span>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <h2>Sign in to continue</h2>

        <form method="post" action="dashboard.php" autocomplete="off" novalidate>
            <div class="form-group">
                <label for="username">Username</label>
                <input id="username" name="username" type="text" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required>
            </div>

            <button type="submit">Login</button>
        </form>

        <div class="footer small">Protected area — authorized personnel only.</div>
    </div>
</div>
</body>
</html>

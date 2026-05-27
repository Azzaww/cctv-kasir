<!-- login.php -->
<?php
session_start();
include 'koneksi.php';

if(isset($_POST['login'])){
$username = $_POST['username'];
$password = md5($_POST['password']);
$query = mysqli_query($conn,"SELECT * FROM users WHERE username='$username' AND password='$password'");
if(mysqli_num_rows($query)>0){
    $_SESSION['login']=true;
    header("Location: dashboard.php");
}else{
    $error = "Username atau password salah.";
}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login – Waluyo Teknik CCTV</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --bg: #0f1117;
    --bg2: #161b27;
    --surface: #1e2535;
    --border: rgba(255,255,255,0.07);
    --accent: #3b82f6;
    --accent2: #60a5fa;
    --accent-glow: rgba(59,130,246,0.25);
    --text: #f1f5f9;
    --text2: #94a3b8;
    --text3: #64748b;
    --red: #ef4444;
}

html, body {
    height: 100%;
    background: var(--bg);
    color: var(--text);
    font-family: "Plus Jakarta Sans", sans-serif;
    font-size: 14px;
}

body {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    position: relative;
    overflow: hidden;
}

/* animated background */
body::before {
    content: "";
    position: fixed;
    inset: 0;
    background:
        radial-gradient(ellipse 80% 60% at 20% 0%, rgba(59,130,246,0.12) 0%, transparent 60%),
        radial-gradient(ellipse 60% 50% at 80% 100%, rgba(99,102,241,0.08) 0%, transparent 60%);
    pointer-events: none;
}

/* grid lines */
body::after {
    content: "";
    position: fixed;
    inset: 0;
    background-image:
        linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
    background-size: 40px 40px;
    pointer-events: none;
}

.login-wrapper {
    width: 420px;
    position: relative;
    z-index: 10;
    animation: fadeUp 0.6s ease both;
}

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
}

.login-card {
    background: var(--bg2);
    border: 1px solid var(--border);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 32px 64px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.04);
}

.login-header {
    padding: 36px 36px 24px;
    text-align: center;
    background: linear-gradient(180deg, rgba(59,130,246,0.08) 0%, transparent 100%);
    border-bottom: 1px solid var(--border);
}

.logo-icon {
    width: 64px; height: 64px;
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    border-radius: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: white;
    margin-bottom: 16px;
    box-shadow: 0 8px 24px var(--accent-glow);
}

.login-header h2 {
    font-size: 18px;
    font-weight: 800;
    letter-spacing: -0.02em;
    color: var(--text);
}

.login-header p {
    font-size: 13px;
    color: var(--text3);
    margin-top: 6px;
}

.login-body {
    padding: 32px 36px 36px;
}

.alert-error {
    background: rgba(239,68,68,0.1);
    border: 1px solid rgba(239,68,68,0.2);
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 13px;
    color: #fca5a5;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-group { margin-bottom: 18px; }

.form-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text2);
    margin-bottom: 8px;
}

.input-wrap {
    position: relative;
}

.input-wrap i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text3);
    font-size: 14px;
}

.form-control {
    width: 100%;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 12px 14px 12px 42px;
    font-size: 14px;
    font-family: "Plus Jakarta Sans", sans-serif;
    color: var(--text);
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px var(--accent-glow);
}

.form-control::placeholder { color: var(--text3); }

.btn-login {
    width: 100%;
    padding: 13px;
    background: linear-gradient(135deg, var(--accent), #2563eb);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    font-family: "Plus Jakarta Sans", sans-serif;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 4px 16px var(--accent-glow);
    margin-top: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px var(--accent-glow);
}

.login-footer {
    text-align: center;
    padding: 16px;
    color: var(--text3);
    font-size: 12px;
    border-top: 1px solid var(--border);
}
</style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">

        <div class="login-header">
            <div class="logo-icon"><i class="fa-solid fa-video"></i></div>
            <h2>WALUYO TEKNIK CCTV</h2>
            <p>Silakan masuk ke akun Anda</p>
        </div>

        <div class="login-body">
            <?php if(isset($error)): ?>
            <div class="alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?= $error ?>
            </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username" autocomplete="username">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password" autocomplete="current-password">
                    </div>
                </div>
                <button type="submit" name="login" class="btn-login">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Masuk
                </button>
            </form>
        </div>

        <div class="login-footer">© 2024 Waluyo Teknik CCTV • Sistem Kasir</div>
    </div>
</div>

</body>
</html>

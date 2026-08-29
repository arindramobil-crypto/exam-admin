<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Login — CBT Admin Panel</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Inter',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;
background:linear-gradient(135deg,#0f0c29 0%,#302b63 50%,#24243e 100%);overflow:hidden;position:relative}
body::before{content:'';position:absolute;width:600px;height:600px;background:radial-gradient(circle,rgba(99,102,241,.15) 0%,transparent 70%);top:-100px;right:-100px;border-radius:50%}
.card{background:rgba(255,255,255,.06);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.1);border-radius:24px;padding:48px 40px;width:100%;max-width:420px;position:relative;z-index:1;box-shadow:0 32px 64px rgba(0,0,0,.4)}
.brand{text-align:center;margin-bottom:36px}
.brand-icon{width:64px;height:64px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;color:#fff;box-shadow:0 8px 24px rgba(99,102,241,.4)}
.brand h1{font-size:22px;font-weight:700;color:#fff}
.brand p{font-size:13px;color:rgba(255,255,255,.5);margin-top:4px}
.alert-error{background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.3);color:#fca5a5;padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:20px}
.form-group{margin-bottom:20px}
label{display:block;font-size:13px;font-weight:500;color:rgba(255,255,255,.7);margin-bottom:8px}
.input-wrap{position:relative}
.input-wrap i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,.3);font-size:15px}
input{width:100%;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:12px 14px 12px 42px;color:#fff;font-size:14px;font-family:'Inter',sans-serif;transition:all .2s;outline:none}
input:focus{border-color:rgba(99,102,241,.6);background:rgba(255,255,255,.1);box-shadow:0 0 0 3px rgba(99,102,241,.15)}
input::placeholder{color:rgba(255,255,255,.25)}
.btn{width:100%;padding:13px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border:none;border-radius:10px;color:#fff;font-size:15px;font-weight:600;font-family:'Inter',sans-serif;cursor:pointer;transition:all .2s;box-shadow:0 4px 16px rgba(99,102,241,.3);margin-top:8px}
.btn:hover{transform:translateY(-1px);box-shadow:0 8px 24px rgba(99,102,241,.4)}
.footer-text{text-align:center;margin-top:24px;font-size:12px;color:rgba(255,255,255,.25)}
</style>
</head>
<body>
<div class="card">
<div class="brand">
<div class="brand-icon"><i class="fas fa-graduation-cap"></i></div>
<h1>CBT Admin Panel</h1>
<p>Sistem Manajemen Ujian Berbasis Komputer</p>
</div>
<?php if(session()->getFlashdata('error')): ?>
<div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?></div>
<?php endif ?>
<form action="<?= base_url('auth/login') ?>" method="POST">
<?= csrf_field() ?>
<div class="form-group"><label>Username</label><div class="input-wrap"><i class="fas fa-user"></i><input type="text" name="username" placeholder="Masukkan username" required autofocus></div></div>
<div class="form-group"><label>Password</label><div class="input-wrap"><i class="fas fa-lock"></i><input type="password" name="password" placeholder="Masukkan password" required></div></div>
<button type="submit" class="btn"><i class="fas fa-sign-in-alt"></i> &nbsp;Masuk</button>
</form>
<div class="footer-text">CBT Admin &copy; <?= date('Y') ?></div>
</div>
</body></html>
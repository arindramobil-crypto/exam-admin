<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $title ?? 'Layar Token CBT' ?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&family=JetBrains+Mono:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'Inter', sans-serif;
    background: radial-gradient(circle at center, #1e1b4b 0%, #0f172a 70%, #020617 100%);
    color: #fff;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.header {
    padding: 24px 40px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
.brand-title {
    display: flex;
    align-items: center;
    gap: 16px;
}
.brand-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}
.brand-text h1 {
    font-size: 20px;
    font-weight: 800;
    letter-spacing: -0.02em;
}
.brand-text p {
    font-size: 13px;
    color: #94a3b8;
}
.digital-clock {
    font-family: 'JetBrains Mono', monospace;
    font-size: 32px;
    font-weight: 800;
    color: #38bdf8;
    background: rgba(56, 189, 248, 0.1);
    padding: 8px 20px;
    border-radius: 12px;
    border: 1px solid rgba(56, 189, 248, 0.25);
    letter-spacing: 2px;
}
.main-display {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    text-align: center;
    position: relative;
}
.exam-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 20px;
    background: rgba(99, 102, 241, 0.15);
    border: 1px solid rgba(99, 102, 241, 0.3);
    border-radius: 100px;
    color: #a5b4fc;
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 24px;
}
.token-container {
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(20px);
    border: 2px solid rgba(99, 102, 241, 0.4);
    border-radius: 32px;
    padding: 48px 80px;
    box-shadow: 0 0 100px rgba(99, 102, 241, 0.2), inset 0 0 40px rgba(99, 102, 241, 0.1);
    margin-bottom: 28px;
    position: relative;
}
.token-label {
    font-size: 16px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.2em;
    color: #94a3b8;
    margin-bottom: 12px;
}
.token-box {
    font-family: 'JetBrains Mono', monospace;
    font-size: 110px;
    font-weight: 900;
    letter-spacing: 16px;
    line-height: 1;
    background: linear-gradient(135deg, #ffffff 0%, #a5b4fc 50%, #c084fc 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 0 0 60px rgba(192, 132, 252, 0.5);
}
.countdown-box {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 18px;
    color: #cbd5e1;
}
.countdown-timer {
    font-family: 'JetBrains Mono', monospace;
    font-weight: 800;
    color: #f59e0b;
    font-size: 24px;
}
.footer-bar {
    padding: 16px 40px;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13px;
    color: #64748b;
}
.btn-fs {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: #fff;
    padding: 8px 16px;
    border-radius: 8px;
    cursor: pointer;
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.btn-fs:hover { background: rgba(255, 255, 255, 0.15); }
</style>
</head>
<body>

<div class="header">
    <div class="brand-title">
        <div class="brand-icon"><i class="fas fa-graduation-cap"></i></div>
        <div class="brand-text">
            <h1>CBT ADMIN PANEL</h1>
            <p>Sistem Ujian Berbasis Komputer &bull; <?= esc($exam['nama_ujian']) ?></p>
        </div>
    </div>
    <div class="digital-clock" id="clock">00:00:00</div>
</div>

<div class="main-display">
    <div class="exam-badge">
        <i class="fas fa-clipboard-check"></i>
        <span><?= !empty($subject) ? esc($subject['nama_mapel']) . ' (Kelas ' . esc($subject['kelas']) . ')' : esc($exam['nama_ujian']) ?></span>
    </div>

    <div class="token-container">
        <div class="token-label">TOKEN MASUK UJIAN</div>
        <div class="token-box" id="tokenDisplay"><?= !empty($activeToken) && $activeToken['is_active'] ? esc($activeToken['token']) : '------' ?></div>
    </div>

    <div class="countdown-box" id="countdownContainer">
        <i class="fas fa-hourglass-half" style="color:#f59e0b"></i>
        <span>Masa Berlaku Token: <strong class="countdown-timer" id="countdown">--:--</strong></span>
    </div>
</div>

<div class="footer-bar">
    <div>
        <i class="fas fa-info-circle" style="margin-right:6px"></i> Masukkan kode token di atas ke aplikasi ujian untuk memulai tes.
    </div>
    <div style="display:flex;gap:12px;align-items:center">
        <button class="btn-fs" onclick="toggleFullscreen()"><i class="fas fa-expand"></i> Layar Penuh (F11)</button>
    </div>
</div>

<script>
// Digital Clock
function updateClock() {
    var now = new Date();
    var h = String(now.getHours()).padStart(2, '0');
    var m = String(now.getMinutes()).padStart(2, '0');
    var s = String(now.getSeconds()).padStart(2, '0');
    document.getElementById('clock').innerText = h + ':' + m + ':' + s;
}
setInterval(updateClock, 1000);
updateClock();

// Token & Countdown Polling
var remainingSeconds = 0;
function pollToken() {
    fetch('<?= base_url('token/get-active-json/' . $exam['id']) ?>')
        .then(res => res.json())
        .then(data => {
            var display = document.getElementById('tokenDisplay');
            if (data.is_active && data.token !== '-') {
                display.innerText = data.token;
                remainingSeconds = data.remaining;
            } else {
                display.innerText = '------';
                remainingSeconds = 0;
            }
            updateCountdownText();
        })
        .catch(err => console.error('Poll error:', err));
}

function updateCountdownText() {
    var timerEl = document.getElementById('countdown');
    if (remainingSeconds > 0) {
        var m = Math.floor(remainingSeconds / 60);
        var s = remainingSeconds % 60;
        timerEl.innerText = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
    } else {
        timerEl.innerText = '00:00 (Kedaluwarsa)';
    }
}

setInterval(function() {
    if (remainingSeconds > 0) {
        remainingSeconds--;
        updateCountdownText();
    }
}, 1000);

setInterval(pollToken, 5000);
pollToken();

function toggleFullscreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(err => alert('Fullscreen error: ' + err.message));
    } else {
        document.exitFullscreen();
    }
}
</script>

</body>
</html>

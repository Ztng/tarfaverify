<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TARFAVERIFY — Secure ID Protocol</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Sora:wght@300;400;600&display=swap" rel="stylesheet">
<style>
:root {
  --green: #00ff66;
  --green-glow: rgba(0, 255, 100, 0.4);
  --dark: #000a04;
  --gold: #FFBF00;
}
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    background: var(--dark);
    color: #fff;
    font-family: 'Sora', sans-serif;
    height: 100vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* ═══════════ BALANCED HEADER ═══════════ */
header {
    flex: 0 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 5%;
    background: rgba(0, 10, 4, 0.9);
    border-bottom: 1px solid rgba(0, 255, 100, 0.15);
    z-index: 100;
}

/* Logo Left */
.brand-left { display: flex; align-items: center; flex: 1; }
.brand-left img { height: 60px; transition: 0.3s; }

/* Center Text */
.header-center { text-align: center; flex: 2; }
.header-center strong { 
    font-family: 'Orbitron'; 
    font-size: 1.3rem; 
    letter-spacing: 2px;
    display: block; 
    color: #fff;
    text-transform: uppercase;
}
.header-center span { 
    font-size: 0.6rem; 
    color: var(--green); 
    letter-spacing: 3px; 
    text-transform: uppercase; 
    font-weight: 600;
}

/* Login Right */
.brand-right { flex: 1; display: flex; justify-content: flex-end; }
.login-btn {
    background: var(--gold);
    color: #000;
    padding: 8px 22px;
    border-radius: 4px;
    font-family: 'Orbitron';
    font-weight: 900;
    text-decoration: none;
    font-size: 0.8rem;
    transition: 0.3s;
}

/* ═══════════ HERO & SCANNER ═══════════ */
.hero {
    flex: 1;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
}
#scannerCanvas { position: absolute; z-index: 1; pointer-events: none; }

.hero-content { position: relative; z-index: 10; padding: 0 20px; }
.hero-content h1 {
    font-family: 'Orbitron';
    font-size: clamp(1.8rem, 5vw, 3rem);
    text-transform: uppercase;
    margin-bottom: 20px;
}
.hero-content h1 em { color: var(--green); font-style: normal; }

.cta-main {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    background: var(--green);
    color: #000;
    padding: 16px 40px;
    border-radius: 50px;
    font-family: 'Orbitron';
    font-weight: 900;
    text-decoration: none;
    box-shadow: 0 0 25px var(--green-glow);
}

/* ═══════════ PARTNERS BAR ═══════════ */
.partners-bar {
    flex: 0 0 auto;
    background: rgba(255, 255, 255, 0.02);
    padding: 30px 5%;
    border-top: 2px solid var(--green);
    z-index: 10;
}
.partners-grid {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 80px;
    max-width: 1300px;
    margin: 0 auto;
}
.partners-grid img {
    height: 120px; 
    width: auto;
    object-fit: contain;
}

/* ═══════════ FOOTER ═══════════ */
footer { background: #000; padding: 12px 5%; font-size: 11px; border-top: 1px solid #111; }
.footer-row { display: flex; justify-content: space-between; align-items: center; }
.wa-link { color: #25D366; text-decoration: none; font-weight: bold; }

/* ═══════════ MOBILE OPTIMIZATION ═══════════ */
@media (max-width: 768px) {
    body { overflow-y: auto; height: auto; }
    header { padding: 10px 15px; }
    
    /* Bigger Logo on Mobile */
    .brand-left img { height: 75px; } 
    
    /* Smaller Branding Text */
    .header-center strong { font-size: 1rem; }
    .header-center span { font-size: 0.5rem; letter-spacing: 1px; }

    /* Smaller Login on Mobile */
    .login-btn { padding: 5px 12px; font-size: 0.65rem; } 
    
    .partners-grid { flex-direction: column; gap: 30px; }
    .partners-grid img { height: 100px; max-width: 85%; }
    .footer-row { flex-direction: column; gap: 12px; text-align: center; }
}
</style>
</head>
<body>

<header>
    <div class="brand-left">
        <img src="images/tarfalogo.png" alt="Logo">
    </div>
    <div class="header-center">
        <strong>TARFAVERIFY</strong>
        <span>SECURE PROTOCOL</span>
    </div>
    <div class="brand-right">
        <a href="login.php" class="login-btn">LOGIN</a>
    </div>
</header>

<main class="hero">
    <canvas id="scannerCanvas"></canvas>
    <div class="hero-content">
        <h1>Secure <em>Identity</em> Access</h1>
        <p style="color: var(--green); font-family: 'Orbitron'; font-size: 0.75rem; letter-spacing: 4px; margin-bottom: 25px; opacity: 0.8;">BIOMETRIC AUTHENTICATION LOADED</p>
        <a href="login.php" class="cta-main"><i class="fas fa-fingerprint"></i> START VERIFICATION</a>
    </div>
</main>

<section class="partners-bar">
    <div class="partners-grid">
        <img src="images/logo 1.jpg" alt="Partner Row A">
        <img src="images/logo 2.jpg" alt="Partner Row B">
    </div>
</section>

<footer>
    <div class="footer-row">
        <a href="https://wa.me/2348133961111" class="wa-link"><i class="fab fa-whatsapp"></i> JOIN WHATSAPP GROUP</a>
        <div style="color: #666;">&copy; 2026 TARFAVERIFY — ZeeTech Solutions</div>
        <div style="display: flex; gap: 15px;">
            <a href="mailto:zeetecsolutions@gmail.com" style="color: #fff;"><i class="fas fa-envelope"></i></a>
            <a href="https://wa.me/2349016052380" style="color: #fff;"><i class="fab fa-whatsapp"></i></a>
        </div>
    </div>
</footer>

<script>
const canvas = document.getElementById('scannerCanvas');
const ctx = canvas.getContext('2d');
let w, h, t = 0;

function resize() {
    w = canvas.width = window.innerWidth > 600 ? 500 : 350;
    h = canvas.height = window.innerWidth > 600 ? 500 : 350;
}
window.addEventListener('resize', resize);
resize();

function draw() {
    ctx.clearRect(0, 0, w, h);
    t += 0.015;
    const cx = w/2, cy = h/2;

    for(let i=0; i<18; i++) {
        ctx.beginPath();
        ctx.strokeStyle = `rgba(0, 255, 100, ${0.15 - (i/18)*0.15})`;
        ctx.lineWidth = 2;
        let r = 40 + i * 20 + Math.sin(t * 2 + i) * 8;
        ctx.arc(cx, cy - 10, r, Math.PI * 1.1, Math.PI * 1.9);
        ctx.stroke();
        ctx.beginPath();
        ctx.arc(cx, cy + 20, r, Math.PI * 0.1, Math.PI * 0.9);
        ctx.stroke();
    }

    let scanY = (Math.sin(t * 2) * 140) + cy;
    let grad = ctx.createLinearGradient(cx-160, 0, cx+160, 0);
    grad.addColorStop(0, 'transparent');
    grad.addColorStop(0.5, 'rgba(0, 255, 100, 0.7)');
    grad.addColorStop(1, 'transparent');
    ctx.strokeStyle = grad;
    ctx.lineWidth = 5;
    ctx.beginPath();
    ctx.moveTo(cx-160, scanY); ctx.lineTo(cx+160, scanY);
    ctx.stroke();

    requestAnimationFrame(draw);
}
draw();
</script>
</body>
</html>
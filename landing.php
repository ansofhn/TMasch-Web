<?php require_once __DIR__ . '/includes/auth.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>T-Masch — Ticketing Management System Administration School</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
:root{
  --bg:#1A3263; --surface:#1D2127; --surface-2:#242933; --line:#ffffff;
  --text:#ffffff; --muted:#E8E2DB; --accent:#FAB95B; --accent-dim:#547792;
  --ok:#FAB95B;
}
*{box-sizing:border-box;}
body{background:var(--bg); color:var(--text); font-family:'Inter',sans-serif; margin:0; padding-top:74px;}
h1,h2,h3,.display{font-family:'Space Grotesk',sans-serif;}
.container-tm{max-width:1180px; margin:0 auto; padding:0 24px;}
a{text-decoration:none;}

/* Nav */
.nav{position:fixed; top:0; left:0; right:0; z-index:100; backdrop-filter:blur(30px);}
.nav-inner{display:flex; align-items:center; justify-content:space-between; padding:18px 24px;}

.brand{display:flex; align-items:center; gap:10px; font-family:'Space Grotesk'; font-weight:700; font-size:1.15rem; color:var(--text);}
.brand .dot{width:10px; height:10px; border-radius:2px; background:var(--accent);}
.nav-links{display:flex; gap:28px; font-size:.9rem; color:var(--muted);}
.nav-links a{color:var(--muted);}
.nav-links a:hover{color:var(--text);}
.btn-tm{display:inline-flex; align-items:center; gap:8px; padding:10px 18px; border-radius:8px; font-weight:600; font-size:.9rem; border:1px solid transparent;}
.btn-primary-tm{background:var(--accent); color:#14171C;}
.btn-primary-tm:hover{background:#FAB95B; color:#14171C;}
.btn-ghost-tm{border-color:var(--line); color:var(--text);}
.btn-ghost-tm:hover{border-color:var(--accent);}

/* Hero */
.hero{padding:64px 0 40px; display:grid; grid-template-columns:1.05fr .95fr; gap:48px; align-items:center;}
.eyebrow{display:inline-flex; align-items:center; gap:8px; font-family:'JetBrains Mono'; font-size:.72rem; letter-spacing:.06em; color:#14171C; background:var(--accent); padding:5px 10px; border-radius:20px; text-transform:uppercase;}
.hero h1{font-size:2.9rem; line-height:1.12; margin:18px 0 16px; font-weight:700;}
.hero h1 span{color:var(--accent);}
.hero p.lead{color:var(--muted); font-size:1.05rem; line-height:1.6; max-width:480px; margin-bottom:28px;}
.hero-ctas{display:flex; gap:12px;}

/* Console mock */
.console{background:var(--surface); border-radius:14px; overflow:hidden; box-shadow:0 30px 60px -20px rgba(0,0,0,.5);}
.console-bar{display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid var(--line); background:var(--surface-2);}
.console-bar .mono{font-size:.75rem; color:var(--muted);}
.console-body{padding:18px;}
.ticket-card{background:var(--surface-2); border-radius:10px; padding:14px 16px; margin-bottom:10px;}
.ticket-top{display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;}
.ticket-id{font-family:'JetBrains Mono'; font-size:.78rem; color:var(--muted);}
.pill{font-family:'JetBrains Mono'; font-size:.68rem; padding:3px 9px; border-radius:20px; font-weight:500;}
.pill-open{background:#1A3263; color:#ffffff;}
.pill-progress{background:#FAB95B; color:#14171C;}
.pill-closed{background:#E8E2DB; color:#14171C;}
.ticket-title{font-size:.92rem; font-weight:600; margin-bottom:4px;}
.ticket-meta{font-size:.76rem; color:var(--muted); display:flex; gap:12px;}
.sla{margin-top:14px; padding-top:14px; border-top:1px dashed var(--muted); display:flex; justify-content:space-between; align-items:center;}
.sla .mono{font-size:.75rem; color:var(--muted);}
.sla-bar{width:120px; height:5px; background:var(--line); border-radius:3px; overflow:hidden;}
.sla-bar span{display:block; height:100%; background:var(--ok); width:92%;}

/* Flow */
.section{padding:70px 0;}
.section-head{max-width:560px; margin-bottom:44px;}
.section-head .eyebrow{margin-bottom:14px;}
.section-head h2{font-size:2rem; margin:0 0 10px;}
.section-head p{color:var(--muted); font-size:1rem;}
.flow{display:grid; grid-template-columns:repeat(3,1fr); gap:2px; border-radius:14px; overflow:hidden;}
.flow-step{padding:28px 26px; background:var(--surface); position:relative;}
.flow-num{font-family:'JetBrains Mono'; color:var(--accent); font-size:.8rem; margin-bottom:14px; display:block;}
.flow-step h3{font-size:1.15rem; margin:0 0 8px;}
.flow-step p{color:var(--muted); font-size:.88rem; line-height:1.55; margin:0;}

/* Roles */
.roles{display:grid; grid-template-columns:repeat(3,1fr); gap:18px;}
.role-card{background:var(--surface); border-radius:14px; padding:26px;}
.role-card .icon{width:42px; height:42px; border-radius:10px; background:var(--surface-2); display:flex; align-items:center; justify-content:center; color:var(--accent); font-size:1.2rem; margin-bottom:16px;}
.role-card h3{font-size:1.1rem; margin-bottom:8px;}
.role-card p{color:var(--muted); font-size:.87rem; line-height:1.55; margin-bottom:14px;}
.role-card ul{list-style:none; padding:0; margin:0; font-size:.82rem; color:var(--text);}
.role-card li{display:flex; gap:8px; align-items:flex-start; margin-bottom:8px; color:var(--muted);}
.role-card li i{color:var(--ok); margin-top:3px;}

/* Stats strip */
.stats{display:grid; grid-template-columns:repeat(4,1fr); border-radius:14px; overflow:hidden;}
.stat{padding:26px; border-right:1px solid var(--line);}
.stat:last-child{border-right:none;}
.stat .num{font-family:'Space Grotesk'; font-size:1.9rem; font-weight:700;}
.stat .lbl{color:var(--muted); font-size:.8rem; margin-top:4px;}

/* CTA */
.cta-band{background:var(--surface); border-radius:16px; padding:48px; display:flex; align-items:center; justify-content:space-between; gap:24px;}
.cta-band h2{font-size:1.6rem; margin:0 0 6px;}
.cta-band p{color:var(--muted); margin:0;}

/* Footer */
.footer{border-top:1px solid var(--line); padding:28px 0; color:var(--muted); font-size:.82rem; display:flex; justify-content:space-between;}

@media (max-width:900px){
  .hero{grid-template-columns:1fr;}
  .flow{grid-template-columns:1fr;}
  .flow-step{border-right:none; border-bottom:1px solid var(--line);}
  .roles{grid-template-columns:1fr;}
  .stats{grid-template-columns:repeat(2,1fr);}
  .stat{border-bottom:1px solid var(--line);}
  .cta-band{flex-direction:column; text-align:center;}
  .nav-links{display:none;}
}
</style>
</head>
<body>

<nav class="nav">
  <div class="container-tm nav-inner">
    <a href="/landing.php" class="brand"><span class="dot"></span>T-Masch</a>
    <div class="nav-links">
      <a href="#alur">Alur Kerja</a>
      <a href="#peran">Untuk Siapa</a>
      <a href="#tentang">Tentang</a>
    </div>
    <a href="/login.php" class="btn-tm btn-primary-tm">Masuk</i></a>
  </div>
</nav>
<div class="container-tm">
  <section class="hero">
    <div>
      <span class="eyebrow"><i class="bi bi-dot"></i> Sistem Tiket Internal Sekolah</span>
      <h1>Satu Alur untuk<br>Setiap <span>Permohonan</span><br>Sekolah.</h1>
      <p class="lead">T-Masch merapikan permintaan akademik, non-akademik, legalisir, hingga perubahan data siswa jadi satu antrian tiket yang jelas — siapa mengerjakan, sejak kapan, dan sampai mana.</p>
      <div class="hero-ctas">
        <a href="/login.php" class="btn-tm btn-primary-tm">Masuk ke Sistem</a>
        <a href="#alur" class="btn-tm btn-ghost-tm">Lihat Alur Kerja</a>
      </div>
    </div>

    <div class="console">
      <div class="console-body">
        <div class="ticket-card">
          <div class="ticket-top">
            <span class="ticket-id">#TM-2049</span>
            <span class="pill pill-open">OPEN</span>
          </div>
          <div class="ticket-title">Legalisir Ijazah — Kelas XII</div>
          <div class="ticket-meta"><span>Siti Rahma</span><span>Legalisir</span></div>
        </div>
        <div class="ticket-card">
          <div class="ticket-top">
            <span class="ticket-id">#TM-2048</span>
            <span class="pill pill-progress">IN PROGRESS</span>
          </div>
          <div class="ticket-title">Perubahan Data Wali Murid</div>
          <div class="ticket-meta"><span>Budi Santoso</span><span>Prioritas: High</span></div>
        </div>
        <div class="ticket-card" style="margin-bottom:0;">
          <div class="ticket-top">
            <span class="ticket-id">#TM-2047</span>
            <span class="pill pill-closed">CLOSED</span>
          </div>
          <div class="ticket-title">Permohonan Transkrip Nilai</div>
          <div class="ticket-meta"><span>Rangga P.</span><span>Selesai 1 hari lalu</span></div>
        </div>
        <div class="sla">
          <span class="mono">SLA rata-rata</span>
          <div class="sla-bar"><span></span></div>
          <span class="mono" style="color:var(--ok)">92%</span>
        </div>
      </div>
    </div>
  </section>

  <section class="section" id="alur">
    <div class="section-head">
      <span class="eyebrow">Alur Kerja</span>
      <h2>Tiga langkah, dari masuk sampai selesai.</h2>
      <p>Setiap tiket bergerak lewat urutan yang sama — tidak ada permintaan yang tersesat di grup chat.</p>
    </div>
    <div class="flow">
      <div class="flow-step">
        <span class="flow-num">01 · Submit</span>
        <h3>Siswa membuat tiket</h3>
        <p>Pilih kategori, tulis subjek dan deskripsi. Status otomatis <strong>Open</strong> begitu dikirim.</p>
      </div>
      <div class="flow-step">
        <span class="flow-num">02 · Assign</span>
        <h3>Admin menugaskan</h3>
        <p>Admin memilih guru/staf dan tingkat prioritas. Status berubah jadi <strong>In Progress</strong>.</p>
      </div>
      <div class="flow-step">
        <span class="flow-num">03 · Resolve</span>
        <h3>Guru menyelesaikan</h3>
        <p>Guru menulis catatan penyelesaian, tiket ditutup sebagai <strong>Closed</strong> dengan riwayat lengkap.</p>
      </div>
    </div>
  </section>

  <section class="section" id="peran">
    <div class="section-head">
      <span class="eyebrow">Untuk Siapa</span>
      <h2>Satu sistem, tiga sudut pandang.</h2>
      <p>Setiap peran hanya melihat apa yang relevan untuknya.</p>
    </div>
    <div class="roles">
      <div class="role-card">
        <div class="icon"><i class="bi bi-shield-lock"></i></div>
        <h3>Admin</h3>
        <p>Mengatur seluruh operasional tiket dan data master.</p>
        <ul>
          <li><i class="bi bi-check-circle-fill"></i> Menugaskan tiket ke guru</li>
          <li><i class="bi bi-check-circle-fill"></i> Mengatur prioritas &amp; kategori</li>
          <li><i class="bi bi-check-circle-fill"></i> Melihat laporan penuh</li>
        </ul>
      </div>
      <div class="role-card">
        <div class="icon"><i class="bi bi-person-workspace"></i></div>
        <h3>Guru</h3>
        <p>Fokus hanya pada tiket yang ditugaskan padanya.</p>
        <ul>
          <li><i class="bi bi-check-circle-fill"></i> Melihat tiket miliknya saja</li>
          <li><i class="bi bi-check-circle-fill"></i> Menulis catatan penyelesaian</li>
          <li><i class="bi bi-check-circle-fill"></i> Menutup tiket yang selesai</li>
        </ul>
      </div>
      <div class="role-card">
        <div class="icon"><i class="bi bi-mortarboard"></i></div>
        <h3>Siswa</h3>
        <p>Mengajukan permohonan tanpa perlu bertanya ke sana-sini.</p>
        <ul>
          <li><i class="bi bi-check-circle-fill"></i> Membuat tiket baru</li>
          <li><i class="bi bi-check-circle-fill"></i> Memantau status real-time</li>
          <li><i class="bi bi-check-circle-fill"></i> Riwayat semua pengajuan</li>
        </ul>
      </div>
    </div>
  </section>

  <section class="section" id="tentang">
    <div class="stats">
      <div class="stat"><div class="num mono">3</div><div class="lbl">Peran akses berbeda</div></div>
      <div class="num-wrap stat"><div class="num mono">5</div><div class="lbl">Kategori permohonan</div></div>
      <div class="stat"><div class="num mono">92%</div><div class="lbl">Rata-rata SLA terpenuhi</div></div>
      <div class="stat"><div class="num mono">1</div><div class="lbl">Antrian, tanpa duplikasi</div></div>
    </div>
  </section>

  <section class="section" style="padding-top:0;">
    <div class="cta-band">
      <div>
        <h2>Siap merapikan alur tiket sekolahmu?</h2>
        <p>Masuk dengan akun yang sudah terdaftar untuk mulai memantau tiket hari ini.</p>
      </div>
      <a href="/login.php" class="btn-tm btn-primary-tm">Masuk ke Sistem</a>
    </div>
  </section>

  <footer class="footer">
    <span>© <?= date('Y') ?> T-Masch — Universitas Indraprasta PGRI</span>
    <span>made with spell and potions</span>
  </footer>

</div>
</body>
</html>

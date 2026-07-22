<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
require_once __DIR__ . '/../config/db.php';

$pageTitle = 'Laporan Tren Tiket';

$sql = "SELECT DATE_FORMAT(t.created_at,'%Y-%m') AS bulan,
               COUNT(*) AS total,
               SUM(s.name='Open') AS jml_open,
               SUM(s.name='In Progress') AS jml_progress,
               SUM(s.name='Closed') AS jml_closed
        FROM tickets t
        JOIN statuses s ON t.status_id = s.id
        GROUP BY bulan
        ORDER BY bulan DESC
        LIMIT 12";
$trend = $conn->query($sql);

$rows = [];
while ($r = $trend->fetch_assoc()) $rows[] = $r;
$rowsChart = array_reverse($rows); // urut ascending untuk chart

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-start no-print">
    <div>
        <h2>Laporan Tren Tiket</h2>
        <p>Jumlah tiket per bulan berdasarkan status.</p>
    </div>
    <button onclick="window.print()" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-printer me-1"></i> Cetak / PDF
    </button>
</div>

<div class="card mb-3 no-print">
    <div class="card-body">
        <canvas id="trendChart" height="90"></canvas>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <strong>T-Masch</strong> — Laporan Tren Tiket &nbsp;|&nbsp;
        <span class="text-muted">Dicetak: <?= date('d/m/Y H:i') ?></span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0 align-middle">
            <thead>
                <tr><th>Bulan</th><th>Total</th><th>Open</th><th>In Progress</th><th>Closed</th></tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data.</td></tr>
                <?php endif; ?>
                <?php foreach ($rows as $r): ?>
                <tr>
                    <td class="fw-semibold"><?= h($r['bulan']) ?></td>
                    <td><?= $r['total'] ?></td>
                    <td><span class="text-primary"><?= $r['jml_open'] ?></span></td>
                    <td><span style="color:#f59e0b"><?= $r['jml_progress'] ?></span></td>
                    <td><span class="text-secondary"><?= $r['jml_closed'] ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
const labels = <?= json_encode(array_column($rowsChart, 'bulan')) ?>;
const openData = <?= json_encode(array_map('intval', array_column($rowsChart, 'jml_open'))) ?>;
const progressData = <?= json_encode(array_map('intval', array_column($rowsChart, 'jml_progress'))) ?>;
const closedData = <?= json_encode(array_map('intval', array_column($rowsChart, 'jml_closed'))) ?>;

new Chart(document.getElementById('trendChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            { label: 'Open', data: openData, backgroundColor: '#3b82f6' },
            { label: 'In Progress', data: progressData, backgroundColor: '#f59e0b' },
            { label: 'Closed', data: closedData, backgroundColor: '#94a3b8' }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } },
        scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } }
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/config.php');

if (strlen($_SESSION['wlogin']) == 0) {
    header('location:index.php');
    exit();
}

// Fetch treated/untreated counts per category + medicines used
$sql = "SELECT 
            c.categoryName,
            SUM(CASE WHEN mu.status = 'treated' THEN 1 ELSE 0 END) AS treated_count,
            SUM(CASE WHEN mu.status != 'treated' OR mu.status IS NULL THEN 1 ELSE 0 END) AS untreated_count,
            GROUP_CONCAT(DISTINCT fs2.medecine ORDER BY fs2.medecine ASC SEPARATOR ', ') AS medicines_used
        FROM tblfound_symptoms fs
        JOIN tbldeseases d ON fs.dis_id = d.id
        JOIN tblcategory c ON d.deseaseCategory = c.id
        LEFT JOIN medecine_usage mu ON fs.fid = mu.fid
        LEFT JOIN faostock fs2 ON mu.medecine_id = fs2.medecine_id
        GROUP BY c.categoryName
        ORDER BY c.categoryName ASC";

$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

// Prepare data for charts (still used on page)
$categories = [];
$treatedPercentages = [];
$untreatedPercentages = [];
$totalTreated = 0;
$totalUntreated = 0;

foreach ($results as $row) {
    $categories[] = $row->categoryName;
    $total = $row->treated_count + $row->untreated_count;
    $treatedPercent = $total > 0 ? round(($row->treated_count / $total) * 100, 2) : 0;
    $untreatedPercent = $total > 0 ? round(($row->untreated_count / $total) * 100, 2) : 0;

    $treatedPercentages[] = $treatedPercent;
    $untreatedPercentages[] = $untreatedPercent;

    $totalTreated += $row->treated_count;
    $totalUntreated += $row->untreated_count;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>INTELLIGENT AGRICULTURAL ASSISTANCE PLATFORM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .bg-black-custom { background-color: #000000; color: #ffffff; }
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2 { text-align: center; }
        .chart-container { width: 50%; margin: auto; }
        table { width: 90%; margin: 30px auto; border-collapse: collapse; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 10px; text-align: center; }
        .btn-export { 
            display: block; 
            width: 220px; 
            margin: 30px auto; 
            padding: 12px; 
            background: #28a745; 
            color: #fff; 
            text-align: center; 
            border: none; 
            border-radius: 6px; 
            font-size: 16px; 
            cursor: pointer; 
        }
        .btn-export:hover { background: #218838; }
        #report-loading {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.4);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 1.4rem;
        }
        .spinner-border { margin-right: 10px; }
    </style>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- jsPDF - add this line! -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body>
<?php include('includes/header.php'); ?>
<div class="ts-main-content">
    <?php include('includes/leftbar.php'); ?>
    <div class="content-wrapper">
        <div class="container-fluid">
            <div id="report-section">

                <h2>Treated vs Untreated Report by Category</h2>

                <div class="chart-container">
                    <canvas id="barChart"></canvas>
                </div>

                <div class="chart-container" style="margin-top:40px;">
                    <canvas id="pieChart"></canvas>
                </div>

                <table id="report-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Treated</th>
                            <th>Untreated</th>
                            <th>Treated %</th>
                            <th>Untreated %</th>
                            <th>Medicine(s) Used</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $row): 
                            $total = $row->treated_count + $row->untreated_count;
                            $treatedPercent = $total > 0 ? round(($row->treated_count / $total) * 100, 2) : 0;
                            $untreatedPercent = $total > 0 ? round(($row->untreated_count / $total) * 100, 2) : 0;
                        ?>
                        <tr>
                            <td><?php echo htmlentities($row->categoryName); ?></td>
                            <td><?php echo $row->treated_count; ?></td>
                            <td><?php echo $row->untreated_count; ?></td>
                            <td><?php echo $treatedPercent; ?>%</td>
                            <td><?php echo $untreatedPercent; ?>%</td>
                            <td><?php echo htmlentities($row->medicines_used ?: 'N/A'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <button id="download-report-btn" class="btn-export">
                    Download PDF Report
                </button>

            </div>
        </div>
    </div>
</div>

<!-- Loading overlay -->
<div id="report-loading">
    <div class="spinner-border text-success" role="status"></div>
    Generating PDF...
</div>

<script>
// Your existing Chart.js code remains unchanged
const ctxBar = document.getElementById('barChart').getContext('2d');
new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($categories); ?>,
        datasets: [
            {
                label: 'Treated (%)',
                data: <?php echo json_encode($treatedPercentages); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.7)'
            },
            {
                label: 'Untreated (%)',
                data: <?php echo json_encode($untreatedPercentages); ?>,
                backgroundColor: 'rgba(255, 99, 132, 0.7)'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 1.5,
        plugins: { legend: { position: 'top' } },
        scales: {
            y: { beginAtZero: true, max: 100 }
        }
    }
});

const ctxPie = document.getElementById('pieChart').getContext('2d');
new Chart(ctxPie, {
    type: 'pie',
    data: {
        labels: ['Treated', 'Untreated'],
        datasets: [{
            data: [<?php echo $totalTreated; ?>, <?php echo $totalUntreated; ?>],
            backgroundColor: ['rgba(54, 162, 235, 0.7)', 'rgba(255, 99, 132, 0.7)']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 1.3,
        plugins: { legend: { position: 'top' } }
    }
});

// ─────────────────────────────────────────────────────────────
// PDF DOWNLOAD FUNCTION (table only - same style as previous)
// ─────────────────────────────────────────────────────────────
async function downloadTreatedUntreatedReportPDF() {
    if (!window.jspdf) {
        alert("PDF library not loaded.");
        return;
    }

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4');

    document.getElementById('report-loading').style.display = 'flex';
    document.getElementById('download-report-btn').disabled = true;

    try {
        let y = 25;

        // ── Header ────────────────────────────────────────────────
        doc.setFontSize(16);
        doc.setFont("helvetica", "bold");
        doc.text('INTELLIGENT AGRICULTURAL ASSISTANCE PLATFORM', 105, y, { align: 'center' });
        y += 10;

        doc.setFontSize(14);
        doc.text('Treated vs Untreated Report by Category', 105, y, { align: 'center' });
        y += 15;

        // Generated on – top right
        doc.setFontSize(9);
        doc.setFont("helvetica", "normal");
        const generated = 'Generated on: ' + new Date().toLocaleString();
        doc.text(generated, 190 - doc.getTextWidth(generated), y - 5);

        y += 12;

        // Table title
        doc.setFontSize(12);
        doc.setFont("helvetica", "bold");
        doc.text('Category Treatment Summary', 105, y, { align: 'center' });
        y += 10;

        // ── Table setup ───────────────────────────────────────────
        const headers = ['Category', 'Treated', 'Untreated', 'Treated %', 'Untreated %', 'Medicine(s) Used'];
        const colWidths = [18, 14, 16, 20, 18, 40]; // total ~200 mm, with more space for medicines
        let x = 20;

        // Light purple header background
        doc.setFillColor(230, 217, 242);
        doc.rect(20, y - 7, 170, 11, 'F');

        // Centered headers
        doc.setFontSize(10);
        doc.setFont("helvetica", "bold");
        headers.forEach((h, i) => {
            const cx = x + (colWidths[i] / 2);
            doc.text(h, cx, y + 2, { align: 'center', baseline: 'middle' });
            x += colWidths[i];
        });

        doc.setLineWidth(0.7);
        doc.line(20, y + 4, 190, y + 4);
        y += 11;

        // ── Table body ────────────────────────────────────────────
        doc.setFont("helvetica", "normal");
        doc.setFontSize(9);
        doc.setLineWidth(0.25);

        const rows = <?php echo json_encode($results); ?>;
        console.log("Number of rows for PDF:", rows.length); // ← helpful debug

        let tableTop = y;

        rows.forEach(row => {
            if (y > 272) {
                // Draw remaining vertical lines before page break
                let vx = 20;
                colWidths.forEach(w => {
                    doc.line(vx, tableTop - 7, vx, y + 4);
                    vx += w;
                });
                doc.line(190, tableTop - 7, 190, y + 4);

                doc.addPage();
                y = 25;
                tableTop = 25;
            }

            x = 20;

            const treated = Number(row.treated_count);
            const untreated = Number(row.untreated_count);
            const total = treated + untreated;
            const treatedPct = total > 0 ? (treated / total * 100).toFixed(2) + '%' : '0%';
            const untreatedPct = total > 0 ? (untreated / total * 100).toFixed(2) + '%' : '0%';

            const data = [
                row.categoryName || '-',
                treated.toString(),
                untreated.toString(),
                treatedPct,
                untreatedPct,
                row.medicines_used || 'N/A'
            ];

            data.forEach((txt, i) => {
                if (i >= 1 && i <= 4) { // right-align numbers & percentages
                    const tw = doc.getTextWidth(txt);
                    doc.text(txt, x + colWidths[i] - tw - 3, y + 3.5);
                } else {
                    // wrap long medicine names if needed (simple version)
                    doc.text(txt.substring(0, 60) + (txt.length > 60 ? '...' : ''), x + 2, y + 3.5, { baseline: 'middle' });
                }
                x += colWidths[i];
            });

            doc.line(20, y + 7, 190, y + 7);
            y += 7;
        });

        // Final grid closing lines
        let vx = 20;
        colWidths.forEach(w => {
            doc.line(vx, tableTop - 7, vx, y);
            vx += w;
        });
        doc.line(190, tableTop - 7, 190, y);

        doc.setLineWidth(0.6);
        doc.line(20, y, 190, y);

        // Prepared by
        doc.setFontSize(9);
        doc.setFont("helvetica", "italic");
        const prep = 'Prepared by: Kabatesi Patience';
        doc.text(prep, 190 - doc.getTextWidth(prep), y + 8);

        // Save file
        const filename = `Treated_Untreated_Report_${new Date().toISOString().split('T')[0]}.pdf`;
        doc.save(filename);

    } catch (err) {
        console.error('PDF error:', err);
        alert('Error creating PDF: ' + err.message);
    } finally {
        document.getElementById('report-loading').style.display = 'none';
        document.getElementById('download-report-btn').disabled = false;
    }
}

// Attach button click
document.getElementById('download-report-btn').addEventListener('click', downloadTreatedUntreatedReportPDF);
</script>

</body>
</html>
<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/config.php');

if (strlen($_SESSION['wlogin']) == 0) {
    header('location:index.php');
    exit();
}

// Pagination & filter inputs
$medecine_id = isset($_GET['medecine_id']) ? $_GET['medecine_id'] : '';
$withdrawn_at = isset($_GET['withdrawn_at']) ? $_GET['withdrawn_at'] : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Fetch all medicines for dropdown
try {
    $sql = "SELECT medecine_id, medecine FROM faostock ORDER BY medecine ASC";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $medicines = $stmt->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    echo "Error fetching medicines: " . $e->getMessage();
    exit();
}

// Fetch selected medicine name for PDF
$selected_medicine = '';
if ($medecine_id !== '' && is_numeric($medecine_id)) {
    try {
        $sql = "SELECT medecine FROM faostock WHERE medecine_id = :medecine_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':medecine_id', $medecine_id, PDO::PARAM_INT);
        $stmt->execute();
        $selected_medicine = $stmt->fetchColumn();
    } catch (PDOException $e) {
        echo "Error fetching medicine name: " . $e->getMessage();
        exit();
    }
}

// Build WHERE clause for filters
$where = [];
$params = [];

if ($medecine_id !== '' && is_numeric($medecine_id)) {
    $where[] = 'fs.medecine_id = :medecine_id';
    $params[':medecine_id'] = $medecine_id;
}
if ($withdrawn_at) {
    $where[] = 'DATE(mu.withdrawn_at) = :withdrawn_at';
    $params[':withdrawn_at'] = $withdrawn_at;
}
$whereSql = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Count total filtered records (for pagination)
try {
    $countSql = "
        SELECT COUNT(DISTINCT fs.medecine_id) 
        FROM faostock fs
        LEFT JOIN medecine_usage mu ON fs.medecine_id = mu.medecine_id
        $whereSql
    ";
    $countStmt = $dbh->prepare($countSql);
    foreach ($params as $k => $v) {
        $countStmt->bindValue($k, $v);
    }
    $countStmt->execute();
    $totalRecords = $countStmt->fetchColumn();
    $totalPages = ceil($totalRecords / $perPage);
} catch (PDOException $e) {
    echo "Error counting records: " . $e->getMessage();
    exit();
}

// Fetch filtered records with pagination (for display)
try {
    $dataSql = "
        SELECT 
            fs.medecine_id,
            fs.medecine,
            COALESCE(fs.quantity, 0) AS current_stock,
            COALESCE(SUM(mu.quantity_withdrawn), 0) AS total_used
        FROM faostock fs
        LEFT JOIN medecine_usage mu ON fs.medecine_id = mu.medecine_id
        $whereSql
        GROUP BY fs.medecine_id, fs.medecine, fs.quantity
        ORDER BY fs.medecine ASC
        LIMIT :offset, :limit
    ";
    $dataStmt = $dbh->prepare($dataSql);
    foreach ($params as $k => $v) {
        $dataStmt->bindValue($k, $v);
    }
    $dataStmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $dataStmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
    $dataStmt->execute();
    $rows = $dataStmt->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    echo "Error fetching data: " . $e->getMessage();
    exit();
}

// Fetch all filtered records for PDF (no pagination)
try {
    $pdfSql = "
        SELECT 
            fs.medecine,
            COALESCE(fs.quantity, 0) AS current_stock,
            COALESCE(SUM(mu.quantity_withdrawn), 0) AS total_used
        FROM faostock fs
        LEFT JOIN medecine_usage mu ON fs.medecine_id = mu.medecine_id
        $whereSql
        GROUP BY fs.medecine_id, fs.medecine, fs.quantity
        ORDER BY fs.medecine ASC
    ";
    $pdfStmt = $dbh->prepare($pdfSql);
    foreach ($params as $k => $v) {
        $pdfStmt->bindValue($k, $v);
    }
    $pdfStmt->execute();
    $pdfData = $pdfStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching PDF data: " . $e->getMessage();
    exit();
}

// Calculate summary statistics for PDF
$total_medicines = count($pdfData);
$total_stock = array_sum(array_column($pdfData, 'current_stock'));
$total_used = array_sum(array_column($pdfData, 'total_used'));
$avg_stock_per_medicine = $total_medicines > 0 ? round($total_stock / $total_medicines, 1) : 0;
$most_stocked_medicine = '';
$max_stock = 0;
foreach ($pdfData as $row) {
    if ($row['current_stock'] > $max_stock) {
        $max_stock = $row['current_stock'];
        $most_stocked_medicine = $row['medecine'];
    }
}

// Prepare chart data for bar chart
$chartLabels = [];
$chartCurrentStocks = [];
$chartTotalUsed = [];
foreach ($rows as $row) {
    $chartLabels[] = $row->medecine;
    $chartCurrentStocks[] = (int)$row->current_stock;
    $chartTotalUsed[] = (int)$row->total_used;
}

// Prepare data for pie chart (total stock vs total used)
$totalStock = array_sum($chartCurrentStocks);
$totalUsed = array_sum($chartTotalUsed);
$pieData = [$totalStock, $totalUsed];
$pieLabels = ['Total Current Stock', 'Total Used'];

// Prepare data for line chart (usage over time)
try {
    $lineSql = "
        SELECT DATE(mu.withdrawn_at) AS usage_date, 
               COALESCE(SUM(mu.quantity_withdrawn), 0) AS total_used
        FROM medecine_usage mu
        " . ($medecine_id ? 'WHERE mu.medecine_id = :medecine_id' : '') . "
        GROUP BY DATE(mu.withdrawn_at)
        ORDER BY usage_date ASC
    ";
    $lineStmt = $dbh->prepare($lineSql);
    if ($medecine_id) {
        $lineStmt->bindValue(':medecine_id', $medecine_id, PDO::PARAM_INT);
    }
    $lineStmt->execute();
    $lineData = $lineStmt->fetchAll(PDO::FETCH_OBJ);

    $lineLabels = [];
    $lineValues = [];
    foreach ($lineData as $row) {
        $lineLabels[] = $row->usage_date;
        $lineValues[] = (int)$row->total_used;
    }
} catch (PDOException $e) {
    echo "Error fetching line chart data: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Stock Management Dashboard - Report3</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <style>
        body { font-family: Arial, sans-serif; margin: 0; }
        .ts-main-content { display: flex; min-height: 100vh; }
        .left-sidebar { 
            width: 250px; 
            background: #f8f9fa; 
            padding: 20px; 
            border-right: 1px solid #ddd; 
        }
        .left-sidebar h3 { 
            font-size: 1.5rem; 
            margin-bottom: 20px; 
            text-align: center; 
        }
        .left-sidebar .nav-link { 
            color: #333; 
            padding: 10px; 
            border-radius: 5px; 
        }
        .left-sidebar .nav-link:hover, 
        .left-sidebar .nav-link.active { 
            background: #007bff; 
            color: white; 
        }
        .content-wrapper { 
            flex: 1; 
            padding: 20px; 
            background: #fff; 
        }
        h2 { text-align: center; margin-bottom: 30px; }
        .filter-row { margin-bottom: 20px; }
        .chart-row { 
            display: flex; 
            flex-wrap: wrap; 
            justify-content: space-around; 
            margin-bottom: 50px; 
        }
        .chart-col { 
            flex: 1; 
            min-width: 300px; 
            max-width: 450px; 
            margin: 10px; 
        }
        table { 
            width: 90%; 
            margin: 0 auto 30px; 
            border-collapse: collapse; 
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: center; 
        }
        th { background: #f4f4f4; }
        .pagination { justify-content: center; }
        .error-message { color: red; text-align: center; margin-bottom: 20px; }
        .download-btn { margin-left: 10px; }
        #loading { display: none; color: green; margin-left: 10px; }
    </style>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
</head>
<body>

<?php include('includes/header.php'); ?>
<div class="ts-main-content">
    <?php include('includes/leftbar.php'); ?>
    <div class="content-wrapper">
        <div class="container-fluid">
            <h2>Stock Management Dashboard</h2>

            <form method="get" class="form-inline filter-row justify-content-center">
                <div class="form-group mb-2">
                    <label for="medecine_id" class="mr-2">Medicine:</label>
                    <select id="medecine_id" name="medecine_id" class="form-control">
                        <option value="">All Medicines</option>
                        <?php foreach ($medicines as $med): ?>
                            <option value="<?php echo htmlentities($med->medecine_id); ?>" <?php if ($medecine_id == $med->medecine_id) echo 'selected'; ?>>
                                <?php echo htmlentities($med->medecine); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mb-2 ml-3">
                    <label for="withdrawn_at" class="mr-2">Withdrawn At:</label>
                    <input type="date" id="withdrawn_at" name="withdrawn_at" value="<?php echo htmlentities($withdrawn_at); ?>" class="form-control" />
                </div>

                <button type="submit" class="btn btn-primary mb-2 ml-3">Filter</button>
                <button type="button" id="download-btn" class="btn btn-danger mb-2 ml-3 download-btn" onclick="downloadStockReportPDF()">Download as PDF</button>
                <span id="loading">Generating PDF...</span>
            </form>

            <div class="chart-row">
                <div class="chart-col">
                    <h3>Stock vs Usage (Bar)</h3>
                    <div class="chart-container">
                        <canvas id="stockUsageChart"></canvas>
                    </div>
                </div>
                <div class="chart-col">
                    <h3>Stock Distribution (Pie)</h3>
                    <div class="chart-container">
                        <canvas id="stockPieChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="chart-row">
                <div class="chart-col">
                    <h3>Usage Over Time (Line)</h3>
                    <div class="chart-container">
                        <canvas id="usageLineChart"></canvas>
                    </div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Current Stock</th>
                        <th>Total Used</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($rows) === 0): ?>
                        <tr><td colspan="3">No records found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <td><?php echo htmlentities($row->medecine); ?></td>
                                <td><?php echo (int)$row->current_stock; ?></td>
                                <td><?php echo (int)$row->total_used; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <nav>
                <ul class="pagination">
                    <?php if ($page > 1): ?>
                        <li class="page-item"><a class="page-link" href="?<?php
                            $query = $_GET;
                            $query['page'] = $page - 1;
                            echo http_build_query($query);
                        ?>">Prev</a></li>
                    <?php endif; ?>

                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <li class="page-item <?php if ($p == $page) echo 'active'; ?>">
                            <a class="page-link" href="?<?php
                                $query = $_GET;
                                $query['page'] = $p;
                                echo http_build_query($query);
                            ?>"><?php echo $p; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <li class="page-item"><a class="page-link" href="?<?php
                            $query = $_GET;
                            $query['page'] = $page + 1;
                            echo http_build_query($query);
                        ?>">Next</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Bar Chart
    const ctxBar = document.getElementById('stockUsageChart').getContext('2d');
    const stockUsageChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chartLabels); ?>,
            datasets: [
                {
                    label: 'Current Stock',
                    data: <?php echo json_encode($chartCurrentStocks); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)'
                },
                {
                    label: 'Total Used',
                    data: <?php echo json_encode($chartTotalUsed); ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.7)'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            aspectRatio: 1.5,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });

    // Pie Chart
    const ctxPie = document.getElementById('stockPieChart').getContext('2d');
    const stockPieChart = new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($pieLabels); ?>,
            datasets: [{
                data: <?php echo json_encode($pieData); ?>,
                backgroundColor: [
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 99, 132, 0.7)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            aspectRatio: 1.5
        }
    });

    // Line Chart
    const ctxLine = document.getElementById('usageLineChart').getContext('2d');
    const usageLineChart = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($lineLabels); ?>,
            datasets: [{
                label: 'Total Used Over Time',
                data: <?php echo json_encode($lineValues); ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                fill: false,
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            aspectRatio: 1.5,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                }
            }
        }
    });

async function downloadStockReportPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4');
    
    // Show loading
    document.getElementById('loading').style.display = 'inline';
    document.getElementById('download-btn').disabled = true;
    
    try {
        let yPosition = 20;
        
        // Add logo and header on the same line
        try {
            // Create an image element to load the logo
            const logoImg = new Image();
            logoImg.onload = function() {
                // Add logo on the left side, aligned with title on same line
                doc.addImage(logoImg, 'JPEG', 15, yPosition - 5, 25, 25);
            };
            logoImg.src = '../assets/images/agri_logo.jpg';
            
            // Wait a moment for logo to potentially load
            await new Promise(resolve => setTimeout(resolve, 100));
        } catch (error) {
            console.log('Logo loading failed, continuing without logo');
        }
        
        // Header with logo on same line - shortened title
        doc.setFontSize(18);
        doc.setFont(undefined, 'bold');
        doc.text('AGRICULTURAL PLATFORM', 105, yPosition, { align: 'center' });
        yPosition += 8;
        
        doc.setFontSize(16);
        doc.setFont(undefined, 'bold');
        doc.text('Stock Management Report', 105, yPosition, { align: 'center' });
        yPosition += 15;
        
        // Add "Generated on" above the table
        doc.setFontSize(10);
        doc.setFont(undefined, 'normal');
        const now = new Date();
        const currentDateTime = now.toLocaleString('en-US', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        }).replace(/(\d+)\/(\d+)\/(\d+),/, '$3-$1-$2').replace(',', '');
        
        doc.text(`Generated on: ${currentDateTime}`, 175, yPosition, { align: 'right' });
        yPosition += 10;
        
        // Table data from PHP
        const tableHead = [['#', 'Medicine', 'Current Stock', 'Total Used']];
        
        // Get the data from PHP
        const stockData = <?php echo json_encode($pdfData); ?>;
        const tableBody = stockData.map((row, index) => [
            (index + 1).toString(),
            row.medecine.length > 35 ? row.medecine.substring(0, 32) + '...' : row.medecine,
            row.current_stock.toString(),
            row.total_used.toString()
        ]);
        
        // Create table with improved styling matching maintenance report format
        doc.autoTable({
            head: tableHead,
            body: tableBody,
            startY: yPosition,
            theme: 'grid',
            styles: { 
                fontSize: 10, 
                halign: 'center',
                valign: 'middle',
                lineColor: [0, 0, 0],
                lineWidth: 0.5
            },
            headStyles: { 
                fillColor: [230, 230, 250], // Light purple/lavender background
                textColor: [0, 0, 0], 
                fontStyle: 'bold',
                fontSize: 11
            },
            bodyStyles: {
                fillColor: [255, 255, 255], // White background for body
                textColor: [0, 0, 0]
            },
            alternateRowStyles: {
                fillColor: [248, 248, 255] // Very light purple for alternate rows
            },
            columnStyles: {
                0: { cellWidth: 15, halign: 'center' }, // #
                1: { cellWidth: 90, halign: 'left' },   // Medicine
                2: { cellWidth: 30, halign: 'center' }, // Current Stock
                3: { cellWidth: 30, halign: 'center' }  // Total Used
            },
            margin: { left: 15, right: 15 }
        });
        
        // Footer - positioned at bottom of table area
        const finalY = doc.lastAutoTable.finalY + 15;
        doc.setFontSize(10);
        doc.setFont(undefined, 'normal');
        
        // Add filter information and prepared by
        let footerText = '';
        <?php if (!empty($selected_medicine)): ?>
        footerText += 'Medicine: <?php echo addslashes($selected_medicine); ?> | ';
        <?php endif; ?>
        
        footerText += 'Prepared by: Patience Kabatesi';
        
        doc.text(footerText, 20, finalY);
        
        // Save file with timestamp
        const fileName = `Stock_Report_${new Date().toISOString().split('T')[0]}.pdf`;
        doc.save(fileName);
        
    } catch (error) {
        console.error('Error generating Stock Report PDF:', error);
        alert('Error generating PDF. Please try again.');
    } finally {
        // Hide loading
        document.getElementById('loading').style.display = 'none';
        document.getElementById('download-btn').disabled = false;
    }
}
</script>

</body>
</html>
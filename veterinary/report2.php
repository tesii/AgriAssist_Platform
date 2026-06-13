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
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$disease_filter = isset($_GET['disease']) ? $_GET['disease'] : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Build WHERE clause for filters
$where = [];
$params = [];

if ($from_date && $to_date) {
    $where[] = 'DATE(fs.PostingDate) BETWEEN :from_date AND :to_date';
    $params[':from_date'] = $from_date;
    $params[':to_date'] = $to_date;
} elseif ($from_date) {
    $where[] = 'DATE(fs.PostingDate) >= :from_date';
    $params[':from_date'] = $from_date;
} elseif ($to_date) {
    $where[] = 'DATE(fs.PostingDate) <= :to_date';
    $params[':to_date'] = $to_date;
}

if ($status !== '') {
    $where[] = 'fs.Status = :status';
    $params[':status'] = $status;
}

if ($disease_filter !== '') {
    if ($disease_filter === 'Unknown') {
        $where[] = '(fs.dis_id IS NULL OR d.deseaseTitle IS NULL)';
    } else {
        $where[] = 'd.deseaseTitle = :disease';
        $params[':disease'] = $disease_filter;
    }
}

// Exclude records where disease is "Unknown" from main queries
$where[] = '(fs.dis_id IS NOT NULL AND d.deseaseTitle IS NOT NULL AND d.deseaseTitle != "Unknown")';

$whereSql = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Get available diseases for filter dropdown
try {
    $diseasesSql = "
        SELECT DISTINCT d.deseaseTitle 
        FROM tblfound_symptoms fs
        LEFT JOIN tbldeseases d ON fs.dis_id = d.id
        WHERE fs.dis_id IS NOT NULL 
        AND d.deseaseTitle IS NOT NULL 
        AND d.deseaseTitle != 'Unknown'
        ORDER BY d.deseaseTitle
    ";
    $diseasesStmt = $dbh->prepare($diseasesSql);
    $diseasesStmt->execute();
    $availableDiseases = $diseasesStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    echo "Error fetching diseases: " . $e->getMessage();
    exit();
}

// Count total filtered records (for pagination)
try {
    $countSql = "
        SELECT COUNT(*) 
        FROM tblfound_symptoms fs
        LEFT JOIN tbldeseases d ON fs.dis_id = d.id
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
            fs.fid,
            fs.requestno,
            fs.dis_id,
            d.deseaseTitle AS disease_deseaseTitle,
            fs.found_symptoms,
            fs.userEmail,
            fs.ToDate,
            fs.Status,
            fs.PostingDate
        FROM tblfound_symptoms fs
        LEFT JOIN tbldeseases d ON fs.dis_id = d.id
        $whereSql
        ORDER BY fs.PostingDate DESC
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
            fs.requestno,
            d.deseaseTitle AS disease_deseaseTitle,
            fs.found_symptoms,
            fs.userEmail,
            fs.ToDate,
            fs.Status,
            fs.PostingDate
        FROM tblfound_symptoms fs
        LEFT JOIN tbldeseases d ON fs.dis_id = d.id
        $whereSql
        ORDER BY fs.PostingDate DESC
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

// Calculate summary statistics and disease counts
$total_submissions = count($pdfData);
$disease_counts = [];
foreach ($pdfData as $row) {
    $disease = $row['disease_deseaseTitle'];
    if ($disease) {
        $disease_counts[$disease] = ($disease_counts[$disease] ?? 0) + 1;
    }
}
$unique_diseases = count($disease_counts);
$most_common_disease = '';
$max_disease_count = 0;
foreach ($disease_counts as $disease => $count) {
    if ($count > $max_disease_count) {
        $max_disease_count = $count;
        $most_common_disease = $disease;
    }
}
$avg_submissions_per_disease = $unique_diseases > 0 ? round($total_submissions / $unique_diseases, 1) : 0;

// Determine color thresholds for disease occurrences
$counts = array_values($disease_counts);
sort($counts);
$count_length = count($counts);
$low_threshold = $count_length > 0 ? $counts[max(0, floor($count_length / 3) - 1)] : 0;
$high_threshold = $count_length > 0 ? $counts[max(0, floor(2 * $count_length / 3) - 1)] : 0;

// Assign colors to diseases
$disease_colors = [];
foreach ($disease_counts as $disease => $count) {
    if ($count > $high_threshold) {
        $disease_colors[$disease] = 'high-disease'; // Red
    } elseif ($count > $low_threshold) {
        $disease_colors[$disease] = 'avg-disease'; // Yellow
    } else {
        $disease_colors[$disease] = 'low-disease'; // Green
    }
}

// Count submissions by status for pie chart
$status_counts = array_count_values(array_column($pdfData, 'Status'));
$status_labels = array_keys($status_counts);
$status_values = array_values($status_counts);

// Prepare chart data for bar chart (disease frequency)
$barLabels = array_keys($disease_counts);
$barValues = array_values($disease_counts);

// Prepare data for line chart (submissions over time)
try {
    $lineSql = "
        SELECT DATE(fs.PostingDate) AS submission_date, 
               COUNT(*) AS submission_count
        FROM tblfound_symptoms fs
        LEFT JOIN tbldeseases d ON fs.dis_id = d.id
        $whereSql
        GROUP BY DATE(fs.PostingDate)
        ORDER BY submission_date ASC
    ";
    $lineStmt = $dbh->prepare($lineSql);
    foreach ($params as $k => $v) {
        $lineStmt->bindValue($k, $v);
    }
    $lineStmt->execute();
    $lineData = $lineStmt->fetchAll(PDO::FETCH_OBJ);

    $lineLabels = [];
    $lineValues = [];
    foreach ($lineData as $row) {
        $lineLabels[] = $row->submission_date;
        $lineValues[] = (int)$row->submission_count;
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
    <title>Symptom Report Dashboard</title>
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
            text-align: left; 
        }
        th { background: #f4f4f4; }
        .pagination { justify-content: center; }
        .error-message { color: red; text-align: center; margin-bottom: 20px; }
        .download-btn { margin-left: 10px; }
        #loading { display: none; color: green; margin-left: 10px; }
        .high-disease { background-color: #ff4d4d; color: white; }
        .avg-disease { background-color: #ffd700; color: black; }
        .low-disease { background-color: #32cd32; color: black; }
        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: end;
            justify-content: center;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            margin-bottom: 5px;
            font-weight: bold;
        }
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
            <h2>Symptom Report Dashboard</h2>

            <form method="get" class="filter-form filter-row">
                <div class="form-group">
                    <label for="from_date">From:</label>
                    <input type="date" id="from_date" name="from_date" value="<?php echo htmlentities($from_date); ?>" class="form-control" />
                </div>

                <div class="form-group">
                    <label for="to_date">To:</label>
                    <input type="date" id="to_date" name="to_date" value="<?php echo htmlentities($to_date); ?>" class="form-control" />
                </div>

                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="Pending" <?php if ($status == 'Pending') echo 'selected'; ?>>Pending</option>
                        <option value="Sent" <?php if ($status == 'Sent') echo 'selected'; ?>>Sent</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="disease">Disease:</label>
                    <select id="disease" name="disease" class="form-control">
                        <option value="">All Diseases</option>
                        <?php foreach ($availableDiseases as $disease): ?>
                            <option value="<?php echo htmlentities($disease); ?>" 
                                    <?php if ($disease_filter === $disease) echo 'selected'; ?>>
                                <?php echo htmlentities($disease); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
                
                <div class="form-group">
                    <button type="button" id="download-btn" class="btn btn-danger download-btn" onclick="downloadSymptomReportPDF()">Download as PDF</button>
                    <span id="loading">Generating PDF...</span>
                </div>
            </form>

            <div class="chart-row">
                <div class="chart-col">
                    <h3>Disease Frequency (Bar)</h3>
                    <div class="chart-container">
                        <canvas id="diseaseChart"></canvas>
                    </div>
                </div>
                <div class="chart-col">
                    <h3>Status Distribution (Pie)</h3>
                    <div class="chart-container">
                        <canvas id="statusPieChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="chart-row">
                <div class="chart-col">
                    <h3>Submissions Over Time (Line)</h3>
                    <div class="chart-container">
                        <canvas id="submissionLineChart"></canvas>
                    </div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Request No</th>
                        <th>Disease</th>
                        <th>Symptoms</th>
                        <th>User Email</th>
                        <th>Status</th>
                        <th>Posting Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($rows) === 0): ?>
                        <tr><td colspan="6">No records found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <td><?php echo htmlentities($row->requestno); ?></td>
                                <td class="<?php echo $disease_colors[$row->disease_deseaseTitle] ?? 'low-disease'; ?>">
                                    <?php echo htmlentities($row->disease_deseaseTitle); ?>
                                </td>
                                <td><?php echo htmlentities($row->found_symptoms); ?></td>
                                <td><?php echo htmlentities($row->userEmail); ?></td>
                                <td><?php echo htmlentities($row->Status); ?></td>
                                <td><?php echo date('Y-m-d H:i', strtotime($row->PostingDate)); ?></td>
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
    // Bar Chart (Disease Frequency)
    const ctxBar = document.getElementById('diseaseChart').getContext('2d');
    const diseaseChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($barLabels); ?>,
            datasets: [{
                label: 'Number of Reports',
                data: <?php echo json_encode($barValues); ?>,
                backgroundColor: <?php
                    $chartColors = [];
                    foreach ($barLabels as $disease) {
                        if ($disease_counts[$disease] > $high_threshold) {
                            $chartColors[] = 'rgba(255, 77, 77, 0.7)'; // Red
                        } elseif ($disease_counts[$disease] > $low_threshold) {
                            $chartColors[] = 'rgba(255, 215, 0, 0.7)'; // Yellow
                        } else {
                            $chartColors[] = 'rgba(50, 205, 50, 0.7)'; // Green
                        }
                    }
                    echo json_encode($chartColors);
                ?>
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
                }
            }
        }
    });

    // Pie Chart (Status Distribution)
    const ctxPie = document.getElementById('statusPieChart').getContext('2d');
    const statusPieChart = new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($status_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($status_values); ?>,
                backgroundColor: [
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(255, 206, 86, 0.7)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            aspectRatio: 1.5
        }
    });

    // Line Chart (Submissions Over Time)
    const ctxLine = document.getElementById('submissionLineChart').getContext('2d');
    const submissionLineChart = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($lineLabels); ?>,
            datasets: [{
                label: 'Submissions Over Time',
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

    async function downloadSymptomReportPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'mm', 'a4');
        
        // Show loading
        document.getElementById('loading').style.display = 'inline';
        document.getElementById('download-btn').disabled = true;
        
        try {
            let yPosition = 20;
            
            // Add header
            doc.setFontSize(16);
            doc.setFont(undefined, 'bold');
            doc.text('INTELLIGENT AGRICULTURAL ASSISTANCE PLATFORM', 105, yPosition, { align: 'center' });
            yPosition += 10;
            
            doc.setFontSize(14);
            doc.text('Symptom Report', 105, yPosition, { align: 'center' });
            yPosition += 15;
            
            // Add filter information
            doc.setFontSize(10);
            doc.setFont(undefined, 'normal');
            <?php if (!empty($from_date) && !empty($to_date)): ?>
            doc.text('Period: <?php echo date('F j, Y', strtotime($from_date)) . ' to ' . date('F j, Y', strtotime($to_date)); ?>', 20, yPosition);
            yPosition += 6;
            <?php endif; ?>
            
            <?php if (!empty($status)): ?>
            doc.text('Status: <?php echo addslashes($status); ?>', 20, yPosition);
            yPosition += 6;
            <?php endif; ?>
            
            <?php if (!empty($disease_filter)): ?>
            doc.text('Disease: <?php echo addslashes($disease_filter); ?>', 20, yPosition);
            yPosition += 6;
            <?php endif; ?>
            
            doc.text('Generated on: ' + new Date().toLocaleString(), 20, yPosition);
            yPosition += 10;
            
            // Add summary statistics
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');
            doc.text('Executive Summary', 20, yPosition);
            yPosition += 8;
            
            doc.setFontSize(10);
            doc.setFont(undefined, 'normal');
            const summaryStats = [
                'Total Submissions: <?php echo $total_submissions; ?>',
                'Unique Diseases: <?php echo $unique_diseases; ?>',
                'Average Submissions per Disease: <?php echo $avg_submissions_per_disease; ?>',
                'Most Common Disease: <?php echo addslashes($most_common_disease) . ' (' . $max_disease_count . ' reports)'; ?>'
            ];
            
            summaryStats.forEach(stat => {
                doc.text('• ' + stat, 25, yPosition);
                yPosition += 5;
            });
            
            yPosition += 10;
            
            // Capture and add bar chart
            const barCanvas = document.getElementById('diseaseChart');
            const barImgData = barCanvas.toDataURL('image/png');
            doc.addImage(barImgData, 'PNG', 20, yPosition, 170, 80);
            yPosition += 90;
            
            // Add new page for pie chart
            doc.addPage();
            yPosition = 20;
            
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');
            doc.text('Status Distribution Analysis', 105, yPosition, { align: 'center' });
            yPosition += 15;
            
            // Capture and add pie chart
            const pieCanvas = document.getElementById('statusPieChart');
            const pieImgData = pieCanvas.toDataURL('image/png');
            doc.addImage(pieImgData, 'PNG', 20, yPosition, 170, 80);
            yPosition += 90;
            
            // Add disease statistics table
            doc.setFontSize(10);
            doc.setFont(undefined, 'bold');
            doc.text('Disease Report Distribution:', 20, yPosition);
            yPosition += 8;
            
            doc.setFont(undefined, 'normal');
            const diseaseData = <?php echo json_encode($disease_counts); ?>;
            Object.keys(diseaseData).forEach((disease, index) => {
                const percentage = (<?php echo $total_submissions; ?> > 0 ? (diseaseData[disease] / <?php echo $total_submissions; ?> * 100).toFixed(1) : 0);
                doc.text(`• ${disease}: ${diseaseData[disease]} reports (${percentage}%)`, 25, yPosition);
                yPosition += 5;
            });
            
            // Add detailed symptom table on new page
            doc.addPage();
            yPosition = 20;
            
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');
            doc.text('Detailed Symptom Report', 105, yPosition, { align: 'center' });
            yPosition += 15;
            
            // Table using autoTable with color coding
            doc.autoTable({
                startY: yPosition,
                head: [['#', 'Request No', 'Disease', 'Symptoms', 'User Email', 'Status', 'Posting Date']],
                body: <?php echo json_encode($pdfData); ?>.map((row, index) => [
                    (index + 1).toString(),
                    row.requestno,
                    row.disease_deseaseTitle,
                    row.found_symptoms.length > 20 ? row.found_symptoms.substring(0, 17) + '...' : row.found_symptoms,
                    row.userEmail.length > 25 ? row.userEmail.substring(0, 22) + '...' : row.userEmail,
                    row.Status,
                    new Date(row.PostingDate).toLocaleDateString()
                ]),
                theme: 'grid',
                styles: { fontSize: 8 },
                  columnStyles: {
                0: { cellWidth: 10, halign: 'center' },     // #
                1: { cellWidth: 20 },                       // Request No
                2: { cellWidth: 36 },                       // Disease
                3: { cellWidth: 46 },                       // Symptoms – widest
                4: { cellWidth: 38 },                       // User Email
                5: { cellWidth: 14, halign: 'center' },     // Status
                6: { cellWidth: 28, halign: 'center' }      // Posting Date
            
            },
            });
            
            // Add insights and recommendations on new page
            doc.addPage();
            yPosition = 20;
            
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');
            doc.text('Key Insights & Recommendations', 105, yPosition, { align: 'center' });
            yPosition += 15;
            
            doc.setFontSize(10);
            doc.setFont(undefined, 'bold');
            doc.text('Key Findings:', 20, yPosition);
            yPosition += 8;
            
            doc.setFont(undefined, 'normal');
            const insights = [
                'Certain diseases are reported more frequently, indicating higher prevalence',
                'Pending reports may indicate delays in response or verification',
                'Symptom patterns can help identify outbreaks early',
                'User engagement varies by submission frequency'
            ];
            
            insights.forEach(insight => {
                doc.text('• ' + insight, 25, yPosition);
                yPosition += 6;
            });
            
            yPosition += 10;
            doc.setFont(undefined, 'bold');
            doc.text('Recommendations:', 20, yPosition);
            yPosition += 8;
            
            doc.setFont(undefined, 'normal');
            const recommendations = [
                'Prioritize response to high-frequency diseases',
                'Enhance verification processes for pending reports',
                'Implement automated alerts for recurring symptoms',
                'Provide training to users on accurate symptom reporting',
                'Strengthen disease surveillance based on report data'
            ];
            
            recommendations.forEach(rec => {
                doc.text('• ' + rec, 25, yPosition);
                yPosition += 6;
            });
            
            // Save the PDF
            const fileName = `Symptom_Report_${new Date().toISOString().split('T')[0]}.pdf`;
            doc.save(fileName);
            
        } catch (error) {
            console.error('Error generating Symptom Report PDF:', error);
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
<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit;
}

// DB connection
$conn = new mysqli("localhost", "root", "", "faodb");
if ($conn->connect_error) {
    die("<div class='alert alert-danger'>Database Connection Failed: " . $conn->connect_error . "</div>");
}

// Date and region filters
$from_date = $_POST['from_date'] ?? '';
$to_date = $_POST['to_date'] ?? '';
$selected_region = $_POST['region'] ?? '';
$date_condition = "";
$region_condition = "";

if (!empty($from_date) && !empty($to_date)) {
    $date_condition = "AND fs.PostingDate BETWEEN '$from_date' AND '$to_date'";
}
if (!empty($selected_region)) {
    $region_condition = "AND f.region = '$selected_region'";
}

// Bar Chart Query
$query = "SELECT f.FullName, f.EmailId, f.ContactNo, f.region, COUNT(fs.fid) AS total_submissions
          FROM tblfound_symptoms fs
          INNER JOIN tblfarmers f ON fs.userEmail = f.EmailId
          WHERE 1=1 $date_condition $region_condition
          GROUP BY f.EmailId
          ORDER BY total_submissions DESC";

$result = $conn->query($query);
if (!$result) {
    die("Query Failed: " . $conn->error);
}

$labels = [];
$submission_counts = [];
$table_data = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['FullName'];
    $submission_counts[] = (int)$row['total_submissions'];

    $table_data[] = [
        'name' => $row['FullName'],
        'email' => $row['EmailId'],
        'contact' => $row['ContactNo'],
        'region' => $row['region'],
        'total' => $row['total_submissions']
    ];
}

// Pie Chart Query (by region)
$region_query = "SELECT f.region, COUNT(fs.fid) AS total_cases 
                 FROM tblfound_symptoms fs
                 INNER JOIN tblfarmers f ON fs.userEmail = f.EmailId
                 WHERE 1=1 $date_condition
                 GROUP BY f.region";

$region_result = $conn->query($region_query);
$region_labels = [];
$region_counts = [];

while ($row = $region_result->fetch_assoc()) {
    $region_labels[] = $row['region'];
    $region_counts[] = (int)$row['total_cases'];
}

$conn->close();

// Calculate summary statistics
$total_cases = array_sum($submission_counts);
$total_regions = count($region_labels);
$total_farmers = count($table_data);
$avg_cases_per_farmer = $total_farmers > 0 ? round($total_cases / $total_farmers, 2) : 0;
$most_affected_region = '';
$max_region_cases = 0;
foreach ($region_counts as $index => $count) {
    if ($count > $max_region_cases) {
        $max_region_cases = $count;
        $most_affected_region = $region_labels[$index];
    }
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
        .bg-black-custom {
            background-color: #000000;
            color: #ffffff;
        }
    </style>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>

<body>
<?php include('includes/header.php'); ?>
<div class="ts-main-content">
    <?php include('includes/leftbar.php'); ?>
    <div class="content-wrapper">
        <div class="container-fluid">
            <div id="report-section">
                <div class="row">
                    <div class="col-md-12 text-center"><br><br>
                        <div class="col-md-12">
                        <form method="POST" class="form-inline mb-3">
                            <label class="mr-2">From Date:</label>
                            <input type="date" name="from_date" class="form-control mr-2" value="<?php echo $from_date; ?>">
                            <label class="mr-2">To Date:</label>
                            <input type="date" name="to_date" class="form-control mr-2" value="<?php echo $to_date; ?>">
                            <label class="text-uppercase text-sm">Region</label>
                            <select name="region" class="form-control mb">
                                <option value="">-- Select Region --</option>
                                <?php
                                $districts = ["Gasabo", "Kicukiro", "Nyarugenge", "Bugesera", "Gatsibo", "Kayonza", "Kirehe", "Ngoma", "Nyagatare", "Rwamagana",
                                    "Burera", "Gakenke", "Gicumbi", "Musanze", "Rulindo",
                                    "Gisagara", "Huye", "Kamonyi", "Muhanga", "Nyamagabe", "Nyanza", "Nyaruguru", "Ruhango",
                                    "Karongi", "Ngororero", "Nyabihu", "Rubavu", "Rusizi", "Rutsiro"];
                                foreach ($districts as $district) {
                                    $selected = ($selected_region == $district) ? 'selected' : '';
                                    echo "<option value=\"$district\" $selected>$district</option>";
                                }
                                ?>
                            </select>
                            <button type="submit" class="btn btn-primary ml-2">Search</button>
                        </form><br><br>
                    </div>
                        <div id="report-header">
                            <img src="../logo1.jpg" alt="Logo" style="width: 200px; height:50px;">
                            <h2 class="page-title">Diseases Affected Report</h2>
                            <?php if (!empty($from_date) && !empty($to_date)): ?>
                                <p><strong>Period:</strong> <?php echo date('F j, Y', strtotime($from_date)); ?> to <?php echo date('F j, Y', strtotime($to_date)); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($selected_region)): ?>
                                <p><strong>Region:</strong> <?php echo $selected_region; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Summary Statistics Cards -->
                    <div class="col-md-12 mb-4">
                        <div class="row" id="summary-stats">
                            <div class="col-md-3">
                                <div class="card text-white" style="background-color: green;">

                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4><?php echo $total_cases; ?></h4>
                                                <p class="mb-0">Total Cases</p>
                                            </div>
                                            <div><i class="fa fa-exclamation-triangle fa-2x"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-white" style="background-color: green;">

                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4><?php echo $total_farmers; ?></h4>
                                                <p class="mb-0">Farmers Affected</p>
                                            </div>
                                            <div><i class="fa fa-users fa-2x"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-white" style="background-color: green;">

                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4><?php echo $total_regions; ?></h4>
                                                <p class="mb-0">Regions Affected</p>
                                            </div>
                                            <div><i class="fa fa-map-marker fa-2x"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                              <div class="card text-white" style="background-color: green;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4><?php echo $avg_cases_per_farmer; ?></h4>
                                                <p class="mb-0">Avg Cases/Farmer</p>
                                            </div>
                                            <div><i class="fa fa-calculator fa-2x"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12" style="width: 100%; margin: auto; height: 400px;">
                        <h4 class="text-center mb-3">Disease Cases per Farmer</h4>
                        <canvas id="barChart"></canvas>
                    </div>

                    <div class="col-md-12 mt-5">
                        <h4 class="text-center mb-3">Regional Distribution of Disease Cases</h4>
                        <canvas id="pieChart" style="width: 100%; max-height: 400px;"></canvas>
                    </div>

                    <div class="col-md-12 mt-5">
                        <h4 class="text-center mb-3">Detailed Farmer Report</h4>
                        <div id="report-table">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Farmer Name</th>
                                        <th>Email</th>
                                        <th>Contact</th>
                                        <th>Region</th>
                                        <th>Total Cases Detected</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; foreach ($table_data as $row): ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['contact']); ?></td>
                                        <td><?php echo htmlspecialchars($row['region']); ?></td>
                                        <td><span class="badge badge-<?php echo $row['total'] > 5 ? 'danger' : ($row['total'] > 2 ? 'warning' : 'success'); ?>"><?php echo $row['total']; ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Key Insights Section -->
                    <div class="col-md-12 mt-4">
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <h5><i class="fa fa-lightbulb-o"></i> Key Insights & Recommendations</h5>
                            </div>
                            <div class="card-body" id="insights-section">
                                <div class="row">
                                    <div class="col-md-6">
                                        
                                    </div>
                                    <!-- <div class="col-md-6">
                                        <h6><i class="fa fa-lightbulb-o text-warning"></i> Recommendations:</h6>
                                        <ul class="list-unstyled">
                                            <li>• Focus prevention efforts on high-risk regions</li>
                                            <li>• Provide additional support to heavily affected farmers</li>
                                            <li>• Implement early warning systems in vulnerable areas</li>
                                            <li>• Conduct training programs for disease management</li>
                                        </ul>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 text-center mt-4">
                        <button class="btn btn-success btn-lg" onclick="downloadDiseaseReportPDF()" id="download-disease-btn">
                            <i class="fa fa-download"></i> Download PDF Report
                        </button>
                        <div id="disease-loading" style="display: none; margin-top: 10px;">
                            <i class="fa fa-spinner fa-spin"></i> Generating Disease Report PDF...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let diseaseBarChart, diseasePieChart;

async function downloadDiseaseReportPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4');
    
    // Show loading
    document.getElementById('disease-loading').style.display = 'block';
    document.getElementById('download-disease-btn').disabled = true;
    
    try {
        let yPosition = 25;
        
        // ── Main Header ───────────────────────────────────────────────
        doc.setFontSize(16);
        doc.setFont("helvetica", "bold");
        doc.text('INTELLIGENT AGRICULTURAL ASSISTANCE PLATFORM', 105, yPosition, { align: 'center' });
        yPosition += 10;
        
        doc.setFontSize(14);
        doc.text('Diseases Affected Report', 105, yPosition, { align: 'center' });
        yPosition += 12;
        
        // ── Filter information ────────────────────────────────────────
        doc.setFontSize(10);
        doc.setFont("helvetica", "normal");
        
        <?php if (!empty($from_date) && !empty($to_date)): ?>
        doc.text('Period: <?php echo date('F j, Y', strtotime($from_date)) . ' to ' . date('F j, Y', strtotime($to_date)); ?>', 20, yPosition);
        yPosition += 6;
        <?php endif; ?>
        
        <?php if (!empty($selected_region)): ?>
        doc.text('Region: <?php echo $selected_region; ?>', 20, yPosition);
        yPosition += 6;
        <?php endif; ?>
        
        yPosition += 8;
        
        // ── Table Title + Generated on (upper right) ──────────────────
        doc.setFontSize(12);
        doc.setFont("helvetica", "bold");
        doc.text('Detailed Farmer Report', 105, yPosition, { align: 'center' });
        
        // Generated on – right aligned, slightly above table start
        doc.setFontSize(9);
        doc.setFont("helvetica", "normal");
        const generatedText = 'Generated on: ' + new Date().toLocaleString();
        const textWidth = doc.getTextWidth(generatedText);
        doc.text(generatedText, 190 - textWidth, yPosition - 2);
        
        yPosition += 10;
        
        // ── Table ─────────────────────────────────────────────────────
        doc.setFontSize(10);
        doc.setFont("helvetica", "bold");
        const headers = ['#', 'Farmer Name', 'Email', 'Contact', 'Region', 'Cases'];
        const colWidths = [10, 42, 50, 25, 27, 18];
        let xPosition = 20;
        
        // Light purple header background (RGB: approx #E6D9F2)
        doc.setFillColor(230, 217, 242);
        doc.rect(20, yPosition - 6, 170, 9, 'F');
        
        // Header text (centered in cells)
        headers.forEach((header, index) => {
            const cellX = xPosition + colWidths[index]/2;
            doc.text(header, cellX, yPosition + 1, { align: 'center', baseline: 'middle' });
            xPosition += colWidths[index];
        });
        
        // Bold line under header
        doc.setLineWidth(0.7);
        doc.line(20, yPosition + 3, 190, yPosition + 3);
        yPosition += 9;
        
        // Table body
        doc.setFont("helvetica", "normal");
        doc.setFontSize(9);
        doc.setLineWidth(0.25); // light grid lines
        const tableData = <?php echo json_encode($table_data); ?>;
        const tableStartY = yPosition;
        
        tableData.forEach((row, index) => {
            if (yPosition > 272) { // leave margin at bottom
                // Draw vertical lines before page break
                let vx = 20;
                doc.setLineWidth(0.25);
                colWidths.forEach(w => {
                    doc.line(vx, tableStartY - 6, vx, yPosition + 3);
                    vx += w;
                });
                doc.line(190, tableStartY - 6, 190, yPosition + 3); // right border
                
                doc.addPage();
                yPosition = 20;
                tableStartY = 20;
            }
            
            xPosition = 20;
            const rowData = [
                (index + 1).toString(),
                row.name?.length > 24 ? row.name.substring(0, 21) + '…' : (row.name || '-'),
                row.email?.length > 30 ? row.email.substring(0, 27) + '…' : (row.email || '-'),
                row.contact || '-',
                row.region || '-',
                row.total?.toString() || '0'
            ];
            
            rowData.forEach((data, colIndex) => {
                doc.text(data, xPosition + 2, yPosition + 3.5, { baseline: 'middle' });
                xPosition += colWidths[colIndex];
            });
            
            // Horizontal line after row
            doc.line(20, yPosition + 7, 190, yPosition + 7);
            yPosition += 7;
        });
        
        // Final vertical lines (full height)
        let vx = 20;
        doc.setLineWidth(0.25);
        colWidths.forEach(w => {
            doc.line(vx, tableStartY - 6, vx, yPosition);
            vx += w;
        });
        doc.line(190, tableStartY - 6, 190, yPosition);
        
        // Bottom border (slightly bolder)
        doc.setLineWidth(0.6);
        doc.line(20, yPosition, 190, yPosition);
        
        // Prepared by – bottom right
        doc.setFontSize(9);
        doc.setFont("helvetica", "italic");
        const preparedText = 'Prepared by: Kabatesi Patience';
        const preparedWidth = doc.getTextWidth(preparedText);
        doc.text(preparedText, 190 - preparedWidth, yPosition + 8);
        
        // ── Save ──────────────────────────────────────────────────────
        const fileName = `Disease_Report_${new Date().toISOString().split('T')[0]}.pdf`;
        doc.save(fileName);
        
    } catch (error) {
        console.error('Error generating Disease Report PDF:', error);
        alert('Error generating PDF. Please try again.');
    } finally {
        document.getElementById('disease-loading').style.display = 'none';
        document.getElementById('download-disease-btn').disabled = false;
    }
}

const ctxBar = document.getElementById('barChart').getContext('2d');
diseaseBarChart = new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            label: 'Disease Cases per Farmer',
            data: <?php echo json_encode($submission_counts); ?>,
            backgroundColor: 'rgba(51, 150, 29, 0.7)',
            borderColor: 'rgba(51, 150, 29, 1)',
            borderWidth: 0.5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: {
                display: true,
                text: 'Disease Cases Distribution by Farmer',
                font: { size: 14 }
            },
            legend: { display: true }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: 'Number of Cases' }
            },
            x: {
                title: { display: true, text: 'Farmers' }
            }
        }
    }
});

const ctxPie = document.getElementById('pieChart').getContext('2d');
diseasePieChart = new Chart(ctxPie, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($region_labels); ?>,
        datasets: [{
            data: <?php echo json_encode($region_counts); ?>,
            backgroundColor: [
                'rgba(63, 202, 45, 0.8)',
                'rgba(176, 226, 171, 0.8)',
                'rgba(12, 207, 90, 0.8)',
                'rgba(13, 95, 44, 0.8)',
                'rgba(56, 211, 58, 0.8)',
                'rgba(59, 182, 25, 0.8)',
                'rgba(20, 155, 52, 0.8)',
                'rgba(100, 200, 80, 0.8)',
                'rgba(40, 160, 30, 0.8)',
                'rgba(80, 180, 60, 0.8)'
            ],
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { 
                position: 'right',
                labels: { padding: 15 }
            },
            title: {
                display: true,
                text: 'Regional Distribution of Disease Cases',
                font: { size: 14 }
            }
        }
    }
});
</script>
</body>
</html>
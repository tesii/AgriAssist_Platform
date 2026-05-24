<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['wlogin']) == 0) {
    header('location:index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>INTELLIGENT AGRICULTURAL ASSISTANCE PLATFORM</title>

    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-social.css">
    <link rel="stylesheet" href="css/bootstrap-select.css">
    <link rel="stylesheet" href="css/fileinput.min.css">
    <link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
<?php include('includes/header.php'); ?>
<div class="ts-main-content">
    <?php include('includes/leftbar.php'); ?>
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="page-title">Report Analytics</h2>

                    <?php
                    $conn = new mysqli("localhost", "root", "", "faodb");
                    if ($conn->connect_error) {
                        die("<div class='alert alert-danger'>Database Connection Failed: " . $conn->connect_error . "</div>");
                    }

                    $disease_query = "SELECT MONTH(RegDate) AS month, COUNT(id) AS total_disease_symptoms FROM tbldeseases WHERE RegDate IS NOT NULL GROUP BY month ORDER BY month";
                    $disease_result = $conn->query($disease_query);

                    $disease_symptoms = [];
                    $months = [];
                    while ($row = $disease_result->fetch_assoc()) {
                        $months[] = (int)$row['month'];
                        $disease_symptoms[$row['month']] = (int)$row['total_disease_symptoms'];
                    }

                    $recommended_query = "SELECT MONTH(ToDate) AS month, COUNT(id) AS total_recommended_symptoms FROM tblrecomanded_symptoms WHERE ToDate IS NOT NULL GROUP BY month ORDER BY month";
                    $recommended_result = $conn->query($recommended_query);

                    $recommended_symptoms = [];
                    while ($row = $recommended_result->fetch_assoc()) {
                        $recommended_symptoms[$row['month']] = (int)$row['total_recommended_symptoms'];
                    }

                    $final_disease_symptoms = [];
                    $final_recommended_symptoms = [];
                    $missing_symptoms_percentage = [];

                    foreach ($months as $month) {
                        $total_disease = $disease_symptoms[$month] ?? 0;
                        $total_recommended = $recommended_symptoms[$month] ?? 0;
                        $missing_percentage = $total_disease > 0 ? ((1 - ($total_recommended / $total_disease)) * 100) : 0;

                        $final_disease_symptoms[] = $total_disease;
                        $final_recommended_symptoms[] = $total_recommended;
                        $missing_symptoms_percentage[] = round($missing_percentage, 2);
                    }

                    $conn->close();
                    ?>

                    <div style="width: 80%; margin: auto; height: 400px; position: relative;">
                        <canvas id="barChart" style="display: block; position: relative;"></canvas>
                    </div>

                    <script src="js/jquery.min.js"></script>
                    <script src="js/bootstrap.min.js"></script>
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            const ctx = document.getElementById('barChart').getContext('2d');
                            const data = {
                                labels: <?php echo json_encode(array_map('strval', $months)); ?>,
                                datasets: [
                                    {
                                        label: 'Reported Disease Symptoms',
                                        data: <?php echo json_encode($final_disease_symptoms); ?>,
                                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                                        borderColor: 'rgba(255, 99, 132, 1)',
                                        borderWidth: 1
                                    },
                                    {
                                        label: 'Recommended Symptoms',
                                        data: <?php echo json_encode($final_recommended_symptoms); ?>,
                                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                        borderColor: 'rgba(75, 192, 192, 1)',
                                        borderWidth: 1
                                    },
                                    {
                                        label: 'Missing Symptoms (%)',
                                        data: <?php echo json_encode($missing_symptoms_percentage); ?>,
                                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                        borderColor: 'rgba(54, 162, 235, 1)',
                                        borderWidth: 1
                                    }
                                ]
                            };

                            const config = {
                                type: 'bar',
                                data: data,
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    animation: false,
                                    plugins: {
                                        legend: {
                                            display: true
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: 'Count / Percentage'
                                            }
                                        },
                                        x: {
                                            title: {
                                                display: true,
                                                text: 'Months'
                                            }
                                        }
                                    }
                                }
                            };

                            new Chart(ctx, config);
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

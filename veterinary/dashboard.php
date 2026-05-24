<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Secure session check
if (!isset($_SESSION['wlogin'])) {
    header('Location: ../index.php');
    exit();
}
?>

<!doctype html>
<html lang="en" class="no-js">
<head>
    <meta charset="UTF-8">
    <title>INTELLIGENT AGRICULTURAL ASSISTANCE PLATFORM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Stylesheets -->
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
                    <h2 class="page-title">Dashboard</h2>

                    <div class="row">
                        <!-- Registered Farmers -->
                        <div class="col-md-3">
                            <div class="panel panel-default">
                                <div class="panel-body bk-primary text-light">
                                    <div class="stat-panel text-center">
                                        <?php 
                                        $sql = "SELECT id FROM tblfarmers";
                                        $query = $dbh->prepare($sql);
                                        $query->execute();
                                        $regusers = $query->rowCount();
                                        ?>
                                        <div class="stat-panel-number h1"><?php echo htmlentities($regusers); ?></div>
                                        <div class="stat-panel-title text-uppercase">Registered Farmers</div>
                                    </div>
                                </div>
                                <a href="reg-users.php" class="block-anchor panel-footer">Full Detail <i class="fa fa-arrow-right"></i></a>
                            </div>
                        </div>

                        <!-- Listed Diseases -->
                        <div class="col-md-3">
                            <div class="panel panel-default">
                                <div class="panel-body bk-success text-light">
                                    <div class="stat-panel text-center">
                                        <?php 
                                        $sql1 = "SELECT id FROM tbldeseases";
                                        $query1 = $dbh->prepare($sql1);
                                        $query1->execute();
                                        $totaldiseases = $query1->rowCount();
                                        ?>
                                        <div class="stat-panel-number h1"><?php echo htmlentities($totaldiseases); ?></div>
                                        <div class="stat-panel-title text-uppercase">Listed Diseases</div>
                                    </div>
                                </div>
                                <a href="manage-deseases.php" class="block-anchor panel-footer text-center">Full Detail <i class="fa fa-arrow-right"></i></a>
                            </div>
                        </div>

                        <!-- Contact Queries -->
                        <div class="col-md-3">
                            <div class="panel panel-default">
                                <div class="panel-body bk-danger text-light">
                                    <div class="stat-panel text-center">
                                        <?php 
                                        $sql6 = "SELECT id FROM tblcontactusquery";
                                        $query6 = $dbh->prepare($sql6);
                                        $query6->execute();
                                        $totalqueries = $query6->rowCount();
                                        ?>
                                        <div class="stat-panel-number h1"><?php echo htmlentities($totalqueries); ?></div>
                                        <div class="stat-panel-title text-uppercase">User Queries</div>
                                    </div>
                                </div>
                                <a href="manage-conactusquery.php" class="block-anchor panel-footer text-center">Full Detail <i class="fa fa-arrow-right"></i></a>
                            </div>
                        </div>

                        <!-- Recommended Symptoms -->
                        <div class="col-md-3">
                            <div class="panel panel-default">
                                <div class="panel-body bk-info text-light">
                                    <div class="stat-panel text-center">
                                        <?php 
                                        $sql2 = "SELECT id FROM tblrecomanded_symptoms";
                                        $query2 = $dbh->prepare($sql2);
                                        $query2->execute();
                                        $bookings = $query2->rowCount();
                                        ?>
                                        <div class="stat-panel-number h1"><?php echo htmlentities($bookings); ?></div>
                                        <div class="stat-panel-title text-uppercase">Recommended Symptoms</div>
                                    </div>
                                </div>
                                <a href="#" class="block-anchor panel-footer text-center">Full Detail <i class="fa fa-arrow-right"></i></a>
                            </div>
                        </div>

                    </div> <!-- row -->
                </div> <!-- col-md-12 -->
            </div> <!-- row -->

        </div> <!-- container-fluid -->
    </div> <!-- content-wrapper -->
</div> <!-- ts-main-content -->

<!-- JS Scripts -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-select.min.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>
<script src="js/Chart.min.js"></script>
<script src="js/fileinput.js"></script>
<script src="js/chartData.js"></script>
<script src="js/main.js"></script>

<script>
window.onload = function () {
    var ctx = document.getElementById("dashReport")?.getContext("2d");
    if (ctx && typeof swirlData !== 'undefined') {
        new Chart(ctx).Line(swirlData, {
            responsive: true,
            scaleShowVerticalLines: false,
            scaleBeginAtZero: true
        });
    }
};
</script>
</body>
</html>

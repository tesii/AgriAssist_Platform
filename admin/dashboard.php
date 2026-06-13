<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{	
    header('location:index.php');
}
else{
?>
<!doctype html>
<html lang="en" class="no-js">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="theme-color" content="#15964b">
	
	<title>INTELLIGENT AGRICULTURAL ASSISTANCE PLATFORM</title>

	<!-- Font awesome -->
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<!-- Sandstone Bootstrap CSS -->
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<!-- Bootstrap Datatables -->
	<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
	<!-- Bootstrap social button library -->
	<link rel="stylesheet" href="css/bootstrap-social.css">
	<!-- Bootstrap select -->
	<link rel="stylesheet" href="css/bootstrap-select.css">
	<!-- Bootstrap file input -->
	<link rel="stylesheet" href="css/fileinput.min.css">
	<!-- Awesome Bootstrap checkbox -->
	<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
	<!-- Admin Stye -->
	<link rel="stylesheet" href="css/style.css">
</head>

<body>
<?php include('includes/header.php');?>

	<div class="ts-main-content">
<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">

				<div class="row">
					<div class="col-md-12">

						<h2 class="page-title">Dashboard</h2>
						
						<div class="row">
							<div class="col-md-3">
								<div class="panel panel-default">
									<div class="panel-body bk-primary text-light">
										<div class="stat-panel text-center">
<?php 
$sql ="SELECT id from tblfarmers ";
$query = $dbh -> prepare($sql);
$query->execute();
$regusers=$query->rowCount();
?>
											<div class="stat-panel-number h1 "><?php echo htmlentities($regusers);?></div>
											<div class="stat-panel-title text-uppercase">Registered Farmers</div>
										</div>
									</div>
									<a href="reg-users.php" class="block-anchor panel-footer">Full Detail <i class="fa fa-arrow-right"></i></a>
								</div>
							</div>
							
							<div class="col-md-3">
								<div class="panel panel-default">
									<div class="panel-body bk-success text-light">
										<div class="stat-panel text-center">
<?php 
$sql1 ="SELECT id from tbldeseases ";
$query1 = $dbh -> prepare($sql1);
$query1->execute();
$totaldeseases=$query1->rowCount();
?>
											<div class="stat-panel-number h1 "><?php echo htmlentities($totaldeseases);?></div>
											<div class="stat-panel-title text-uppercase">Listed Diseases</div>
										</div>
									</div>
									<a href="manage-deseases.php" class="block-anchor panel-footer text-center">Full Detail &nbsp; <i class="fa fa-arrow-right"></i></a>
								</div>
							</div>
							
							<div class="col-md-3">
								<div class="panel panel-default">
									<div class="panel-body bk-info text-light">
										<div class="stat-panel text-center">
<?php 
$sql2 ="SELECT id from tblrecomanded_symptoms ";
$query2= $dbh -> prepare($sql2);
$query2->execute();
$bookings=$query2->rowCount();
?>
											<div class="stat-panel-number h1 "><?php echo htmlentities($bookings);?></div>
											<div class="stat-panel-title text-uppercase">Total Recommended Symptoms</div>
										</div>
									</div>
									<a href="#" class="block-anchor panel-footer text-center">Full Detail &nbsp; <i class="fa fa-arrow-right"></i></a>
								</div>
							</div>
							
							<div class="col-md-3">
								<div class="panel panel-default">
									<div class="panel-body bk-warning text-light">
										<div class="stat-panel text-center">
<?php 
$sql3 ="SELECT id from tblcategory ";
$query3= $dbh -> prepare($sql3);
$query3->execute();
$category=$query3->rowCount();
?>													
											<div class="stat-panel-number h1 "><?php echo htmlentities($category);?></div>
											<div class="stat-panel-title text-uppercase">Listed Categories</div>
										</div>
									</div>
									<a href="manage-category.php" class="block-anchor panel-footer text-center">Full Detail &nbsp; <i class="fa fa-arrow-right"></i></a>
								</div>
							</div>
						</div>

						<!-- NEW report3 panel -->
						<div class="row" style="margin-top:20px;">
							<div class="col-md-3">
								<div class="panel panel-default">
									<div class="panel-body bk-info text-light">
										<div class="stat-panel text-center">
<?php 
// Query to count medicine usage records (example for report3)
$sqlReport3 = "SELECT COUNT(*) FROM medecine_usage";
$queryReport3 = $dbh->prepare($sqlReport3);
$queryReport3->execute();
$report3Count = $queryReport3->fetchColumn();
?>
											<div class="stat-panel-number h1 "><?php echo htmlentities($report3Count); ?></div>
											<div class="stat-panel-title text-uppercase">Medicine Usage Records</div>
										</div>
									</div>
									<a href="medecine-usage.php" class="block-anchor panel-footer text-center">Full Detail &nbsp; <i class="fa fa-arrow-right"></i></a>
								</div>
							</div>
						</div>
						<!-- end new report3 panel -->

					</div>
				</div>

			</div>
		</div>
	</div>

	<!-- Loading Scripts -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/Chart.min.js"></script>
	<script src="js/fileinput.js"></script>
	<script src="js/chartData.js"></script>
	<script src="js/main.js"></script>
	
	<script>
		
	window.onload = function(){
    
		// Line chart from swirlData for dashReport
		var ctx = document.getElementById("dashReport")?.getContext("2d");
		if(ctx) {
			window.myLine = new Chart(ctx).Line(swirlData, {
				responsive: true,
				scaleShowVerticalLines: false,
				scaleBeginAtZero : true,
				multiTooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>",
			}); 
		}
		
		// Pie Chart from doughutData
		var doctx3 = document.getElementById("chart-area3")?.getContext("2d");
		if(doctx3) {
			window.myDoughnut = new Chart(doctx3).Pie(doughnutData, {responsive : true});
		}

		// Doughnut Chart from doughnutData
		var doctx4 = document.getElementById("chart-area4")?.getContext("2d");
		if(doctx4) {
			window.myDoughnut = new Chart(doctx4).Doughnut(doughnutData, {responsive : true});
		}

	}
	</script>
</body>
</html>
<?php } ?>

<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) {	
	header('location:index.php');
} else {
	if(isset($_GET['del'])) {
		$id = $_GET['del'];
		$sql = "DELETE FROM veterinary WHERE worker_id = :id";
		$query = $dbh->prepare($sql);
		$query->bindParam(':id', $id, PDO::PARAM_STR);
		$query->execute();
		$msg = "Worker deleted successfully";
	}
?>

<!doctype html>
<html lang="en" class="no-js">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>IAAP | Manage Worker</title>

	<!-- Stylesheets -->
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
	<link rel="stylesheet" href="css/style.css">

	<!-- Custom Styling -->
	<style>
		body {
			background-color: #ffffff;
			color: #000;
		}
		.page-title {
			background-color: #e0ffe0;
			color: #004d00;
			padding: 15px;
			margin-bottom: 20px;
			font-size: 24px;
			font-weight: 600;
			border-radius: 6px;
		}
		.panel-heading {
			background-color: #a8e6a3;
			color: #002d00;
			padding: 10px 15px;
			font-size: 18px;
			font-weight: bold;
			border-radius: 4px 4px 0 0;
		}
		.panel {
			border: 1px solid #b3e6b3;
			box-shadow: none;
		}
		.table > thead {
			background-color: #f4fff4;
			color: #000;
		}
		.table td, .table th {
			vertical-align: middle;
		}
		a i.fa {
			color: #006400;
			transition: color 0.3s ease;
		}
		a i.fa:hover {
			color: #00cc00;
		}
		.errorWrap, .succWrap {
			padding: 10px;
			margin-bottom: 20px;
			background: #fff;
			border-left: 4px solid;
			box-shadow: 0 1px 1px rgba(0,0,0,.1);
		}
		.errorWrap { border-color: #dd3d36; color: #a94442; }
		.succWrap { border-color: #5cb85c; color: #3c763d; }
	</style>
</head>

<body>
	<?php include('includes/header.php'); ?>

	<div class="ts-main-content">
		<?php include('includes/leftbar.php'); ?>
		<div class="content-wrapper">
			<div class="container-fluid">

				<div class="row">
					<div class="col-md-12">

						<h2 class="page-title">Manage Worker</h2>

						<!-- Worker Table -->
						<div class="panel panel-default">
							<div class="panel-heading">Listed Workers</div>
							<div class="panel-body">
								<?php if($error){ ?>
									<div class="errorWrap"><strong>ERROR</strong>: <?php echo htmlentities($error); ?> </div>
								<?php } else if($msg){ ?>
									<div class="succWrap"><strong>SUCCESS</strong>: <?php echo htmlentities($msg); ?> </div>
								<?php } ?>
								
								<table id="zctb" class="display table table-striped table-bordered" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>#</th>
											<th>First Name</th>
											<th>Last Name</th>
											<th>Phone</th>
											<th>Email</th>
											<th>Role</th>
											<th>Action</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
											<th>#</th>
											<th>First Name</th>
											<th>Last Name</th>
											<th>Phone</th>
											<th>Email</th>
											<th>Role</th>
											<th>Action</th>
										</tr>
									</tfoot>
									<tbody>
										<?php 
										$sql = "SELECT * FROM veterinary";
										$query = $dbh->prepare($sql);
										$query->execute();
										$results = $query->fetchAll(PDO::FETCH_OBJ);
										$cnt = 1;
										if($query->rowCount() > 0) {
											foreach($results as $result) {
										?>	
										<tr>
											<td><?php echo htmlentities($cnt); ?></td>
											<td><?php echo htmlentities($result->worker_first_name); ?></td>
											<td><?php echo htmlentities($result->worker_last_name); ?></td>
											<td><?php echo htmlentities($result->worker_phone); ?></td>
											<td><?php echo htmlentities($result->worker_email); ?></td>
											<td><?php echo htmlentities($result->role); ?></td>
											<td>
												<a href="manageworker.php?del=<?php echo $result->worker_id; ?>" onclick="return confirm('Do you want to delete?');">
													<i class="fa fa-close"></i>
												</a>
											</td>
										</tr>
										<?php $cnt++; }} ?>
									</tbody>
								</table>

							</div>
						</div>

					</div>
				</div>

			</div>
		</div>
	</div>

	<!-- Scripts -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/chartData.js"></script>
	<script src="js/main.js"></script>
</body>
</html>
<?php } ?>

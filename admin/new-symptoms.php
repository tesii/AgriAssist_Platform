<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) {	
	header('location:index.php');
} else {
?>

<!doctype html>
<html lang="en" class="no-js">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>IAAP | New Recommended Symptoms</title>

	<!-- Styles -->
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
	<link rel="stylesheet" href="css/style.css">

	<style>
		body {
			background: #ffffff;
			color: #000;
		}
		.page-title {
			background-color: #d9fdd3;
			color: #004d00;
			padding: 15px;
			border-radius: 8px;
			font-size: 24px;
			font-weight: bold;
			margin-bottom: 20px;
		}
		.panel-heading {
			background-color: #b3e6a5;
			color: #003300;
			font-size: 18px;
			font-weight: 600;
			padding: 10px 15px;
			border-radius: 6px 6px 0 0;
		}
		.panel {
			border: 1px solid #b2d8b2;
		}
		.table thead {
			background-color: #eefdf0;
		}
		.table td, .table th {
			vertical-align: middle;
		}
		a {
			color: #006400;
			font-weight: 500;
		}
		a:hover {
			color: #00aa00;
			text-decoration: none;
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
						<h2 class="page-title">New Recommended Symptoms</h2>

						<div class="panel panel-default">
							<div class="panel-heading">New Recommended Symptoms Info</div>
							<div class="panel-body">

								<table id="zctb" class="display table table-striped table-bordered" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Request No</th>
											<th>Recommended Symptoms</th>
											<th>User Email</th>
											<th>To Date</th>
											<th>Status</th>
											<th>Posting Date</th>
											<th>Action</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
											<th>#</th>
											<th>Request No</th>
											<th>Recommended Symptoms</th>
											<th>User Email</th>
											<th>To Date</th>
											<th>Status</th>
											<th>Posting Date</th>
											<th>Action</th>
										</tr>
									</tfoot>
									<tbody>
										<?php 
										$status = 0;
										$sql = "SELECT * FROM tblrecomanded_symptoms WHERE Status = :status";  
										$query = $dbh->prepare($sql);
										$query->bindParam(':status', $status, PDO::PARAM_STR);
										$query->execute();
										$results = $query->fetchAll(PDO::FETCH_OBJ);
										$cnt = 1;
										if ($query->rowCount() > 0) {
											foreach ($results as $result) {
										?>	
										<tr>
											<td><?php echo htmlentities($cnt); ?></td>
											<td><?php echo htmlentities($result->requestno); ?></td>
											<td><?php echo htmlentities($result->recommended_symptoms); ?></td>
											<td>
												<a href="add-desease.php?ids=<?php echo htmlentities($result->id); ?>">
													<?php echo htmlentities($result->userEmail); ?>
												</a>
											</td>
											<td><?php echo htmlentities($result->ToDate); ?></td>
											<td>
												<?php 
													if ($result->Status == 0) echo "Not Accepted yet";
													elseif ($result->Status == 1) echo "Accepted";
													else echo "Pending";
												?>
											</td>
											<td><?php echo htmlentities($result->PostingDate); ?></td>
											<td>
												<a href="add-desease.php?ids=<?php echo htmlentities($result->id); ?>">View</a>
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
	<script src="js/bootstrap.min.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/main.js"></script>

</body>
</html>
<?php } ?>

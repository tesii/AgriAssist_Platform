<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['wlogin'])==0)
{	
	header('location:index.php');
}
else{
	if(isset($_GET['del']))
	{
		$id = $_GET['del'];
		$sql = "DELETE FROM tblcategory WHERE id = :id";
		$query = $dbh->prepare($sql);
		$query->bindParam(':id', $id, PDO::PARAM_STR);
		$query->execute();
		$msg = "Category deleted successfully";
	}
?>

<!doctype html>
<html lang="en" class="no-js">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<title>IAAP | Admin - Manage Categories</title>

	<!-- CSS Dependencies -->
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-social.css">
	<link rel="stylesheet" href="css/bootstrap-select.css">
	<link rel="stylesheet" href="css/fileinput.min.css">
	<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
	<link rel="stylesheet" href="css/style.css">

	<style>
		body {
			background-color: #f5fff5;
		}
		.page-title {
			color: #006400;
			background-color: #ccffcc;
			padding: 15px;
			border-radius: 5px;
			font-weight: bold;
		}
		.panel-default > .panel-heading {
			background-color: #90ee90;
			color: #004d00;
			font-weight: bold;
			border-color: #77dd77;
		}
		.panel {
			border-color: #66bb66;
		}
		.table > thead {
			background-color: #d9fdd3;
			color: #003300;
		}
		a i.fa {
			color: #006400;
		}
		a i.fa:hover {
			color: #00cc00;
		}
		.errorWrap {
			padding: 10px;
			margin: 0 0 20px 0;
			background: #fff;
			border-left: 4px solid #dd3d36;
			box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
		}
		.succWrap {
			padding: 10px;
			margin: 0 0 20px 0;
			background: #e6ffed;
			border-left: 4px solid #2ecc71;
			box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
			color: #155724;
		}
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

						<h2 class="page-title">Manage Categories</h2>

						<div class="panel panel-default">
							<div class="panel-heading">Listed Categories</div>
							<div class="panel-body">

								<?php if($error){ ?>
									<div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div>
								<?php } else if($msg){ ?>
									<div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div>
								<?php } ?>

								<table id="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Category Name</th>
											<th>Creation Date</th>
											<th>Updation Date</th>
											<th>Action</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
											<th>#</th>
											<th>Category Name</th>
											<th>Creation Date</th>
											<th>Updation Date</th>
											<th>Action</th>
										</tr>
									</tfoot>
									<tbody>
										<?php 
										$sql = "SELECT * FROM tblcategory";
										$query = $dbh->prepare($sql);
										$query->execute();
										$results = $query->fetchAll(PDO::FETCH_OBJ);
										$cnt = 1;
										if($query->rowCount() > 0) {
											foreach($results as $result) {
										?>	
										<tr>
											<td><?php echo htmlentities($cnt); ?></td>
											<td><?php echo htmlentities($result->categoryName); ?></td>
											<td><?php echo htmlentities($result->CreationDate); ?></td>
											<td><?php echo htmlentities($result->UpdationDate); ?></td>
											<td>
												<a href="edit-category.php?id=<?php echo $result->id; ?>"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;
												<a href="manage-category.php?del=<?php echo $result->id; ?>" onclick="return confirm('Do you want to delete?');"><i class="fa fa-close"></i></a>
											</td>
										</tr>
										<?php $cnt++; } } ?>
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
	<script src="js/Chart.min.js"></script>
	<script src="js/fileinput.js"></script>
	<script src="js/chartData.js"></script>
	<script src="js/main.js"></script>

</body>
</html>
<?php } ?>

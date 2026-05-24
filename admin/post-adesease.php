<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{ 

if(isset($_POST['submit']))
  {
$deseasetitle=$_POST['deseasetitle'];
$category=$_POST['deseaseCategory'];
$deseaseoverview=$_POST['deseaseoverview'];

$symptoms = implode(", ",$_REQUEST['symptoms']);
$desease_prevention=$_POST['desease_prevention'];
$vimage1=$_FILES["img1"]["name"];
$vimage2=$_FILES["img2"]["name"];
$vimage3=$_FILES["img3"]["name"];
$vimage4=$_FILES["img4"]["name"];
$vimage5=$_FILES["img5"]["name"];



move_uploaded_file($_FILES["img1"]["tmp_name"],"img/deseaseimages/".$_FILES["img1"]["name"]);
move_uploaded_file($_FILES["img2"]["tmp_name"],"img/deseaseimages/".$_FILES["img2"]["name"]);
move_uploaded_file($_FILES["img3"]["tmp_name"],"img/deseaseimages/".$_FILES["img3"]["name"]);
move_uploaded_file($_FILES["img4"]["tmp_name"],"img/deseaseimages/".$_FILES["img4"]["name"]);
move_uploaded_file($_FILES["img5"]["tmp_name"],"img/deseaseimages/".$_FILES["img5"]["name"]);

$sql="INSERT INTO tbldeseases(deseaseTitle,deseaseCategory,deseaseOverview,Vimage1,Vimage2,Vimage3,Vimage4,Vimage5,desease_symptoms,desease_prevention) VALUES(:deseasetitle,:deseaseCategory,:deseaseoverview,:vimage1,:vimage2,:vimage3,:vimage4,:vimage5,:symptoms,:desease_prevention)";
$query = $dbh->prepare($sql);
$query->bindParam(':deseasetitle',$deseasetitle,PDO::PARAM_STR);
$query->bindParam(':deseaseCategory',$category,PDO::PARAM_STR);
$query->bindParam(':deseaseoverview',$deseaseoverview,PDO::PARAM_STR);
$query->bindParam(':desease_prevention',$desease_prevention,PDO::PARAM_STR);
$query->bindParam(':vimage1',$vimage1,PDO::PARAM_STR);
$query->bindParam(':vimage2',$vimage2,PDO::PARAM_STR);
$query->bindParam(':vimage3',$vimage3,PDO::PARAM_STR);
$query->bindParam(':vimage4',$vimage4,PDO::PARAM_STR);
$query->bindParam(':vimage5',$vimage5,PDO::PARAM_STR);
$query->bindParam(':symptoms',$symptoms,PDO::PARAM_STR);

$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
$msg="Desease posted successfully";
}
else 
{
$error="Something went wrong. Please try again";
}

}


	?>
<!doctype html>
<html lang="en" class="no-js">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="theme-color" content="#3e454c">
	<link rel="stylesheet" type="text/css" href="css/select3.min.css"> 
	
	<title>ITELLIGENT AGRICULTURAL ASSISTANCE PLATFORM</title>

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
	<!-- Inside <head>, add this new style -->
<style>
    body {
        background-color: #f6fff6;
    }
    .panel-heading {
        background-color: #4CAF50 !important;
        color: white !important;
        font-weight: bold;
    }
    .page-title {
        color: #388E3C;
        font-weight: bold;
        border-left: 5px solid #4CAF50;
        padding-left: 10px;
    }
    .form-control:focus {
        border-color: #66bb6a;
        box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
    }
    .btn-primary {
        background-color: #4CAF50;
        border-color: #4CAF50;
    }
    .btn-primary:hover {
        background-color: #43A047;
        border-color: #388E3C;
    }
    .btn-default:hover {
        background-color: #e0f2f1;
        border-color: #a5d6a7;
    }
    .succWrap {
        border-left: 4px solid #4CAF50;
    }
    .errorWrap {
        border-left: 4px solid #e53935;
    }
</style>

</head>

<body>
	<?php include('includes/header.php');?>
	<div class="ts-main-content">
	<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">

				<div class="row">
					<div class="col-md-12">
					
						<h2 class="page-title">Post A Desease</h2>

						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">Basic Info</div>
<?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
				else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>

									<div class="panel-body">
<form method="post" class="form-horizontal" enctype="multipart/form-data">
<div class="form-group">
<label class="col-sm-2 control-label">Desease Title<span style="color:red">*</span></label>
<div class="col-sm-4">
<input type="text" name="deseasetitle" class="form-control" required>
</div>
<label class="col-sm-2 control-label">Select category<span style="color:red">*</span></label>
<div class="col-sm-4">
<select class="selectpicker" name="deseaseCategory" required>
<option value=""> Select </option>
<?php $ret="select id,categoryName from tblcategory";
$query= $dbh -> prepare($ret);
//$query->bindParam(':id',$id, PDO::PARAM_STR);
$query-> execute();
$results = $query -> fetchAll(PDO::FETCH_OBJ);
if($query -> rowCount() > 0)
{
foreach($results as $result)
{
?>
<option value="<?php echo htmlentities($result->id);?>"><?php echo htmlentities($result->categoryName);?></option>
<?php }} ?>

</select>
</div>
</div>
											
<div class="hr-dashed"></div>
<div class="form-group">
<label class="col-sm-2 control-label">Desease Overview<span style="color:red">*</span></label>
<div class="col-sm-10">
<textarea class="form-control" name="deseaseoverview" rows="3" required></textarea>
</div>
</div>

<div class="hr-dashed"></div>


<div class="form-group">
<div class="col-sm-12">
<h4><b>Upload Images</b></h4>
</div>
</div>


<div class="form-group">
<div class="col-sm-4">
Image 1 <span style="color:red">*</span><input type="file" name="img1" required>
</div>
<div class="col-sm-4">
Image 2<span style="color:red">*</span><input type="file" name="img2" required>
</div>
<div class="col-sm-4">
Image 3<span style="color:red">*</span><input type="file" name="img3" required>
</div>
</div>


<div class="form-group">
<div class="col-sm-4">
Image 4<span style="color:red">*</span><input type="file" name="img4" required>
</div>
<div class="col-sm-4">
Image 5<input type="file" name="img5">
</div>

</div>
<div class="hr-dashed"></div>									
</div>
</div>
</div>
</div>
							

<div class="row">
<div class="col-md-12">
<div class="panel panel-default">
<div class="panel-heading">Symptoms</div>
<div class="panel-body">


<div class="form-group">
    <div class="col-md-6">
        <label for="symptoms">Select Symptoms Of Disease</label>
        <select class="select" id="symptoms" required onchange="updateSelectedSymptoms()">
            <option value="">-- Select Symptom --</option>
            <?php
            // Database connection
            $conn = new mysqli("localhost", "root", "", "faodb");

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch symptoms from the database
            $query = "SELECT symptom_id, symptoms FROM symptoms";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['symptom_id'] . "' data-name='" . $row['symptoms'] . "'>" . $row['symptoms'] . "</option>";
                }
            } else {
                echo "<option value=''>No Symptoms Available</option>";
            }

            // Close connection
            $conn->close();
            ?>
        </select>
    </div>

    <div class="col-md-6">
        <label for="selectedSymptoms">Selected Symptoms</label>
        <input type="text" id="selectedSymptoms" name="symptoms[]" class="space" required readonly>
    </div>
</div>

<script>
    let selectedSymptoms = [];

    function updateSelectedSymptoms() {
        let select = document.getElementById("symptoms");
        let selectedValue = select.value;
        let selectedText = select.options[select.selectedIndex].getAttribute("data-name");

        if (selectedValue && selectedText) {
            // Check if symptom is already in the array
            let exists = selectedSymptoms.some(symptom => symptom.id === selectedValue);

            if (!exists) {
                selectedSymptoms.push({ id: selectedValue, name: selectedText });
            } else {
                // Remove if already selected
                selectedSymptoms = selectedSymptoms.filter(symptom => symptom.id !== selectedValue);
            }
        }

        // Display selected symptom names in input field
        document.getElementById("selectedSymptoms").value = selectedSymptoms.map(symptom => symptom.name).join(", ");
    }
</script>

<div class="form-group">

<label class="col-sm-2 control-label">Desease Prevention and Assistance<span style="color:red">*</span></label>
<div class="col-sm-4">
<input type="text" name="desease_prevention" class="form-control" required>
</div>
</div>




</div>
<div class="form-group">
			<div class="col-sm-8 col-sm-offset-2">
		<button class="btn btn-default" type="reset">Cancel</button>
		<button class="btn btn-primary" name="submit" type="submit">Save changes</button>
												</div>
											</div>

										</form>
									</div>
								</div>
							</div>
						</div>
						
					

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
</body>
</html>
<?php } ?>
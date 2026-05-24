<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['wlogin'])==0)
	{	
header('location:index.php');
}
else{ 

if(isset($_POST['submit']))
  {
$deseasetitle=$_POST['deseasetitle'];
$category=$_POST['categoryname'];
$deseaseoverview=$_POST['deseaseoverview']; 
$prevention=$_POST['prevention'];
$symptoms = implode(", ",$_REQUEST['symptoms']);

$id=intval($_GET['id']);

$sql="update tbldeseases set deseaseTitle=:deseasetitle,deseaseCategory=:categoryname,deseaseOverview=:deseaseoverview,desease_symptoms=:symptoms,desease_prevention=:prevention where id=:id ";
$query = $dbh->prepare($sql);
$query->bindParam(':deseasetitle',$deseasetitle,PDO::PARAM_STR);
$query->bindParam(':categoryname',$category,PDO::PARAM_STR);
$query->bindParam(':deseaseoverview',$deseaseoverview,PDO::PARAM_STR);
$query->bindParam(':prevention',$prevention,PDO::PARAM_STR);
$query->bindParam(':symptoms',$symptoms,PDO::PARAM_STR);

$query->bindParam(':id',$id,PDO::PARAM_STR);
$query->execute();

$msg="Data updated successfully";


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
	<style>
		.errorWrap {
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #dd3d36;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #5cb85c;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.space{
	width:600px;
	height:50px
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
					
						<h2 class="page-title">Edit Disease</h2>

						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">Basic Info</div>
									<div class="panel-body">
<?php if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php } ?>
<?php 
$id=intval($_GET['id']);
$sql ="SELECT tbldeseases.*,tblcategory.categoryName,tblcategory.id as bid from tbldeseases join tblcategory on tblcategory.id=tbldeseases.deseaseCategory where tbldeseases.id=:id";
$query = $dbh -> prepare($sql);
$query-> bindParam(':id', $id, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{	?>

<form method="post" class="form-horizontal" enctype="multipart/form-data">
<div class="form-group">
<label class="col-sm-2 control-label">Disease Title<span style="color:red">*</span></label>
<div class="col-sm-4">
<input type="text" name="deseasetitle" class="form-control" value="<?php echo htmlentities($result->deseaseTitle)?>" required>
</div>
<label class="col-sm-2 control-label">Select Category<span style="color:red">*</span></label>
<div class="col-sm-4">
<select class="selectpicker" name="categoryname" required>
<option value="<?php echo htmlentities($result->bid);?>"><?php echo htmlentities($bdname=$result->categoryName); ?> </option>
<?php $ret="select id,categoryName from tblcategory";
$query= $dbh -> prepare($ret);
//$query->bindParam(':id',$id, PDO::PARAM_STR);
$query-> execute();
$resultss = $query -> fetchAll(PDO::FETCH_OBJ);
if($query -> rowCount() > 0)
{
foreach($resultss as $results)
{
if($results->categoryName==$bdname)
{
continue;
} else{
?>
<option value="<?php echo htmlentities($results->id);?>"><?php echo htmlentities($results->categoryName);?></option>
<?php }}} ?>

</select>
</div>
</div>
											
<div class="hr-dashed"></div>
<div class="form-group">
<label class="col-sm-2 control-label">Disease Overview<span style="color:red">*</span></label>
<div class="col-sm-10">
<textarea class="form-control" name="deseaseoverview" rows="3" required><?php echo htmlentities($result->deseaseOverview);?></textarea>
</div>
</div>

<div class="hr-dashed"></div>
<div class="form-group">
<label class="col-sm-2 control-label">Disease Prevention<span style="color:red">*</span></label>
<div class="col-sm-10">
<textarea class="form-control" name="prevention" rows="3" required><?php echo htmlentities($result->desease_prevention);?></textarea>
</div>
</div>


<div class="hr-dashed"></div>								
<div class="form-group">
<div class="col-sm-12">
<h4><b>Disease Images</b></h4>
</div>
</div>


<div class="form-group">
<div class="col-sm-4">
Image 1 <img src="../admin/img/deseaseimages/<?php echo htmlentities($result->Vimage1);?>" width="300" height="200" style="border:solid 1px #000">
<a href="changeimage1.php?imgid=<?php echo htmlentities($result->id)?>">Change Image 1</a>
</div>
<div class="col-sm-4">
Image 2<img src="../admin/img/deseaseimages/<?php echo htmlentities($result->Vimage2);?>" width="300" height="200" style="border:solid 1px #000">
<a href="changeimage2.php?imgid=<?php echo htmlentities($result->id)?>">Change Image 2</a>
</div>
<div class="col-sm-4">
Image 3<img src="../admin/img/deseaseimages/<?php echo htmlentities($result->Vimage3);?>" width="300" height="200" style="border:solid 1px #000">
<a href="changeimage3.php?imgid=<?php echo htmlentities($result->id)?>">Change Image 3</a>
</div>
</div>


<div class="form-group">
<div class="col-sm-4">
Image 4<img src="../admin/img/deseaseimages/<?php echo htmlentities($result->Vimage4);?>" width="300" height="200" style="border:solid 1px #000">
<a href="changeimage4.php?imgid=<?php echo htmlentities($result->id)?>">Change Image 4</a>
</div>
<div class="col-sm-4">
Image 5
<?php if($result->Vimage5=="")
{
echo htmlentities("File not available");
} else {?>
<img src="../admin/img/deseaseimages/<?php echo htmlentities($result->Vimage5);?>" width="300" height="200" style="border:solid 1px #000">
<a href="changeimage5.php?imgid=<?php echo htmlentities($result->id)?>">Change Image 5</a>
<?php } ?>
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
<div class="panel-heading">Desease Symptoms</div>
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
        <input type="text" id="selectedSymptoms" name="symptoms[]"  class="space" readonly>
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

<?php }} ?>



											<div class="form-group">
												<div class="col-sm-8 col-sm-offset-2" >
													
<button class="btn btn-primary" name="submit" type="submit" style="margin-top:4%">Save changes</button>
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
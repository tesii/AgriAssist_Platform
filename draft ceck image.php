
<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\PerceptualHash;

session_start();
include('includes/config.php');
require "cn/vendor/autoload.php"; // Ensure all libraries are autoloaded

error_reporting(E_ALL);
ini_set('display_errors', 1);

function sendEmail($email, $name, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
       $mail->Username="mihigojeanpele1@gmail.com";
        $mail->Password="zkzcovgbwapqjuno";
        $mail->setFrom("YOUR_GMAIL_USERNAME@gmail.com", "FAO Assistance Platform");
        $mail->addAddress($email, $name);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if (isset($_POST['submit'])) {
    $requestno = mt_rand(100000000, 999999999);
    $symptoms = implode(", ", $_POST['symptoms']);
    $email = trim($_POST['email']);
    $todate = trim($_POST['todate']);
    $status = 0;
    $name = "Farmer";

    $stmt = $dbh->prepare("SELECT deseaseTitle, desease_prevention FROM tbldeseases WHERE desease_symptoms LIKE CONCAT('%', :symptoms, '%')");
    $stmt->bindParam(':symptoms', $symptoms, PDO::PARAM_STR);
    $stmt->execute();
    $disease = $stmt->fetch(PDO::FETCH_OBJ);

    if ($disease) {
        $message = "Dear Farmer, The symptoms showed that the disease is: {$disease->deseaseTitle} and the prevention methods are: {$disease->desease_prevention}. Thank you!";
        if (sendEmail($email, $name, "FAO Diseases Assistance Response", $message)) {
            echo "<script>alert('Disease found! Email sent to farmer.'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Disease found but email could not be sent.');</script>";
        }
    } else {
        $stmt = $dbh->prepare("INSERT INTO tblrecomanded_symptoms(requestno, recommended_symptoms, userEmail, ToDate, Status) VALUES(:requestno, :symptoms, :email, :todate, :status)");
        $stmt->bindParam(':requestno', $requestno);
        $stmt->bindParam(':symptoms', $symptoms);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':todate', $todate);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        echo "<script>alert('No exact match found. Our team will analyze your symptoms and notify you via email.'); window.location.href='index.php';</script>";
    }
}

if (isset($_POST['sub'])) {
    $uploadDir = "uploads/";
    $knownDir = "worker/img/deseaseimages/";
    $email = trim($_POST['email']);
    $todate = trim($_POST['todate']);
    $requestno = mt_rand(100000000, 999999999);
    $status = 0;
    $name = "Farmer";

    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        echo "<script>alert('Photo upload failed.');</script>";
        exit;
    }

    $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $photoName = uniqid("img_", true) . '.' . $ext;
    $uploadedPath = $uploadDir . $photoName;
    move_uploaded_file($_FILES['photo']['tmp_name'], $uploadedPath);

    $hasher = new ImageHash(new PerceptualHash());
    $uploadedHash = $hasher->hash($uploadedPath);
    $lowestDiff = PHP_INT_MAX;
    $bestMatchImage = null;

    foreach (glob($knownDir . "*.{jpg,jpeg,png}", GLOB_BRACE) as $knownImagePath) {
        $knownHash = $hasher->hash($knownImagePath);
        $distance = $uploadedHash->distance($knownHash);

        if ($distance < $lowestDiff) {
            $lowestDiff = $distance;
            $bestMatchImage = basename($knownImagePath);
        }
    }

    if ($lowestDiff <= 10 && $bestMatchImage) {
        $stmt = $dbh->prepare("SELECT deseaseTitle, desease_prevention FROM tbldeseases WHERE Vimage1 = :img OR Vimage2 = :img OR Vimage3 = :img OR Vimage4 = :img OR Vimage5 = :img");
        $stmt->bindParam(':img', $bestMatchImage, PDO::PARAM_STR);
        $stmt->execute();
        $match = $stmt->fetch(PDO::FETCH_OBJ);

        if ($match) {
            $msg = "Dear Farmer,\n\nYour uploaded image matches disease: {$match->deseaseTitle}.\nPrevention: {$match->desease_prevention}.";
            if (sendEmail($email, $name, "FAO Diseases Assistance Response", $msg)) {
                echo "<script>alert('Disease matched! Email sent to farmer.');</script>";
            } else {
                echo "<script>alert('Disease matched but email failed.');</script>";
            }
        }
    } else {
        echo "<script>alert('No close match found. Submitted for review.');</script>";
    }

    echo "<script>window.location.href = 'index.php';</script>";
}
?>

<!DOCTYPE HTML>
<html lang="en"> 
<head>

<title>INTELLIGENT AGRICULTURAL ASSISTANCE PLATFORM</title>
<!--Bootstrap -->
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
<!--Custome Style -->
<link rel="stylesheet" href="assets/css/stylee.css" type="text/css">
<!--OWL Carousel slider-->
<link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
<link rel="stylesheet" href="assets/css/owl.transitions.css" type="text/css">
<!--slick-slider -->
<link href="assets/css/slick.css" rel="stylesheet">
<!--bootstrap-slider -->
<link href="assets/css/bootstrap-slider.min.css" rel="stylesheet">
<!--FontAwesome Font Style -->
<link href="assets/css/font-awesome.min.css" rel="stylesheet">

<!-- SWITCHER -->
		<link rel="stylesheet" id="switcher-css" type="text/css" href="assets/switcher/css/switcher.css" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/red.css" title="red" media="all" data-default-color="true" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/orange.css" title="orange" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/blue.css" title="blue" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/pink.css" title="pink" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/green.css" title="green" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/purple.css" title="purple" media="all" />
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/images/favicon-icon/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/images/favicon-icon/apple-touch-icon-114-precomposed.html">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/images/favicon-icon/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="assets/images/favicon-icon/apple-touch-icon-57-precomposed.png">
<link rel="shortcut icon" href="logo2.jpg">
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">
</head>
<body>
<style>
.select{
  width:230px;
  height:30px;
}
.space{
  width:230px;
  height:50px;
}
</style>
<!-- Start Switcher -->
<?php include('includes/colorswitcher.php');?>
<!-- /Switcher -->  

<!--Header-->
<?php include('includes/header.php');?>
<!-- /Header --> 

<!--Listing-Image-Slider-->

<?php 
$vhid=intval($_GET['vhid']);
$sql = "SELECT tbldeseases.*,tblcategory.categoryName,tbldeseases.id as bid  from tbldeseases join tblcategory on tblcategory.id=tbldeseases.deseaseCategory where tbldeseases.id=:vhid";
$query = $dbh -> prepare($sql);
$query->bindParam(':vhid',$vhid, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{  
$_SESSION['brndid']=$result->bid;  
?>  

<section id="listing_img_slider">
  <div><img src="admin/img/deseaseimages/<?php echo htmlentities($result->Vimage1);?>" class="img-responsive" alt="image" width="900" height="560"></div>
  <div><img src="admin/img/deseaseimages/<?php echo htmlentities($result->Vimage2);?>" class="img-responsive" alt="image" width="900" height="560"></div>
  <div><img src="admin/img/deseaseimages/<?php echo htmlentities($result->Vimage3);?>" class="img-responsive" alt="image" width="900" height="560"></div>
  <div><img src="admin/img/deseaseimages/<?php echo htmlentities($result->Vimage4);?>" class="img-responsive"  alt="image" width="900" height="560"></div>
  <?php if($result->Vimage5=="")
{

} else {
  ?>
  <div><img src="admin/img/deseaseimages/<?php echo htmlentities($result->Vimage5);?>" class="img-responsive" alt="image" width="900" height="560"></div>
  <?php } ?>
</section>
<!--/Listing-Image-Slider-->
<!--Listing-detail-->
<section class="listing-detail">
  <div class="container">
    <div class="listing_detail_head row">
      <div class="col-md-9">
        <h2><?php echo htmlentities($result->categoryName);?> , <?php echo htmlentities($result->deseaseTitle);?></h2>
      </div>
      <div class="col-md-3">
        <div class="price_info">   
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-9">
        <div class="main_features">
          <ul>
          
          
          </ul>
        </div>
        <div class="listing_more_info">
          <div class="listing_detail_wrap"> 
            <!-- Nav tabs -->
            <ul class="nav nav-tabs gray-bg" role="tablist">
              <li role="presentation" class="active"><a href="#vehicle-overview " aria-controls="desease-overview" role="tab" data-toggle="tab">Desease Overview </a></li>
          
              <li role="presentation"><a href="#accessories" aria-controls="accessories" role="tab" data-toggle="tab">Desease Symptoms</a></li>
             
            </ul>
            
            <!-- Tab panes -->
            <div class="tab-content"> 
              <!-- vehicle-overview -->
              <div role="tabpanel" class="tab-pane active" id="desease-overview">
                
                <p><?php echo htmlentities($result->deseaseOverview);?></p>
              </div>
              
              
              <!-- Accessories -->
              <div role="tabpanel" class="tab-pane" id="accessories"> 
                <!--Accessories-->
                <table>
                  <thead>
                    <tr>
                      <th colspan="2">Symptoms of Desease</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>
           <p><?php echo htmlentities($result->desease_symptoms);?></p>   
         </td> 
                </tr>
                  </tbody>
                </table>
              <!-- Accessories -->
           
                <table>
                  <thead>
                    <tr>
                      <th colspan="2">Desease Prevention and Assistance</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>
           <p><?php echo htmlentities($result->desease_prevention);?></p>   
         </td> 
                </tr>

                  </tbody>
                </table>  
              </div>
            </div>
          </div>
          
        </div>
<?php }} ?>
   
      </div>
      
      <!--Side-Bar-->
      <aside class="col-md-3">
      
        <div class="share_vehicle">
          <p>Share: <a href="#"><i class="fa fa-facebook-square" aria-hidden="true"></i></a> <a href="#"><i class="fa fa-twitter-square" aria-hidden="true"></i></a> <a href="#"><i class="fa fa-linkedin-square" aria-hidden="true"></i></a> <a href="#"><i class="fa fa-google-plus-square" aria-hidden="true"></i></a> </p>
        </div>
        <div class="sidebar_widget">
          <div class="widget_heading">
            <h5><i class="fa fa-envelope" aria-hidden="true"></i>Diseases Checking By symptoms</h5>
          </div>
          <form method="post">
             
<div class="form-group">
   
        <label for="symptoms">Select Symptoms Of Disease</label>
        <select class="select"  id="symptoms" required onchange="updateSelectedSymptoms()">
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
    </div><br>
    <div class="form-group">

        <label for="selectedSymptoms">Selected Symptoms</label>
        <input type="text" id="selectedSymptoms" name="symptoms[]" class="space" required readonly>
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
              <label>Your Email:</label>
              <input type="email" class="form-control" name="email" placeholder="Enter your Email" required> 
            </div>
            <div class="form-group">
              <label>To Date:</label>
              <input type="date" class="form-control" name="todate" placeholder="To Date" required>
            </div>

               <div class="form-group">
                <input type="submit" class="btn"  name="submit" value="Check Now">
              </div>
             
          </form>
        </div>


        <div class="sidebar_widget">
          <div class="widget_heading">
            <h5><i class="fa fa-envelope" aria-hidden="true"></i>Diseases Checking By Photo</h5>
          </div>
          <form method="post" enctype="multipart/form-data">
             
<div class="form-group">
   
        <label for="symptoms">Upload photo </label>
       <input type="file" id="photo" name="photo" class="space" required >
    </div><br>
   
             <div class="form-group">
              <label>Your Email:</label>
              <input type="email" class="form-control" name="email" placeholder="Enter your Email" required> 
            </div>
            <div class="form-group">
              <label>To Date:</label>
              <input type="date" class="form-control" name="todate" placeholder="To Date" required>
            </div>

               <div class="form-group">
                <input type="submit" class="btn"  name="sub" value="Check Now">
              </div>
             
          </form>
        </div>


      </aside>
      <!--/Side-Bar--> 
    </div>
    
    <div class="space-20"></div>
    <div class="divider"></div>
    
    <!--Similar-Cars-->
    <div class="similar_cars">
      <h3>Similar Diseases</h3>
      <div class="row">
<?php 
$bid=$_SESSION['brndid'];
$sql="SELECT tbldeseases.deseaseTitle,tblcategory.categoryName,tbldeseases.id,tbldeseases.desease_prevention,tbldeseases.deseaseOverview,tbldeseases.Vimage1 from tbldeseases join tblcategory on tblcategory.id=tbldeseases.deseaseCategory where tbldeseases.deseaseCategory=:bid";
$query = $dbh -> prepare($sql);
$query->bindParam(':bid',$bid, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{ ?>      
        <div class="col-md-3 grid_listing">
          <div class="product-listing-m gray-bg">
            <div class="product-listing-img"> <a href="desease-details.php?vhid=<?php echo htmlentities($result->id);?>"><img src="admin/img/deseaseimages/<?php echo htmlentities($result->Vimage1);?>" class="img-responsive" alt="image" /> </a>
            </div>
            <div class="product-listing-content">
              <h5><a href="desease-details.php?vhid=<?php echo htmlentities($result->id);?>"><?php echo htmlentities($result->categoryName);?> , <?php echo htmlentities($result->deseaseTitle);?></a></h5>
              <ul class="features_list">
              </ul>
            </div>
          </div>
        </div>
 <?php }} ?>       

      </div>
    </div>
    <!--/Similar-Cars--> 
    
  </div>
</section>
<!--/Listing-detail--> 

<!--Footer -->
<?php include('includes/footer.php');?>
<!-- /Footer--> 

<!--Back to top-->
<div id="back-top" class="back-top"> <a href="#top"><i class="fa fa-angle-up" aria-hidden="true"></i> </a> </div>
<!--/Back to top--> 

<!--Login-Form -->
<?php include('includes/login.php');?>
<!--/Login-Form --> 

<!--Register-Form -->
<?php include('includes/registration.php');?>

<!--/Register-Form --> 

<!--Forgot-password-Form -->
<?php include('includes/forgotpassword.php');?>

<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script> 
<script src="assets/js/interface.js"></script> 
<script src="assets/switcher/js/switcher.js"></script>
<script src="assets/js/bootstrap-slider.min.js"></script> 
<script src="assets/js/slick.min.js"></script> 
<script src="assets/js/owl.carousel.min.js"></script>

</body>
</html>
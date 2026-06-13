<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require "../cn/vendor/autoload.php";

if (strlen($_SESSION['wlogin']) == 0) {    
    header('location:index.php');
    exit();
}

// Email function
function sendEmail($email, $name, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->Username = "menyachannel@gmail.com";
        $mail->Password = "lrscbasxxmvqlxuq";
        $mail->setFrom("menyachannel@gmail.com", "FAO Assistance Platform");
        $mail->addAddress($email, $name);
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Failed to send email: " . $e->getMessage();
    }
}

// Handle withdraw request via AJAX
if (isset($_POST['action']) && $_POST['action'] == "withdraw") {
    $medecine_id = intval($_POST['medecine_id']);
    $quantity_to_withdraw = intval($_POST['quantity']);
    $fid = intval($_POST['fid']);
    $user = $_SESSION['wlogin'];  // Logged-in user

    // Get current quantity
    $stmt = $dbh->prepare("SELECT quantity FROM faostock WHERE medecine_id = :id");
    $stmt->bindParam(':id', $medecine_id, PDO::PARAM_INT);
    $stmt->execute();
    $stock = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($stock && $stock['quantity'] >= $quantity_to_withdraw && $quantity_to_withdraw > 0) {
        // Update stock quantity
        $new_qty = $stock['quantity'] - $quantity_to_withdraw;
        $update = $dbh->prepare("UPDATE faostock SET quantity = :qty WHERE medecine_id = :id");
        $update->bindParam(':qty', $new_qty, PDO::PARAM_INT);
        $update->bindParam(':id', $medecine_id, PDO::PARAM_INT);
        $update->execute();

        // Insert usage record with quantity withdrawn, timestamp and user
        $insert = $dbh->prepare("
            INSERT INTO medecine_usage (medecine_id, fid, status, quantity_withdrawn, withdrawn_at, withdrawn_by)
            VALUES (:mid, :fid, :status, :qty, NOW(), :user)
        ");
        $status = "treated";
        $insert->bindParam(':mid', $medecine_id, PDO::PARAM_INT);
        $insert->bindParam(':fid', $fid, PDO::PARAM_INT);
        $insert->bindParam(':status', $status, PDO::PARAM_STR);
        $insert->bindParam(':qty', $quantity_to_withdraw, PDO::PARAM_INT);
        $insert->bindParam(':user', $user, PDO::PARAM_STR);

        if ($insert->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Medicine withdrawn successfully. Quantity withdrawn: $quantity_to_withdraw",
                "remaining" => $new_qty
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Failed to record medicine usage."
            ]);
        }
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Not enough stock or invalid quantity."
        ]);
    }
    exit;
}

// Handle confirmation form
if (isset($_POST['submit'])) {
    $deseasetitle   = $_POST['deseasetitle'];
    $deseaseoverview= $_POST['deseaseoverview']; 
    // $prevention     = $_POST['prevention'];
    $treatment      = $_POST['treatment'];
    $visit_date     = $_POST['date'];
    $id = intval($_GET['id']);

    $stmt = $dbh->prepare("SELECT userEmail, status FROM tblfound_symptoms WHERE fid=:fid");
    $stmt->bindParam(':fid', $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_OBJ);

    if ($result) {
        if ($result->status == 0) {
            $email = $result->userEmail;
            $name = "Farmer";
            $subject = "FAO Agricultural Assistance: Disease Confirmation";
            $body = "Muraho neza,\n\nTwemeje ko indwara yagaragaye ari: $deseasetitle.\n\n📝 Ibisobanuro:\n- $deseaseoverview\n- Ubuvuzi: $treatment\n\n📅 Umunsi tuzagusura: $visit_date\n\nMurakoze.";

            $emailResult = sendEmail($email, $name, $subject, $body);
            if ($emailResult === true) {
                $update_stmt = $dbh->prepare("UPDATE tblfound_symptoms SET status=1 WHERE fid=:fid");
                $update_stmt->bindParam(':fid', $id, PDO::PARAM_INT);
                $update_stmt->execute();
                $_SESSION['msg'] = "Data updated and email sent successfully to $email.";
            } else {
                $_SESSION['msg'] = "Error: $emailResult";
            }
        } else {
            $_SESSION['msg'] = "Email not sent: Record already confirmed.";
        }
    } else {
        $_SESSION['msg'] = "No matching farmer found.";
    }
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit();
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>INTELLIGENT AGRICULTURAL ASSISTANCE PLATFORM</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<style>
    .succWrap { padding:10px; margin:0 0 20px 0; background:#fff; border-left:4px solid #5cb85c; }
    .errorWrap { padding:10px; margin:0 0 20px 0; background:#fff; border-left:4px solid #dd3d36; }
</style>
</head>
<body>
<?php include('includes/header.php'); ?>
<div class="ts-main-content">
<?php include('includes/leftbar.php'); ?>
<div class="content-wrapper">
<div class="container-fluid">

<?php if (isset($_SESSION['msg'])) { ?>
<div class="<?php echo (strpos($_SESSION['msg'], 'Error') !== false) ? 'errorWrap' : 'succWrap'; ?>">
    <?php echo htmlentities($_SESSION['msg']); ?>
</div>
<?php unset($_SESSION['msg']); } ?>

<!-- Disease confirmation form -->
<?php
$id = intval($_GET['id']);
$sql = "SELECT * FROM tbldeseases 
INNER JOIN tblcategory ON tblcategory.id = tbldeseases.deseaseCategory 
INNER JOIN tblfound_symptoms ON tblfound_symptoms.dis_id = tbldeseases.id 
WHERE tblfound_symptoms.fid = :id";
$query = $dbh->prepare($sql);
$query->bindParam(':id', $id, PDO::PARAM_INT);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
if ($query->rowCount() > 0) {
foreach ($results as $result) { ?>
<form method="post" class="form-horizontal"><br><br>
<div class="form-group">
    <label class="col-sm-2 control-label">Disease Name</label>
    <div class="col-sm-4"><input type="text" name="deseasetitle" class="form-control" value="<?php echo htmlentities($result->deseaseTitle); ?>" readonly></div>
    <label class="col-sm-2 control-label">Select Category</label>
    <div class="col-sm-4"><input type="text" class="form-control" value="<?php echo htmlentities($result->categoryName); ?>" readonly></div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">Disease Overview</label>
    <div class="col-sm-10"><textarea class="form-control" name="deseaseoverview" rows="3" readonly><?php echo htmlentities($result->deseaseOverview); ?></textarea></div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Treatment</label>
    <div class="col-sm-10"><textarea class="form-control" name="treatment" rows="3" required><?php echo htmlentities($result->treatment); ?></textarea></div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">Schedule Visit:  
        <a href="#" data-toggle="modal" data-target="#checkStockModal">Check Stock</a>
    </label>
    <div class="col-sm-10"><input type="date" class="form-control" name="date" required></div>
</div>
<div class="form-group"><div class="col-sm-8 col-sm-offset-2">
    <button class="btn btn-primary" name="submit" type="submit">Confirm</button>
</div></div>
</form>
<?php } } ?>

</div></div></div>

<!-- Check Stock Modal -->
<div class="modal fade" id="checkStockModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header"><h4>Vaccine Stock</h4></div>
      <div class="modal-body">
        <table id="stockTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Medicine</th>
                    <th>Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $stocks = $dbh->query("SELECT * FROM faostock")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($stocks as $stock) {
                echo "<tr>
                    <td>{$stock['medecine_id']}</td>
                    <td>{$stock['medecine']}</td>
                    <td>{$stock['quantity']}</td>
                    <td><button class='btn btn-info btn-sm withdrawBtn' data-id='{$stock['medecine_id']}' data-name='{$stock['medecine']}'>View</button></td>
                </tr>";
            }
            ?>
            </tbody>
        </table>
        <div id="withdrawForm" style="display:none;margin-top:20px;">
            <h5 id="medName"></h5>
            <input type="number" id="withdrawQty" class="form-control" placeholder="Enter quantity to withdraw" min="1">
            <button id="confirmWithdraw" class="btn btn-success" style="margin-top:10px;">Withdraw</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function(){
    $('#stockTable').DataTable();

    let selectedMedId = null;
    let fid = <?php echo json_encode($id); ?>;

    // Delegated event handler for dynamically generated rows/pages
    $(document).on('click', '.withdrawBtn', function(){
        selectedMedId = $(this).data('id');
        $('#medName').text("Withdraw: " + $(this).data('name'));
        $('#withdrawForm').show();
    });

    $('#confirmWithdraw').click(function(){
        let qty = $('#withdrawQty').val();
        if(qty <= 0) {
            alert('Please enter a valid quantity greater than zero.');
            return;
        }
        $.post('', {action: "withdraw", medecine_id: selectedMedId, quantity: qty, fid: fid}, function(response){
            let res = JSON.parse(response);
            alert(res.message);
            if(res.success){
                location.reload();
            }
        });
    });
});
</script>
</body>
</html>

<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (!isset($_SESSION['flogin']) || strlen($_SESSION['flogin']) == 0) {    
    header('location:index.php');
    exit; 
}

$current = $_SESSION['flogin']; // Ensure session is set before using it

if (isset($_POST['send'])) {
    $name = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);
    $response = ""; // Default empty response  

    if (!empty($name) && !empty($email) && !empty($message)) {
        $sql = "INSERT INTO support (id,name,email,message,response) VALUES (:id,:name,:email,:message,:response)";
        $query = $dbh->prepare($sql);   
        $query->bindParam(':id', $current, PDO::PARAM_STR);
        $query->bindParam(':name', $name, PDO::PARAM_STR);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':message', $message, PDO::PARAM_STR);
        $query->bindParam(':response', $response, PDO::PARAM_STR);

        if ($query->execute()) {
            $msg = "Query Sent. We will respond to you shortly.";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    } else {
        $error = "All fields are required!";
    }
}
?>
<!doctype html>
<html lang="en" class="no-js">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>INTELLIGENT AGRICULTURAL ASSISTANCE PLATFORM</title>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-social.css">
    <link rel="stylesheet" href="css/bootstrap-select.css">
    <link rel="stylesheet" href="css/fileinput.min.css">
    <link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        .errorWrap {
            padding: 10px;
            margin: 0 0 20px;
            background: #fff;
            border-left: 4px solid #dd3d36;
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
        .succWrap {
            padding: 10px;
            margin: 0 0 20px;
            background: #fff;
            border-left: 4px solid #5cb85c;
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
    </style>
</head>

<body>
    <?php include('includes/header.php'); ?>
    <div class="ts-main-content">
        <?php include('includes/leftbar.php'); ?>
        <div class="content-wrapper">
            <div class="container-fluid">
                <!-- Contact Us Section -->
                <section class="contact_us section-padding">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <h3>Support Form</h3>
                                <?php if (isset($error)) { ?>
                                    <div class="errorWrap"><strong>ERROR:</strong> <?php echo htmlentities($error); ?></div>
                                <?php } else if (isset($msg)) { ?>
                                    <div class="succWrap"><strong>SUCCESS:</strong> <?php echo htmlentities($msg); ?></div>
                                <?php } ?>
                                
                                <div class="contact_form gray-bg">
                                    <form method="post">
                                        <div class="form-group">
                                            <label>Full Name <span>*</span></label>
                                            <input type="text" name="fullname" class="form-control white_bg" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Email Address <span>*</span></label>
                                            <input type="email" name="email" class="form-control white_bg" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Message <span>*</span></label>
                                            <textarea class="form-control white_bg" name="message" rows="4" required></textarea>
                                        </div>
                                        <div class="form-group">
                                            <button class="btn btn-primary" type="submit" name="send">Send Message 
                                                <span class="angle_arrow"><i class="fa fa-angle-right" aria-hidden="true"></i></span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>  
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
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

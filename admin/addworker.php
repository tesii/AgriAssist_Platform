<?php
session_start();
error_reporting(0);
include('connect.php');

if (!isset($_SESSION['alogin'])) { 
    header('location:index.php');
    exit();
}

$error = "";
$msg = "";

if (isset($_POST['submit'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Hash password
    $role = $_POST['role'];

    // Check if email already exists
    $check_email = $conn->prepare("SELECT worker_email FROM veterinary WHERE worker_email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    if ($check_email->num_rows > 0) {
        $error = "Email already exists! Please use another email.";
    } else {
        // Insert into database
        $sql = "INSERT INTO extension_worker (worker_first_name, worker_last_name, worker_phone, worker_email, worker_password, role) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $first_name, $last_name, $phone, $email, $password, $role);

        if ($stmt->execute()) {
            $msg = "Registration successful! You can now login.";
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>Intelligent Agricultural Assistance Platform</title>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        .errorWrap, .succWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
        .errorWrap { border-left: 4px solid #dd3d36; color: red; }
        .succWrap { border-left: 4px solid #5cb85c; color: green; }
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
                        <h2 class="page-title">Add Veterinary</h2>
                        <div class="row">
                            <div class="col-md-10">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Add Veterinary</div>
                                    <div class="panel-body">
                                        <form method="post" class="form-horizontal">

                                            <?php if ($error) { ?>
                                                <div class="errorWrap"><strong>ERROR:</strong> <?php echo htmlentities($error); ?> </div>
                                            <?php } elseif ($msg) { ?>
                                                <div class="succWrap"><strong>SUCCESS:</strong> <?php echo htmlentities($msg); ?> </div>
                                            <?php } ?>

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">First Name</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="first_name" required>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Last Name</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="last_name" required>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Phone</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="phone" required>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Worker Email</label>
                                                <div class="col-sm-8">
                                                    <input type="email" class="form-control" name="email" required>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Worker Password</label>
                                                <div class="col-sm-8">
                                                    <input type="password" class="form-control" name="password" required>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Role</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control" name="role" required>
                                                        <option value="Veterinary">Veterinary</option>  
                                                        <option value="Researcher">Researcher</option>  
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-sm-8 col-sm-offset-4">
                                                    <button class="btn btn-primary" name="submit" type="submit">Submit</button>
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

    <!-- Scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</body>
</html>

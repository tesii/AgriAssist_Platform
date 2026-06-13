<?php
session_start(); 
include('connect.php');

if (isset($_POST['login'])) { 
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check Extension Worker
    $sql1 = "SELECT * FROM veterinary WHERE worker_email = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("s", $email);
    $stmt1->execute();
    $result1 = $stmt1->get_result();   

    // Check Farmer
    $sql2 = "SELECT * FROM tblfarmers WHERE EmailId = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s", $email);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    if ($result1->num_rows > 0) {
        $fetch1 = $result1->fetch_assoc();
        if ($password == $fetch1['worker_password']) {
            $_SESSION['wlogin'] = $fetch1['worker_id'];     
            header('Location: veterinary/dashboard.php'); 
            exit();  
        }
    } 
    else if ($result2->num_rows > 0) {
        $fetch2 = $result2->fetch_assoc();
        if (password_verify($password, $fetch2['Password'])) {    
            $_SESSION['flogin'] = $fetch2['id'];
            header('Location: farmer/dashboard.php');
            exit();
        }
    } else {
        $error = "Imeyili cyangwa ijambo ry’ibanga si byo.";
    }
}
?>

<!doctype html>
<html lang="rw" class="no-js">
<head>
    <meta charset="UTF-8">
    <title>Injira ku Rubuga</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="admin/css/bootstrap.min.css">
    <style>
        body {
            background-image: url("assets/images/b1.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .form-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        h1 {
            text-align: center;
            color: #2E7D32;
        }
        .form-group label {
            font-weight: 600;
        }
        .btn-primary {
            background: #4CAF50;
            border: none;
        }
        .btn-primary:hover {
            background: #388E3C;
        }
        .error-message {
            background: #FFEBEE;
            color: #C62828;
            border: 1px solid #EF9A9A;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h1>Injira ku Rubuga</h1>

    <?php if (!empty($error)): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label>Imeyili Yawe</label>
            <input type="email" name="email" class="form-control" required placeholder="Andika Imeyili">
        </div>
        <div class="form-group">
            <label>Ijambo ry'ibanga</label>
            <input type="password" name="password" class="form-control" required placeholder="Andika Ijambo ry'ibanga">
        </div>
        <button class="btn btn-primary btn-block" name="login" type="submit">Injira</button>
    </form>
</div>
</body>
</html>

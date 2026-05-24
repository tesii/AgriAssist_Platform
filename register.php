<?php
include('includes/config.php');

$msg = "";  // To store success message
$error = ""; // To store error message

if (isset($_POST['register'])) {
    // Get and sanitize input values
    $FullName  = filter_input(INPUT_POST, 'FullName', FILTER_SANITIZE_STRING);
    $EmailId   = filter_input(INPUT_POST, 'EmailId', FILTER_VALIDATE_EMAIL);
    $Password  = filter_input(INPUT_POST, 'Password', FILTER_SANITIZE_STRING);
    $ContactNo = filter_input(INPUT_POST, 'ContactNo', FILTER_SANITIZE_STRING);
    $region   = filter_input(INPUT_POST, 'region', FILTER_SANITIZE_STRING);

    // Check if email is valid
    if (!$EmailId) {
        $error = "Invalid email format.";
    } else {
        try {
            // Secure password hashing
            $hashedPassword = password_hash($Password, PASSWORD_DEFAULT);

            // SQL Query
            $sql = "INSERT INTO tblfarmers (FullName, EmailId, Password, ContactNo, region) 
                    VALUES (:FullName, :EmailId, :Password, :ContactNo, :region)";
            
            // Prepare statement
            $query = $dbh->prepare($sql);
            $query->bindParam(':FullName', $FullName, PDO::PARAM_STR);
            $query->bindParam(':EmailId', $EmailId, PDO::PARAM_STR);
            $query->bindParam(':Password', $hashedPassword, PDO::PARAM_STR);
            $query->bindParam(':ContactNo', $ContactNo, PDO::PARAM_STR);
            $query->bindParam(':region', $region, PDO::PARAM_STR);

            // Execute Query
            $query->execute();
            $lastInsertId = $dbh->lastInsertId();

            if ($lastInsertId) {
                $msg = "Account Created Successfully.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>Intelligent Agricultural Assistance Platform</title>
    
    <link rel="stylesheet" href="admin/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #59d35dff 0%, #eaf5ebff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .form-container {
            max-width: 550px;
            width: 100%;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            padding: 40px;
            margin: 0 auto;
            animation: slideIn 0.6s ease-out;
            position: relative;
            overflow: hidden;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        h1 {
            color: #2E7D32;
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
            font-size: 30px;
            letter-spacing: 1px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-control {
            border: 1px solid #d0d0d0;
            border-radius: 10px;
            padding: 14px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f9fafb;
        }
        .form-control:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 10px rgba(76, 175, 80, 0.3);
            background: #fff;
            outline: none;
        }
        label {
            color: #1A3C34;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .btn-primary {
            background: #4CAF50;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 1px;
            transition: background 0.3s ease, transform 0.2s ease;
            margin-top: 20px;
        }
        .btn-primary:hover {
            background: #388E3C;
            transform: translateY(-2px);
        }
        .btn-primary:active {
            transform: translateY(0);
        }
        .alert {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }
        .alert-success {
            background: #E8F5E9;
            color: #2E7D32;
            border: 1px solid #C8E6C9;
        }
        .alert-danger {
            background: #FFEBEE;
            color: #C62828;
            border: 1px solid #EF9A9A;
        }
        .text-center a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: color 0.3s ease;
        }
        .text-center a:hover {
            color: #2E7D32;
        }
        select.form-control {
            appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="%234CAF50" viewBox="0 0 16 16"><path d="M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 14px;
        }
        @media (max-width: 576px) {
            .form-container {
                padding: 25px;
            }
            h1 {
                font-size: 26px;
            }
            .form-control {
                font-size: 14px;
                padding: 12px;
            }
            .btn-primary {
                font-size: 14px;
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Farmer Registration</h1>
        
        <!-- Display Success or Error Messages -->
        <?php if ($msg) { ?>
            <div class="alert alert-success"><?php echo $msg; ?></div>
        <?php } elseif ($error) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>
        
        <form method="post">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" placeholder="Enter Full Name" name="FullName" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" placeholder="Enter Email" name="EmailId" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" placeholder="Enter Password" name="Password" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" placeholder="Enter Contact Number" name="ContactNo" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Region</label>
                <select name="region" class="form-control" required>
                    <option value="">-- Select Region --</option>
                    <option value="Gasabo">Gasabo</option>
                    <option value="Kicukiro">Kicukiro</option>
                    <option value="Nyarugenge">Nyarugenge</option>
                    <option value="Bugesera">Bugesera</option>
                    <option value="Gatsibo">Gatsibo</option>
                    <option value="Kayonza">Kayonza</option>
                    <option value="Kirehe">Kirehe</option>
                    <option value="Ngoma">Ngoma</option>
                    <option value="Nyagatare">Nyagatare</option>
                    <option value="Rwamagana">Rwamagana</option>
                    <option value="Burera">Burera</option>
                    <option value="Gakenke">Gakenke</option>
                    <option value="Gicumbi">Gicumbi</option>
                    <option value="Musanze">Musanze</option>
                    <option value="Rulindo">Rulindo</option>
                    <option value="Gisagara">Gisagara</option>
                    <option value="Huye">Huye</option>
                    <option value="Kamonyi">Kamonyi</option>
                    <option value="Muhanga">Muhanga</option>
                    <option value="Nyamagabe">Nyamagabe</option>
                    <option value="Nyanza">Nyanza</option>
                    <option value="Nyaruguru">Nyaruguru</option>
                    <option value="Ruhango">Ruhango</option>
                    <option value="Karongi">Karongi</option>
                    <option value="Ngororero">Ngororero</option>
                    <option value="Nyabihu">Nyabihu</option>
                    <option value="Rubavu">Rubavu</option>
                    <option value="Rusizi">Rusizi</option>
                    <option value="Rutsiro">Rutsiro</option>
                </select>
            </div>

            <button class="btn btn-primary btn-block" name="register" type="submit">Register</button>
        </form>

        <p class="text-center" style="margin-top: 20px;">
            <a href="index.php">Back to Home</a>
        </p>
    </div>

    <!-- Scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
<?php
session_start();
include 'config.php';

if (!isset($_SESSION['account_id']) || $_SESSION['account_type_id'] != 2) {
    header('Location: login.php');
    exit();
}

$modal_message = "";
$modal_type = "";

// Handle Add User
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $department_name = $_POST['department'];
    $fn = $_POST['fn'];
    $ln = $_POST['ln'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $account_type = $_POST['account_type'];

    // Check if username exists
    $check_query = "SELECT * FROM account WHERE un = ?";
    $stmt_check = $conn->prepare($check_query);
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $modal_message = "Username already exists! Choose another.";
        $modal_type = "danger";
    } else {
        // Get department_id
        $dept_query = "SELECT department_id FROM department WHERE name = ?";
        $stmt_dept = $conn->prepare($dept_query);
        $stmt_dept->bind_param("s", $department_name);
        $stmt_dept->execute();
        $result = $stmt_dept->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $department_id = $row['department_id'];
        } else {
            die("Error: Department does not exist!");
        }

        // Insert user into account table
        $insert_query = "INSERT INTO account (un, pw, account_type_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ssi", $username, $password, $account_type);

        if ($stmt->execute()) {
            $account_id = $stmt->insert_id;

            // Insert teacher details if user is a teacher
            if ($account_type == 1) {
                $teacher_query = "INSERT INTO teacher (department_id, fn, ln, email, contact_number, account_id) 
                                  VALUES (?, ?, ?, ?, ?, ?)";
                $stmt_teacher = $conn->prepare($teacher_query);
                $stmt_teacher->bind_param("issssi", $department_id, $fn, $ln, $email, $contact_number, $account_id);
                $stmt_teacher->execute();
            }

            $modal_message = "User added successfully!";
            $modal_type = "success";
        } else {
            $modal_message = "Error adding user!";
            $modal_type = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <style>
        .container {
            max-width: 650px; /* Wider form width */
            padding: 20px;
        }
        .form-control, .btn {
            font-size: 14px; /* Balanced input & button text */
            padding: 10px;
        }
        .button-container {
            display: flex;
            justify-content: space-between;
            gap: 10px; /* Add space between buttons */
        }
        .btn {
            width: 40%; /* Reduce button width */
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="container card p-4 shadow">
        <h4 class="text-center">Add User</h4>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Department</label>
                <select name="department" class="form-control" required>
                    <option value="" disabled selected>Select Department</option>
                    <option value="Junior High">Junior High</option>
                    <option value="Senior High">Senior High</option>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="fn" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="ln" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="text" name="contact_number" class="form-control" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Account Type</label>
                <select name="account_type" class="form-control" required>
                    <option value="1">Teacher</option>
                    <option value="2">Admin</option>
                </select>
            </div>
            <div class="button-container">
                <a href="user_table.php" class="btn btn-secondary">Back</a>
                <button type="submit" name="add_user" class="btn btn-success">Add User</button>
            </div>
        </form>
    </div>

    <!-- Modal Notification -->
    <?php if (!empty($modal_message)) { ?>
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog role="document">
            <div class="modal-content">
                <div class="modal-header bg-<?= $modal_type; ?> text-white">
                    <h5 class="modal-title">Notification</h5>
                    <button type="button" class="btn-close" onclick="window.location='user_table.php';"></button>
                </div>
                <div class="modal-body">
                    <p><?= $modal_message; ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="window.location='user_table.php';">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</body>
</html>

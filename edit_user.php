<?php
session_start();
include 'config.php';

if (!isset($_SESSION['account_id']) || $_SESSION['account_type_id'] != 2) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: user_table.php");
    exit();
}

$user_id = $_GET['id'];
$query = "SELECT account.*, teacher.fn, teacher.ln, teacher.email, teacher.contact_number, department.name AS department 
          FROM account 
          LEFT JOIN teacher ON account.account_id = teacher.account_id 
          LEFT JOIN department ON teacher.department_id = department.department_id 
          WHERE account.account_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header("Location: user_table.php");
    exit();
}

// Fetch all available departments for the dropdown
$dept_query = "SELECT department_id, name FROM department";
$departments = $conn->query($dept_query);

// Handle Edit User
$modal_message = "";
$modal_type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $department = $_POST['department'];
    $fn = $_POST['fn'];
    $ln = $_POST['ln'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $username = $_POST['username'];
    $account_type = $_POST['account_type'];
    $password = $_POST['password'];

    // Fetch department ID
    $dept_query = "SELECT department_id FROM department WHERE name = ?";
    $stmt_dept = $conn->prepare($dept_query);
    $stmt_dept->bind_param("s", $department);
    $stmt_dept->execute();
    $result_dept = $stmt_dept->get_result();

    if ($result_dept->num_rows > 0) {
        $dept_row = $result_dept->fetch_assoc();
        $department_id = $dept_row['department_id'];
    } else {
        die("Error: Department does not exist!");
    }

    // Update account details (with optional password change)
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_account = "UPDATE account SET un = ?, pw = ?, account_type_id = ? WHERE account_id = ?";
        $stmt = $conn->prepare($update_account);
        $stmt->bind_param("ssii", $username, $hashed_password, $account_type, $user_id);
    } else {
        $update_account = "UPDATE account SET un = ?, account_type_id = ? WHERE account_id = ?";
        $stmt = $conn->prepare($update_account);
        $stmt->bind_param("sii", $username, $account_type, $user_id);
    }
    $stmt->execute();

    // Update teacher details if user is a teacher
    if ($account_type == 1) {
        $update_teacher = "UPDATE teacher SET department_id = ?, fn = ?, ln = ?, email = ?, contact_number = ? WHERE account_id = ?";
        $stmt_teacher = $conn->prepare($update_teacher);
        $stmt_teacher->bind_param("issssi", $department_id, $fn, $ln, $email, $contact_number, $user_id);
        $stmt_teacher->execute();
    }

    $modal_message = "User details updated successfully!";
    $modal_type = "success";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <style>
        .container { max-width: 600px; }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="container card p-4 shadow">
        <h2 class="text-center mb-4">Edit User</h2>
        <?php if (!empty($modal_message)) { ?>
            <div class="alert alert-<?= $modal_type; ?>"><?= $modal_message; ?></div>
        <?php } ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Department</label>
                <select name="department" class="form-control" required>
                    <?php while ($dept = $departments->fetch_assoc()) { ?>
                        <option value="<?= $dept['name'] ?>" <?= ($user['department'] == $dept['name']) ? 'selected' : '' ?>>
                            <?= $dept['name'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="fn" value="<?= $user['fn'] ?>" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="ln" value="<?= $user['ln'] ?>" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="<?= $user['email'] ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="text" name="contact_number" value="<?= $user['contact_number'] ?>" class="form-control" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" value="<?= $user['un'] ?>" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">New Password (Leave blank to keep current)</label>
                    <input type="password" name="password" class="form-control">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Account Type</label>
                <select name="account_type" class="form-control" required>
                    <option value="1" <?= ($user['account_type_id'] == 1) ? 'selected' : '' ?>>Teacher</option>
                    <option value="2" <?= ($user['account_type_id'] == 2) ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <div class="d-flex justify-content-between">
                <a href="user_table.php" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-success">Save Changes</button>
            </div>
        </form>
    </div>
</body>
</html>

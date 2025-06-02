<?php
include 'config.php';

$modal_message = "";
$modal_type = "";

// Store form values to retain them if signup fails
$department = $fn = $ln = $email = $contact_number = $username = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $department = $_POST['department'];
    $fn = $_POST['fn'];
    $ln = $_POST['ln'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $account_type = 1; // Default to Teacher

    // ✅ Check if username already exists
    $check_username = "SELECT * FROM account WHERE un = ?";
    $stmt = $conn->prepare($check_username);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $modal_message = "Username already exists! Please choose another.";
        $modal_type = "danger";
    } else {
        // ✅ Check if department exists
        $check_department = "SELECT department_id FROM department WHERE name=?";
        $stmt = $conn->prepare($check_department);
        $stmt->bind_param("s", $department);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $department_id = $row['department_id'];
        } else {
            // ✅ Insert new department if not found
            $insert_department = "INSERT INTO department (name) VALUES (?)";
            $stmt = $conn->prepare($insert_department);
            $stmt->bind_param("s", $department);
            $stmt->execute();
            $department_id = $stmt->insert_id;
        }

        // ✅ Insert into account table
        $insert_account = "INSERT INTO account (un, pw, account_type_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_account);
        $stmt->bind_param("ssi", $username, $password, $account_type);
        
        if ($stmt->execute()) {
            $account_id = $stmt->insert_id;

            // ✅ Insert into teacher table
            $insert_teacher = "INSERT INTO teacher (department_id, fn, ln, email, contact_number, account_id) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_teacher);
            $stmt->bind_param("issssi", $department_id, $fn, $ln, $email, $contact_number, $account_id);

            if ($stmt->execute()) {
                $modal_message = "Teacher account created successfully!";
                $modal_type = "success";
                echo "<script>
                        setTimeout(function() {
                            window.location.href = 'login.php';
                        }, 2000);
                      </script>";
            } else {
                $modal_message = "Error saving teacher details!";
                $modal_type = "danger";
            }
        } else {
            $modal_message = "Error creating account!";
            $modal_type = "danger";
        }
    }
}

// ✅ Fetch departments dynamically
$dept_query = "SELECT name FROM department";
$dept_result = $conn->query($dept_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <style>
        .container {
            max-width: 600px; /* ✅ Increased width for better appearance */
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="container card p-4 shadow">
        <h2 class="text-center">Sign Up</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Department</label>
                <select name="department" class="form-control" required>
                    <option value="" disabled>Select Department</option>
                    <?php while ($dept = $dept_result->fetch_assoc()) { ?>
                        <option value="<?= $dept['name'] ?>" <?= ($department == $dept['name']) ? "selected" : ""; ?>>
                            <?= $dept['name'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="fn" class="form-control" placeholder="First Name" value="<?= htmlspecialchars($fn); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="ln" class="form-control" placeholder="Last Name" value="<?= htmlspecialchars($ln); ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Email" value="<?= htmlspecialchars($email); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="text" name="contact_number" class="form-control" placeholder="Contact Number" value="<?= htmlspecialchars($contact_number); ?>" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Username" value="<?= htmlspecialchars($username); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <a href="login.php" class="btn btn-secondary" style="width: 48%;">Login</a>
                <button type="submit" class="btn btn-success" style="width: 48%;">Sign Up</button>
            </div>
        </form>
    </div>

    <!-- Modal Notification -->
    <?php if (!empty($modal_message)) { ?>
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-<?= $modal_type; ?> text-white">
                    <h5 class="modal-title">Notification</h5>
                </div>
                <div class="modal-body">
                    <p><?= $modal_message; ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</body>
<script>
    function closeModal() {
        document.querySelector(".modal").style.display = "none";
        document.body.style.overflow = "auto";
    }
</script>
</html>

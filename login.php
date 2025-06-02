<?php
session_start();
include 'config.php';

$modal_message = "";
$modal_type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $un = $_POST['username'];
    $pw = $_POST['password'];

    // Fetch user details
    $query = "SELECT * FROM account WHERE un = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $un);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify password for ALL users (including admin)
        if (password_verify($pw, $row['pw'])) {
            $_SESSION['account_id'] = $row['account_id'];
            $_SESSION['account_type_id'] = $row['account_type_id'];

            $modal_message = "Login successful! Redirecting...";
            $modal_type = "success";
            $redirect = ($row['account_type_id'] == 2) ? "admin_dashboard.php" : "dashboard.php";
        } else {
            $modal_message = "Invalid password!";
            $modal_type = "danger";
            $redirect = "login.php";
        }
    } else {
        $modal_message = "Invalid username!";
        $modal_type = "danger";
        $redirect = "login.php";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
   
</head>

<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 w-25" style="min-height: 400px;">
        <h2 class="text-center">Login</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
            
            <!-- Sign-Up Link -->
             <br>
            <div class="text-center mt-3">
                <a href="signup.php">Don't have an account? Sign up</a>
            </div>
        </form>
    </div>

    <!-- Modal Notification -->
    <?php if (!empty($modal_message)) { ?>
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-<?= $modal_type; ?> text-white">
                    <h5 class="modal-title">Notification</h5>
                </div>
                <div class="modal-body">
                    <p><?= $modal_message; ?></p>
                </div>
                <div class="modal-footer">
                    <?php if ($modal_type == "success") { ?>
                        <script>
                            setTimeout(function () {
                                window.location.href = "<?= $redirect ?>";
                            }, 2000);
                        </script>
                    <?php } else { ?>
                        <button type="button" class="btn btn-secondary" onclick="window.location='<?= $redirect ?>';">Close</button>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</body>
</html>

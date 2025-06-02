<?php
session_start();
include 'config.php';

if (!isset($_SESSION['account_id']) || $_SESSION['account_type_id'] != 2) {
    header('Location: login.php');
    exit();
}

// ✅ Handle search functionality
$search_query = "";
if (isset($_GET['search']) && !empty(trim($_GET['search']))) { 
    $search = trim($_GET['search']);
    $search_query = "WHERE CONCAT(teacher.fn, ' ', teacher.ln) LIKE '%$search%' OR account.un LIKE '%$search%'";
}

// ✅ Fetch all users (Including teachers with department info)
$query = "SELECT account.*, 
                 CONCAT(teacher.fn, ' ', teacher.ln) AS full_name, 
                 teacher.email, teacher.contact_number, department.name AS department 
          FROM account 
          LEFT JOIN teacher ON account.account_id = teacher.account_id 
          LEFT JOIN department ON teacher.department_id = department.department_id 
          $search_query";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        /* Sidebar Styles */
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar a {
            padding: 12px 15px;
            text-decoration: none;
            font-size: 16px;
            color: white;
            display: block;
            transition: 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #007bff;
            color: white;
        }
        .content {
            margin-left: 260px;
            padding: 20px;
        }
        /* Table Styles */
        .table thead {
            background-color: #007bff;
            color: white;
        }
        .table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .table tbody tr:hover {
            background-color: #e9ecef;
        }
        /* Button Styles */
        .btn-action {
            padding: 5px 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="user_table.php" class="active">User Management</a>
    <a href="return_report.php">Return Report</a>
    <a href="logout.php" class="text-danger">Logout</a>
</div>

<!-- Main Content -->
<div class="content">
    <h2 class="mb-4">User Management</h2>

    <!-- ✅ Search Bar -->
    <form method="GET" class="mb-3 d-flex">
        <div class="input-group" style="max-width: 350px;">
            <input type="text" name="search" class="form-control" placeholder="Search users..." value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>" id="searchInput">
            <button type="submit" class="btn btn-primary">🔍</button>
        </div>
    </form>

    <!-- ✅ Add User Button -->
    <a href="add_user.php" class="btn btn-success mb-3">➕ Add User</a>

    <!-- ✅ User Table -->
    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Department</th>
                <th>Username</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['account_id'] ?></td>
                    <td><?= $row['full_name'] ?: 'N/A' ?></td>
                    <td><?= $row['email'] ?: 'N/A' ?></td>
                    <td><?= $row['contact_number'] ?: 'N/A' ?></td>
                    <td><?= $row['department'] ?: 'N/A' ?></td>
                    <td><?= $row['un'] ?></td>
                    <td><?= $row['account_type_id'] == 2 ? 'Admin' : 'Teacher' ?></td>
                    <td>
                        <a href="edit_user.php?id=<?= $row['account_id'] ?>" class="btn btn-warning btn-action">✏ Edit</a>
                        <button class="btn btn-danger btn-action" onclick="confirmDelete(<?= $row['account_id'] ?>)">🗑 Delete</button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- ✅ Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this user?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a id="confirmDeleteBtn" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(userId) {
        let deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        document.getElementById('confirmDeleteBtn').setAttribute('href', 'user_table.php?delete_id=' + userId);
        deleteModal.show();
    }

    document.getElementById("searchInput").addEventListener("input", function () {
        if (this.value.trim() === "") {
            window.location.href = "user_table.php"; // ✅ Auto refresh if search is cleared
        }
    });
</script>

</body>
</html>

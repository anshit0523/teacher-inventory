<?php
session_start();
include 'config.php';

if (!isset($_SESSION['account_id']) || $_SESSION['account_type_id'] != 2) {
    header('Location: login.php');
}

// Fetch total issued and returned per teacher
$total_query = "SELECT teacher.fn, teacher.ln, 
                SUM(teacher_detail.qty) AS total_issued, 
                SUM(teacher_detail.return) AS total_returned 
                FROM teacher_detail 
                JOIN teacher ON teacher_detail.teacher_id = teacher.teacher_id 
                GROUP BY teacher.teacher_id";
$total_result = $conn->query($total_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Return Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico">

    <style>
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
            padding: 10px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .content {
            margin-left: 260px;
            padding: 20px;
        }
    </style>
    
</head>
<body>

<div class="sidebar">
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="user_table.php">User Table</a>
    <a href="return_report.php">Return Report</a> 
    <a href="logout.php" class="text-danger">Logout</a>
</div>

    <div class="container mt-5">
        <h2>Total Items & Returned</h2>
        <table class="table table-bordered">
        <thead class="table-primary">
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Total Items </th>
                    <th>Total Items Returned</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $total_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row['fn'] ?></td>
                        <td><?= $row['ln'] ?></td>
                        <td><?= $row['total_issued'] ?></td>
                        <td><?= $row['total_returned'] ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

       
    </div>
</body>
</html>

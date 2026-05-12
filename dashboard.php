<?php
include 'config.php';
session_start();

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

// ===== STATISTIK =====
$total_student = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM student"))['total'];

$total_course = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM course"))['total'];

$total_enrollment = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM Enrollment"))['total'];

$avg_gpa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT ROUND(AVG(grade),2) as avg FROM Enrollment"))['avg'];

// ===== DATA CHART =====
$dept_query = mysqli_query($conn, "
SELECT d.department_name, COUNT(s.student_id) as total
FROM department d
LEFT JOIN student s ON d.department_id = s.department_id
GROUP BY d.department_id
");

$dept_name = [];
$dept_total = [];

while($row = mysqli_fetch_assoc($dept_query)){
    $dept_name[] = $row['department_name'];
    $dept_total[] = $row['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Campus</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: Arial;
            background: #f4f6f9;
            margin: 0;
        }

        .header {
            background: #2c3e50;
            color: white;
            padding: 15px;
            text-align: center;
        }

        .container {
            padding: 20px;
        }

        .cards {
            display: flex;
            gap: 15px;
        }

        .card {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ddd;
            text-align: center;
        }

        .card h2 {
            margin: 0;
            color: #2c3e50;
        }

        .card p {
            font-size: 20px;
            margin-top: 10px;
        }

        .chart-box {
            margin-top: 30px;
            background: white;
            padding: 20px;
            border-radius: 10px;
        }

        .logout {
            float: right;
            color: white;
            text-decoration: none;
        }
    </style>
</head>

<body>

<div class="header">
    <h2>🎓 Campus Dashboard</h2>
    <a class="logout" href="logout.php">Logout</a>
</div>

<div class="container">

    <!-- CARDS -->
    <div class="cards">

        <div class="card">
            <h2>Students</h2>
            <p><?= $total_student ?></p>
        </div>

        <div class="card">
            <h2>Courses</h2>
            <p><?= $total_course ?></p>
        </div>

        <div class="card">
            <h2>Enrollment</h2>
            <p><?= $total_enrollment ?></p>
        </div>

        <div class="card">
            <h2>Avg GPA</h2>
            <p><?= $avg_gpa ?></p>
        </div>

    </div>

    <!-- CHART -->
    <div class="chart-box">
        <h3>Student per Department</h3>
        <canvas id="deptChart"></canvas>
    </div>

</div>

<script>
const ctx = document.getElementById('deptChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($dept_name) ?>,
        datasets: [{
            label: 'Students',
            data: <?= json_encode($dept_total) ?>,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true
    }
});
</script>

</body>
</html>
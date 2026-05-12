<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

function safe_count($conn, $query){
    $res = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($res);
    return isset($row['total']) ? $row['total'] : 0;
}

$total_student = safe_count($conn, "SELECT COUNT(*) as total FROM student");
$total_course = safe_count($conn, "SELECT COUNT(*) as total FROM course");
$total_enrollment = safe_count($conn, "SELECT COUNT(*) as total FROM Enrollment");

$avg_res = mysqli_query($conn, "SELECT ROUND(AVG(grade),2) as avg FROM Enrollment");
$avg_row = mysqli_fetch_assoc($avg_res);
$avg_gpa = isset($avg_row['avg']) ? $avg_row['avg'] : 0;

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
<title>Campus Elite Dashboard</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background: radial-gradient(circle at top, #0a0a0a, #000);
    color:#f5f5f5;
    overflow-x:hidden;
}

/* SOFT GLOW BACKGROUND */
body::before{
    content:"";
    position:fixed;
    width:600px;
    height:600px;
    background:radial-gradient(circle, rgba(212,175,55,0.08), transparent);
    top:-200px;
    left:-200px;
    filter:blur(80px);
    z-index:-1;
}

/* HEADER */
.header{
    background:rgba(0,0,0,0.9);
    padding:20px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-bottom:1px solid rgba(212,175,55,0.3);
    backdrop-filter:blur(10px);
}

.header h2{
    color:#d4af37;
    letter-spacing:1px;
}

/* LOGOUT BUTTON */
.logout{
    background:linear-gradient(135deg,#d4af37,#b8860b);
    padding:8px 16px;
    border-radius:10px;
    text-decoration:none;
    color:black;
    font-weight:bold;
    transition:0.3s;
}

.logout:hover{
    transform:translateY(-2px);
    box-shadow:0 0 20px rgba(212,175,55,0.6);
}

/* CONTAINER */
.container{
    padding:30px;
}

/* CARDS GRID */
.cards{
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap:20px;
}

/* CARD */
.card{
    background:rgba(255,255,255,0.04);
    border:1px solid rgba(212,175,55,0.2);
    border-radius:18px;
    padding:20px;
    text-align:center;
    transition:0.35s;
    position:relative;
    overflow:hidden;
}

.card::before{
    content:"";
    position:absolute;
    width:200%;
    height:200%;
    background:radial-gradient(circle, rgba(212,175,55,0.08), transparent);
    top:-50%;
    left:-50%;
    opacity:0;
    transition:0.4s;
}

.card:hover::before{
    opacity:1;
}

.card:hover{
    transform:translateY(-6px) scale(1.02);
    box-shadow:0 0 35px rgba(212,175,55,0.25);
}

.card h2{
    color:#d4af37;
    margin:0;
}

.card p{
    font-size:30px;
    margin-top:10px;
    font-weight:bold;
}

/* MENU NAV */
.menu{
    margin-top:30px;
    display:flex;
    flex-wrap:wrap;
    gap:12px;
}

.menu a{
    flex:1;
    min-width:180px;
    text-align:center;
    padding:14px;
    border-radius:12px;
    border:1px solid rgba(212,175,55,0.25);
    color:#d4af37;
    text-decoration:none;
    transition:0.3s;
    background:rgba(255,255,255,0.03);
}

.menu a:hover{
    background:#d4af37;
    color:black;
    transform:translateY(-3px);
}

/* CHART */
.chart-box{
    margin-top:30px;
    padding:20px;
    border-radius:18px;
    background:rgba(255,255,255,0.03);
    border:1px solid rgba(212,175,55,0.2);
}

/* TITLE */
.chart-box h3{
    color:#d4af37;
}
</style>

</head>

<body>

<div class="header">
    <h2>🏛 CAMPUS ELITE SYSTEM</h2>
    <a class="logout" href="logout.php">LOGOUT</a>
</div>

<div class="container">

<!-- CARDS -->
<div class="card">
    <h2>👨‍🎓 Students</h2>
    <p><?= $total_student ?></p>
</div>

<div class="card">
    <h2>📚 Courses</h2>
    <p><?= $total_course ?></p>
</div>

<div class="card">
    <h2>🧾 Enrollment</h2>
    <p><?= $total_enrollment ?></p>
</div>

<div class="card">
    <h2>📊 Avg GPA</h2>
    <p><?= $avg_gpa ?></p>
</div>

<!-- MENU -->
<div class="menu">
    <a href="students.php">👨‍🎓 Students</a>
    <a href="enrollment.php">📚 Enrollment</a>
    <a href="schedule.php">🗓 Schedule</a>
    <a href="top_students.php">🏆 Top Students</a>
    <a href="add_student.php">➕ Add Student</a>
    <a href="lecturers.php">👨‍🏫 Lecturers</a>
	<a href="course_lookup.php">🔍 Course Lookup</a>
</div>

<!-- CHART -->
<div class="chart-box">
    <h3 style="color:#d4af37;">📊 Student Distribution</h3>
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
            borderColor: '#d4af37',
            backgroundColor: 'rgba(212,175,55,0.3)',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: { ticks: { color: '#fff' } },
            y: { ticks: { color: '#fff' } }
        }
    }
});
</script>

</body>
</html>

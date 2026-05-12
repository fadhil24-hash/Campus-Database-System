<?php 
include 'config.php';

/* ================= DELETE ================= */
if(isset($_GET['delete'])){
    $id = $_GET['delete'];

    $check = mysqli_query($conn, "SELECT * FROM course WHERE lecturer_id='$id'");

    if(mysqli_num_rows($check) > 0){
        echo "<script>alert('Cannot delete! Lecturer still assigned to course'); window.location='lecturers.php';</script>";
    } else {
        mysqli_query($conn, "DELETE FROM lecturer WHERE lecturer_id='$id'");
        echo "<script>alert('Lecturer deleted'); window.location='lecturers.php';</script>";
    }
}

/* ================= ADD ================= */
if(isset($_POST['add'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $spec = $_POST['spec'];
    $dept = $_POST['dept'];

    mysqli_query($conn,"
    INSERT INTO lecturer (lecturer_name, email, specialization, department_id)
    VALUES ('$name','$email','$spec','$dept')
    ");

    echo "<script>alert('Lecturer Added'); window.location='lecturers.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Lecturer System</title>

<style>
body{
    margin:0;
    background: radial-gradient(circle at top, #1a1a1a, #000);
    font-family:'Segoe UI', sans-serif;
    color:white;
}

/* HEADER */
.header{
    padding:20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-bottom:1px solid #d4af37;
}

.header h2{ color:#d4af37; }

/* BUTTON */
.btn{
    background:#d4af37;
    color:black;
    padding:8px 14px;
    border-radius:8px;
    text-decoration:none;
    font-weight:bold;
    cursor:pointer;
}

/* CONTAINER */
.container{ padding:25px; }

/* GRID */
.grid{
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap:20px;
}

/* CARD */
.card{
    position:relative;
    background: rgba(255,255,255,0.05);
    border:1px solid rgba(212,175,55,0.3);
    border-radius:16px;
    padding:15px;
    transition:0.3s;
}

.card:hover{
    transform: translateY(-6px);
    box-shadow:0 0 25px rgba(212,175,55,0.2);
}

/* DELETE */
.delete-btn{
    position:absolute;
    top:10px;
    right:10px;
    color:red;
    text-decoration:none;
}

/* PHOTO */
.photo{
    height:140px;
    background:#111;
    border-radius:10px;
    margin-bottom:10px;
    display:flex;
    align-items:center;
    justify-content:center;
}

/* TEXT */
.card h3{ color:#d4af37; margin:0; }
.info{ font-size:13px; color:#bbb; }
.course{ font-size:13px; margin-top:5px; }

/* ADD FORM */
.add-box{
    margin-bottom:25px;
    padding:20px;
    border-radius:12px;
    background: rgba(255,255,255,0.05);
    border:1px solid rgba(212,175,55,0.3);
}

.add-box input, .add-box select{
    width:100%;
    padding:10px;
    margin-top:8px;
    border-radius:8px;
    border:none;
    background:#222;
    color:white;
}

.add-box button{
    margin-top:12px;
    width:100%;
}
</style>

</head>

<body>

<div class="header">
    <h2>👨‍🏫 Lecturer Directory</h2>
    <a class="btn" href="index.php">⬅ Back</a>
</div>

<div class="container">

<!-- ================= ADD LECTURER ================= -->
<div class="add-box">
<h3 style="color:#d4af37;">➕ Add Lecturer</h3>

<form method="POST">

<input type="text" name="name" placeholder="Lecturer Name" required>
<input type="email" name="email" placeholder="Email" required>
<input type="text" name="spec" placeholder="Specialization">

<select name="dept" required>
<option value="">Select Department</option>
<?php
$dq = mysqli_query($conn,"SELECT * FROM department");
while($d=mysqli_fetch_assoc($dq)){
    echo "<option value='{$d['department_id']}'>{$d['department_name']}</option>";
}
?>
</select>

<button class="btn" name="add">Add Lecturer</button>

</form>
</div>

<!-- ================= GRID ================= -->
<div class="grid">

<?php
$query = "
SELECT l.*, d.department_name, c.course_name
FROM lecturer l
LEFT JOIN department d ON l.department_id = d.department_id
LEFT JOIN course c ON l.lecturer_id = c.lecturer_id
ORDER BY l.lecturer_name
";

$result = mysqli_query($conn, $query);

$lecturers = [];

while($row = mysqli_fetch_assoc($result)){
    $lid = $row['lecturer_id'];

    if(!isset($lecturers[$lid])){
        $lecturers[$lid] = [
            "name"=>$row['lecturer_name'],
            "email"=>$row['email'],
            "dept"=>$row['department_name'],
            "courses"=>[]
        ];
    }

    if($row['course_name']){
        $lecturers[$lid]['courses'][] = $row['course_name'];
    }
}

foreach($lecturers as $id=>$lec){
?>

<div class="card">

<a class="delete-btn" href="?delete=<?= $id ?>" 
onclick="return confirm('Delete this lecturer?')">🗑</a>

<a href="lecturer_detail.php?id=<?= $id ?>" style="text-decoration:none; color:white;">

<div class="photo">📷</div>

<h3><?= $lec['name'] ?></h3>
<div class="info">📧 <?= $lec['email'] ?></div>
<div class="info">🏢 <?= $lec['dept'] ?></div>

<b style="color:#d4af37;">Courses:</b>

<?php
if(count($lec['courses'])>0){
    foreach($lec['courses'] as $c){
        echo "<div class='course'>• $c</div>";
    }
}else{
    echo "<div class='course'>No course</div>";
}
?>

</a>
</div>

<?php } ?>

</div>
</div>

</body>
</html>

<link rel="stylesheet" href="style.css">
<?php include 'config.php'; ?>

<?php
/* ================= DELETE ================= */
if(isset($_GET['delete'])){
    $eid = $_GET['delete'];
    $sid = $_GET['id'];

    mysqli_query($conn,"DELETE FROM enrollment WHERE enrollment_id='$eid'");
    header("Location: enrollment.php?id=$sid");
    exit();
}

/* ================= UPDATE ================= */
if(isset($_POST['update'])){
    $eid = $_POST['enrollment_id'];
    $grade = $_POST['grade'];
    $semester = $_POST['semester'];

    mysqli_query($conn,"
    UPDATE enrollment SET
        grade='$grade',
        semester='$semester'
    WHERE enrollment_id='$eid'
    ");
}

/* ================= ADD ================= */
if(isset($_POST['add'])){
    $course = $_POST['course_id'];
    $semester = $_POST['semester'];
    $student = $_POST['student_id'];

    $check = mysqli_query($conn,"
    SELECT * FROM enrollment 
    WHERE student_id='$student' AND course_id='$course'
    ");

    if(mysqli_num_rows($check)==0){
        mysqli_query($conn,"
        INSERT INTO enrollment(student_id,course_id,semester,grade)
        VALUES('$student','$course','$semester',NULL)
        ");
    }
}
?>

<style>
body{
    margin:0;
    background:#000;
    color:white;
    font-family:'Segoe UI';
}

/* HEADER */
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:20px;
    border-bottom:1px solid rgba(212,175,55,0.3);
}

.header h2{ color:#d4af37; }

.back-btn{
    background:linear-gradient(135deg,#d4af37,#b8860b);
    color:black;
    padding:8px 14px;
    border-radius:8px;
    text-decoration:none;
}

/* CONTAINER */
.container{
    width:85%;
    margin:30px auto;
}

/* CARD */
.card{
    background:rgba(255,255,255,0.03);
    border:1px solid rgba(212,175,55,0.2);
    padding:20px;
    border-radius:15px;
    margin-bottom:20px;
}

/* STUDENT PROFILE */
.profile{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.profile h3{
    margin:0;
    color:#d4af37;
}

/* FORM */
input, select{
    padding:10px;
    border-radius:8px;
    border:1px solid rgba(212,175,55,0.3);
    background:#111;
    color:white;
    margin:5px;
}

button{
    padding:8px 12px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    background:#d4af37;
    color:black;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
}

th{
    background:rgba(212,175,55,0.1);
    color:#d4af37;
    padding:12px;
}

td{
    padding:10px;
    border-bottom:1px solid rgba(255,255,255,0.05);
}

tr:hover{
    background:rgba(212,175,55,0.08);
}

/* BUTTONS */
.btn-update{ background:#f1c40f; }
.btn-delete{ background:#e74c3c; color:white; }

/* GPA BOX */
.gpa-box{
    margin-top:15px;
    padding:15px;
    border-radius:12px;
    background:#111;
    border:1px solid rgba(212,175,55,0.3);
    color:#d4af37;
}
</style>

<div class="header">
    <h2>🎓 Enrollment Dashboard</h2>
    <a class="back-btn" href="index.php">⬅ Back</a>
</div>

<div class="container">

<form method="GET" class="card">
    <b>Search Student</b><br>
    <input type="text" name="id" placeholder="Student ID"
    value="<?php echo isset($_GET['id']) ? $_GET['id'] : ''; ?>">
    <button type="submit">Search</button>
</form>

<?php
if(isset($_GET['id']) && $_GET['id'] != ''){

$id = $_GET['id'];

/* GET STUDENT INFO */
$student = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT s.student_name, d.department_name
FROM student s
JOIN department d ON s.department_id=d.department_id
WHERE s.student_id='$id'
"));

/* GET COURSES */
$courses = mysqli_query($conn,"SELECT * FROM course");
?>

<!-- PROFILE -->
<div class="card profile">
    <div>
        <h3><?php echo $student['student_name']; ?></h3>
        <div style="color:#aaa;"><?php echo $student['department_name']; ?></div>
    </div>
    <div>ID: <?php echo $id; ?></div>
</div>

<!-- ADD -->
<div class="card">
<form method="POST">
<input type="hidden" name="student_id" value="<?php echo $id; ?>">

<select name="course_id" required>
<option value="">Select Course</option>
<?php while($c=mysqli_fetch_assoc($courses)){ ?>
<option value="<?php echo $c['course_id']; ?>">
<?php echo $c['course_name']; ?>
</option>
<?php } ?>
</select>

<input type="number" name="semester" placeholder="Semester" required>

<button name="add">➕ Add</button>
</form>
</div>

<?php

$query = "
SELECT e.enrollment_id, c.course_name, c.credits, e.semester, e.grade
FROM enrollment e
JOIN course c ON e.course_id = c.course_id
WHERE e.student_id = '$id'
ORDER BY e.semester
";

$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) > 0){

$total = 0;
$count = 0;
$total_credit = 0;

echo "<div class='card'>";
echo "<table>
<tr>
<th>Course</th>
<th>Credits</th>
<th>Semester</th>
<th>Grade</th>
<th>Action</th>
</tr>";

while($row = mysqli_fetch_assoc($result)){

echo "<tr>
<form method='POST'>

<td>{$row['course_name']}</td>
<td>{$row['credits']}</td>

<td>
<input type='number' name='semester' value='{$row['semester']}' style='width:70px'>
</td>

<td>
<input type='number' step='0.01' name='grade' value='{$row['grade']}' style='width:70px'>
</td>

<td>
<input type='hidden' name='enrollment_id' value='{$row['enrollment_id']}'>

<button class='btn-update' name='update'>Update</button>

<a href='?id=$id&delete={$row['enrollment_id']}'
onclick=\"return confirm('Delete this?')\">
<button type='button' class='btn-delete'>Delete</button>
</a>
</td>

</form>
</tr>";

if($row['grade'] !== NULL){
    $total += $row['grade'];
    $count++;
}

$total_credit += $row['credits'];
}

echo "</table>";

$gpa = ($count>0) ? round($total/$count,2) : 0;

echo "<div class='gpa-box'>
📊 GPA: $gpa <br>
🎯 Total Credits: $total_credit SKS
</div>";

echo "</div>";

}else{
echo "<div class='card'>No enrollment data</div>";
}

}
?>

</div>

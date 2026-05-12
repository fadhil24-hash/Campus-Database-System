<?php
include 'config.php';

$id = $_GET['id'];

/* ================= UPDATE SCHEDULE ================= */
if(isset($_POST['update_schedule'])){

    $schedule_id = $_POST['schedule_id'];
    $day = $_POST['day'];
    $time = $_POST['time'];
    $room = $_POST['classroom_id'];

    // CEK TABRAKAN
    $check = mysqli_query($conn,"
    SELECT * FROM schedule 
    WHERE day='$day' 
    AND time='$time' 
    AND classroom_id='$room'
    AND schedule_id != '$schedule_id'
    ");

    if(mysqli_num_rows($check) > 0){
        echo "<script>alert('Schedule Crashed!');</script>";
    } else {

        mysqli_query($conn,"
        UPDATE schedule SET
        day='$day',
        time='$time',
        classroom_id='$room'
        WHERE schedule_id='$schedule_id'
        ");

        echo "<script>
        alert('Schedule Updated!');
        window.location='';
        </script>";
    }
}

/* ================= COURSE INFO ================= */
$cq = mysqli_query($conn,"
SELECT c.course_name, c.credits, l.lecturer_name
FROM course c
LEFT JOIN lecturer l ON c.lecturer_id = l.lecturer_id
WHERE c.course_id='$id'
");
$c = mysqli_fetch_assoc($cq);

/* ================= STUDENTS ================= */
$sq = mysqli_query($conn,"
SELECT s.student_id, s.student_name, d.department_name, e.grade
FROM enrollment e
JOIN student s ON e.student_id=s.student_id
JOIN department d ON s.department_id=d.department_id
WHERE e.course_id='$id'
");

$students = [];
$total = 0;
$count = 0;

while($row = mysqli_fetch_assoc($sq)){
    $students[] = $row;
    $total += $row['grade'];
    $count++;
}
$total_credit = $count * $c['credits'];

$avg = ($count > 0) ? round($total/$count,2) : 0;

/* ================= STATS ================= */
$dept_count = [];
$grade_dist = ['A'=>0,'B'=>0,'C'=>0,'D'=>0];
$max = 0;
$min = 999;
$top_student = "";

foreach($students as $s){

    if(!isset($dept_count[$s['department_name']])){
        $dept_count[$s['department_name']] = 0;
    }
    $dept_count[$s['department_name']]++;

    if($s['grade'] >= 3.5) $grade_dist['A']++;
    elseif($s['grade'] >= 3) $grade_dist['B']++;
    elseif($s['grade'] >= 2) $grade_dist['C']++;
    else $grade_dist['D']++;

    if($s['grade'] > $max){
        $max = $s['grade'];
        $top_student = $s['student_name'];
    }

    if($s['grade'] < $min){
        $min = $s['grade'];
    }
}

/* ================= SCHEDULE ================= */
$scq = mysqli_query($conn,"
SELECT schedule_id, day, time, classroom_id 
FROM schedule 
WHERE course_id='$id'
");

/* CLASSROOM */
$classrooms = mysqli_query($conn,"SELECT * FROM classroom");
?>

<!DOCTYPE html>
<html>
<head>
<title>Course Detail</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background: radial-gradient(circle at top, #0a0a0a, #000);
    color:#f5f5f5;
    overflow-x:hidden;
}

/* GLOW BACKGROUND */
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

/* CONTAINER */
.detail{
    margin:30px;
    padding:30px;
    border-radius:20px;
    background: rgba(255,255,255,0.03);
    border:1px solid rgba(212,175,55,0.25);
    box-shadow:0 0 40px rgba(212,175,55,0.08);
}

/* HEADER */
.course-header{
    margin-bottom:20px;
}

.course-header h2{
    color:#d4af37;
    margin:0;
    text-shadow:0 0 10px rgba(212,175,55,0.5);
}

.meta{
    color:#aaa;
    margin-top:5px;
}

/* STATS */
.stats{
    display:flex;
    flex-wrap:wrap;
    gap:12px;
    margin-top:15px;
}

.stat{
    background:rgba(0,0,0,0.6);
    border:1px solid rgba(212,175,55,0.2);
    padding:12px 16px;
    border-radius:12px;
    font-size:13px;
    transition:0.3s;
}

.stat:hover{
    transform:translateY(-3px);
    box-shadow:0 0 20px rgba(212,175,55,0.3);
}

/* GRID */
.grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
    margin-top:25px;
}

/* BOX */
.box{
    background:rgba(0,0,0,0.6);
    border:1px solid rgba(212,175,55,0.2);
    padding:18px;
    border-radius:16px;
    transition:0.3s;
}

.box:hover{
    box-shadow:0 0 30px rgba(212,175,55,0.2);
}

/* STUDENT */
.student{
    padding:10px;
    margin-bottom:8px;
    border-radius:10px;
    display:flex;
    justify-content:space-between;
    transition:0.3s;
}

.student:hover{
    background:rgba(212,175,55,0.08);
}

.student small{
    display:block;
    font-size:11px;
    opacity:0.6;
}

/* GPA */
.gpa{
    padding:5px 10px;
    border-radius:8px;
    font-size:12px;
    font-weight:bold;
}

/* FORM SCHEDULE */
form{
    display:flex;
    flex-wrap:wrap;
    gap:8px;
    align-items:center;
    padding:10px;
    border-radius:10px;
    background:rgba(255,255,255,0.02);
    margin-bottom:10px;
    transition:0.3s;
}

form:hover{
    background:rgba(212,175,55,0.05);
}

/* INPUT */
select, input{
    padding:8px;
    border-radius:8px;
    border:1px solid rgba(212,175,55,0.2);
    background:#0a0a0a;
    color:#fff;
}

/* BUTTON */
button{
    padding:8px 12px;
    background:linear-gradient(135deg,#d4af37,#b8860b);
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-weight:bold;
    transition:0.3s;
}
.back-btn{
    display:inline-flex;
    align-items:center;
    gap:6px;
    margin-top:10px;
    padding:8px 14px;
    border-radius:12px;
    text-decoration:none;
    font-size:13px;
    font-weight:bold;

    background:rgba(212,175,55,0.1);
    color:#d4af37;

    border:1px solid rgba(212,175,55,0.3);

    transition:all 0.3s ease;
    backdrop-filter:blur(6px);
}

.back-btn:hover{
    background:#d4af37;
    color:#000;
    transform:translateY(-2px) scale(1.03);
    box-shadow:0 0 15px rgba(212,175,55,0.6);
}
button:hover{
    transform:translateY(-2px);
    box-shadow:0 0 15px rgba(212,175,55,0.6);
}

/* TITLE */
h3{
    color:#d4af37;
    margin-bottom:10px;
}

/* EMPTY */
.empty{
    opacity:0.6;
    font-style:italic;
}
</style>

</head>

<body>

<div class="detail">

<!-- HEADER -->
<div class="course-header">
    <h2>📘 <?= $c['course_name']; ?></h2>
	<a class="back-btn" href="course_lookup.php">
    ⬅ Back to Courses
</a>
<div class="meta">
👨‍🏫 <?= $c['lecturer_name'] ?: 'Not Assigned'; ?> <br>
🎯 <?= $c['credits']; ?> SKS
</div>
</div>

<!-- STATS -->
<div class="stats">
<div class="stat">👨‍🎓 <?= $count ?> Students</div>
<div class="stat">⭐ Avg: <?= $avg ?></div>
<div class="stat">🏆 <?= $top_student ?: '-' ?></div>
<div class="stat">📈 <?= $max ?></div>
<div class="stat">📉 <?= $min ?></div>
<div class="stat">🎯 Course Credit: <?= $c['credits']; ?> SKS</div>
<div class="stat">📦 Total Taken: <?= $total_credit; ?> SKS</div>
</div>

<div class="grid">

<!-- STUDENTS -->
<div class="box">
<h3>Students</h3>

<?php 
if($count > 0){
$no=1;
foreach($students as $s){

$color = ($s['grade'] >= 3.5) ? "#2ecc71" :
         (($s['grade'] >= 3) ? "#f1c40f" : "#e74c3c");

echo "<div class='student'>
<div>
<b>$no.</b> {$s['student_name']}
<small>🏢 {$s['department_name']}</small>
</div>
<div class='gpa' style='background:$color'>
⭐ {$s['grade']}
</div>
</div>";

$no++;
}
}else{
echo "<div class='empty'>No students</div>";
}
?>
</div>

<!-- RIGHT -->
<div>

<!-- EDIT SCHEDULE -->
<div class="box">
<h3>🗓 Edit Schedule</h3>

<?php 
if(mysqli_num_rows($scq)>0){

while($sc=mysqli_fetch_assoc($scq)){
?>

<form method="POST">

<input type="hidden" name="schedule_id" value="<?= $sc['schedule_id'] ?>">

<select name="day">
<option <?= $sc['day']=='Monday'?'selected':'' ?>>Monday</option>
<option <?= $sc['day']=='Tuesday'?'selected':'' ?>>Tuesday</option>
<option <?= $sc['day']=='Wednesday'?'selected':'' ?>>Wednesday</option>
<option <?= $sc['day']=='Thursday'?'selected':'' ?>>Thursday</option>
<option <?= $sc['day']=='Friday'?'selected':'' ?>>Friday</option>
</select>

<input type="time" name="time" value="<?= $sc['time'] ?>">

<select name="classroom_id">
<?php 
mysqli_data_seek($classrooms,0);
while($c = mysqli_fetch_assoc($classrooms)){
$sel = ($c['classroom_id']==$sc['classroom_id'])?'selected':'';
echo "<option value='{$c['classroom_id']}' $sel>{$c['room_name']}</option>";
}
?>
</select>

<button name="update_schedule">Update</button>

</form>

<?php
}

}else{
echo "<div class='empty'>No schedule</div>";
}
?>

</div>

<!-- DEPT -->
<div class="box" style="margin-top:10px;">
<h3>Department</h3>

<?php 
foreach($dept_count as $d=>$t){
echo "<div class='student'><div>$d</div><div>$t</div></div>";
}
?>
</div>

</div>

</div>

</div>

</body>
</html>

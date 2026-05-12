<link rel="stylesheet" href="style.css">
<?php include 'config.php'; ?>

<?php
/* ================= DELETE ================= */
if(isset($_GET['delete'])){
    $id = $_GET['delete'];

    mysqli_query($conn,"DELETE FROM schedule WHERE schedule_id='$id'");

    header("Location: schedule.php?msg=deleted");
    exit();
}

/* ================= UPDATE ================= */
if(isset($_POST['update'])){

    $id = $_POST['schedule_id'];
    $day = $_POST['day'];
    $time = $_POST['time'];
    $room = $_POST['classroom_id'];

    $check = mysqli_query($conn,"
    SELECT * FROM schedule
    WHERE day='$day'
    AND time='$time'
    AND classroom_id='$room'
    AND schedule_id != '$id'
    ");

    if(mysqli_num_rows($check) > 0){
        echo "<script>alert('⚠️ Schedule Crashed!');</script>";
    } else {
        mysqli_query($conn,"
        UPDATE schedule SET
        day='$day',
        time='$time',
        classroom_id='$room'
        WHERE schedule_id='$id'
        ");

        echo "<script>alert('✅ Updated Successfully'); window.location='';</script>";
    }
}
?>
<?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'){ ?>
<script>
alert('✅ Schedule Deleted Successfully');
</script>
<?php } ?>
<!DOCTYPE html>
<html>
<head>
<title>Schedule Manager</title>

<style>

/* ===== BODY ===== */
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background: radial-gradient(circle at top, #0a0a0a, #000);
    color:#fff;
}

/* glow aura */
body::before{
    content:"";
    position:fixed;
    width:500px;
    height:500px;
    background:radial-gradient(circle, rgba(212,175,55,0.08), transparent);
    top:-150px;
    left:-150px;
    filter:blur(80px);
    z-index:-1;
}

/* ===== HEADER ===== */
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:20px 30px;
    background:rgba(0,0,0,0.9);
    border-bottom:1px solid rgba(212,175,55,0.3);
    backdrop-filter:blur(10px);
}

.header h2{
    color:#d4af37;
    letter-spacing:1px;
}

.back-btn{
    background:linear-gradient(135deg,#d4af37,#b8860b);
    color:black;
    padding:8px 14px;
    border-radius:10px;
    text-decoration:none;
    font-weight:bold;
    transition:0.3s;
}

.back-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 0 20px rgba(212,175,55,0.6);
}

/* ===== CONTAINER ===== */
.container{
    padding:30px;
}

/* ===== TABLE ===== */
table{
    width:100%;
    border-collapse:collapse;
    border-radius:16px;
    overflow:hidden;
    background:rgba(255,255,255,0.03);
    border:1px solid rgba(212,175,55,0.2);
}

/* sticky header */
th{
    background:rgba(212,175,55,0.15);
    color:#d4af37;
    padding:14px;
    position:sticky;
    top:0;
    z-index:2;
    text-align:left;
}

/* cells */
td{
    padding:12px;
    border-bottom:1px solid rgba(255,255,255,0.05);
}

/* row hover */
tr:hover{
    background:rgba(212,175,55,0.07);
    transform:scale(1.005);
}

/* ===== INPUT ===== */
select, input{
    background:#111;
    color:white;
    border:1px solid rgba(212,175,55,0.3);
    border-radius:8px;
    padding:6px;
    outline:none;
    transition:0.2s;
}

select:focus, input:focus{
    border-color:#d4af37;
    box-shadow:0 0 8px rgba(212,175,55,0.3);
}

/* ===== BUTTONS ===== */
.action-btn{
    padding:6px 10px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-size:12px;
    transition:0.25s;
}

/* update */
.edit{
    background:#f1c40f;
    color:black;
}
.edit:hover{
    box-shadow:0 0 12px #f1c40f;
}

/* delete */
.delete{
    background:#e74c3c;
    color:white;
}
.delete:hover{
    box-shadow:0 0 12px #e74c3c;
}

/* capacity badge */
.capacity{
    padding:5px 10px;
    border-radius:20px;
    font-size:12px;
    font-weight:bold;
}

/* scroll wrapper */
.table-wrap{
    overflow-x:auto;
    border-radius:16px;
}

</style>
</head>

<body>

<div class="header">
    <h2>📅 Schedule Management</h2>
    <a class="back-btn" href="index.php">⬅ Back</a>
</div>

<div class="container">
<div class="table-wrap">

<table>

<tr>
    <th>Course</th>
    <th>Day</th>
    <th>Time</th>
    <th>Classroom</th>
    <th>Capacity</th>
    <th>Action</th>
</tr>

<?php
$query = "
SELECT s.schedule_id, c.course_name, s.day, s.time, 
       cl.room_name, cl.capacity, cl.classroom_id
FROM schedule s
JOIN course c ON s.course_id = c.course_id
JOIN classroom cl ON s.classroom_id = cl.classroom_id
ORDER BY s.day, s.time
";

$result = mysqli_query($conn, $query);
$classrooms = mysqli_query($conn,"SELECT * FROM classroom");

while($row = mysqli_fetch_assoc($result)){

$capColor = ($row['capacity'] >= 40) ? "#2ecc71" :
            (($row['capacity'] >= 25) ? "#f1c40f" : "#e74c3c");
?>

<tr>
<form method="POST">

<td><?= $row['course_name']; ?></td>

<td>
<select name="day">
<option <?= $row['day']=='Monday'?'selected':'' ?>>Monday</option>
<option <?= $row['day']=='Tuesday'?'selected':'' ?>>Tuesday</option>
<option <?= $row['day']=='Wednesday'?'selected':'' ?>>Wednesday</option>
<option <?= $row['day']=='Thursday'?'selected':'' ?>>Thursday</option>
<option <?= $row['day']=='Friday'?'selected':'' ?>>Friday</option>
</select>
</td>

<td>
<input type="time" name="time" value="<?= $row['time']; ?>">
</td>

<td>
<select name="classroom_id">
<?php 
mysqli_data_seek($classrooms,0);
while($c=mysqli_fetch_assoc($classrooms)){
$sel = ($c['classroom_id']==$row['classroom_id'])?'selected':'';
echo "<option value='{$c['classroom_id']}' $sel>{$c['room_name']}</option>";
}
?>
</select>
</td>

<td>
<span class="capacity" style="background:<?= $capColor ?>">
👥 <?= $row['capacity']; ?>
</span>
</td>

<td>
<input type="hidden" name="schedule_id" value="<?= $row['schedule_id']; ?>">

<button class="action-btn edit" name="update">Update</button>

<a href="?delete=<?= $row['schedule_id']; ?>" 
onclick="return confirm('Delete this schedule?')">
<button type="button" class="action-btn delete">Delete</button>
</a>
</td>

</form>
</tr>

<?php } ?>

</table>

</div>
</div>

</body>

</html>

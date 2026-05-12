<?php include 'config.php'; ?>

<?php
/* ================= DELETE COURSE (SAFE POST + FIXED REDIRECT) ================= */
if(isset($_POST['delete_course'])){
    $id = mysqli_real_escape_string($conn, $_POST['course_id']);

    $check = mysqli_query($conn,"SELECT 1 FROM enrollment WHERE course_id='$id' LIMIT 1");

    if(mysqli_num_rows($check) > 0){
        header("Location: course_lookup.php?error=delete_failed");
        exit();
    }

    mysqli_query($conn,"DELETE FROM course WHERE course_id='$id'");
    header("Location: course_lookup.php");
    exit();
}

/* ================= ADD COURSE (FIXED) ================= */
if(isset($_POST['add'])){
    $name = mysqli_real_escape_string($conn, $_POST['course_name']);
    $lecturer = mysqli_real_escape_string($conn, $_POST['lecturer_id']);
    $credits = mysqli_real_escape_string($conn, $_POST['credits']);

    if(!empty($name) && !empty($credits)){
        mysqli_query($conn,"INSERT INTO course(course_name, lecturer_id, credits) VALUES('$name','$lecturer','$credits')");
    }

    header("Location: course_lookup.php");
    exit();
}

/* ================= UPDATE COURSE (FIXED) ================= */
if(isset($_POST['update'])){
    $id = mysqli_real_escape_string($conn, $_POST['course_id']);
    $name = mysqli_real_escape_string($conn, $_POST['course_name']);
    $lecturer = mysqli_real_escape_string($conn, $_POST['lecturer_id']);
    $credits = mysqli_real_escape_string($conn, $_POST['credits']);

    mysqli_query($conn,"UPDATE course SET course_name='$name', lecturer_id='$lecturer', credits='$credits' WHERE course_id='$id'");

    header("Location: course_lookup.php");
    exit();
}

$lecturers = mysqli_query($conn,"SELECT * FROM lecturer");

$query = "
SELECT c.*, l.lecturer_name,
COUNT(e.student_id) as total_student
FROM course c
LEFT JOIN lecturer l ON c.lecturer_id = l.lecturer_id
LEFT JOIN enrollment e ON c.course_id = e.course_id
GROUP BY c.course_id
";

$result = mysqli_query($conn,$query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Course Intelligence</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background: radial-gradient(circle at top, #0f0f0f, #000);
    color:#fff;
}

.header{
    display:flex;
    justify-content:space-between;
    padding:20px 30px;
    border-bottom:1px solid rgba(212,175,55,0.4);
}

.header h2{ color:#d4af37; }

.back{
    background:#d4af37;
    color:black;
    padding:8px 14px;
    border-radius:10px;
    text-decoration:none;
}

.container{ padding:30px; }

.form-box{
    background:rgba(255,255,255,0.04);
    padding:20px;
    border-radius:14px;
    border:1px solid rgba(212,175,55,0.2);
    margin-bottom:25px;
}

input, select{
    padding:10px;
    margin:5px;
    border-radius:10px;
    border:1px solid rgba(212,175,55,0.3);
    background:#111;
    color:white;
}

button{
    padding:10px 16px;
    background:#d4af37;
    border:none;
    border-radius:10px;
    cursor:pointer;
    font-weight:bold;
}

.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
    gap:18px;
}

.card{
    background:rgba(255,255,255,0.04);
    padding:18px;
    border-radius:16px;
    border:1px solid rgba(212,175,55,0.15);
    transition:all 0.3s ease;
    position:relative;
}

.card:hover{
    transform:translateY(-6px) scale(1.01);
    box-shadow:0 15px 40px rgba(212,175,55,0.15);
    border-color:#d4af37;
}

.card h3{ color:#d4af37; margin:0; }

.meta{ margin-top:8px; font-size:14px; opacity:0.85; }

.badge{
    display:inline-block;
    padding:4px 10px;
    border-radius:999px;
    background:rgba(212,175,55,0.15);
    color:#d4af37;
    font-size:12px;
    margin-top:8px;
}

.action{
    margin-top:12px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    font-size:13px;
}

.action button,
.action span{
    padding:6px 10px;
    border-radius:8px;
    font-size:12px;
    transition:0.2s;
}

.del{
    color:#ff4d4d;
}
input:focus, select:focus{
    outline:none;
    border-color:#d4af37;
    box-shadow:0 0 10px rgba(212,175,55,0.3);
}
.form-box form{
    display:flex;
    flex-wrap:wrap;
    gap:10px;
}
.del:hover{
    background:rgba(255,77,77,0.15);
}

.edit{
    color:#f1c40f;
}

.edit:hover{
    background:rgba(241,196,15,0.15);
}

.edit-box{ display:none; margin-top:10px; }

.course-link{
    display:inline-block;
    margin-top:12px;
    padding:8px 12px;
    border-radius:10px;
    background:linear-gradient(135deg,#d4af37,#b8860b);
    color:#000;
    font-weight:bold;
    text-decoration:none;
    font-size:12px;
    transition:0.3s;
}

.course-link:hover{
    transform:translateY(-2px);
    box-shadow:0 0 15px rgba(212,175,55,0.6);
}
</style>
</head>

<body>

<div class="header">
    <h2>📚 COURSE INTELLIGENCE</h2>
    <a class="back" href="index.php">⬅ Back</a>
</div>

<div class="container">
<?php if(isset($_GET['success'])){ ?>
<script>alert('Action successful!');</script>
<?php } ?>
<?php if(isset($_GET['error'])){ ?>
    <script>alert('Cannot delete: course still has enrolled students');</script>
<?php } ?>

<div class="form-box">
<h3 style="color:#d4af37;">➕ Add Course</h3>
<form method="POST">
<input type="text" name="course_name" placeholder="Course Name" required>
<select name="lecturer_id">
<option value="">Select Lecturer</option>
<?php while($l=mysqli_fetch_assoc($lecturers)){ ?>
<option value="<?= $l['lecturer_id'] ?>"><?= $l['lecturer_name'] ?></option>
<?php } ?>
</select>
<input type="number" name="credits" placeholder="Credits" required>
<button name="add">Add Course</button>
</form>
</div>

<div class="grid">

<?php while($c=mysqli_fetch_assoc($result)){ ?>

<div class="card">

<h3>📘 <?= $c['course_name'] ?></h3>
<div class="meta">👨‍🏫 <?= $c['lecturer_name'] ?: 'Not Assigned' ?></div>
<div class="meta">🎯 <?= $c['credits'] ?> SKS</div>
<div class="meta">👥 <?= $c['total_student'] ?> enrolled</div>

<a class="course-link" href="course_detail.php?id=<?= $c['course_id'] ?>">Open Course Detail →</a>

<div class="action">

<form method="POST" onsubmit="return confirm('Delete this course?')">
<input type="hidden" name="course_id" value="<?= $c['course_id'] ?>">
<button class="del" name="delete_course">🗑 Delete</button>
</form>

<span class="edit" onclick="toggleEdit(<?= $c['course_id'] ?>)">✏ Edit</span>

</div>

<div class="edit-box" id="edit-<?= $c['course_id'] ?>">
<form method="POST">
<input type="hidden" name="course_id" value="<?= $c['course_id'] ?>">
<input type="text" name="course_name" value="<?= $c['course_name'] ?>">
<select name="lecturer_id">
<?php
$lect = mysqli_query($conn,"SELECT * FROM lecturer");
while($l=mysqli_fetch_assoc($lect)){
$sel = ($l['lecturer_id']==$c['lecturer_id'])?'selected':'';
echo "<option value='{$l['lecturer_id']}' $sel>{$l['lecturer_name']}</option>";
}
?>
</select>
<input type="number" name="credits" value="<?= $c['credits'] ?>">
<button name="update">Save</button>
</form>
</div>

</div>

<?php } ?>

</div>

</div>

<script>
function toggleEdit(id){
    let box = document.getElementById("edit-"+id);
    box.style.display = (box.style.display === "block") ? "none" : "block";
}
</script>

</body>
</html>

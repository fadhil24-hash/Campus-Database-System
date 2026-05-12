<?php
include 'config.php';

$id = $_GET['id'];

/* MODE */
$edit = isset($_GET['edit']);

/* UPDATE */
if(isset($_POST['update'])){
    $name = $_POST['name'];
    $spec = $_POST['spec'];
    $email = $_POST['email'];
    $dept = $_POST['dept'];

    mysqli_query($conn,"
    UPDATE lecturer SET
        lecturer_name='$name',
        specialization='$spec',
        email='$email',
        department_id='$dept'
    WHERE lecturer_id='$id'
    ");

    echo "<script>alert('Updated Successfully'); window.location='lecturer_detail.php?id=$id';</script>";
}

/* DATA */
$data = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT l.*, d.department_name
FROM lecturer l
LEFT JOIN department d ON l.department_id=d.department_id
WHERE l.lecturer_id='$id'
"));

$courses = mysqli_query($conn,"
SELECT course_name FROM course WHERE lecturer_id='$id'
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Lecturer Profile</title>

<style>
body{
    margin:0;
    background: radial-gradient(circle at top, #0a0a0a, #000);
    font-family:'Segoe UI';
    color:white;
}

/* HEADER */
.header{
    display:flex;
    justify-content:space-between;
    padding:20px;
    border-bottom:1px solid rgba(212,175,55,0.3);
}

.btn{
    padding:8px 14px;
    border-radius:8px;
    text-decoration:none;
    font-weight:bold;
}

.back{ background:#d4af37; color:black; }
.edit{ background:#444; color:white; }

/* CONTAINER */
.container{
    max-width:700px;
    margin:40px auto;
}

/* CARD */
.card{
    background:rgba(255,255,255,0.04);
    border:1px solid rgba(212,175,55,0.25);
    border-radius:18px;
    padding:30px;
    text-align:center;
    box-shadow:0 0 30px rgba(212,175,55,0.08);
}

/* PHOTO */
.photo{
    width:150px;
    height:150px;
    border-radius:50%;
    background:linear-gradient(135deg,#111,#222);
    margin:auto;
    margin-bottom:20px;
    display:flex;
    align-items:center;
    justify-content:center;
    border:2px solid rgba(212,175,55,0.4);
}

/* NAME */
h2{ color:#d4af37; }

/* INFO */
.info{
    text-align:left;
    margin-top:20px;
}

.info div{
    margin:10px 0;
    padding:10px;
    background:rgba(255,255,255,0.03);
    border-radius:10px;
}

/* INPUT */
input, select{
    width:100%;
    padding:8px;
    margin-top:5px;
    border-radius:8px;
    border:none;
    background:#222;
    color:white;
}

/* BUTTON */
.update-btn{
    margin-top:20px;
    padding:12px;
    width:100%;
    background:#d4af37;
    border:none;
    border-radius:10px;
    font-weight:bold;
    cursor:pointer;
}

/* COURSES */
.course{
    display:inline-block;
    margin:5px;
    padding:6px 10px;
    border-radius:20px;
    background:rgba(212,175,55,0.1);
    border:1px solid rgba(212,175,55,0.4);
    color:#d4af37;
    font-size:13px;
}
</style>

</head>

<body>

<div class="header">
    <a href="lecturers.php" class="btn back">⬅ Back</a>

    <?php if(!$edit){ ?>
        <a href="?id=<?= $id ?>&edit=1" class="btn edit">✏️ Edit</a>
    <?php } ?>
</div>

<div class="container">

<?php if($edit){ ?>
<!-- EDIT MODE -->
<form method="POST" class="card">

<div class="photo">📷</div>

<h2>Edit Lecturer</h2>

<div class="info">

<div>
Name:
<input type="text" name="name" value="<?= $data['lecturer_name'] ?>">
</div>

<div>
Specialization:
<input type="text" name="spec" value="<?= $data['specialization'] ?>">
</div>

<div>
Email:
<input type="email" name="email" value="<?= $data['email'] ?>">
</div>

<div>
Department:
<select name="dept">
<?php
$dq = mysqli_query($conn,"SELECT * FROM department");
while($d=mysqli_fetch_assoc($dq)){
$sel = ($d['department_id']==$data['department_id'])?'selected':'';
echo "<option value='{$d['department_id']}' $sel>{$d['department_name']}</option>";
}
?>
</select>
</div>

</div>

<button class="update-btn" name="update">💾 Save Changes</button>

</form>

<?php } else { ?>
<!-- VIEW MODE -->

<div class="card">

<div class="photo">📷</div>

<h2><?= $data['lecturer_name'] ?></h2>

<div class="info">
<div>🧠 <b>Specialization:</b> <?= $data['specialization'] ?: 'Not set' ?></div>
<div>📧 <b>Email:</b> <?= $data['email'] ?: 'Not set' ?></div>
<div>🏢 <b>Department:</b> <?= $data['department_name'] ?: 'Not set' ?></div>
</div>

<h3 style="color:#d4af37; margin-top:20px;">📚 Courses</h3>

<?php
while($c=mysqli_fetch_assoc($courses)){
echo "<span class='course'>{$c['course_name']}</span>";
}
?>

</div>

<?php } ?>

</div>

</body>
</html>

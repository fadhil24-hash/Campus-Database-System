<?php
include 'config.php';

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM student WHERE student_id='$id'"));

if(isset($_POST['update'])){
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $dept = $_POST['dept'];

    mysqli_query($conn, "UPDATE student SET 
        student_name='$name',
        gender='$gender',
        date_of_birth='$dob',
        email='$email',
        address='$address',
        department_id='$dept'
        WHERE student_id='$id'
    ");

    echo "<script>alert('Updated Successfully'); window.location='students.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Student</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background: radial-gradient(circle at top, #0a0a0a, #000);
    color:#f5f5f5;
}

/* glow */
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

/* HEADER */
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:20px 30px;
    background:rgba(0,0,0,0.9);
    border-bottom:1px solid rgba(212,175,55,0.3);
}

.header h2{
    color:#d4af37;
}

/* BACK */
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
    box-shadow:0 0 15px rgba(212,175,55,0.6);
}

/* CONTAINER */
.container{
    display:flex;
    justify-content:center;
    align-items:center;
    padding:40px;
}

/* CARD FORM */
.form-box{
    width:420px;
    background:rgba(255,255,255,0.04);
    border:1px solid rgba(212,175,55,0.2);
    border-radius:18px;
    padding:25px;
    box-shadow:0 0 30px rgba(212,175,55,0.1);
    transition:0.3s;
}

.form-box:hover{
    box-shadow:0 0 40px rgba(212,175,55,0.25);
}

/* LABEL */
label{
    display:block;
    margin-top:12px;
    font-size:14px;
    color:#d4af37;
}

/* INPUT */
input, select{
    width:100%;
    padding:10px;
    margin-top:5px;
    border-radius:10px;
    border:1px solid rgba(212,175,55,0.3);
    background:#0a0a0a;
    color:white;
}

/* BUTTON */
.btn{
    width:100%;
    margin-top:20px;
    padding:12px;
    border:none;
    border-radius:12px;
    font-weight:bold;
    background:linear-gradient(135deg,#d4af37,#b8860b);
    cursor:pointer;
    transition:0.3s;
}

.btn:hover{
    transform:translateY(-2px);
    box-shadow:0 0 20px rgba(212,175,55,0.6);
}

/* STUDENT ID DISPLAY */
.student-id{
    font-size:13px;
    opacity:0.7;
    margin-bottom:10px;
}
</style>

</head>

<body>

<div class="header">
    <h2>✏️ EDIT STUDENT</h2>
    <a href="students.php" class="back-btn">⬅ Back</a>
</div>

<div class="container">

<div class="form-box">

<div class="student-id">
ID: #<?= $data['student_id']; ?>
</div>

<form method="POST">

<label>Name</label>
<input type="text" name="name" value="<?= $data['student_name']; ?>">

<label>Gender</label>
<select name="gender">
    <option value="Male" <?= $data['gender']=='Male' ? 'selected' : '' ?>>Male</option>
    <option value="Female" <?= $data['gender']=='Female' ? 'selected' : '' ?>>Female</option>
</select>

<label>Date of Birth</label>
<input type="date" name="dob" value="<?= $data['date_of_birth']; ?>">

<label>Email</label>
<input type="email" name="email" value="<?= $data['email']; ?>">

<label>Address</label>
<input type="text" name="address" value="<?= $data['address']; ?>">

<label>Department</label>
<select name="dept">
    <option value="1" <?= $data['department_id']==1?'selected':'' ?>>Accounting</option>
    <option value="2" <?= $data['department_id']==2?'selected':'' ?>>English Literature</option>
    <option value="3" <?= $data['department_id']==3?'selected':'' ?>>Japanese Literature</option>
    <option value="4" <?= $data['department_id']==4?'selected':'' ?>>Information Technology</option>
    <option value="5" <?= $data['department_id']==5?'selected':'' ?>>Information System</option>
</select>

<button class="btn" name="update">💾 Update Student</button>

</form>

</div>

</div>

</body>
</html>

<?php
include 'config.php';

$id = $_GET['id'];

/* GET STUDENT */
$student = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT student_name FROM student WHERE student_id='$id'
"));

/* GET COURSES */
$courses = mysqli_query($conn,"SELECT * FROM course");

/* INSERT ENROLL */
if(isset($_POST['enroll'])){

    if(!empty($_POST['course'])){
        foreach($_POST['course'] as $course_id){

            // CEK DUPLIKAT
            $check = mysqli_query($conn,"
            SELECT * FROM enrollment 
            WHERE student_id='$id' AND course_id='$course_id'
            ");

            if(mysqli_num_rows($check) == 0){
                mysqli_query($conn,"
                INSERT INTO enrollment (student_id, course_id, grade)
                VALUES ('$id','$course_id',NULL)
                ");
            }
        }

        echo "<script>
        alert('Enrollment Success');
        window.location='students.php';
        </script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Enroll Student</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background: radial-gradient(circle at top, #0a0a0a, #000);
    color:#fff;
}

/* GLOW BG */
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

/* CARD */
.box{
    width:450px;
    background:rgba(255,255,255,0.04);
    border:1px solid rgba(212,175,55,0.2);
    border-radius:18px;
    padding:25px;
    box-shadow:0 0 30px rgba(212,175,55,0.1);
    transition:0.3s;
}

.box:hover{
    box-shadow:0 0 40px rgba(212,175,55,0.25);
}

/* STUDENT NAME */
.student{
    margin-bottom:15px;
    font-size:14px;
    opacity:0.8;
}

/* COURSE LIST */
.course-list{
    max-height:250px;
    overflow-y:auto;
    padding-right:5px;
}

/* COURSE ITEM */
.course{
    padding:10px;
    border-radius:10px;
    margin-bottom:6px;
    background:rgba(0,0,0,0.4);
    border:1px solid rgba(212,175,55,0.15);
    transition:0.2s;
}

.course:hover{
    background:rgba(212,175,55,0.08);
}

/* CHECKBOX */
.course label{
    cursor:pointer;
    display:flex;
    gap:10px;
    align-items:center;
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

/* SCROLLBAR */
.course-list::-webkit-scrollbar{
    width:6px;
}

.course-list::-webkit-scrollbar-thumb{
    background:#d4af37;
    border-radius:10px;
}
</style>

</head>

<body>

<div class="header">
    <h2>📚 ENROLL COURSE</h2>
    <a href="students.php" class="back-btn">⬅ Back</a>
</div>

<div class="container">

<div class="box">

<div class="student">
Student: <b><?= $student['student_name']; ?></b>
</div>

<form method="POST">

<div class="course-list">

<?php while($c = mysqli_fetch_assoc($courses)){ ?>

<div class="course">
<label>
<input type="checkbox" name="course[]" value="<?= $c['course_id']; ?>">
<?= $c['course_name']; ?>
</label>
</div>

<?php } ?>

</div>

<button class="btn" name="enroll">💾 Enroll Selected Courses</button>

</form>

</div>

</div>

</body>
</html>

<?php
include 'config.php';
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Student</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background: radial-gradient(circle at top, #1a1a1a, #000);
    color:#fff;
}

/* HEADER */
.header{
    padding:20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-bottom:1px solid #d4af37;
}

.header a{
    text-decoration:none;
    color:#000;
    background:#d4af37;
    padding:8px 12px;
    border-radius:8px;
    font-weight:bold;
}

/* CENTER CONTAINER */
.wrapper{
    display:flex;
    justify-content:center;
    align-items:center;
    height:90vh;
}

/* FORM CARD */
.form-box{
    width:420px;
    background: rgba(255,255,255,0.05);
    border:1px solid rgba(212,175,55,0.3);
    backdrop-filter: blur(12px);
    padding:25px;
    border-radius:18px;
    box-shadow:0 0 30px rgba(212,175,55,0.15);
}

/* TITLE */
.form-box h2{
    text-align:center;
    color:#d4af37;
    margin-bottom:20px;
}

/* INPUT */
input, select{
    width:100%;
    padding:10px;
    margin-top:6px;
    margin-bottom:14px;
    border-radius:10px;
    border:1px solid rgba(212,175,55,0.3);
    background: rgba(0,0,0,0.3);
    color:white;
    outline:none;
}

input:focus, select:focus{
    border-color:#d4af37;
    box-shadow:0 0 8px rgba(212,175,55,0.3);
}

/* BUTTON */
button{
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    background: linear-gradient(135deg, #d4af37, #b8860b);
    color:black;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    transform:scale(1.03);
}

/* MESSAGE */
.success{
    margin-top:15px;
    padding:10px;
    background:rgba(0,255,0,0.1);
    border:1px solid #2ecc71;
    border-radius:10px;
    color:#2ecc71;
    text-align:center;
}

.error{
    margin-top:15px;
    padding:10px;
    background:rgba(255,0,0,0.1);
    border:1px solid #e74c3c;
    border-radius:10px;
    color:#e74c3c;
    text-align:center;
}
</style>

</head>

<body>

<div class="header">
    <h3>➕ Add Student</h3>
    <a href="index.php">Back</a>
</div>

<div class="wrapper">

<div class="form-box">

<h2>Student Registration</h2>

<form method="POST">

Name
<input type="text" name="name">

Gender
<select name="gender">
    <option value="">Select</option>
    <option>Male</option>
    <option>Female</option>
</select>

Date of Birth
<input type="date" name="dob">

Email
<input type="email" name="email">

Address
<input type="text" name="address">

Department
<select name="dept">
    <option value="">Select Department</option>
    <option value="1">Accounting</option>
    <option value="2">English Literature</option>
    <option value="3">Japanese Literature</option>
    <option value="4">Information Technology</option>
    <option value="5">Information System</option>
    <option value="6">Visual Communication Design</option>
    <option value="7">Global Management</option>
    <option value="8">Education</option>
</select>

<button type="submit">Create Student</button>

</form>

<?php
if($_POST){

    if($_POST['name']=='' || $_POST['gender']=='' || $_POST['dob']=='' || $_POST['email']=='' || $_POST['address']=='' || $_POST['dept']==''){
        echo "<div class='error'>Please fill all fields</div>";
    } else {

        $query = "INSERT INTO student 
        (student_name, gender, date_of_birth, email, address, department_id)
        VALUES (
            '{$_POST['name']}',
            '{$_POST['gender']}',
            '{$_POST['dob']}',
            '{$_POST['email']}',
            '{$_POST['address']}',
            '{$_POST['dept']}'
        )";

        if(mysqli_query($conn, $query)){
            echo "<div class='success'>Student successfully added</div>";
        } else {
            echo "<div class='error'>Insert failed</div>";
        }
    }
}
?>

</div>

</div>

</body>
</html>

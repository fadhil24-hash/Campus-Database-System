<?php 
session_start(); 
include 'config.php'; 
?>

<!DOCTYPE html>
<html>
<head>
<title>Campus Access</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background: radial-gradient(circle at top, #1a1a1a, #000);
    color:white;
}

/* CARD */
.box{
    width:380px;
    padding:30px;
    border-radius:18px;
    background: rgba(255,255,255,0.05);
    border:1px solid rgba(212,175,55,0.3);
    backdrop-filter: blur(12px);
    box-shadow:0 0 35px rgba(212,175,55,0.15);
    text-align:center;
}

/* TITLE */
.title{
    font-size:24px;
    color:#d4af37;
    margin-bottom:10px;
}

.subtitle{
    font-size:12px;
    color:#bbb;
    margin-bottom:15px;
}

/* INPUT */
input{
    width:100%;
    padding:12px;
    margin:8px 0;
    border-radius:10px;
    border:1px solid rgba(212,175,55,0.3);
    background:#111;
    color:white;
}

/* BUTTON */
.btn{
    background:linear-gradient(135deg,#d4af37,#b8860b);
    color:black;
    font-weight:bold;
    cursor:pointer;
    margin-top:10px;
    transition:0.3s;
}

.btn:hover{
    transform:scale(1.05);
}

/* SWITCH */
.switch{
    margin-top:12px;
    font-size:12px;
    color:#ccc;
    cursor:pointer;
}

/* ALERT */
.error{
    margin-top:10px;
    color:#e74c3c;
}

.success{
    margin-top:10px;
    color:#2ecc71;
}
</style>

<script>
function toggleForm(type){
    document.getElementById('loginForm').style.display = (type=='login')?'block':'none';
    document.getElementById('registerForm').style.display = (type=='register')?'block':'none';
}
</script>

</head>

<body>

<div class="box">

<div class="title">🏛 CAMPUS ACCESS</div>

<!-- LOGIN -->
<form method="POST" id="loginForm">

<div class="subtitle">Login</div>

<input type="text" name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>

<input type="submit" name="login" value="LOGIN" class="btn">

<div class="switch" onclick="toggleForm('register')">
No account? Sign up
</div>

</form>

<!-- REGISTER -->
<form method="POST" id="registerForm" style="display:none;">

<div class="subtitle">Sign Up</div>

<input type="text" name="reg_username" placeholder="Username" required>
<input type="password" name="reg_password" placeholder="Password" required>

<input type="submit" name="register" value="SIGN UP" class="btn">

<div class="switch" onclick="toggleForm('login')">
Already have account? Login
</div>

</form>

<?php

/* ================= LOGIN ================= */
if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $query = mysqli_query($conn,"
    SELECT * FROM users 
    WHERE username='$username' AND password='$password'
    ");

    if(mysqli_num_rows($query) == 1){
        $_SESSION['user'] = $username;
        header("Location: index.php");
        exit();
    } else {
        echo "<div class='error'>❌ Wrong username or password</div>";
    }
}

/* ================= REGISTER ================= */
if(isset($_POST['register'])){

    $username = $_POST['reg_username'];
    $password_raw = $_POST['reg_password'];
    $password = md5($password_raw);

    // VALIDATION
    if(strlen($username) < 3){
        echo "<div class='error'>Username too short</div>";
    }
    elseif(strlen($password_raw) < 4){
        echo "<div class='error'>Password too short</div>";
    }
    else{

        // CHECK DUPLICATE
        $check = mysqli_query($conn,"
        SELECT * FROM users WHERE username='$username'
        ");

        if(mysqli_num_rows($check) > 0){
            echo "<div class='error'>Username already exists</div>";
        } else {

            mysqli_query($conn,"
            INSERT INTO users (username, password, role)
            VALUES ('$username','$password','student')
            ");

            echo "<script>
            alert('Account created!');
            toggleForm('login');
            </script>";
        }
    }
}
?>

</div>

</body>
</html>

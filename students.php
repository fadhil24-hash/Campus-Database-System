<?php
include 'config.php';

/* DELETE */
if(isset($_GET['delete'])){
    $id = $_GET['delete'];

    // hapus dulu child
    mysqli_query($conn, "DELETE FROM enrollment WHERE student_id='$id'");

    // baru hapus parent
    mysqli_query($conn, "DELETE FROM student WHERE student_id='$id'");

    echo "<script>alert('Student deleted'); window.location='students.php';</script>";
}

/* FILTER */
$where = "WHERE 1=1";

if(!empty($_GET['search'])){
    $search = $_GET['search'];
    $where .= " AND s.student_name LIKE '%$search%'";
}

if(!empty($_GET['dept'])){
    $dept = $_GET['dept'];
    $where .= " AND s.department_id = '$dept'";
}

$sort = "s.student_name ASC";

if(!empty($_GET['sort'])){
    if($_GET['sort'] == "name_desc") $sort = "s.student_name DESC";
    if($_GET['sort'] == "id_asc") $sort = "s.student_id ASC";
    if($_GET['sort'] == "id_desc") $sort = "s.student_id DESC";
}

$query = "
SELECT s.*, d.department_name
FROM student s
JOIN department d ON s.department_id = d.department_id
$where
ORDER BY $sort
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Registry</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background: radial-gradient(circle at top, #0a0a0a, #000);
    color:#f5f5f5;
}

/* glow background */
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
    backdrop-filter:blur(10px);
}

.header h2{
    color:#d4af37;
}

/* BUTTON */
.btn{
    padding:8px 14px;
    border-radius:10px;
    border:none;
    cursor:pointer;
    font-weight:bold;
    transition:0.3s;
}

/* back */
.back-btn{
    background:linear-gradient(135deg,#d4af37,#b8860b);
    color:black;
    text-decoration:none;
}

.back-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 0 15px rgba(212,175,55,0.6);
}

/* CONTAINER */
.container{
    padding:30px;
}

/* FILTER BOX */
.filter-box{
    background:rgba(255,255,255,0.03);
    border:1px solid rgba(212,175,55,0.2);
    padding:20px;
    border-radius:16px;
    margin-bottom:20px;
}

/* INPUT */
input, select{
    padding:10px;
    margin:5px;
    border-radius:10px;
    border:1px solid rgba(212,175,55,0.3);
    background:#0a0a0a;
    color:white;
}

/* APPLY BUTTON */
.apply-btn{
    background:linear-gradient(135deg,#d4af37,#b8860b);
    color:black;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    background:rgba(255,255,255,0.03);
    border:1px solid rgba(212,175,55,0.2);
    border-radius:16px;
    overflow:hidden;
}

/* HEADER TABLE */
th{
    background:rgba(212,175,55,0.15);
    color:#d4af37;
    padding:12px;
}

/* CELL */
td{
    padding:10px;
    border-bottom:1px solid rgba(255,255,255,0.05);
}

/* ROW HOVER */
tr:hover{
    background:rgba(212,175,55,0.08);
}

/* ACTION BUTTONS */
.edit{
    background:#f39c12;
    color:black;
}

.edit:hover{
    box-shadow:0 0 10px #f39c12;
}

.delete{
    background:#e74c3c;
    color:white;
}

.delete:hover{
    box-shadow:0 0 10px #e74c3c;
}
</style>

</head>

<body>

<div class="header">
    <h2>🎓 STUDENT REGISTRY</h2>
    <a class="btn back-btn" href="index.php">⬅ Back</a>
</div>

<div class="container">

<!-- FILTER -->
<div class="filter-box">
<form method="GET">

<input type="text" name="search" placeholder="Search student..."
value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>"
<div class="select-wrapper">
<select name="dept">
    <option value="">All Dept</option>
    <option value="1" <?= (isset($_GET['dept']) && $_GET['dept']==1)?'selected':'' ?>>Accounting</option>
    <option value="2" <?= (isset($_GET['dept']) && $_GET['dept']==2)?'selected':'' ?>>English</option>
    <option value="3" <?= (isset($_GET['dept']) && $_GET['dept']==3)?'selected':'' ?>>Japanese</option>
    <option value="4" <?= (isset($_GET['dept']) && $_GET['dept']==4)?'selected':'' ?>>IT</option>
    <option value="5" <?= (isset($_GET['dept']) && $_GET['dept']==5)?'selected':'' ?>>IS</option>
</select>
</div>


<select name="sort">
<option value="name_asc">Name A-Z</option>
<option value="name_desc">Name Z-A</option>
<option value="id_asc">ID Asc</option>
<option value="id_desc">ID Desc</option>
</select>

<button class="btn apply-btn">Apply</button>

</form>
</div>

<!-- TABLE -->
<table>
<tr>
<th>ID</th>
<th>Name</th>
<th>Gender</th>
<th>DOB</th>
<th>Email</th>
<th>Address</th>
<th>Department</th>
<th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)){ ?>

<tr>
<td>#<?= $row['student_id']; ?></td>
<td><?= $row['student_name']; ?></td>
<td><?= $row['gender']; ?></td>
<td><?= $row['date_of_birth']; ?></td>
<td><?= $row['email']; ?></td>
<td><?= $row['address']; ?></td>
<td><?= $row['department_name']; ?></td>

<td>
<a href="edit_student.php?id=<?= $row['student_id']; ?>">
<button class="btn edit">Edit</button>
</a>

<a href="?delete=<?= $row['student_id']; ?>" 
onclick="return confirm('Delete this student?')">
<button class="btn delete">Delete</button>
</a>

<a href="enroll_student.php?id=<?= $row['student_id']; ?>">
<button class="btn" style="background:#27ae60;color:white;">Enroll</button>
</a>
</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>

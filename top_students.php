<?php
include 'config.php';
?>

<!DOCTYPE html>
<html>
<head>
<title>Top Students</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background: radial-gradient(circle at top, #1a1a1a, #000);
    color:white;
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
    background:#d4af37;
    color:black;
    padding:8px 12px;
    border-radius:8px;
    text-decoration:none;
    font-weight:bold;
}

/* CONTAINER */
.container{
    padding:25px;
}

/* FILTER BOX */
.filter-box{
    background: rgba(255,255,255,0.05);
    border:1px solid rgba(212,175,55,0.2);
    padding:15px;
    border-radius:14px;
    margin-bottom:20px;
}

input, select{
    padding:8px;
    border-radius:8px;
    border:1px solid rgba(212,175,55,0.3);
    background:rgba(0,0,0,0.4);
    color:white;
    margin:5px;
}

/* BUTTON */
button{
    padding:8px 14px;
    border:none;
    border-radius:8px;
    background:linear-gradient(135deg,#d4af37,#b8860b);
    font-weight:bold;
    cursor:pointer;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    background: rgba(255,255,255,0.04);
    border-radius:14px;
    overflow:hidden;
    border:1px solid rgba(212,175,55,0.2);
}

th{
    background:rgba(212,175,55,0.15);
    color:#d4af37;
    padding:12px;
    text-align:left;
}

td{
    padding:12px;
    border-bottom:1px solid rgba(255,255,255,0.05);
}

/* RANK STYLES */
.rank-1{ background: rgba(255,215,0,0.12); }
.rank-2{ background: rgba(192,192,192,0.08); }
.rank-3{ background: rgba(205,127,50,0.08); }

/* GPA BADGE */
.badge{
    padding:5px 10px;
    border-radius:8px;
    font-weight:bold;
    display:inline-block;
}
</style>

</head>

<body>

<div class="header">
    <h2>🏆 ELITE STUDENT LEADERBOARD</h2>
    <a href="index.php">Back</a>
</div>

<div class="container">

<?php
/* ======================
   SAFE GET (PHP 5.6)
====================== */
$search = isset($_GET['search']) ? $_GET['search'] : '';
$dept   = isset($_GET['dept']) ? $_GET['dept'] : '';
$limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$order  = isset($_GET['order']) ? $_GET['order'] : 'DESC';

if($limit <= 0) $limit = 10;
if($limit > 100) $limit = 100;

if($order != 'ASC') $order = 'DESC';

/* ======================
   WHERE FILTER
====================== */
$where = "WHERE 1=1";

if($search != ''){
    $where .= " AND s.student_name LIKE '%$search%'";
}

if($dept != ''){
    $where .= " AND s.department_id = '$dept'";
}
?>

<!-- FILTER -->
<div class="filter-box">

<form method="GET">

🔍
<input type="text" name="search" placeholder="Search student..." value="<?php echo $search; ?>">

🏢
<select name="dept">
    <option value="">All Department</option>
    <option value="1" <?php if($dept==1) echo "selected"; ?>>Accounting</option>
    <option value="2" <?php if($dept==2) echo "selected"; ?>>English Literature</option>
    <option value="3" <?php if($dept==3) echo "selected"; ?>>Japanese Literature</option>
    <option value="4" <?php if($dept==4) echo "selected"; ?>>Information Technology</option>
    <option value="5" <?php if($dept==5) echo "selected"; ?>>Information System</option>
    <option value="6" <?php if($dept==6) echo "selected"; ?>>Visual Communication Design</option>
    <option value="7" <?php if($dept==7) echo "selected"; ?>>Global Management</option>
    <option value="8" <?php if($dept==8) echo "selected"; ?>>Education</option>
</select>

📊
<select name="limit">
    <option value="5" <?php if($limit==5) echo "selected"; ?>>5</option>
    <option value="10" <?php if($limit==10) echo "selected"; ?>>10</option>
    <option value="20" <?php if($limit==20) echo "selected"; ?>>20</option>
</select>

🔃
<select name="order">
    <option value="DESC" <?php if($order=='DESC') echo "selected"; ?>>Highest GPA</option>
    <option value="ASC" <?php if($order=='ASC') echo "selected"; ?>>Lowest GPA</option>
</select>

<button type="submit">Apply</button>

</form>

</div>

<?php
/* ======================
   QUERY
====================== */
$query = "
SELECT s.student_name, d.department_name,
ROUND(AVG(e.grade),2) AS GPA
FROM Enrollment e
JOIN student s ON e.student_id = s.student_id
JOIN department d ON s.department_id = d.department_id
$where
GROUP BY s.student_id
ORDER BY GPA $order
LIMIT $limit
";

$result = mysqli_query($conn, $query);
?>

<!-- TABLE -->
<table>

<tr>
    <th>Rank</th>
    <th>Name</th>
    <th>Department</th>
    <th>GPA</th>
</tr>

<?php
$rank = 1;

while($row = mysqli_fetch_assoc($result)){

    $gpa = $row['GPA'];

    if($gpa >= 3.5){
        $color = "#2ecc71";
    } elseif($gpa >= 3){
        $color = "#f1c40f";
    } else {
        $color = "#e74c3c";
    }

    $class = "";
    if($rank == 1) $class = "rank-1";
    else if($rank == 2) $class = "rank-2";
    else if($rank == 3) $class = "rank-3";

    echo "<tr class='$class'>
        <td><b>#{$rank}</b></td>
        <td>{$row['student_name']}</td>
        <td>{$row['department_name']}</td>
        <td>
            <span class='badge' style='background:$color; color:black'>
                {$gpa}
            </span>
        </td>
    </tr>";

    $rank++;
}
?>

</table>

</div>

</body>
</html>

<?php
include 'config.php';

$keyword = $_GET['keyword'];

$query = "
SELECT course_name 
FROM course
WHERE course_name LIKE '%$keyword%'
LIMIT 5
";

$result = mysqli_query($conn, $query);

while($row = mysqli_fetch_assoc($result)){
    echo "<div onclick=\"selectCourse('".$row['course_name']."')\">
            ".$row['course_name']."
          </div>";
}
?>

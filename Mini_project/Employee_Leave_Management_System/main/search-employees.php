<?php
session_start();
include('includes/config.php');

// Ensure the user is logged in
if (strlen($_SESSION['emplogin']) == 0) {   
    header('location:index.php');
    exit;
}

$eid = $_SESSION['eid']; // Get the logged-in employee ID
$queryStr = isset($_POST['query']) ? trim($_POST['query']) : '';

// Prepare SQL query based on input
if (!empty($queryStr)) {
    $queryStr = "%$queryStr%"; // Append wildcards for LIKE search
    $sql = "SELECT id, FirstName, LastName, EmailId, Department 
            FROM tblemployees 
            WHERE id != :eid 
              AND (CONCAT(FirstName, ' ', LastName) LIKE :queryStr)";
} else {
    $sql = "SELECT id, FirstName, LastName, EmailId, Department 
            FROM tblemployees 
            WHERE id != :eid";
}

$query = $dbh->prepare($sql);
$query->bindParam(':eid', $eid, PDO::PARAM_INT);
if (!empty($queryStr)) {
    $query->bindParam(':queryStr', $queryStr, PDO::PARAM_STR);
}
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

// Display employees in a table format
if ($query->rowCount() > 0) {
    echo '<table class="employee-table" style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">';
    echo '<thead><tr><th>S.No</th><th>Emp ID</th><th>Emp Name</th><th>Email</th><th>Department</th></tr></thead><tbody>';
    $sno = 1;
    foreach ($results as $result) {
        echo '<tr class="employee-option" data-id="' . htmlspecialchars($result->id) . '" data-name="' . htmlspecialchars($result->FirstName . ' ' . $result->LastName) . '">';
        echo '<td>' . $sno . '</td>';
        echo '<td>' . htmlspecialchars($result->id) . '</td>';
        echo '<td>' . htmlspecialchars($result->FirstName . ' ' . $result->LastName) . '</td>';
        echo '<td>' . htmlspecialchars($result->EmailId) . '</td>';
        echo '<td>' . htmlspecialchars($result->Department) . '</td>';
        echo '</tr>';
        $sno++;
    }
    echo '</tbody></table>';
} else {
    echo "<div>No employees found</div>";
}
?>

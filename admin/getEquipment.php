<?php
require_once '../includes/config.php'; // Include database configuration

// Set the number of results per page
$results_per_page = 10;

// Find out the number of results stored in database
$result = $conn->query("SELECT COUNT(id) AS total FROM equipment");
$row = $result->fetch_assoc();
$total_pages = ceil($row['total'] / $results_per_page);

// Determine which page number visitor is currently on
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Determine the sql LIMIT starting number for the results on the displaying page
$this_page_first_result = ($page-1) * $results_per_page;

// Retrieve selected results from database and display them on page
$sql = "SELECT * FROM equipment LIMIT " . $this_page_first_result . ',' . $results_per_page;
$result = $conn->query($sql);

echo json_encode($result->fetch_all(MYSQLI_ASSOC));

// Pagination controls
for ($page=1;$page<=$total_pages;$page++) {
    echo '<a href="getEquipment.php?page=' . $page . '">' . $page . '</a> ';
}
?>

<?php $servername = "localhost";
$username = "root";
$password = "";
$dbname = "transit_db";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die('<div class="no-results">Database connection failed: ' . $conn->connect_error . '</div>');
}
$search_query = "";
if (isset($_GET['search_query'])) {
    $search_query = $_GET['search_query'];
} elseif (isset($_POST['search_query'])) {
    $search_query = $_POST['search_query'];
}
$search_query = mysqli_real_escape_string($conn, $search_query);
if (empty($search_query)) {
    echo '<div class="no-results">Please enter a search term</div>';
    exit;
}
$sql = "SELECT * FROM locations WHERE name LIKE '%$search_query%' OR details LIKE '%$search_query%'";
$result = $conn->query($sql);
$found_results = false;
if ($result && $result->num_rows > 0) {
    $found_results = true;
    while ($location = $result->fetch_assoc()) {
        $location_id = $location['id'];
        $headers_sql = "SELECT * FROM fare_headers WHERE location_id = $location_id ORDER BY display_order";
        $headers_result = $conn->query($headers_sql);
        $fares_sql = "SELECT * FROM fare_matrices WHERE location_id = $location_id";
        $fares_result = $conn->query($fares_sql);
        if ($headers_result && $fares_result && $headers_result->num_rows > 0 && $fares_result->num_rows > 0) {
            echo '<div style="width: 100%; overflow-x: auto; margin-bottom: 30px;">';
            echo '<h2>' . htmlspecialchars($location['name']) . ' - Fare Matrix</h2>';
            echo '<table style="width: 100%; border-collapse: collapse; font-size: 14px;">';
            echo '<thead><tr>';
            while ($header = $headers_result->fetch_assoc()) {
                echo '<th style="padding: 12px 15px; background-color: #2F2E2E; color: #ffffff; text-align: left; font-weight: bold; border-bottom: 2px solid #131313;">' . htmlspecialchars($header['header_name']) . '</th>';
            }
            echo '</tr></thead>';
            echo '<tbody>';
            $row_count = 0;
            while ($fare = $fares_result->fetch_assoc()) {
                $row_style = $row_count % 2 === 0 ? 'background-color: #f2f2f2;' : 'background-color: #ffffff;';
                echo '<tr style="' . $row_style . '">';
                echo '<td style="padding: 10px 15px; border: 1px solid #ddd;">' . htmlspecialchars($fare['route_name']) . '</td>';
                echo '<td style="padding: 10px 15px; border: 1px solid #ddd;">' . htmlspecialchars($fare['regular_fare']) . '</td>';
                echo '<td style="padding: 10px 15px; border: 1px solid #ddd;">' . htmlspecialchars($fare['discounted_fare']) . '</td>';
                echo '<td style="padding: 10px 15px; border: 1px solid #ddd;">' . htmlspecialchars($fare['travel_time']) . '</td>';
                echo '<td style="padding: 10px 15px; border: 1px solid #ddd;">' . htmlspecialchars($fare['first_trip']) . '</td>';
                echo '<td style="padding: 10px 15px; border: 1px solid #ddd;">' . htmlspecialchars($fare['last_trip']) . '</td>';
                echo '</tr>';
                $row_count++;
            }
            echo '</tbody>';
            echo '</table>';
            echo '<p style="margin-top: 15px; font-style: italic; font-size: 12px; color: #666; text-align: right;">Last updated: March 1, 2025. Fares subject to change without prior notice.</p>';
            echo '</div>';
        }
    }
}
if (!$found_results) {
    $search_query_lower = strtolower($search_query);
    $locations = ["central station", "airport terminal", "downtown plaza", "university campus", "shopping mall"];
    $found_match = false;
    foreach ($locations as $location) {
        if (strpos($location, $search_query_lower) !== false || strpos($search_query_lower, $location) !== false) {
            $found_match = true;
            include 'fare_matrices_fallback.php';
            break;
        }
    }
    if (!$found_match) {
        echo '<div class="no-results" style="padding: 20px; text-align: center; color: #333;">';
        echo 'No fare information found for your search. Please try searching for Central Station, Airport Terminal, Downtown Plaza, University Campus, or Shopping Mall.';
        echo '</div>';
    }
}
$conn->close(); ?>
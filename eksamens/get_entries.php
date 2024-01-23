<?php
require_once 'GuestBook.php';

$sortOption = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

$guestBook = new GuestBook();
$entries = $guestBook->getEntries($sortOption, $searchQuery);

if (!empty($entries)) {
    foreach ($entries as $entry) {
        echo '<p><strong>' . $entry['name'] . '</strong> (' . $entry['email'] . '): ' . $entry['message'] . ' - ' . $entry['created_at'] . '</p>';
    }
} else {
    echo '<p>Nav ierakstu.</p>';
}
?>

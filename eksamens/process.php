<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'GuestBook.php';

    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    $guestBook = new GuestBook();
    $guestBook->addEntry($name, $email, $message);
}

header('Location: index.php');
exit;
?>

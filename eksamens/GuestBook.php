<?php

class GuestBook
{
    private $db;
    public function __construct()
    {
        $host = 'localhost';
        $dbname = 'eksamens';
        $username = 'root';
        $password = '';

        $this->db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    }

    public function getEntries($sortOption = 'created_at', $searchQuery = '')
{
    $allowedSortOptions = ['created_at', 'name'];

    if (!in_array($sortOption, $allowedSortOptions)) {
        $sortOption = 'created_at';
    }

    $searchCondition = '';
    if (!empty($searchQuery)) {
        $searchCondition = " WHERE name LIKE :search OR email LIKE :search OR message LIKE :search";
    }

    $stmt = $this->db->prepare("SELECT * FROM entries" . $searchCondition . " ORDER BY $sortOption DESC");

    if (!empty($searchQuery)) {
        $stmt->bindValue(':search', '%' . $searchQuery . '%', PDO::PARAM_STR);
    }

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    

    public function addEntry($name, $email, $message)
    {
        $stmt = $this->db->prepare("INSERT INTO entries (name, email, message) VALUES (:name, :email, :message)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':message', $message);

        return $stmt->execute();
    }
}

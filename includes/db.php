<?php
// db.php
require_once(__DIR__ . '/../config/config.php');

function getDatabaseConnection($dbName = null) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Si un nom de base de données est fourni, le créer ou le sélectionner
    if ($dbName) {
        $sql = "CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
        if ($conn->query($sql) !== TRUE) {
            die("Error creating database: " . $conn->error);
        }

        $conn->select_db($dbName);
    } else {
        // Sélectionner la base de données par défaut
        $conn->select_db(DB_NAME);
    }

    return $conn;
}

// Obtenir la connexion à la base de données
$conn = getDatabaseConnection();


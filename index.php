<?php
session_start();

// Obtenir la connexion à la base de données
require_once(__DIR__ . '/includes/db.php');

// Inclure les contrôleurs
require_once(__DIR__ . '/controllers/CompetitionController.php');
require_once(__DIR__ . '/controllers/EnregistrerController.php');

// Obtenir la connexion à la base de données
$conn = getDatabaseConnection();

// Fonction pour créer la table `competitions` (ou `tournois`)
function createCompetitionsTable($conn) {
    // Requête SQL pour créer la table
    $sql = "CREATE TABLE IF NOT EXISTS competitions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        tournoi VARCHAR(255) NOT NULL,
        lieu VARCHAR(255) NOT NULL,
        juge VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    // Exécuter la requête
    if ($conn->query($sql) === FALSE) {
        die("Erreur lors de la création de la table: " . $conn->error);
    }
}

// Créer la table `competitions`
createCompetitionsTable($conn);

// Initialiser les contrôleurs
$competitionController = new CompetitionController($conn);
$enregistrerController = new EnregistrerController($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Traiter le formulaire via EnregistrerController
    $enregistrerController->enregistrerCompetition();
} else {
    // Afficher le formulaire d'initialisation
    $competitionController->afficherFormulaireInitialisation();
}

// Fermer la connexion à la base de données
$conn->close();

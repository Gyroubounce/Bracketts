<?php
session_start();

// Obtenir la connexion à la base de données
require_once(__DIR__ . '/../includes/db.php');
// Inclure le modèle Competition
require_once(__DIR__ . '/../models/Competition.php');



// Récupérer l'ID de la compétition depuis l'URL
$competitionId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Récupérer les informations de la compétition
$competition = new Competition($conn);
$competitionData = $competition->read($competitionId);

// Vérifier si la compétition existe
if (!$competitionData) {
    echo "Compétition introuvable.";
    exit;
}

// Fermer la connexion à la base de données
$conn->close();
?>

<?php require_once(__DIR__ . '/../includes/header.php'); ?>

<main>
    <h1>Bienvenue à la compétition <?php echo htmlspecialchars($competitionData['nom']); ?>!</h1>
    <p>Tournoi: <?php echo htmlspecialchars($competitionData['tournoi']); ?></p>
    <p>Lieu: <?php echo htmlspecialchars($competitionData['lieu']); ?></p>
    <p>Juge: <?php echo htmlspecialchars($competitionData['juge']); ?></p>
    <a href="./inscription_joueurs.php">Inscription des joueurs</a>
    <a href="./inscription_juges.php">Inscription des juges</a>
</main>


<?php require_once(__DIR__ . '/../includes/footer.php'); ?>

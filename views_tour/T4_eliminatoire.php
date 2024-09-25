<?php
session_start();
require_once(__DIR__ . '/../includes/db.php');

// Connexion à la base de données
$conn = getDatabaseConnection(); // Assurez-vous que cette fonction est correctement définie

// Vérifier la connexion
if (!$conn) {
    die('Échec de la connexion à la base de données : ' . htmlspecialchars(mysqli_connect_error()));
}

// Vérifier si la session contient les tours ou récupérer les tours de la base de données
if (isset($_SESSION['tours_session4'])) {
    $tours = $_SESSION['tours_session4'];
} else {
    $tours = array();
    $sql = "SELECT * FROM tournoi_impairs";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $tours = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        echo 'Aucun tournoi en cours.';
        $conn->close();
        exit;
    }
}

// Fonction pour réinitialiser les tours
function reinitialiserTours() {
    global $conn;

    // Réinitialisation complète de la table tournoi_impairs
    $sql = "DELETE FROM tournoi_impairs";
    if ($conn->query($sql) === TRUE) {
        echo "Table tournoi_impairs réinitialisée avec succès.";
    } else {
        echo "Erreur lors de la réinitialisation de la table tournoi_impairs : " . $conn->error;
    }

    // Réinitialiser les données en session
    $_SESSION['tours_session4'] = array();
}

// Vérifier si le formulaire de réinitialisation est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_tours'])) {
    reinitialiserTours();
    // Redirection vers la page actuelle pour éviter la soumission multiple du formulaire
    header("Location: {$_SERVER['PHP_SELF']}");
    exit;
}

// Grouper les matchs par groupe de 3 joueurs
function grouperParTour($tours) {
    $groupes = array();
    foreach ($tours as $match) {
        $id_tournoi = $match['id_tournoi'];
        if (!isset($groupes[$id_tournoi])) {
            $groupes[$id_tournoi] = array();
        }
        $groupes[$id_tournoi][] = $match;
    }
    return $groupes;
}

$groupes = grouperParTour($tours);
?>

<!-- Header.php -->
<?php require_once(__DIR__ . '/../includes/header.php'); ?>

<main>
    <h1>Éliminatoire Tour 4</h1>
    <?php if (!empty($groupes)): ?>
        <?php foreach ($groupes as $id_tournoi => $matches): ?>
            <!-- H2 cliquable pour voir tous les matchs du groupe -->
            <h2 class="clickable-header" onclick="window.location.href='T4_match.php?id_tournoi=<?php echo htmlspecialchars($id_tournoi); ?>'">
                Groupe <?php echo htmlspecialchars($id_tournoi); ?>
            </h2>
            <table>
                <thead>
                    <tr>
                        <th>Match ID</th>
                        <th>Nom Joueur</th>
                        <th>Club</th>
                        <th>Nom Joueur Adverse</th>
                        <th>Club Adverse</th>
                        <th>Nom Joueur Impair</th>
                        <th>Club Impair</th>
                        <th>Juge</th>
                        <th>Club Juge</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matches as $match): ?>
                        <tr onclick="window.location.href='match_individuel.php?id_match=<?php echo htmlspecialchars($match['id_match']); ?>'">
                            <td><?php echo htmlspecialchars($match['id_match']); ?></td>
                            <td><?php echo htmlspecialchars($match['nom_joueur']); ?></td>
                            <td><?php echo htmlspecialchars($match['club']); ?></td>
                            <td><?php echo htmlspecialchars($match['nom_joueur_adverse']); ?></td>
                            <td><?php echo htmlspecialchars($match['club_adverse']); ?></td>
                            <td><?php echo htmlspecialchars($match['nom_joueur_impair']); ?></td>
                            <td><?php echo htmlspecialchars($match['club_impair']); ?></td>
                            <td><?php echo htmlspecialchars($match['juge1']); ?></td>
                            <td><?php echo htmlspecialchars($match['juge1_club']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucun tournoi n'est actuellement en cours.</p>
    <?php endif; ?>

    <!-- Formulaire pour réinitialiser les tours -->
    <form action="/bracketts/views/Affichage.php" method="post">
        <input type="hidden" name="joueurs" value="<?php echo htmlspecialchars(serialize($joueurs)); ?>">
        <input type="submit" name="generer_combinaisons" value="Générer les combinaisons">
    </form>
</main>

<!-- Footer.php -->
<?php require_once(__DIR__ . '/../includes/footer.php'); ?>


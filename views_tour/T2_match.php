<?php
// Connexion à la base de données
require_once(__DIR__ . '/../includes/db.php');

// Initialisation des variables
$group_matches = array();

// Récupérer les paramètres d'URL
$id_tournoi = isset($_GET['id_tournoi']) ? (int)$_GET['id_tournoi'] : null;

if ($id_tournoi !== null) {
    // Débogage : Afficher l'ID du tournoi
    echo "ID du tournoi : " . htmlspecialchars($id_tournoi) . "<br>";

    // Récupérer les détails des matchs pour le tournoi spécifié
    $sql = "SELECT * FROM tournoi_impairs WHERE id_tournoi = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("i", $id_tournoi);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $group_matches = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        echo "Aucun match trouvé pour l'ID du tournoi spécifié.";
    }

    $stmt->close();
} else {
    echo "ID du tournoi non spécifié.";
}

// Fermer la connexion à la base de données
$conn->close();
?>


    <!-- Header.php -->
    <?php require_once(__DIR__ . '/../includes/header.php'); ?>

    <main>
        <h1>Mactch Groupe <?php echo htmlspecialchars($id_tournoi); ?></h1>

        <?php if (!empty($group_matches)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Match</th>
                        <th>Joueur</th>
                        <th>Club</th>
                        <th>Joueur Adverse</th>
                        <th>Club Adverse</th>
                        <th>Joueur Impair</th>
                        <th>Club Impair</th>
                        <th>Juge</th>
                        <th>Club Juge</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($group_matches as $match): ?>
                        <tr onclick="window.location.href='T2_score.php?id_match=<?php echo urlencode($match['id_match']); ?>'">
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
        <?php else: ?>
            <p>Aucun match trouvé pour le tournoi spécifié.</p>
        <?php endif; ?>

        <div class="button-group">
            <a href="T2_eliminatoire.php" class="button">Retour au tableau éliminatoire</a>
        </div>
    </main>

    
    <?php require_once(__DIR__ . '/../includes/footer.php'); ?>


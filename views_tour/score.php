<?php
// Connexion à la base de données
require_once(__DIR__ . '/../includes/db.php');

// Récupérer les paramètres d'URL
$id_match = isset($_GET['id_match']) ? (int)$_GET['id_match'] : null;

if ($id_match === null) {
    die("ID du match non spécifié.");
}

// Récupérer les détails du match spécifié
$sql = "SELECT id_tournoi, nom_joueur, club AS club_joueur, nom_joueur_adverse, club_adverse, juge1, juge1_club FROM tournoi_impairs WHERE id_match = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $id_match);
$stmt->execute();
$match_result = $stmt->get_result();

if ($match_result->num_rows > 0) {
    $match_details = $match_result->fetch_assoc();
    $id_tournoi = $match_details['id_tournoi']; // Extraire l'ID du tournoi depuis les détails du match
    $stmt->close();
} else {
    echo "Aucun match trouvé avec l'ID spécifié.<br>";
    $id_tournoi = null; // Assigner une valeur nulle pour l'ID du tournoi si aucun match trouvé
}

// Requête pour récupérer tous les enregistrements de score_impairs pour le match spécifié
$scores_sql = "SELECT * FROM score_impairs WHERE id_match = ?";
$scores_stmt = $conn->prepare($scores_sql);
if ($scores_stmt === false) {
    die("Prepare failed: " . htmlspecialchars($conn->error));
}
$scores_stmt->bind_param("i", $id_match);
$scores_stmt->execute();
$scores_result = $scores_stmt->get_result();

?>

     <!-- Header.php -->
     <?php require_once(__DIR__ . '/../includes/header.php'); ?>
<style>
        input[type="number"] {
        width: 50px; /* Ajustez cette valeur pour obtenir la largeur souhaitée */
        padding: 5px;
        font-size: 16px; /* Ajustez la taille de la police selon les besoins */
        text-align: center; /* Centrer le texte pour une meilleure lisibilité */
    }
</style>


    <main>
        <h1>Tour 1 Groupe <?php echo htmlspecialchars($id_tournoi); ?></h1>

        <?php if (!empty($match_details)): ?>
            <div class="match-container">
                <h2>Match <?php echo htmlspecialchars($id_match); ?></h2>
                
                <!-- Formulaire pour saisir les scores -->
                <form method="post" action="/bracketts/controllers/Tour1Controller.php?id_tournoi=<?php echo urlencode($id_tournoi); ?>&id_match=<?php echo urlencode($id_match); ?>">
                    <table>
                        <thead>
                            <tr>
                                <th>Joueur</th>
                                <th>Club</th>
                                <th>Score 1</th>
                                <th>Score 2</th>
                                <th>Score 3</th>
                                <th>Score 4</th>
                                <th>Score 5</th>
                                <th>Juge</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo htmlspecialchars($match_details['nom_joueur']); ?></td>
                                <td><?php echo htmlspecialchars($match_details['club_joueur']); ?></td>
                                <td><input type="number" name="score1_joueur1" required></td>
                                <td><input type="number" name="score2_joueur1" required></td>
                                <td><input type="number" name="score3_joueur1" required></td>
                                <td><input type="number" name="score4_joueur1"></td>
                                <td><input type="number" name="score5_joueur1"></td>
                                <td><?php echo htmlspecialchars($match_details['juge1']); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo htmlspecialchars($match_details['nom_joueur_adverse']); ?></td>
                                <td><?php echo htmlspecialchars($match_details['club_adverse']); ?></td>
                                <td><input type="number" name="score1_joueur2" required></td>
                                <td><input type="number" name="score2_joueur2" required></td>
                                <td><input type="number" name="score3_joueur2" required></td>
                                <td><input type="number" name="score4_joueur2"></td>
                                <td><input type="number" name="score5_joueur2"></td>
                                <td><?php echo htmlspecialchars($match_details['juge1_club']); ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="submit">Enregistrer les scores</button>
                </form>
            </div>

            <!-- Affichage des scores existants -->
            <div class="scores-container">
                <h2>Scores des Matchs</h2>
                <?php if ($scores_result->num_rows > 0): ?>
                    <table border="1">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ID Match</th>
                                <th>ID Tournoi</th>
                                <th>Nom Joueur</th>
                                <th>Club Joueur</th>
                                <th>Nom Joueur Adverse</th>
                                <th>Club Joueur Adverse</th>
                                <th>Score 1 Joueur 1</th>
                                <th>Score 2 Joueur 1</th>
                                <th>Score 3 Joueur 1</th>
                                <th>Score 4 Joueur 1</th>
                                <th>Score 5 Joueur 1</th>
                                <th>Score 1 Joueur 2</th>
                                <th>Score 2 Joueur 2</th>
                                <th>Score 3 Joueur 2</th>
                                <th>Score 4 Joueur 2</th>
                                <th>Score 5 Joueur 2</th>
                                <th>Gagnant</th>
                                <th>Gagnant_club</th>
                                <th>Juge</th>
                                <th>Juge Club</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $scores_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['id_match']); ?></td>
                                    <td><?php echo htmlspecialchars($row['id_tournoi']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nom_joueur']); ?></td>
                                    <td><?php echo htmlspecialchars($row['club_joueur']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nom_joueur_adverse']); ?></td>
                                    <td><?php echo htmlspecialchars($row['club_joueur_adverse']); ?></td>
                                    <td><?php echo htmlspecialchars($row['score1_joueur1']); ?></td>
                                    <td><?php echo htmlspecialchars($row['score2_joueur1']); ?></td>
                                    <td><?php echo htmlspecialchars($row['score3_joueur1']); ?></td>
                                    <td><?php echo htmlspecialchars($row['score4_joueur1']); ?></td>
                                    <td><?php echo htmlspecialchars($row['score5_joueur1']); ?></td>
                                    <td><?php echo htmlspecialchars($row['score1_joueur2']); ?></td>
                                    <td><?php echo htmlspecialchars($row['score2_joueur2']); ?></td>
                                    <td><?php echo htmlspecialchars($row['score3_joueur2']); ?></td>
                                    <td><?php echo htmlspecialchars($row['score4_joueur2']); ?></td>
                                    <td><?php echo htmlspecialchars($row['score5_joueur2']); ?></td>
                                    <td><?php echo htmlspecialchars($row['gagnant']); ?></td>
                                    <td><?php echo htmlspecialchars($row['gagnant_club']); ?></td>
                                    <td><?php echo htmlspecialchars($row['juge1']); ?></td>
                                    <td><?php echo htmlspecialchars($row['juge1_club']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Aucun score trouvé pour ce match.</p>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <p>Aucun match trouvé pour le groupe spécifié.</p>
        <?php endif; ?>

        <a href="eliminatoire.php">Retour au tableau éliminatoire</a>
    </main>

    <?php require_once(__DIR__ . '/../includes/footer.php'); ?>

<?php
// Fermer la connexion à la base de données
$conn->close();
?>

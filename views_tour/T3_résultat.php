<?php

// Connexion à la base de données
require_once(__DIR__ . '/../includes/db.php');

// Récupérer les matchs et les scores
$sql = "SELECT id_tournoi, id_match, nom_joueur, club_joueur, nom_joueur_adverse, club_joueur_adverse, 
               score1_joueur1, score2_joueur1, score3_joueur1, score4_joueur1, score5_joueur1, 
               score1_joueur2, score2_joueur2, score3_joueur2, score4_joueur2, score5_joueur2, 
               gagnant,gagnant_club, juge1, juge1_club 
        FROM score_t3_tours";
$result = $conn->query($sql);

// Vérifier la réussite de la requête
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Fermer la connexion à la base de données
$conn->close();
?>

    <style>
     
        .winner {
            font-weight: bold;
            text-align: center;
            background-color: #e0ffe0;
        }
    </style>
</head>
<body>
  <!-- Header.php -->
  <?php require_once(__DIR__ . '/../includes/header.php'); ?>

    <main>
        <h1>Résultats T3</h1>
        <form action="/bracketts/controllers/T3_ClassementController.php" method="post">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="match-container">
                    <table>
                        <thead>
                            <tr>
                                <th colspan="8">Match ID: <?php echo htmlspecialchars($row['id_match']); ?> (Tournoi ID: <?php echo htmlspecialchars($row['id_tournoi']); ?>)</th>
                            </tr>
                            <tr>
                                <th>Joueurs</th>
                                <th>Clubs</th>
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
                                <th><?php echo htmlspecialchars($row['nom_joueur']); ?></th>
                                <td><?php echo htmlspecialchars($row['club_joueur']); ?></td>
                                <td><?php echo htmlspecialchars($row['score1_joueur1']); ?></td>
                                <td><?php echo htmlspecialchars($row['score2_joueur1']); ?></td>
                                <td><?php echo htmlspecialchars($row['score3_joueur1']); ?></td>
                                <td><?php echo htmlspecialchars($row['score4_joueur1']); ?></td>
                                <td><?php echo htmlspecialchars($row['score5_joueur1']); ?></td>
                                <td><?php echo htmlspecialchars($row['juge1']); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo htmlspecialchars($row['nom_joueur_adverse']); ?></th>
                                <td><?php echo htmlspecialchars($row['club_joueur_adverse']); ?></td>
                                <td><?php echo htmlspecialchars($row['score1_joueur2']); ?></td>
                                <td><?php echo htmlspecialchars($row['score2_joueur2']); ?></td>
                                <td><?php echo htmlspecialchars($row['score3_joueur2']); ?></td>
                                <td><?php echo htmlspecialchars($row['score4_joueur2']); ?></td>
                                <td><?php echo htmlspecialchars($row['score5_joueur2']); ?></td>
                                <td><?php echo htmlspecialchars($row['juge1_club']); ?></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="8" class="winner">
                                    <?php
                                        $gagnant = htmlspecialchars($row['gagnant']);
                                        $club_gagnant = '';
                                        $perdant = '';
                                        $club_perdant = '';

                                        // Comparer le gagnant avec les joueurs pour trouver le club
                                        if ($gagnant === $row['nom_joueur']) {
                                            $club_gagnant = $row['club_joueur'];
                                            $perdant = $row['nom_joueur_adverse'];
                                            $club_perdant = $row['club_joueur_adverse'];
                                        } elseif ($gagnant === $row['nom_joueur_adverse']) {
                                            $club_gagnant = $row['club_joueur_adverse'];
                                            $perdant = $row['nom_joueur'];
                                            $club_perdant = $row['club_joueur'];
                                        }

                                        // Afficher les données du gagnant et du perdant
                                        echo "Gagnant: " . $gagnant . " - Club: " . ($club_gagnant ?: 'Club non trouvé') . "<br>";
                                        echo "Perdant: " . $perdant . " - Club: " . ($club_perdant ?: 'Club non trouvé');
                                    ?>
                                    <input type="hidden" name="gagnants[]" value="<?php echo htmlspecialchars($gagnant) . ';' . htmlspecialchars($club_gagnant); ?>">
                                    <input type="hidden" name="perdants[]" value="<?php echo htmlspecialchars($perdant) . ';' . htmlspecialchars($club_perdant); ?>">
                                </td>
                            </tr>
                        </tfoot>

                    </table>
                </div>
            <?php endwhile; ?>
            <input type="submit" value="Enregistrer les gagnants et passer au prochain tour">
        <?php else: ?>
            <p>Aucun match joué n'a été trouvé.</p>
        <?php endif; ?>
        </form>
    </main>

    <?php require_once(__DIR__ . '/../includes/footer.php'); ?>



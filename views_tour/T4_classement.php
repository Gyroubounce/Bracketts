<?php

// Connexion à la base de données
require_once(__DIR__ . '/../includes/db.php');

// Récupérer le classement depuis la table T2_classements
$sql = "SELECT * FROM T2_classements ORDER BY matchs_gagnes DESC, points_gagnes DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Erreur lors de la récupération du classement : " . $conn->error);
}

?>
<?php require_once(__DIR__ . '/../includes/header.php'); ?>

<main>
    <h1>Classement des joueurs T4</h1>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Club</th>
                <th>Matchs Gagnés</th>
                <th>Points Gagnés</th>
                <th>Points Perdus</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nom']); ?></td>
                    <td><?php echo htmlspecialchars($row['club']); ?></td>
                    <td><?php echo htmlspecialchars($row['matchs_gagnes']); ?></td>
                    <td><?php echo htmlspecialchars($row['points_gagnes']); ?></td>
                    <td><?php echo htmlspecialchars($row['points_perdus']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <form action="/bracketts/controllers/T5_SessionController.php" method="post">
        <input type="submit" value="Session 5">
    </form>

</main>

<?php
// Fermer la connexion à la base de données
$conn->close();
require_once(__DIR__ . '/../includes/footer.php');
?>

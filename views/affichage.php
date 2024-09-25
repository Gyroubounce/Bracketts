<?php
session_start();
require_once(__DIR__ . '/../includes/db.php');

$conn = getDatabaseConnection();

$sql = "SELECT * FROM tournoi_impairs";
$result = $conn->query($sql);

if (!$result) {
    die("Erreur lors de la récupération des matchs : " . htmlspecialchars($conn->error));
}

$matches = [];
while ($row = $result->fetch_assoc()) {
    $matches[] = $row;
}

$conn->close();

// Créer un tableau pour stocker les doublons et les uniques
$doublons = [];
$uniqueMatches = [];
foreach ($matches as $match) {
    $key = implode('|', [
        $match['nom_joueur'],
        $match['nom_joueur_adverse'],
    ]);

    // Vérifier la présence de la paire, en considérant l'ordre des joueurs
    $reverseKey = implode('|', [
        $match['nom_joueur_adverse'],
        $match['nom_joueur'],
    ]);

    if (isset($uniqueMatches[$key]) || isset($uniqueMatches[$reverseKey])) {
        $doublons[] = $match; // Ajouter au tableau des doublons
    } else {
        $uniqueMatches[$key] = $match; // Stocker le match unique
    }
}

// Calculer le nombre total de matchs possibles
$joueurs = [];
foreach ($matches as $match) {
    $joueurs[$match['nom_joueur']] = true;
    $joueurs[$match['nom_joueur_adverse']] = true;
}

$joueurs = array_keys($joueurs);
$totalPlayers = count($joueurs);
$matchsPossibles = ($totalPlayers * ($totalPlayers - 1)) / 2; // Formule pour calculer les matchs possibles

// Affichage des matchs
require_once(__DIR__ . '/../includes/header.php'); ?>

<main>
    <h2>Matchs :</h2>
    <table>
        <thead>
            <tr>
                <th>ID Count</th>
                <th>Match ID</th>
                <th>Nom Joueur</th>
                <th>Club</th>
                <th>Nom Joueur Adverse</th>
                <th>Club Adverse</th>
                <th>Juge</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($matches as $match): ?>
                <tr>
                    <td><?php echo htmlspecialchars($match['id_count']); ?></td>
                    <td><?php echo htmlspecialchars($match['id_match']); ?></td>
                    <td><?php echo htmlspecialchars($match['nom_joueur']); ?></td>
                    <td><?php echo htmlspecialchars($match['club']); ?></td>
                    <td><?php echo htmlspecialchars($match['nom_joueur_adverse']); ?></td>
                    <td><?php echo htmlspecialchars($match['club_adverse']); ?></td>
                    <td><?php echo htmlspecialchars($match['juge1']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Doublons :</h2>
    <table>
        <thead>
            <tr>
                <th>ID Count</th>
                <th>Nom Joueur</th>
                <th>Nom Joueur Adverse</th>
                <th>Juge</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($doublons as $doublon): ?>
                <tr>
                    <td><?php echo htmlspecialchars($doublon['id_count']); ?></td>
                    <td><?php echo htmlspecialchars($doublon['nom_joueur']); ?></td>
                    <td><?php echo htmlspecialchars($doublon['nom_joueur_adverse']); ?></td>
                    <td><?php echo htmlspecialchars($doublon['juge1']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Statistiques :</h2>
    <p>Nombre total de matchs uniques : <?php echo count($uniqueMatches); ?></p>
    <p>Nombre total de matchs nécessaires : <?php echo $matchsPossibles; ?></p>



      <!-- Bouton pour rediriger vers corrections.php -->
        <form action="corrections.php" method="get">
        <button type="submit">Afficher les matchs par groupe de 3</button>
    </form>

</main>

<?php require_once(__DIR__ . '/../includes/footer.php'); ?>

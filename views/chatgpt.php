<?php
session_start();
require_once(__DIR__ . '/../includes/db.php');

$conn = getDatabaseConnection();

// Récupérer tous les joueurs avec leurs ID et clubs
$sql = "SELECT id, nom, club FROM joueurs";
$result = $conn->query($sql);

if (!$result) {
    die("Erreur lors de la récupération des joueurs : " . htmlspecialchars($conn->error));
}

$joueurs = [];
while ($row = $result->fetch_assoc()) {
    $joueurs[] = $row; // Stocker chaque joueur avec ses détails
}

// Créer des matchs entre tous les joueurs
$matches = [];
$id_match = 1; // Compteur pour les ID de match
$id_tour = 1;   // ID du tour (peut être incrémenté selon la logique)
$id_count = 1;  // Compteur pour id_count

for ($i = 0; $i < count($joueurs); $i++) {
    for ($j = $i + 1; $j < count($joueurs); $j++) {
        $matches[] = [
            'id_match' => $id_match++,
            'id_tour' => $id_tour,
            'id_count' => $id_count,
            'joueur1' => $joueurs[$i]['nom'],
            'joueur2' => $joueurs[$j]['nom'],
            'club1' => $joueurs[$i]['club'],
            'club2' => $joueurs[$j]['club'],
            'juge' => $joueurs[rand(0, count($joueurs) - 1)]['nom'], // Choisir un juge aléatoire
        ];
        $id_count++; // Incrémenter id_count pour chaque match
    }
}

$conn->close();

// Affichage des matchs
require_once(__DIR__ . '/../includes/header.php'); ?>

<main>
    <h2>Liste des Matchs :</h2>
    <table>
        <thead>
            <tr>
                <th>ID Match</th>
                <th>ID Tour</th>
                <th>ID Count</th>
                <th>Joueur 1</th>
                <th>Club 1</th>
                <th>Joueur 2</th>
                <th>Club 2</th>
                <th>Juge</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($matches as $match): ?>
                <tr>
                    <td><?php echo htmlspecialchars($match['id_match']); ?></td>
                    <td><?php echo htmlspecialchars($match['id_tour']); ?></td>
                    <td><?php echo htmlspecialchars($match['id_count']); ?></td>
                    <td><?php echo htmlspecialchars($match['joueur1']); ?></td>
                    <td><?php echo htmlspecialchars($match['club1']); ?></td>
                    <td><?php echo htmlspecialchars($match['joueur2']); ?></td>
                    <td><?php echo htmlspecialchars($match['club2']); ?></td>
                    <td><?php echo htmlspecialchars($match['juge']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?php require_once(__DIR__ . '/../includes/footer.php'); ?>

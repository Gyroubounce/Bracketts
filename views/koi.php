<?php
// Connexion à la base de données
require_once(__DIR__ . '/../includes/db.php');
$conn = getDatabaseConnection();

// Récupérer tous les joueurs depuis la table joueurs
$sql = "SELECT id, nom, club FROM joueurs ORDER BY id";
$result = $conn->query($sql);

if (!$result) {
    die("Erreur lors de la récupération des joueurs : " . htmlspecialchars($conn->error));
}

$joueurs = [];
while ($row = $result->fetch_assoc()) {
    $joueurs[$row['id']] = [
        'nom' => $row['nom'],
        'club' => $row['club']
    ];
}

// Vérifier s'il y a assez de joueurs (minimum 3 joueurs pour commencer)
$totalPlayers = count($joueurs);
if ($totalPlayers < 3) {
    die("Pas assez de joueurs pour organiser un tournoi.");
}

// Générer toutes les combinaisons de matchs entre joueurs
$matchCombinations = [];
for ($i = 0; $i < $totalPlayers; $i++) {
    for ($j = $i + 1; $j < $totalPlayers; $j++) {
        $matchCombinations[] = [
            'joueur1' => $joueurs[array_keys($joueurs)[$i]],
            'joueur2' => $joueurs[array_keys($joueurs)[$j]],
        ];
    }
}

// Organiser les matchs en sous-tableaux avec identification circulaire
$rounds = [];
$usedPlayers = [];
$totalMatches = count($matchCombinations);
$matchPairs = []; // Pour stocker les matchs par paires

// Créer des paires de matchs (2 par phase)
for ($i = 0; $i < $totalMatches; $i += 2) {
    if (isset($matchCombinations[$i])) {
        $matchPairs[] = [$matchCombinations[$i]];
    }
    if (isset($matchCombinations[$i + 1])) {
        $matchPairs[count($matchPairs) - 1][] = $matchCombinations[$i + 1];
    }
}

// Gérer les sous-tableaux par rounds
foreach ($matchPairs as $pairIndex => $matches) {
    $rounds[] = $matches;
}

// Insérer les matchs dans la table tournois
$id_tournoi = 1; // Remplacez par l'ID de votre tournoi
$id_match = 1;   // Compteur pour les IDs de match

foreach ($rounds as $round) {
    foreach ($round as $match) {
        // Préparez une requête d'insertion
        $stmt = $conn->prepare("INSERT INTO tournois (id_tournoi, id_match, nom_joueur, club, nom_joueur_adverse, club_adverse, juge1, juge1_club) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        // Remplissez les valeurs pour chaque match
        $nom_joueur = $match['joueur1']['nom'];
        $club = $match['joueur1']['club'];
        $nom_joueur_adverse = $match['joueur2']['nom'];
        $club_adverse = $match['joueur2']['club'];
        $juge1 = ''; // Assignez le nom du juge ici
        $juge1_club = ''; // Assignez le club du juge ici

        // Liez les paramètres
        $stmt->bind_param("iissssss", $id_tournoi, $id_match, $nom_joueur, $club, $nom_joueur_adverse, $club_adverse, $juge1, $juge1_club);

        // Exécutez la requête
        if (!$stmt->execute()) {
            echo "Erreur lors de l'insertion : " . htmlspecialchars($stmt->error);
        }

        // Incrémenter l'ID du match
        $id_match++;
    }
}

// Fermer la déclaration
$stmt->close();

session_start();
$_SESSION['rounds'] = $rounds;

require_once(__DIR__ . '/../includes/header.php');
?>

<main>
    <h1>Tournoi Round Robin</h1>
    <p>Nombre total de joueurs : <?= $totalPlayers ?></p>
    <p>Nombre total de matchs : <?= count($matchCombinations) ?></p>

    <!-- Tableau : Toutes les Combinaisons de Matchs -->
    <div class="tableau">
        <h2>Toutes les Combinaisons de Matchs</h2>
        <table>
            <thead>
                <tr>
                    <th>Joueur 1</th>
                    <th>Club 1</th>
                    <th>Joueur 2</th>
                    <th>Club 2</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($matchCombinations as $match): ?>
                    <tr>
                        <td><?= htmlspecialchars($match['joueur1']['nom']) ?></td>
                        <td><?= htmlspecialchars($match['joueur1']['club']) ?></td>
                        <td><?= htmlspecialchars($match['joueur2']['nom']) ?></td>
                        <td><?= htmlspecialchars($match['joueur2']['club']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Tableau : Phases de tournoi avec sous-tableaux de matchs -->
    <div class="tableau">
        <h2>Phases du Tournoi par Rounds</h2>
        <?php foreach ($rounds as $roundIndex => $matches): ?>
            <div class="round">
                <h2>Round <?= ($roundIndex + 1) ?></h2>
                <div class="phase">
                    <table>
                        <thead>
                            <tr>
                                <th>Joueur 1</th>
                                <th>Club 1</th>
                                <th>Joueur 2</th>
                                <th>Club 2</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($matches as $match): ?>
                                <tr>
                                    <td><?= htmlspecialchars($match['joueur1']['nom']) ?></td>
                                    <td><?= htmlspecialchars($match['joueur1']['club']) ?></td>
                                    <td><?= htmlspecialchars($match['joueur2']['nom']) ?></td>
                                    <td><?= htmlspecialchars($match['joueur2']['club']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

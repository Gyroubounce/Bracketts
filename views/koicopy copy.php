<?php
// Connexion à la base de données
require_once(__DIR__ . '/../includes/db.php');
$conn = getDatabaseConnection();

// Récupérer tous les joueurs depuis la table joueurs, incluant le club
$sql = "SELECT id, nom, club FROM joueurs ORDER BY id";
$result = $conn->query($sql);

if (!$result) {
    die("Erreur lors de la récupération des joueurs : " . htmlspecialchars($conn->error));
}

$joueurs = [];
while ($row = $result->fetch_assoc()) {
    $joueurs[$row['id']] = ['nom' => $row['nom'], 'club' => $row['club']];
}

// Vérifier s'il y a assez de joueurs (minimum 9 joueurs pour 3 groupes de 3 par phase)
$totalPlayers = count($joueurs);
if ($totalPlayers < 9) {
    die("Pas assez de joueurs pour organiser un tournoi.");
}

// Fonction pour générer des combinaisons de 3 joueurs
function generatePlayerCombinations($players) {
    $combinations = [];
    $playerIds = array_keys($players);
    $totalPlayers = count($playerIds);

    // Générer des combinaisons de 3 joueurs
    for ($i = 0; $i < $totalPlayers; $i++) {
        for ($j = $i + 1; $j < $totalPlayers; $j++) {
            for ($k = $j + 1; $k < $totalPlayers; $k++) {
                $combinations[] = [$playerIds[$i], $playerIds[$j], $playerIds[$k]]; // Ajoute une combinaison de 3 joueurs
            }
        }
    }
    return $combinations;
}

// Générer toutes les combinaisons de 3 joueurs
$playerCombinations = generatePlayerCombinations($joueurs);
shuffle($playerCombinations); // Mélanger les combinaisons pour les phases

// Diviser les combinaisons en 5 phases, chaque phase ayant 3 joueurs
$phases = array_slice($playerCombinations, 0, 5); // Prendre seulement les 5 premières phases

// Vérifier qu'il y a bien 5 phases
if (count($phases) < 5) {
    echo "Pas assez de combinaisons pour remplir 5 phases.";
    exit;
}
?>
<?php require_once(__DIR__ . '/../includes/header.php'); ?>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            color: #2c3e50;
        }
        h2 {
            color: #2980b9;
        }
        .phase {
            margin-bottom: 20px;
        }
        .match {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .match h3 {
            margin: 0;
        }
    </style>

<main>
    <h1>Tournoi Round Robin avec 5 phases</h1>

    <?php foreach ($phases as $phaseIndex => $phasePlayers): ?>
        <div class="phase">
            <h2>Phase <?= ($phaseIndex + 1) ?></h2>

            <?php
            // Assigner des IDs temporaires (1, 2, 3) pour chaque phase
            $mappedPlayers = [
                1 => $joueurs[$phasePlayers[0]], // Joueur 1
                2 => $joueurs[$phasePlayers[1]], // Joueur 2
                3 => $joueurs[$phasePlayers[2]]  // Joueur 3
            ];

            // Afficher les joueurs de cette phase avec leurs clubs
            echo "<strong>Joueurs :</strong> ";
            echo "1: " . $mappedPlayers[1]['nom'] . " (Club: " . $mappedPlayers[1]['club'] . "), ";
            echo "2: " . $mappedPlayers[2]['nom'] . " (Club: " . $mappedPlayers[2]['club'] . "), ";
            echo "3: " . $mappedPlayers[3]['nom'] . " (Club: " . $mappedPlayers[3]['club'] . ")<br>";
            ?>

            <?php
            // Afficher les 3 matchs 1 vs 2, 2 vs 3 et 3 vs 1
            $matchPairs = [
                [1, 2], // 1 vs 2
                [2, 3], // 2 vs 3
                [3, 1]  // 3 vs 1
            ];

            foreach ($matchPairs as $match) {
                echo "<div class='match'>";
                echo "<h3>Match entre " . $mappedPlayers[$match[0]]['nom'] . " (Club: " . $mappedPlayers[$match[0]]['club'] . ") et " . $mappedPlayers[$match[1]]['nom'] . " (Club: " . $mappedPlayers[$match[1]]['club'] . ")</h3>";
                echo "</div>";
            }
            ?>
        </div>
    <?php endforeach; ?>

</main>

<?php
// Fermer la connexion à la base de données
$conn->close();
require_once(__DIR__ . '/../includes/footer.php');
?>

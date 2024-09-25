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

// Générer toutes les combinaisons de matchs
$matchCombinations = [];
for ($i = 0; $i < $totalPlayers; $i++) {
    for ($j = $i + 1; $j < $totalPlayers; $j++) {
        $matchCombinations[] = [
            'joueur1' => $joueurs[array_keys($joueurs)[$i]],
            'joueur2' => $joueurs[array_keys($joueurs)[$j]],
            'matchID' => count($matchCombinations) + 1
        ];
    }
}

// Calculer le nombre total de matchs
$totalMatches = count($matchCombinations);
$totalPhases = ceil($totalMatches / 3); // Calculer le nombre total de phases en groupes de 3

// Générer les matchs pour chaque phase (pour les groupes de 3)
$phases = [];
for ($i = 0; $i < $totalMatches; $i += 3) {
    $groupMatches = array_slice($matchCombinations, $i, 3);
    if (count($groupMatches) < 3) {
        break; // Ne pas ajouter de phase si moins de 3 matchs
    }
    $phases[] = $groupMatches;
}

// Stocker les phases de trois joueurs dans la session
session_start();
$_SESSION['phasesDeTrois'] = $phases;

require_once(__DIR__ . '/../includes/header.php');
?>

<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 20px;
    }
    h1 {
        color: #2c3e50;
        text-align: center;
    }
    h2 {
        color: #2980b9;
    }
    .container {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
    }
    .tableau {
        width: 100%;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin: 10px 0;
        padding: 15px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    th {
        background-color: #2980b9;
        color: white;
    }
    .button {
        margin: 20px auto;
        padding: 10px 15px;
        background-color: #2980b9;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        display: block;
        width: fit-content;
        text-align: center;
        font-size: 16px;
        transition: background-color 0.3s;
    }
    .button:hover {
        background-color: #1a6b95;
    }
</style>

<main>
    <h1>Tournoi Round Robin</h1>
    <p>Nombre total de joueurs : <?= $totalPlayers ?></p>
    <p>Nombre total de matchs : <?= count($matchCombinations) ?></p>

    <button class="button" onclick="window.location.href='koicopy.php'">Accéder aux Phases de 3 Joueurs</button>

    <div class="tableau">
        <h2>Toutes les Combinaisons de Matchs</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Match</th>
                    <th>Joueur 1</th>
                    <th>Club 1</th>
                    <th>Joueur 2</th>
                    <th>Club 2</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($matchCombinations as $match): ?>
                    <tr>
                        <td><?= $match['matchID'] ?></td>
                        <td><?= htmlspecialchars($match['joueur1']['nom']) ?></td>
                        <td><?= htmlspecialchars($match['joueur1']['club']) ?></td>
                        <td><?= htmlspecialchars($match['joueur2']['nom']) ?></td>
                        <td><?= htmlspecialchars($match['joueur2']['club']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="tableau">
        <h2>Phases du Tournoi</h2>
        <?php foreach ($phases as $phaseIndex => $phaseMatches): ?>
            <div class="phase">
                <h2>Phase <?= ($phaseIndex + 1) ?></h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID Match</th>
                            <th>Joueur 1</th>
                            <th>Club 1</th>
                            <th>Joueur 2</th>
                            <th>Club 2</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($phaseMatches as $match): ?>
                            <tr>
                                <td><?= $match['matchID'] ?></td>
                                <td><?= htmlspecialchars($match['joueur1']['nom']) ?></td>
                                <td><?= htmlspecialchars($match['joueur1']['club']) ?></td>
                                <td><?= htmlspecialchars($match['joueur2']['nom']) ?></td>
                                <td><?= htmlspecialchars($match['joueur2']['club']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php require_once(__DIR__ . '/../includes/footer.php'); ?>

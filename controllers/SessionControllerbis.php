<?php
// Connexion à la base de données
require_once(__DIR__ . '/../includes/db.php');

// Récupérer le classement depuis la table T2_classements
$sql = "SELECT * FROM T2_classements ORDER BY matchs_gagnes DESC, points_gagnes DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Erreur lors de la récupération du classement : " . $conn->error);
}

// Récupérer les données dans un tableau
$joueurs = [];
while ($row = $result->fetch_assoc()) {
    $joueurs[] = $row;
}

$nombre_joueurs = count($joueurs);

// Fonction pour générer les matchs
function genererMatchs($joueurs) {
    $matchs = [];
    $nombre_joueurs = count($joueurs);
    $match_count = 1;

    // Mélanger aléatoirement les joueurs
    shuffle($joueurs);

    for ($i = 0; $i < $nombre_joueurs; $i++) {
        for ($j = $i + 1; $j < $nombre_joueurs; $j++) {
            $joueur = $joueurs[$i];
            $adversaire = $joueurs[$j];
            $juge = $joueurs[($j + 1) % $nombre_joueurs]; // Choisir un joueur comme juge

            $matchs[] = [
                'id_match' => $match_count++,
                'nom_joueur' => $joueur['nom'],
                'club' => $joueur['club'],
                'nom_joueur_adverse' => $adversaire['nom'],
                'club_adverse' => $adversaire['club'],
                'juge' => $juge['nom'],
                'juge_club' => $juge['club']
            ];
        }
    }

    return $matchs;
}

// Fonction pour diviser le tableau en sous-tableaux de 3 matchs chacun
function diviserEnTableaux($matchs, $taille) {
    $resultat = [];
    $total_matchs = count($matchs);
    for ($i = 0; $i < $total_matchs; $i += $taille) {
        $resultat[] = array_slice($matchs, $i, $taille);
    }
    return $resultat;
}

// Insérer les matchs dans la table tournois
$id_tournoi = 1; // Vous pouvez ajuster ou générer dynamiquement cet ID si nécessaire

$matchs = genererMatchs($joueurs);

foreach ($matchs as $match) {
    $stmt = $conn->prepare("
        INSERT INTO tournois (id_tournoi, id_match, nom_joueur, club, nom_joueur_adverse, club_adverse, juge1, juge1_club)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "iissssss",
        $id_tournoi,
        $match['id_match'],
        $match['nom_joueur'],
        $match['club'],
        $match['nom_joueur_adverse'],
        $match['club_adverse'],
        $match['juge'],
        $match['juge_club']
    );

    if (!$stmt->execute()) {
        die("Erreur lors de l'insertion des données : " . $stmt->error);
    }
}

// Fonction pour regrouper les matchs par ensembles de joueurs et juge
function regrouperParEnsemble($matchs) {
    $groupes = [];

    foreach ($matchs as $match) {
        $joueurs_et_juge = [
            $match['nom_joueur'],
            $match['nom_joueur_adverse'],
            $match['juge']
        ];
        sort($joueurs_et_juge);
        $key = implode('-', $joueurs_et_juge);

        if (!isset($groupes[$key])) {
            $groupes[$key] = [];
        }

        $groupes[$key][] = $match;
    }

    return $groupes;
}

$matchs_divises = diviserEnTableaux($matchs, 3);
?>

<?php require_once(__DIR__ . '/../includes/header.php'); ?>

<main>
    <h1>Tableau Éliminatoire Round Robin</h1>

    <?php foreach ($matchs_divises as $index => $sous_tableau): ?>
        <h2>Tableau <?php echo $index + 1; ?></h2>
        <table>
            <thead>
                <tr>
                    <th>ID Match</th>
                    <th>Nom Joueur</th>
                    <th>Club Joueur</th>
                    <th>Nom Joueur Adverse</th>
                    <th>Club Joueur Adverse</th>
                    <th>Juge</th>
                    <th>Juge Club</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sous_tableau as $match): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($match['id_match']); ?></td>
                        <td><?php echo htmlspecialchars($match['nom_joueur']); ?></td>
                        <td><?php echo htmlspecialchars($match['club']); ?></td>
                        <td><?php echo htmlspecialchars($match['nom_joueur_adverse']); ?></td>
                        <td><?php echo htmlspecialchars($match['club_adverse']); ?></td>
                        <td><?php echo htmlspecialchars($match['juge']); ?></td>
                        <td><?php echo htmlspecialchars($match['juge_club']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>

    <form action="/bracketts/controllers/groupe_J.php" method="get">
        <input type="submit" value="Voir les groupes de 3 joueurs">
    </form>
</main>

<?php
// Fermer la connexion à la base de données
$conn->close();
require_once(__DIR__ . '/../includes/footer.php');
?>

<?php
session_start();
require_once(__DIR__ . '/../includes/db.php');

$conn = getDatabaseConnection();

// Récupérer tous les matchs
$sql = "SELECT * FROM tournoi_impairs ORDER BY id_tournoi, id_match";
$result = $conn->query($sql);

if (!$result) {
    die("Erreur lors de la récupération des matchs : " . htmlspecialchars($conn->error));
}

$matches = [];
while ($row = $result->fetch_assoc()) {
    $matches[] = $row;
}

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

// Organiser les matchs uniques par tour
$matchGroups = [];
foreach ($uniqueMatches as $match) {
    $tour = $match['id_tournoi'];

    if (!isset($matchGroups[$tour])) {
        $matchGroups[$tour] = [];
    }

    $matchGroups[$tour][] = $match;
}

// Compter le nombre total de matchs uniques
$totalUniqueMatches = count($uniqueMatches);

// Récupérer tous les joueurs avec leurs ID et clubs
$joueurs = [];
$sqlJoueurs = "SELECT id, nom, club FROM joueurs";
$resultJoueurs = $conn->query($sqlJoueurs);

if (!$resultJoueurs) {
    die("Erreur lors de la récupération des joueurs : " . htmlspecialchars($conn->error));
}

while ($row = $resultJoueurs->fetch_assoc()) {
    $joueurs[$row['nom']] = ['id' => $row['id'], 'club' => $row['club']];
}

// Générer les matchs manquants
$matchesManquants = [];
for ($i = 0; $i < count($joueurs); $i++) {
    $joueur1 = array_keys($joueurs)[$i];
    for ($j = $i + 1; $j < count($joueurs); $j++) {
        $joueur2 = array_keys($joueurs)[$j];
        $key = implode('|', [$joueur1, $joueur2]);

        if (!isset($uniqueMatches[$key]) && !isset($uniqueMatches[implode('|', [$joueur2, $joueur1])])) {
            // Match manquant
            $matchesManquants[] = [
                'nom_joueur' => $joueur1,
                'nom_joueur_adverse' => $joueur2,
                'club' => $joueurs[$joueur1]['club'], // Récupérer le club
                'club_adverse' => $joueurs[$joueur2]['club'], // Récupérer le club adverse
                'juge1' => 'À déterminer', // À remplir plus tard
                'id_joueur' => $joueurs[$joueur1]['id'], // Récupérer l'ID
                'id_joueur_adverse' => $joueurs[$joueur2]['id'], // Récupérer l'ID adverse
            ];
        }
    }
}

$conn->close();

// Affichage des matchs
require_once(__DIR__ . '/../includes/header.php'); ?>

<main>
    <h2>Matchs par Tour (sans doublons) :</h2>
    <p>Nombre total de matchs uniques : <?php echo $totalUniqueMatches; ?></p>
    <?php 
    $globalId = 1; // Initialiser l'ID global
    foreach ($matchGroups as $tour => $matches): ?>
        <h3>Tour <?php echo htmlspecialchars($tour); ?></h3>
        <table>
            <thead>
                <tr>
                    <th>ID Match</th>
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
                        <td><?php echo htmlspecialchars($globalId); ?></td> <!-- Afficher l'ID global -->
                        <td><?php echo htmlspecialchars($match['id_match']); ?></td>
                        <td><?php echo htmlspecialchars($match['nom_joueur']); ?></td>
                        <td><?php echo htmlspecialchars($match['club']); ?></td>
                        <td><?php echo htmlspecialchars($match['nom_joueur_adverse']); ?></td>
                        <td><?php echo htmlspecialchars($match['club_adverse']); ?></td>
                        <td><?php echo htmlspecialchars($match['juge1']); ?></td>
                    </tr>
                    <?php $globalId++; // Incrémenter l'ID global ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>

    <h2>Matchs Manquants :</h2>

<h3>Liste des Joueurs dans les Matchs Manquants :</h3>
<ul>
    <?php
    // Utiliser un tableau pour éviter les doublons
    $joueursManquants = [];
    foreach ($matchesManquants as $matchManquant) {
        $joueursManquants[$matchManquant['nom_joueur']] = [
            'club' => $matchManquant['club'],
        ];
        $joueursManquants[$matchManquant['nom_joueur_adverse']] = [
            'club' => $matchManquant['club_adverse'],
        ];
    }

    // Afficher la liste des joueurs manquants
    if (!empty($joueursManquants)) {
        foreach (array_keys($joueursManquants) as $joueurNom): ?>
            <li><?php echo htmlspecialchars($joueurNom) . " (" . htmlspecialchars($joueursManquants[$joueurNom]['club']) . ")"; ?></li>
        <?php endforeach;
    } else {
        echo "<li>Aucun joueur disponible.</li>";
    }
    ?>
</ul>

<table>
    <thead>
        <tr>
            <th>ID Joueur</th>
            <th>Nom Joueur</th>
            <th>Club</th>
            <th>ID Joueur Adverse</th>
            <th>Nom Joueur Adverse</th>
            <th>Club Adverse</th>
            <th>Juge</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($matchesManquants)): ?>
            <?php foreach ($matchesManquants as $matchManquant): ?>
                <?php
                // Sélectionner un juge parmi les joueurs manquants
                $joueur1 = $matchManquant['nom_joueur'];
                $joueur2 = $matchManquant['nom_joueur_adverse'];
                $jugeNom = '';
                $jugeClub = '';

                // Choisir un juge qui n'est pas impliqué dans le match
                foreach ($joueursManquants as $joueurNom => $details) {
                    if ($joueurNom !== $joueur1 && $joueurNom !== $joueur2) {
                        $jugeNom = htmlspecialchars($joueurNom);
                        $jugeClub = htmlspecialchars($details['club']);
                        break; // On prend le premier joueur disponible comme juge
                    }
                }
                ?>

                <tr>
                    <td><?php echo htmlspecialchars($matchManquant['id_joueur']); ?></td>
                    <td><?php echo htmlspecialchars($matchManquant['nom_joueur']); ?></td>
                    <td><?php echo htmlspecialchars($matchManquant['club']); ?></td>
                    <td><?php echo htmlspecialchars($matchManquant['id_joueur_adverse']); ?></td>
                    <td><?php echo htmlspecialchars($matchManquant['nom_joueur_adverse']); ?></td>
                    <td><?php echo htmlspecialchars($matchManquant['club_adverse']); ?></td>
                    <td><?php echo htmlspecialchars($jugeNom) . " (" . $jugeClub . ")"; ?></td> <!-- Afficher le juge avec son club -->
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">Aucun match manquant.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>


</main>

<?php require_once(__DIR__ . '/../includes/footer.php'); ?>

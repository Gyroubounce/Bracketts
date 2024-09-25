<?php
// Connexion à la base de données
require_once(__DIR__ . '/../includes/db.php');

// Récupérer les joueurs depuis la base de données
$sql = "SELECT * FROM joueurs ORDER BY id";
$result = $conn->query($sql);

if (!$result) {
    die("Erreur lors de la récupération des joueurs : " . $conn->error);
}

// Récupérer les données dans un tableau
$joueurs = [];
while ($row = $result->fetch_assoc()) {
    $joueurs[] = $row;
}

$nombre_joueurs = count($joueurs);

// Fonction pour générer tous les matchs entre joueurs
function genererMatchs($joueurs) {
    $matchs = [];
    $id_match = 1;
    $id_groupe_map = [];

    foreach ($joueurs as $j1) {
        if (!isset($id_groupe_map[$j1['id']])) {
            $id_groupe_map[$j1['id']] = 1;
        }

        foreach ($joueurs as $j2) {
            if ($j1['id'] < $j2['id']) {
                $matchs[] = [
                    'id_match' => $id_match++,
                    'id_groupe' => $id_groupe_map[$j1['id']],
                    'id_joueur1' => $j1['id'],
                    'nom_joueur' => $j1['nom'],
                    'club' => $j1['club'],
                    'id_joueur_adverse' => $j2['id'],
                    'nom_joueur_adverse' => $j2['nom'],
                    'club_adverse' => $j2['club']
                ];

                $id_groupe_map[$j1['id']]++;
            }
        }
    }

    // Trier les matchs par id_groupe
    usort($matchs, fn($a, $b) => $a['id_groupe'] <=> $b['id_groupe']);

    return $matchs;
}

// Diviser les matchs en groupes de 3 sans répétition des joueurs dans le même tour
function diviserEnGroupes($matchs) {
    $groupes = [];
    $current_group = [];
    $joueurs_utilises = [];

    foreach ($matchs as $match) {
        $j1 = $match['id_joueur1'];
        $j2 = $match['id_joueur_adverse'];

        // Vérifier si les joueurs sont déjà utilisés dans le groupe actuel
        if (!in_array($j1, $joueurs_utilises) && !in_array($j2, $joueurs_utilises)) {
            $current_group[] = $match;
            $joueurs_utilises[] = $j1;
            $joueurs_utilises[] = $j2;
        }

        // Si le groupe atteint 3 matchs, on l'ajoute à la liste des groupes
        if (count($current_group) === 3) {
            $groupes[] = $current_group;
            $current_group = [];
            $joueurs_utilises = []; // Réinitialiser pour le prochain groupe
        }
    }

    // Ajouter le reste du groupe si nécessaire
    if (count($current_group) > 0) {
        $groupes[] = $current_group;
    }

    return $groupes;
}

// Fonction pour générer les tours de matchs
function genererTours($groupes_de_matchs) {
    $tours = [];
    $tour_num = 1;

    while (!empty($groupes_de_matchs)) {
        $tour = array_splice($groupes_de_matchs, 0, 3);
        $tours["Tour $tour_num"] = $tour;
        $tour_num++;
    }

    return $tours;
}

// Fonction pour ajouter des juges à chaque match
function ajouterJuges($tours) {
    foreach ($tours as &$tour) {
        $juges_utilises = [];

        foreach ($tour as &$table) {
            $joueurs_table = array_merge(
                array_column($table, 'id_joueur1'),
                array_column($table, 'id_joueur_adverse')
            );

            // Vérifier les juges disponibles
            $juges_disponibles = array_diff(array_column($GLOBALS['joueurs'], 'id'), $joueurs_table, $juges_utilises);

            foreach ($table as &$match) {
                if (!empty($juges_disponibles)) {
                    $id_juge = array_pop($juges_disponibles);
                    $match['id_juge'] = $id_juge;
                    $match['juge'] = $GLOBALS['joueurs'][array_search($id_juge, array_column($GLOBALS['joueurs'], 'id'))]['nom'];
                    $match['juge_club'] = $GLOBALS['joueurs'][array_search($id_juge, array_column($GLOBALS['joueurs'], 'id'))]['club'];
                    $juges_utilises[] = $id_juge;
                } else {
                    $match['id_juge'] = 'N/A';
                    $match['juge'] = 'N/A';
                    $match['juge_club'] = 'N/A';
                }
            }
        }
    }
    return $tours;
}

// Générer les matchs
$matchs = genererMatchs($joueurs);

// Diviser les matchs en groupes de 3 sans répétition des joueurs
$groupes_de_matchs = diviserEnGroupes($matchs);

// Générer les tours
$tours = genererTours($groupes_de_matchs);

// Ajouter des juges à chaque match
$tours = ajouterJuges($tours);

?>

<?php require_once(__DIR__ . '/../includes/header.php'); ?>

<main>
    <h1>Tournoi Round Robin</h1>

    <p>Nombre total de joueurs : <?php echo $nombre_joueurs; ?></p>
    <p>Nombre total de matchs : <?php echo count($matchs); ?></p>

    <?php
    // Affichage des tours
    foreach ($tours as $tour_nom => $tour) {
        echo "<h2>$tour_nom</h2>";

        foreach ($tour as $index => $table) {
            echo "<h3>Table " . ($index + 1) . "</h3>";
            echo "<table>
                <thead>
                    <tr>
                        <th>ID Match</th>
                        <th>ID Groupe</th>
                        <th>ID Joueur 1</th>
                        <th>Nom Joueur</th>
                        <th>Club Joueur</th>
                        <th>ID Joueur Adverse</th>
                        <th>Nom Joueur Adverse</th>
                        <th>Club Joueur Adverse</th>
                        <th>ID Juge</th>
                        <th>Juge</th>
                        <th>Juge Club</th>
                    </tr>
                </thead>
                <tbody>";
            foreach ($table as $match) {
                echo "<tr>
                    <td>" . htmlspecialchars($match['id_match']) . "</td>
                    <td>" . htmlspecialchars($match['id_groupe']) . "</td>
                    <td>" . htmlspecialchars($match['id_joueur1']) . "</td>
                    <td>" . htmlspecialchars($match['nom_joueur']) . "</td>
                    <td>" . htmlspecialchars($match['club']) . "</td>
                    <td>" . htmlspecialchars($match['id_joueur_adverse']) . "</td>
                    <td>" . htmlspecialchars($match['nom_joueur_adverse']) . "</td>
                    <td>" . htmlspecialchars($match['club_adverse']) . "</td>
                    <td>" . htmlspecialchars($match['id_juge']) . "</td>
                    <td>" . htmlspecialchars($match['juge']) . "</td>
                    <td>" . htmlspecialchars($match['juge_club']) . "</td>
                </tr>";
            }
            echo "</tbody></table>";
        }
    }
    ?>
</main>

<?php
// Fermer la connexion à la base de données
$conn->close();
require_once(__DIR__ . '/../includes/footer.php');
?>

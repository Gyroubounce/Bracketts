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

    foreach ($joueurs as $j1) {
        foreach ($joueurs as $j2) {
            if ($j1['id'] < $j2['id']) {
                $matchs[] = [
                    'id_match' => $id_match++,
                    'id_joueur1' => $j1['id'],
                    'nom_joueur' => $j1['nom'],
                    'club' => $j1['club'],
                    'id_joueur_adverse' => $j2['id'],
                    'nom_joueur_adverse' => $j2['nom'],
                    'club_adverse' => $j2['club']
                ];
            }
        }
    }

    return $matchs;
}

// Diviser les matchs en groupes de 3
function diviserEnGroupes($matchs) {
    $groupes = [];
    $current_group = [];

    foreach ($matchs as $match) {
        $current_group[] = $match;

        // Si le groupe atteint 3 matchs, on l'ajoute à la liste des groupes
        if (count($current_group) === 3) {
            $groupes[] = $current_group;
            $current_group = []; // Réinitialiser pour le prochain groupe
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
function ajouterJuges($tours, $joueurs) {
    foreach ($tours as &$tour) {
        foreach ($tour as &$table) {
            $joueurs_table = [$table[0]['id_joueur1'], $table[0]['id_joueur_adverse']];
            $juges_disponibles = array_diff(array_column($joueurs, 'id'), $joueurs_table);

            foreach ($table as &$match) {
                if (!empty($juges_disponibles)) {
                    $id_juge = array_pop($juges_disponibles);
                    $match['id_juge'] = $id_juge;
                    $match['juge'] = $joueurs[array_search($id_juge, array_column($joueurs, 'id'))]['nom'];
                    $match['juge_club'] = $joueurs[array_search($id_juge, array_column($joueurs, 'id'))]['club'];
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

// Fonction pour reclasser les tables sans joueurs dupliqués
function reclasserTables($tours) {
    $sessions = [];
    $session_id = 1;

    foreach ($tours as $tour_nom => $tour) {
        foreach ($tour as $table) {
            $joueurs_assignes = [];
            foreach ($table as $match) {
                $joueurs_assignes[] = $match['id_joueur1'];
                $joueurs_assignes[] = $match['id_joueur_adverse'];
            }

            // Créer une nouvelle session si les joueurs ne sont pas déjà présents
            if (!isset($sessions["Session $session_id"]) || 
                empty(array_intersect($joueurs_assignes, array_column($sessions["Session $session_id"], 'id_joueur1')))) {
                $sessions["Session $session_id"][] = $table;
            } else {
                $session_id++;
                $sessions["Session $session_id"][] = $table;
            }
        }
    }

    return $sessions;
}

// Générer les matchs
$matchs = genererMatchs($joueurs);

// Diviser les matchs en groupes de 3
$groupes_de_matchs = diviserEnGroupes($matchs);

// Générer les tours
$tours = genererTours($groupes_de_matchs);

// Ajouter des juges à chaque match
$tours = ajouterJuges($tours, $joueurs);

// Reclasser les tables
$sessions = reclasserTables($tours);

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

    // Affichage des sessions
    foreach ($sessions as $session_nom => $tables) {
        echo "<h2>$session_nom</h2>";
        foreach ($tables as $index => $table) {
            echo "<h3>Table " . ($index + 1) . "</h3>";
            echo "<table>
                <thead>
                    <tr>
                        <th>ID Match</th>
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

<?php require_once(__DIR__ . '/../includes/footer.php'); ?>

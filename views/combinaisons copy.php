<?php
// Récupérer la liste des joueurs envoyée depuis liste_joueurs.php
if (isset($_POST['joueurs'])) {
    $joueurs = unserialize($_POST['joueurs']);
} else {
    die("Erreur : pas de joueurs reçus.");
}

// Calculer le nombre total de joueurs
$total_joueurs = count($joueurs);

// Fonction pour générer toutes les combinaisons uniques de 2 joueurs (round-robin)
function combinaisonsDeDeux($joueurs) {
    $combinations = [];
    $total_joueurs = count($joueurs);

    for ($i = 0; $i < $total_joueurs - 1; $i++) {
        for ($j = $i + 1; $j < $total_joueurs; $j++) {
            $combinations[] = [$joueurs[$i], $joueurs[$j]];
        }
    }

    return $combinations;
}

// Fonction pour générer les matchs avec des juges répartis équitablement
function genererMatchs($joueurs) {
    $matchs = [];
    $total_joueurs = count($joueurs);
    $combinaisons = combinaisonsDeDeux($joueurs);

    // Assigner chaque joueur comme juge de manière équitable
    $juge_counts = array_fill_keys(array_column($joueurs, 'id'), 0);
    $juge_index = 0;
    $juge_ids = array_column($joueurs, 'id');

    foreach ($combinaisons as $combinaison) {
        list($joueur1, $joueur2) = $combinaison;

        // Trouver un joueur qui n'est ni joueur1 ni joueur2 pour être le juge
        $juge = null;
        for ($i = 0; $i < $total_joueurs; $i++) {
            $possible_juge_id = $juge_ids[$juge_index];
            $juge_index = ($juge_index + 1) % $total_joueurs;
            
            if ($possible_juge_id !== $joueur1['id'] && $possible_juge_id !== $joueur2['id']) {
                $juge = $possible_juge_id;
                break;
            }
        }

        // Assigner le juge et mettre à jour les compteurs
        if ($juge) {
            $juge_counts[$juge]++;
            $matchs[] = [
                'id_match' => count($matchs) + 1,
                'joueur1' => $joueur1['id'],
                'joueur2' => $joueur2['id'],
                'juge' => $juge
            ];
        }
    }

    return [$matchs, $juge_counts];
}

// Générer les matchs en utilisant la fonction genererMatchs
list($matchs, $juge_counts) = genererMatchs($joueurs);

// Calculer le nombre de matchs générés
$total_matchs = count($matchs);
?>

<?php require_once(__DIR__ . '/../includes/header.php'); ?>

<main>
    <h1>Combinaisons de matchs Round-Robin</h1>

    <p>Total de joueurs : <?php echo htmlspecialchars($total_joueurs); ?></p>
    <p>Total de matchs générés : <?php echo htmlspecialchars($total_matchs); ?></p>

    <table>
        <thead>
            <tr>
                <th>ID Match</th>
                <th>ID Joueur 1</th>
                <th>Nom Joueur 1</th>
                <th>ID Joueur 2</th>
                <th>Nom Joueur 2</th>
                <th>ID Juge</th>
                <th>Nom Juge</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($matchs as $match): ?>
                <tr>
                    <td><?php echo htmlspecialchars($match['id_match']); ?></td>
                    <td><?php echo htmlspecialchars($match['joueur1']); ?></td>
                    <td><?php echo htmlspecialchars($joueurs[array_search($match['joueur1'], array_column($joueurs, 'id'))]['nom']); ?></td>
                    <td><?php echo htmlspecialchars($match['joueur2']); ?></td>
                    <td><?php echo htmlspecialchars($joueurs[array_search($match['joueur2'], array_column($joueurs, 'id'))]['nom']); ?></td>
                    <td><?php echo htmlspecialchars($match['juge']); ?></td>
                    <td><?php echo htmlspecialchars($joueurs[array_search($match['juge'], array_column($joueurs, 'id'))]['nom']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Nombre de fois que chaque joueur a été juge</h2>
    <table>
        <thead>
            <tr>
                <th>ID Joueur</th>
                <th>Nom Joueur</th>
                <th>Nombre de fois comme juge</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($joueurs as $joueur): ?>
                <tr>
                    <td><?php echo htmlspecialchars($joueur['id']); ?></td>
                    <td><?php echo htmlspecialchars($joueur['nom']); ?></td>
                    <td><?php echo htmlspecialchars($juge_counts[$joueur['id']]); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?php require_once(__DIR__ . '/../includes/footer.php'); ?>

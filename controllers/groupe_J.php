<?php
// Connexion à la base de données
require_once(__DIR__ . '/../includes/db.php');

// Récupérer les matchs depuis la table tournois
$sql = "SELECT * FROM tournois";
$result = $conn->query($sql);

if (!$result) {
    die("Erreur lors de la récupération des matchs : " . $conn->error);
}

// Récupérer les données dans un tableau
$matchs = [];
while ($row = $result->fetch_assoc()) {
    $matchs[] = $row;
}

// Fonction pour regrouper les matchs où les joueurs et le juge sont les mêmes
function regrouperParEnsemble($matchs) {
    $groupes = [];
    
    foreach ($matchs as $match) {
        $key = implode('-', [
            $match['nom_joueur'],
            $match['nom_joueur_adverse'],
            $match['juge1'] // Assurez-vous que ce champ existe
        ]);
        
        if (!isset($groupes[$key])) {
            $groupes[$key] = [];
        }
        
        $groupes[$key][] = $match;
    }
    
    // Filtrer pour ne garder que les groupes avec exactement 3 matchs
    return array_filter($groupes, function($groupe) {
        return count($groupe) == 3;
    });
}

// Regrouper les matchs
$groupes = regrouperParEnsemble($matchs);

?>

<?php require_once(__DIR__ . '/../includes/header.php'); ?>

<main>
    <h1>Groupes de 3 Joueurs</h1>

    <?php if (!empty($groupes)): ?>
        <?php foreach ($groupes as $key => $sous_tableau): ?>
            <h2>Ensemble : <?php echo htmlspecialchars($key); ?></h2>
            <?php if (count($sous_tableau) > 0): ?>
                <?php
                // Diviser les sous-tableaux en groupes de 3 matchs
                $sous_tableaux_divises = diviserEnTableaux($sous_tableau, 3);
                ?>
                <?php foreach ($sous_tableaux_divises as $index => $tableau_divise): ?>
                    <h3>Tableau <?php echo $index + 1; ?></h3>
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
                            <?php foreach ($tableau_divise as $match): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($match['id_match']); ?></td>
                                    <td><?php echo htmlspecialchars($match['nom_joueur']); ?></td>
                                    <td><?php echo htmlspecialchars($match['club']); ?></td>
                                    <td><?php echo htmlspecialchars($match['nom_joueur_adverse']); ?></td>
                                    <td><?php echo htmlspecialchars($match['club_adverse']); ?></td>
                                    <td><?php echo htmlspecialchars($match['juge1']); ?></td>
                                    <td><?php echo htmlspecialchars($match['juge1_club']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun match trouvé pour cet ensemble.</p>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucun groupe de matchs trouvé.</p>
    <?php endif; ?>
</main>

<?php
// Fermer la connexion à la base de données
$conn->close();
require_once(__DIR__ . '/../includes/footer.php');
?>

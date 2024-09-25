<?php
session_start();
require_once(__DIR__ . '/../includes/db.php');

// Connexion à la base de données
$conn = getDatabaseConnection();

// Vérifier la connexion
if (!$conn) {
    die('Échec de la connexion à la base de données : ' . htmlspecialchars(mysqli_connect_error()));
}

// Initialiser la variable $groupes
$groupes = array(); // Assurez-vous que $groupes est toujours un tableau

// Vérifier si la session contient les tours ou récupérer les tours de la base de données
if (isset($_SESSION['tours_session3'])) {
    $tours = $_SESSION['tours_session3'];
} else {
    $tours = array();
    $sql = "SELECT * FROM tournoi_impairs";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $tours = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        echo 'Aucun tournoi en cours.';
        $conn->close();
        exit;
    }
}

// Fonction pour réinitialiser les tours
function reinitialiserTours() {
    global $conn;  // Utiliser la connexion existante

    // Réinitialisation complète de la table tournoi_impairs
    $sql = "DELETE FROM tournoi_impairs";
    if ($conn->query($sql) === TRUE) {
        echo "Table tournoi_impairs réinitialisée avec succès.";
    } else {
        echo "Erreur lors de la réinitialisation de la table tournoi_impairs : " . $conn->error;
    }

    // Réinitialiser les données en session
    $_SESSION['tours_impairs'] = array();
}

// Vérifier si le formulaire de réinitialisation est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_tours'])) {
    reinitialiserTours();
    // Redirection vers la page actuelle pour éviter la soumission multiple du formulaire
    header("Location: {$_SERVER['PHP_SELF']}");
    exit;
}

// Vérifier si le formulaire de création de session est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_next_session'])) {
    // Récupérer le prochain id_count à partir de la base de données
    $sql = "SELECT MAX(id_count) AS max_id_count FROM tournoi_impairs";
    $result = $conn->query($sql);
    
    if ($result) {
        $row = $result->fetch_assoc();
        $next_id_count = $row['max_id_count'] + 1; // Déterminer le prochain id_count
    } else {
        $next_id_count = 1; // Valeur par défaut si aucune entrée n'existe
    }

    // Stocker le prochain id_count dans la session
    $_SESSION['next_id_count'] = $next_id_count;

    // Redirection vers T2_SessionController.php
    header("Location: /bracketts/controllers/T2_SessionController.php");
    exit;
}

// Grouper les matchs par groupe de 3 joueurs
function grouperParTour($tours) {
    $groupes = array();
    foreach ($tours as $match) {
        $id_tournoi = $match['id_tournoi'];
        if (!isset($groupes[$id_tournoi])) {
            $groupes[$id_tournoi] = array();
        }
        $groupes[$id_tournoi][] = $match;
    }
    return $groupes;
}

// Appeler la fonction pour grouper les tours
$groupes = grouperParTour($tours);

// Récupérer les informations sur les joueurs
$sql = "SELECT COUNT(*) AS total_joueurs FROM joueurs";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_joueurs = $row['total_joueurs'];

// Calculer le nombre total de matchs
$total_matches = ($total_joueurs * ($total_joueurs - 1)) / 2;
$matches_generated = count($tours);

// Récupérer le dernier id_count à partir de la base de données
$sql = "SELECT MAX(id_count) AS max_id_count FROM tournoi_impairs";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$last_id_count = $row['max_id_count'];

// Calculer le nombre de matchs restants
$matches_remaining = $total_matches - $last_id_count;

// Calculer le nombre de tours générés
$number_of_tours = count($groupes);

// Identifier les joueurs qui ont déjà joué
$joueurs_deja_joues = [];
foreach ($tours as $match) {
    $joueurs_deja_joues[] = $match['nom_joueur'];
    $joueurs_deja_joues[] = $match['nom_joueur_adverse'];
    $joueurs_deja_joues[] = $match['nom_joueur_impair'];
}
$joueurs_deja_joues = array_unique($joueurs_deja_joues);

?>

<!-- Header.php -->
<?php require_once(__DIR__ . '/../includes/header.php'); ?>

<main>
    <h1>Éliminatoire Tour 3</h1>
    <!-- Affichage des informations sur les matchs -->
    <div>
        <h2>Informations sur les matchs</h2>
        <p>Total de matchs : <?php echo htmlspecialchars($total_matches); ?></p>
        <p>Nombre de matchs générés : <?php echo htmlspecialchars($matches_generated); ?></p>
        <p>Nombre de matchs restants : <?php echo htmlspecialchars($matches_remaining); ?></p>
    </div>

    <?php if (!empty($groupes)): ?>
        <?php foreach ($groupes as $id_tournoi => $matches): ?>
            <h2 class="clickable-header" onclick="window.location.href='T3_match.php?id_tournoi=<?php echo htmlspecialchars($id_tournoi); ?>'">
                Groupe <?php echo htmlspecialchars($id_tournoi); ?>
            </h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Count</th>
                        <th>Match ID</th>
                        <th>Nom Joueur</th>
                        <th>Club</th>
                        <th>Nom Joueur Adverse</th>
                        <th>Club Adverse</th>
                        <th>Nom Joueur Impair</th>
                        <th>Club Impair</th>
                        <th>Juge</th>
                        <th>Club Juge</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matches as $match): ?>
                        <tr onclick="window.location.href='match_individuel.php?id_match=<?php echo htmlspecialchars($match['id_match']); ?>'">
                            <td><?php echo htmlspecialchars($match['id_count']); ?></td>
                            <td><?php echo htmlspecialchars($match['id_match']); ?></td>
                            <td><?php echo htmlspecialchars($match['nom_joueur']); ?></td>
                            <td><?php echo htmlspecialchars($match['club']); ?></td>
                            <td><?php echo htmlspecialchars($match['nom_joueur_adverse']); ?></td>
                            <td><?php echo htmlspecialchars($match['club_adverse']); ?></td>
                            <td><?php echo htmlspecialchars($match['nom_joueur_impair']); ?></td>
                            <td><?php echo htmlspecialchars($match['club_impair']); ?></td>
                            <td>
                                <?php 
                                // Assigner un juge
                                if (in_array($match['nom_joueur'], $joueurs_deja_joues)) {
                                    echo htmlspecialchars($match['nom_joueur']); // Utiliser le joueur qui a déjà joué comme juge
                                } else {
                                    echo 'N/A'; // Pas de juge disponible
                                }
                                ?>
                            </td>
                            <td>
                                <?php 
                                // Assigner le club du juge
                                if (in_array($match['nom_joueur'], $joueurs_deja_joues)) {
                                    echo htmlspecialchars($match['club']); // Utiliser le club du joueur qui a déjà joué
                                } else {
                                    echo 'N/A'; // Pas de club de juge disponible
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucun tournoi n'est actuellement en cours.</p>
    <?php endif; ?>

    <!-- Formulaire pour réinitialiser les tours -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input type="submit" name="reset_tours" value="Réinitialiser">
    </form>

    <!-- Formulaire pour créer la prochaine session -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input type="submit" name="create_next_session" value="Créer la prochaine session">
    </form>
</main>

<?php require_once(__DIR__ . '/../includes/footer.php'); ?>

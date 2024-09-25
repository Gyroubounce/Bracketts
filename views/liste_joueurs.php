<?php
// Connexion à la base de données
require_once(__DIR__ . '/../includes/db.php');
$conn = getDatabaseConnection(); // Assurez-vous que cette fonction est correctement définie

// Préparer la requête pour obtenir les joueurs inscrits
$sql = "SELECT * FROM joueurs";
$result = $conn->query($sql);

// Vérifier si la requête a échoué
if (!$result) {
    die("Erreur lors de la récupération des joueurs : " . htmlspecialchars($conn->error));
}

// Stocker tous les joueurs dans un tableau $joueurs
$joueurs = [];
while ($row = $result->fetch_assoc()) {
    $joueurs[] = $row;
}

// Compter le nombre total de joueurs
$total_joueurs = count($joueurs);

// Calcul du nombre de matchs pour un tournoi Round Robin
$nombre_matchs = ($total_joueurs * ($total_joueurs - 1)) / 2;

?>

<?php require_once(__DIR__ . '/../includes/header.php'); ?>

<main>
    <h1>Liste des joueurs</h1>

    <!-- Afficher le nombre total de joueurs et le nombre de matchs -->
    <p>Nombre total de joueurs inscrits : <?php echo htmlspecialchars($total_joueurs); ?></p>
    <p>Nombre total de matchs pour un tournoi Round Robin : <?php echo htmlspecialchars($nombre_matchs); ?></p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Âge</th>
                <th>Catégorie</th>
                <th>Division</th>
                <th>Club</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($joueurs as $joueur): ?>
                <tr>
                    <td><?php echo htmlspecialchars($joueur['id']); ?></td>
                    <td><?php echo htmlspecialchars($joueur['nom']); ?></td>
                    <td><?php echo htmlspecialchars($joueur['age']); ?></td>
                    <td><?php echo htmlspecialchars($joueur['categorie']); ?></td>
                    <td><?php echo htmlspecialchars($joueur['division']); ?></td>
                    <td><?php echo htmlspecialchars($joueur['club']); ?></td>
                    <td><?php echo htmlspecialchars($joueur['email']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Formulaire pour rediriger vers la génération des combinaisons -->
    <form action="/bracketts/controllers/SessionController.php" method="post">
        <input type="hidden" name="joueurs" value="<?php echo htmlspecialchars(serialize($joueurs)); ?>">
        <input type="submit" name="generer_combinaisons" value="Générer les combinaisons">
    </form>
</main>

<?php require_once(__DIR__ . '/../includes/footer.php'); ?>

<?php
// Fermer la connexion à la base de données
$conn->close();
?>

<?php
require_once(__DIR__ . '/../includes/db.php');

// Fonction pour exécuter le script SQL
function executeSchema($conn, $schemaFile) {
    $schema = file_get_contents($schemaFile);
    if ($schema === false) {
        die("Erreur lors de la lecture du fichier SQL.");
    }

    if ($conn->multi_query($schema)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->next_result());
    } else {
        echo "Erreur lors de l'exécution du script SQL : " . $conn->error;
    }
}

// Récupérer le nom de la compétition depuis les paramètres GET ou POST
$nomCompetition = isset($_GET['nom']) ? $_GET['nom'] : (isset($_POST['nom']) ? $_POST['nom'] : null);

if (!$nomCompetition) {
    die("Le nom de la compétition est requis.");
}

// Connexion à la base de données avec le nom de la base
$conn = getDatabaseConnection($nomCompetition);

// Chemin vers le fichier Schema.sql
$schemaFile = __DIR__ . '/../database/Schema.sql';

// Exécuter le fichier Schema.sql
executeSchema($conn, $schemaFile);

// Mettre à jour le fichier de configuration avec le nouveau nom de la base de données
$configFile = __DIR__ . '/../config/config.php';
$configContent = "<?php\n";
$configContent .= "define('DB_HOST', 'localhost');\n";
$configContent .= "define('DB_USER', 'root');\n";
$configContent .= "define('DB_PASS', '');\n";
$configContent .= "define('DB_NAME', '$nomCompetition'); // Nom de la base de données\n";
$configContent .= "?>";

if (file_put_contents($configFile, $configContent) === false) {
    die("Erreur lors de la mise à jour du fichier de configuration.");
}

// Fermer la connexion
$conn->close();

echo "Les tables ont été créées avec succès et la configuration mise à jour.";

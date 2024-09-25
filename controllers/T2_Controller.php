<?php

// Connexion à la base de données
require_once(__DIR__ . '/../includes/db.php');


// Récupérer les paramètres d'URL
$id_tournoi = isset($_GET['id_tournoi']) ? (int)$_GET['id_tournoi'] : null;
$id_match = isset($_GET['id_match']) ? (int)$_GET['id_match'] : null;

if ($id_match === null) {
    die("ID du match non spécifié.");
}

// Récupérer les détails du match spécifié
$sql = "SELECT id_tournoi, nom_joueur, club, nom_joueur_adverse, club_adverse, juge1, juge1_club FROM tournoi_impairs WHERE id_match = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $id_match);
$stmt->execute();
$match_result = $stmt->get_result();

if ($match_result->num_rows > 0) {
    $match_details = $match_result->fetch_assoc();
    $stmt->close();

    // Définir id_tournoi si non spécifié dans l'URL
    if ($id_tournoi === null) {
        $id_tournoi = $match_details['id_tournoi'];
    }
} else {
    echo "Aucun match trouvé avec l'ID spécifié.";
    exit;
}

// Afficher les détails du match pour débogage
echo "<h2>Détails du match</h2>";
echo "<p>ID Tournoi: " . htmlspecialchars($match_details['id_tournoi']) . "</p>";
echo "<p>Nom Joueur: " . htmlspecialchars($match_details['nom_joueur']) . "</p>";
echo "<p>Club Joueur: " . htmlspecialchars($match_details['club']) . "</p>";
echo "<p>Nom Joueur Adverse: " . htmlspecialchars($match_details['nom_joueur_adverse']) . "</p>";
echo "<p>Club Joueur Adverse: " . htmlspecialchars($match_details['club_adverse']) . "</p>";
echo "<p>Juge: " . htmlspecialchars($match_details['juge1']) . "</p>";
echo "<p>Club Juge: " . htmlspecialchars($match_details['juge1_club']) . "</p>";

// Traitement des scores soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les scores soumis
    $scores_joueur1 = [
        isset($_POST['score1_joueur1']) ? (int)$_POST['score1_joueur1'] : null,
        isset($_POST['score2_joueur1']) ? (int)$_POST['score2_joueur1'] : null,
        isset($_POST['score3_joueur1']) ? (int)$_POST['score3_joueur1'] : null,
        isset($_POST['score4_joueur1']) ? (int)$_POST['score4_joueur1'] : null,
        isset($_POST['score5_joueur1']) ? (int)$_POST['score5_joueur1'] : null,
    ];

    $scores_joueur2 = [
        isset($_POST['score1_joueur2']) ? (int)$_POST['score1_joueur2'] : null,
        isset($_POST['score2_joueur2']) ? (int)$_POST['score2_joueur2'] : null,
        isset($_POST['score3_joueur2']) ? (int)$_POST['score3_joueur2'] : null,
        isset($_POST['score4_joueur2']) ? (int)$_POST['score4_joueur2'] : null,
        isset($_POST['score5_joueur2']) ? (int)$_POST['score5_joueur2'] : null,
    ];

    // Afficher les scores pour débogage
    echo "<h2>Scores Soumis</h2>";
    echo "<p>Scores Joueur 1: " . implode(', ', $scores_joueur1) . "</p>";
    echo "<p>Scores Joueur 2: " . implode(', ', $scores_joueur2) . "</p>";

    // Validation des scores et détermination du gagnant
    $joueur1_wins = 0;
    $joueur2_wins = 0;

    for ($i = 0; $i < 5; $i++) {
        if ($scores_joueur1[$i] !== null && $scores_joueur2[$i] !== null) {
            if ($scores_joueur1[$i] >= 11 && $scores_joueur1[$i] > $scores_joueur2[$i]) {
                $joueur1_wins++;
            } elseif ($scores_joueur2[$i] >= 11 && $scores_joueur2[$i] > $scores_joueur1[$i]) {
                $joueur2_wins++;
            }
        }
    }

    $winner = "";
    $winner_club = "";
    if ($joueur1_wins == 3 || $joueur2_wins == 3) {
        $winner = $joueur1_wins == 3 ? $match_details['nom_joueur'] : $match_details['nom_joueur_adverse'];
        $winner_club = $joueur1_wins == 3 ? $match_details['club'] : $match_details['club_adverse'];

        // Afficher le gagnant pour débogage
        echo "<p>Gagnant: " . htmlspecialchars($winner) . "</p>";

        // Vérifier si un enregistrement existe déjà
        $check_existing_query = $conn->prepare("SELECT id FROM score_t2_tours WHERE id_match = ? AND id_tournoi = ?");
        if ($check_existing_query === false) {
            die("Prepare failed: " . htmlspecialchars($conn->error));
        }
        $check_existing_query->bind_param("ii", $id_match, $id_tournoi);
        $check_existing_query->execute();
        $check_result = $check_existing_query->get_result();
        $exists = $check_result->num_rows > 0;
        $check_existing_query->close();

        if ($exists) {
            // Mettre à jour l'enregistrement existant
            $update_scores_query = $conn->prepare("UPDATE score_t2_tours SET nom_joueur = ?, club_joueur = ?, nom_joueur_adverse = ?, club_joueur_adverse = ?, score1_joueur1 = ?, score2_joueur1 = ?, score3_joueur1 = ?, score4_joueur1 = ?, score5_joueur1 = ?, score1_joueur2 = ?, score2_joueur2 = ?, score3_joueur2 = ?, score4_joueur2 = ?, score5_joueur2 = ?, gagnant = ?, gagnant_club = ?,juge1 = ?, juge1_club = ? WHERE id_match = ? AND id_tournoi = ?");
            if ($update_scores_query === false) {
                die("Prepare failed: " . htmlspecialchars($conn->error));
            }

            // Lier les paramètres et exécuter la requête
            $update_scores_query->bind_param(
                "ssssiiiiiiiiiissssii",
                $match_details['nom_joueur'],
                $match_details['club'],
                $match_details['nom_joueur_adverse'],
                $match_details['club_adverse'],
                $scores_joueur1[0],
                $scores_joueur1[1],
                $scores_joueur1[2],
                $scores_joueur1[3],
                $scores_joueur1[4],
                $scores_joueur2[0],
                $scores_joueur2[1],
                $scores_joueur2[2],
                $scores_joueur2[3],
                $scores_joueur2[4],
                $winner,
                $winner_club,
                $match_details['juge1'],
                $match_details['juge1_club'],
                $id_match,
                $id_tournoi
            );

            if (!$update_scores_query->execute()) {
                echo "Execute failed: " . htmlspecialchars($update_scores_query->error);
            } else {
                // Redirection vers une page de succès ou un autre endroit
                header('Location: /bracketts/views_tour/T2_résultat.php');
                exit;
            }

            $update_scores_query->close();
        } else {
            // Préparer la requête d'insertion des scores
            $insert_scores_query = $conn->prepare("INSERT INTO score_t2_tours (id_tournoi, id_match, nom_joueur, club_joueur, nom_joueur_adverse, club_joueur_adverse, score1_joueur1, score2_joueur1, score3_joueur1, score4_joueur1, score5_joueur1, score1_joueur2, score2_joueur2, score3_joueur2, score4_joueur2, score5_joueur2, gagnant, gagnant_club,juge1, juge1_club) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($insert_scores_query === false) {
                die("Prepare failed: " . htmlspecialchars($conn->error));
            }

            // Lier les paramètres et exécuter la requête
            $insert_scores_query->bind_param(
                "iissssssssssssssssss",
                $id_tournoi,
                $id_match,
                $match_details['nom_joueur'],
                $match_details['club'],
                $match_details['nom_joueur_adverse'],
                $match_details['club_adverse'],
                $scores_joueur1[0],
                $scores_joueur1[1],
                $scores_joueur1[2],
                $scores_joueur1[3],
                $scores_joueur1[4],
                $scores_joueur2[0],
                $scores_joueur2[1],
                $scores_joueur2[2],
                $scores_joueur2[3],
                $scores_joueur2[4],
                $winner,
                $winner_club,
                $match_details['juge1'],
                $match_details['juge1_club']
            );

            if (!$insert_scores_query->execute()) {
                echo "Execute failed: " . htmlspecialchars($insert_scores_query->error);
            } else {
                // Redirection vers une page de succès ou un autre endroit
                header('Location: /bracketts/views_tour/T2_résultat.php');
                exit;
            }

            $insert_scores_query->close();
        }
    } else {
        echo "Pas de gagnant déterminé (moins de 3 sets gagnés par un joueur).";
    }
}

<?php

// Connexion à la base de données
require_once(__DIR__ . '/../includes/db.php');

// Vérifier si des gagnants et des perdants ont été soumis via le formulaire
if (isset($_POST['gagnants']) && is_array($_POST['gagnants']) && isset($_POST['perdants']) && is_array($_POST['perdants'])) {
    
    // Parcourir chaque gagnant
    foreach ($_POST['gagnants'] as $gagnant) {
        list($nom_gagnant, $club_gagnant) = explode(';', $gagnant);
        $nom_gagnant = trim($nom_gagnant);
        $club_gagnant = trim($club_gagnant);

        // Vérifier si le gagnant est déjà dans la table T1_classements
        $stmt = $conn->prepare("SELECT id, matchs_gagnes, sets_joues FROM T1_classements WHERE nom = ? AND club = ?");
        $stmt->bind_param("ss", $nom_gagnant, $club_gagnant);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Le joueur existe, on met à jour ses statistiques
            $stmt->bind_result($id, $matchs_gagnes, $sets_joues);
            $stmt->fetch();
            $matchs_gagnes++;
            $sets_joues += 3; // Mettre à jour le nombre de sets joués (exemple : 3 sets par match)
            $stmt_update = $conn->prepare("UPDATE T1_classements SET matchs_gagnes = ?, sets_joues = ? WHERE id = ?");
            $stmt_update->bind_param("iii", $matchs_gagnes, $sets_joues, $id);
            $stmt_update->execute();
        } else {
            // Le joueur n'existe pas encore, on l'ajoute
            $stmt_insert = $conn->prepare("INSERT INTO T1_classements (nom, club, matchs_gagnes, sets_joues) VALUES (?, ?, ?, ?)");
            $matchs_gagnes = 1;
            $sets_joues = 3; // Mettre à jour le nombre de sets joués (exemple : 3 sets par match)
            $stmt_insert->bind_param("ssii", $nom_gagnant, $club_gagnant, $matchs_gagnes, $sets_joues);
            $stmt_insert->execute();
        }
    }

    // Parcourir chaque perdant
    foreach ($_POST['perdants'] as $perdant) {
        list($nom_perdant, $club_perdant) = explode(';', $perdant);
        $nom_perdant = trim($nom_perdant);
        $club_perdant = trim($club_perdant);

        // Même processus que pour le gagnant, mais sans incrémenter le nombre de matchs gagnés
        $stmt = $conn->prepare("SELECT id, sets_joues FROM T1_classements WHERE nom = ? AND club = ?");
        $stmt->bind_param("ss", $nom_perdant, $club_perdant);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $sets_joues);
            $stmt->fetch();
            $sets_joues += 0; // Aucun set ajouté, car le joueur a perdu
            $stmt_update = $conn->prepare("UPDATE T1_classements SET sets_joues = ? WHERE id = ?");
            $stmt_update->bind_param("ii", $sets_joues, $id);
            $stmt_update->execute();
        } else {
            // Ajouter le perdant avec 0 sets joués
            $stmt_insert = $conn->prepare("INSERT INTO T1_classements (nom, club, matchs_gagnes, sets_joues) VALUES (?, ?, ?, ?)");
            $matchs_gagnes = 0;
            $sets_joues = 0;
            $stmt_insert->bind_param("ssii", $nom_perdant, $club_perdant, $matchs_gagnes, $sets_joues);
            $stmt_insert->execute();
        }
    }
}

// Fermer la connexion
$conn->close();

// Redirection vers la page de classement
header("Location: /bracketts/views_tour/T2_classement.php");
exit();


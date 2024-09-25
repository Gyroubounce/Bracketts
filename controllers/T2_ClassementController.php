<?php

// Connexion à la base de données
require_once(__DIR__ . '/../includes/db.php');

// Vérifier si des gagnants et des perdants ont été soumis via le formulaire
if (isset($_POST['gagnants']) && is_array($_POST['gagnants']) && isset($_POST['perdants']) && is_array($_POST['perdants'])) {
    
    // Parcourir chaque gagnant
    foreach ($_POST['gagnants'] as $index => $gagnant) {
        list($nom_gagnant, $club_gagnant) = explode(';', $gagnant);
        $nom_gagnant = trim($nom_gagnant);
        $club_gagnant = trim($club_gagnant);

        // Les points gagnés pour un match gagné
        $points_gagnes = 6;

        // Vérifier si le gagnant est déjà dans la table T2_classements
        $stmt = $conn->prepare("SELECT id, matchs_gagnes, points_gagnes FROM T2_classements WHERE nom = ? AND club = ?");
        $stmt->bind_param("ss", $nom_gagnant, $club_gagnant);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Le joueur existe, on met à jour ses statistiques
            $stmt->bind_result($id, $matchs_gagnes, $points_gagnes_actuels);
            $stmt->fetch();
            $matchs_gagnes++;
            $points_gagnes_actuels += $points_gagnes; // Ajouter les points gagnés au total actuel
            $stmt_update = $conn->prepare("UPDATE T2_classements SET matchs_gagnes = ?, points_gagnes = ? WHERE id = ?");
            $stmt_update->bind_param("iii", $matchs_gagnes, $points_gagnes_actuels, $id);
            $stmt_update->execute();
        } else {
            // Le joueur n'existe pas encore, on l'ajoute
            $stmt_insert = $conn->prepare("INSERT INTO T2_classements (nom, club, matchs_gagnes, points_gagnes, points_perdus) VALUES (?, ?, ?, ?, ?)");
            $matchs_gagnes = 1;
            $points_perdus = 0;
            $stmt_insert->bind_param("ssiii", $nom_gagnant, $club_gagnant, $matchs_gagnes, $points_gagnes, $points_perdus);
            $stmt_insert->execute();
        }
    }

    // Parcourir chaque perdant
    foreach ($_POST['perdants'] as $index => $perdant) {
        list($nom_perdant, $club_perdant) = explode(';', $perdant);
        $nom_perdant = trim($nom_perdant);
        $club_perdant = trim($club_perdant);

        // Les points perdus pour un match perdu
        $points_perdus = 5;

        // Même processus que pour le gagnant, mais on ajoute les points perdus au lieu des points gagnés
        $stmt = $conn->prepare("SELECT id, points_perdus FROM T2_classements WHERE nom = ? AND club = ?");
        $stmt->bind_param("ss", $nom_perdant, $club_perdant);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $points_perdus_actuels);
            $stmt->fetch();
            $points_perdus_actuels += $points_perdus; // Ajouter les points perdus au total actuel
            $stmt_update = $conn->prepare("UPDATE T2_classements SET points_perdus = ? WHERE id = ?");
            $stmt_update->bind_param("ii", $points_perdus_actuels, $id);
            $stmt_update->execute();
        } else {
            // Ajouter le perdant avec les points perdus calculés
            $stmt_insert = $conn->prepare("INSERT INTO T2_classements (nom, club, matchs_gagnes, points_gagnes, points_perdus) VALUES (?, ?, ?, ?, ?)");
            $matchs_gagnes = 0;
            $points_gagnes = 0;
            $stmt_insert->bind_param("ssiii", $nom_perdant, $club_perdant, $matchs_gagnes, $points_gagnes, $points_perdus);
            $stmt_insert->execute();
        }
    }
}

// Fermer la connexion
$conn->close();

// Redirection vers la page de classement
header("Location: /bracketts/views_tour/T2_classement.php");
exit();

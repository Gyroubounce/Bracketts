<?php
// JoueurController.php

// Connexion à la base de données
require_once(__DIR__ . '/../includes/db.php');

// Vérifier que la requête est une POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $nom = $_POST['nom'];
    $age = $_POST['age'];
    $categorie = $_POST['categorie'];
    $division = $_POST['division'];
    $club = $_POST['club'];
    $email = $_POST['email'];

        // Vérifier si le joueur existe déjà
        $stmt = $conn->prepare("SELECT COUNT(*) FROM joueurs WHERE nom = ?");
        $stmt->bind_param("s", $nom);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    
        if ($count > 0) {
            // Joueur existe déjà
            echo "Un joueur avec ce nom est déjà inscrit.";
        } else {
    // Préparer et exécuter la requête d'insertion
    $stmt = $conn->prepare("INSERT INTO joueurs (nom, age, categorie, division, club, email) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissss", $nom, $age, $categorie, $division, $club, $email);

    if ($stmt->execute()) {
  
              // Nombre pair de joueurs : redirection vers liste_inscrits_pair.php
                header("Location: /bracketts/views/liste_joueurs.php");
                exit();            
         } else {
        echo "Erreur lors de l'inscription : " . $stmt->error;
    }

    $stmt->close();
}
} else {
echo "Données du formulaire manquantes.";
}

$conn->close();
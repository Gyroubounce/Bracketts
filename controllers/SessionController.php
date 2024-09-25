<?php
session_start();
require_once(__DIR__ . '/../includes/db.php');

// Connexion à la base de données
$conn = getDatabaseConnection(); // Assurez-vous que cette fonction est correctement définie

// Vérifier la connexion
if (!$conn) {
    die('Échec de la connexion à la base de données : ' . htmlspecialchars(mysqli_connect_error()));
}

// Récupérer les joueurs inscrits
$sql = "SELECT id, nom, club FROM joueurs";
$result = $conn->query($sql);

// Vérifier si la requête a réussi
if (!$result) {
    die('Erreur lors de la récupération des joueurs inscrits : ' . htmlspecialchars($conn->error));
}

// Récupérer les joueurs
$joueurs = $result->fetch_all(MYSQLI_ASSOC);

// Fonction pour générer les tours avec groupes de 3 joueurs, en intégrant les joueurs restants
function genererTours($joueurs) {
    $tours = array();
    $nb_joueurs = count($joueurs);

    // Mélanger aléatoirement les joueurs pour des appariements aléatoires
    shuffle($joueurs);

    // Gérer les groupes de 3 joueurs
    $groupes = array_chunk($joueurs, 3);

    // Gérer les joueurs restants
    $restants = array();
    if (count(end($groupes)) < 3 && count(end($groupes)) > 0) {
        $restants = array_pop($groupes);
    }

    // Si 2 joueurs restent, les ajouter à l'avant-dernier groupe et créer un nouveau groupe de 4
    if (count($restants) == 2) {
        $avantDernierGroupe = array_pop($groupes);
        $avantDernierGroupe = array_merge($avantDernierGroupe, array($restants[0]));
        $groupes[] = $avantDernierGroupe;
        $groupes[] = array($restants[1]);
    } elseif (count($restants) == 1) {
        $dernierGroupe = array_pop($groupes);
        $dernierGroupe = array_merge($dernierGroupe, array($restants[0]));
        $groupes[] = $dernierGroupe;
    }

    $id_count = 1; // Initialiser id_count

    // Générer les matchs intra-groupes
    foreach ($groupes as $index => $groupe) {
        if (count($groupe) == 3) {
            list($joueur1, $joueur2, $joueur3) = $groupe;

            // Match 1: Joueur 1 vs Joueur 2, avec Joueur 3 comme juge
            $tours[] = array(
                'id_tournoi' => $index + 1,
                'id_match' => ($index + 1) * 100 + 1,
                'id_count' => $id_count++,
                'nom_joueur' => $joueur1['nom'],
                'club' => $joueur1['club'],
                'nom_joueur_adverse' => $joueur2['nom'],
                'club_adverse' => $joueur2['club'],
                'nom_joueur_impair' => $joueur3['nom'],
                'club_impair' => $joueur3['club'],
                'juge1' => $joueur3['nom'],
                'juge1_club' => $joueur3['club']
            );

            // Match 2: Joueur 2 vs Joueur 3, avec Joueur 1 comme juge
            $tours[] = array(
                'id_tournoi' => $index + 1,
                'id_match' => ($index + 1) * 100 + 2,
                'id_count' => $id_count++,
                'nom_joueur' => $joueur2['nom'],
                'club' => $joueur2['club'],
                'nom_joueur_adverse' => $joueur3['nom'],
                'club_adverse' => $joueur3['club'],
                'nom_joueur_impair' => $joueur1['nom'],
                'club_impair' => $joueur1['club'],
                'juge1' => $joueur1['nom'],
                'juge1_club' => $joueur1['club']
            );

            // Match 3: Joueur 1 vs Joueur 3, avec Joueur 2 comme juge
            $tours[] = array(
                'id_tournoi' => $index + 1,
                'id_match' => ($index + 1) * 100 + 3,
                'id_count' => $id_count++,
                'nom_joueur' => $joueur1['nom'],
                'club' => $joueur1['club'],
                'nom_joueur_adverse' => $joueur3['nom'],
                'club_adverse' => $joueur3['club'],
                'nom_joueur_impair' => $joueur2['nom'],
                'club_impair' => $joueur2['club'],
                'juge1' => $joueur2['nom'],
                'juge1_club' => $joueur2['club']
            );
        } elseif (count($groupe) == 4) {
            list($joueur1, $joueur2, $joueur3, $juge) = $groupe;

            // Match 1: Joueur 1 vs Joueur 2, avec Joueur 3 comme juge
            $tours[] = array(
                'id_tournoi' => $index + 1,
                'id_match' => ($index + 1) * 100 + 1,
                'id_count' => $id_count++,
                'nom_joueur' => $joueur1['nom'],
                'club' => $joueur1['club'],
                'nom_joueur_adverse' => $joueur2['nom'],
                'club_adverse' => $joueur2['club'],
                'nom_joueur_impair' => $joueur3['nom'],
                'club_impair' => $joueur3['club'],
                'juge1' => $juge['nom'],
                'juge1_club' => $juge['club']
            );

            // Match 2: Joueur 2 vs Joueur 3, avec Joueur 1 comme juge
            $tours[] = array(
                'id_tournoi' => $index + 1,
                'id_match' => ($index + 1) * 100 + 2,
                'id_count' => $id_count++,
                'nom_joueur' => $joueur2['nom'],
                'club' => $joueur2['club'],
                'nom_joueur_adverse' => $joueur3['nom'],
                'club_adverse' => $joueur3['club'],
                'nom_joueur_impair' => $juge['nom'],
                'club_impair' => $juge['club'],
                'juge1' => $joueur1['nom'],
                'juge1_club' => $joueur1['club']
            );

            // Match 3: Joueur 1 vs Joueur 3, avec Joueur 2 comme juge
            $tours[] = array(
                'id_tournoi' => $index + 1,
                'id_match' => ($index + 1) * 100 + 3,
                'id_count' => $id_count++,
                'nom_joueur' => $joueur1['nom'],
                'club' => $joueur1['club'],
                'nom_joueur_adverse' => $joueur3['nom'],
                'club_adverse' => $joueur3['club'],
                'nom_joueur_impair' => $juge['nom'],
                'club_impair' => $juge['club'],
                'juge1' => $joueur2['nom'],
                'juge1_club' => $joueur2['club']
            );

            // Match 4: Joueur 1 vs Joueur 4, avec Joueur 2 comme juge
            $tours[] = array(
                'id_tournoi' => $index + 1,
                'id_match' => ($index + 1) * 100 + 4,
                'id_count' => $id_count++,
                'nom_joueur' => $joueur1['nom'],
                'club' => $joueur1['club'],
                'nom_joueur_adverse' => $juge['nom'],
                'club_adverse' => $juge['club'],
                'nom_joueur_impair' => $joueur2['nom'],
                'club_impair' => $joueur2['club'],
                'juge1' => $joueur3['nom'],
                'juge1_club' => $joueur3['club']
            );

            // Match 5: Joueur 4 vs Joueur 2, avec Joueur 3 comme juge
            $tours[] = array(
                'id_tournoi' => $index + 1,
                'id_match' => ($index + 1) * 100 + 5,
                'id_count' => $id_count++,
                'nom_joueur' => $juge['nom'],
                'club' => $juge['club'],
                'nom_joueur_adverse' => $joueur2['nom'],
                'club_adverse' => $joueur2['club'],
                'nom_joueur_impair' => $joueur3['nom'],
                'club_impair' => $joueur3['club'],
                'juge1' => $joueur1['nom'],
                'juge1_club' => $joueur1['club']
            );

            // Match 6: Joueur 3 vs Joueur 4, avec Joueur 2 comme juge
            $tours[] = array(
                'id_tournoi' => $index + 1,
                'id_match' => ($index + 1) * 100 + 6,
                'id_count' => $id_count++,
                'nom_joueur' => $juge['nom'],
                'club' => $juge['club'],
                'nom_joueur_adverse' => $joueur3['nom'],
                'club_adverse' => $joueur3['club'],
                'nom_joueur_impair' => $joueur1['nom'],
                'club_impair' => $joueur1['club'],
                'juge1' => $joueur2['nom'],
                'juge1_club' => $joueur2['club']
            );
        }
    }

    return $tours;
}

// Appel de la fonction pour générer les tours avec tous les joueurs
$tours = genererTours($joueurs);

// Préparer et exécuter la requête SQL pour insérer les tours dans la table tournoi_impairs
$insert_query = $conn->prepare("INSERT INTO tournoi_impairs (id_tournoi, id_match, id_count, nom_joueur, club, nom_joueur_adverse, club_adverse, nom_joueur_impair, club_impair, juge1, juge1_club) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

// Vérifier si la préparation a réussi
if ($insert_query === false) {
    die('Échec de la préparation de la requête : ' . htmlspecialchars($conn->error));
}

// Construire le tableau des paramètres à partir des matches générés
foreach ($tours as $match) {
    $insert_query->bind_param(
        "iiissssssss",
        $match['id_tournoi'],
        $match['id_match'],
        $match['id_count'],
        $match['nom_joueur'],
        $match['club'],
        $match['nom_joueur_adverse'],
        $match['club_adverse'],
        $match['nom_joueur_impair'],
        $match['club_impair'],
        $match['juge1'],
        $match['juge1_club']
    );
    if (!$insert_query->execute()) {
        echo 'Échec de l\'exécution de la requête : ' . htmlspecialchars($insert_query->error) . "<br>";
        echo 'Données : ' . implode(", ", $match) . "<br>";
    }
}

// Fermer la requête d'insertion
$insert_query->close();

// Enregistrer les détails en session
$total_matches = count($tours);
$matches_generated = $total_matches;
$matches_remaining = $total_matches - $matches_generated;

$_SESSION['total_matches'] = $total_matches;
$_SESSION['matches_generated'] = $matches_generated;
$_SESSION['matches_remaining'] = $matches_remaining;
$_SESSION['tours_impairs'] = $tours;

// Rediriger vers la page eliminatoire_impair_tour1.php
header("Location: /bracketts/views_tour/eliminatoire.php");
exit;


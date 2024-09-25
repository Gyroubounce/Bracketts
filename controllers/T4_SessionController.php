<?php
session_start();
require_once(__DIR__ . '/../includes/db.php');

// Connexion à la base de données
$conn = getDatabaseConnection();
if (!$conn) {
    die('Échec de la connexion à la base de données : ' . htmlspecialchars(mysqli_connect_error()));
}

// Fonction pour récupérer les derniers IDs utilisés
function getLastId($conn) {
    $sql = "SELECT id_tournoi, id_match, id_count FROM tournoi_impairs ORDER BY id_tournoi DESC, id_match DESC LIMIT 1";
    $result = $conn->query($sql);

    if (!$result) {
        die('Erreur lors de la récupération des derniers IDs : ' . htmlspecialchars($conn->error));
    }

    return $result->fetch_assoc();
}


// Fonction pour vérifier si deux joueurs ont déjà joué ensemble
function ontDejaJoueEnsemble($joueur1, $joueur2, $matchs_joues) {
    foreach ($matchs_joues as $match) {
        if (
            ($match['nom_joueur'] == $joueur1 && $match['nom_joueur_adverse'] == $joueur2) ||
            ($match['nom_joueur'] == $joueur2 && $match['nom_joueur_adverse'] == $joueur1)
        ) {
            return true;
        }
    }
    return false;
}

// Récupérer les appariements de la session 1 pour éviter les doublons
$sql_joues = "SELECT nom_joueur, nom_joueur_adverse FROM tournoi_impairs";
$result_joues = $conn->query($sql_joues);

$matchs_joues = [];
if ($result_joues && $result_joues->num_rows > 0) {
    while ($row = $result_joues->fetch_assoc()) {
        $matchs_joues[] = [
            'nom_joueur' => $row['nom_joueur'],
            'nom_joueur_adverse' => $row['nom_joueur_adverse']
        ];
    }
}

// Fonction pour générer les tours avec groupes de 3, 4 ou 5 joueurs
function genererToursSession4($joueurs, $matchs_joues, $lastIds) {
    $tours = [];
    $dernierIdTournoi = $lastIds['id_tournoi'] + 1;
    $id_count = $lastIds['id_count'] + 1; // Initialize id_count from the last used count
    shuffle($joueurs);

    $groupes = [];
    $idTournoi = $dernierIdTournoi;

    foreach ($joueurs as $joueur) {
        $ajoute = false;

        foreach ($groupes as &$groupe) {
            if (count($groupe) < 5) {
                $tousDifferents = true;

                foreach ($groupe as $membre) {
                    if (ontDejaJoueEnsemble($joueur['nom'], $membre['nom'], $matchs_joues)) {
                        $tousDifferents = false;
                        break;
                    }
                }

                if ($tousDifferents) {
                    $groupe[] = $joueur;
                    $ajoute = true;
                    break;
                }
            }
        }

        if (!$ajoute) {
            $groupes[] = [$joueur];
        }
    }

    // Gérer les joueurs restants pour compléter les groupes
    $restants = [];
    if (count(end($groupes)) < 5 && count(end($groupes)) > 0) {
        $restants = array_pop($groupes);
    }

    if (count($restants) == 4) {
        $avantDernierGroupe = array_pop($groupes);
        $avantDernierGroupe = array_merge($avantDernierGroupe, [$restants[0]]);
        $groupes[] = $avantDernierGroupe;
        $groupes[] = [$restants[1], $restants[2], $restants[3]];
    } elseif (count($restants) > 0) {
        $dernierGroupe = array_pop($groupes);
        $dernierGroupe = array_merge($dernierGroupe, $restants);
        $groupes[] = $dernierGroupe;
    }

    foreach ($groupes as $index => $groupe) {
        $idTournoi = $dernierIdTournoi;
        if (count($groupe) == 3) {
            list($joueur1, $joueur2, $joueur3) = $groupe;

            // Match 1: Joueur 1 vs Joueur 2, avec Joueur 3 comme juge
            $tours[] = [
                'id_tournoi' => $idTournoi,
                'id_match' => $idTournoi * 100 + $id_count,
                'id_count' => $id_count,
                'nom_joueur' => $joueur1['nom'],
                'club' => $joueur1['club'],
                'nom_joueur_adverse' => $joueur2['nom'],
                'club_adverse' => $joueur2['club'],
                'nom_joueur_impair' => $joueur3['nom'],
                'club_impair' => $joueur3['club'],
                'juge1' => $joueur3['nom'],
                'juge1_club' => $joueur3['club']
            ];
            $id_count++;

            // Match 2: Joueur 2 vs Joueur 3, avec Joueur 1 comme juge
            $tours[] = [
                'id_tournoi' => $idTournoi,
                'id_match' => $idTournoi * 100 + $id_count,
                'id_count' => $id_count,
                'nom_joueur' => $joueur2['nom'],
                'club' => $joueur2['club'],
                'nom_joueur_adverse' => $joueur3['nom'],
                'club_adverse' => $joueur3['club'],
                'nom_joueur_impair' => $joueur1['nom'],
                'club_impair' => $joueur1['club'],
                'juge1' => $joueur1['nom'],
                'juge1_club' => $joueur1['club']
            ];
            $id_count++;

            // Match 3: Joueur 1 vs Joueur 3, avec Joueur 2 comme juge
            $tours[] = [
                'id_tournoi' => $idTournoi,
                'id_match' => $idTournoi * 100 + $id_count,
                'id_count' => $id_count,
                'nom_joueur' => $joueur1['nom'],
                'club' => $joueur1['club'],
                'nom_joueur_adverse' => $joueur3['nom'],
                'club_adverse' => $joueur3['club'],
                'nom_joueur_impair' => $joueur2['nom'],
                'club_impair' => $joueur2['club'],
                'juge1' => $joueur2['nom'],
                'juge1_club' => $joueur2['club']
            ];
            $id_count++;

        } elseif (count($groupe) == 4) {
            list($joueur1, $joueur2, $joueur3, $juge) = $groupe;

            // Match 1: Joueur 1 vs Joueur 2, avec Joueur 3 comme juge
            $tours[] = [
                'id_tournoi' => $idTournoi,
                'id_match' => $idTournoi * 100 + 1,
                'id_count' => $id_count,
                'nom_joueur' => $joueur1['nom'],
                'club' => $joueur1['club'],
                'nom_joueur_adverse' => $joueur2['nom'],
                'club_adverse' => $joueur2['club'],
                'nom_joueur_impair' => $joueur3['nom'],
                'club_impair' => $joueur3['club'],
                'juge1' => $juge['nom'],
                'juge1_club' => $juge['club']
            ];
            $id_count++;

            // Match 2: Joueur 1 vs Joueur 3, avec Joueur 2 comme juge
            $tours[] = [
                'id_tournoi' => $idTournoi,
                'id_match' => $idTournoi * 100 + 2,
                'id_count' => $id_count,
                'nom_joueur' => $joueur1['nom'],
                'club' => $joueur1['club'],
                'nom_joueur_adverse' => $joueur3['nom'],
                'club_adverse' => $joueur3['club'],
                'nom_joueur_impair' => $juge['nom'],
                'club_impair' => $juge['club'],
                'juge1' => $joueur2['nom'],
                'juge1_club' => $joueur2['club']
            ];
            $id_count++;

            // Match 3: Joueur 1 vs Joueur 4, avec Joueur 2 comme juge
            $tours[] = [
                'id_tournoi' => $idTournoi,
                'id_match' => $idTournoi * 100 + 3,
                'id_count' => $id_count,
                'nom_joueur' => $joueur1['nom'],
                'club' => $joueur1['club'],
                'nom_joueur_adverse' => $juge['nom'],
                'club_adverse' => $juge['club'],
                'nom_joueur_impair' => $joueur2['nom'],
                'club_impair' => $joueur2['club'],
                'juge1' => $joueur3['nom'],
                'juge1_club' => $joueur3['club']
            ];
            $id_count++;

            // Match 4: Joueur 2 vs Joueur 4, avec Joueur 3 comme juge
            $tours[] = [
                'id_tournoi' => $idTournoi,
                'id_match' => $idTournoi * 100 + 4,
                'id_count' => $id_count,
                'nom_joueur' => $joueur2['nom'],
                'club' => $joueur2['club'],
                'nom_joueur_adverse' => $juge['nom'],
                'club_adverse' => $juge['club'],
                'nom_joueur_impair' => $joueur3['nom'],
                'club_impair' => $joueur3['club'],
                'juge1' => $joueur1['nom'],
                'juge1_club' => $joueur1['club']
            ];
            $id_count++;

            // Match 5: Joueur 3 vs Joueur 4, avec Joueur 1 comme juge
            $tours[] = [
                'id_tournoi' => $idTournoi,
                'id_match' => $idTournoi * 100 + 5,
                'id_count' => $id_count,
                'nom_joueur' => $joueur3['nom'],
                'club' => $joueur3['club'],
                'nom_joueur_adverse' => $juge['nom'],
                'club_adverse' => $juge['club'],
                'nom_joueur_impair' => $joueur1['nom'],
                'club_impair' => $joueur1['club'],
                'juge1' => $joueur2['nom'],
                'juge1_club' => $joueur2['club']
            ];
            $id_count++;

            // Match 6: Joueur 1 vs Joueur 4, avec Joueur 3 comme juge
            $tours[] = [
                'id_tournoi' => $idTournoi,
                'id_match' => $idTournoi * 100 + 6,
                'id_count' => $id_count,
                'nom_joueur' => $joueur2['nom'],
                'club' => $joueur2['club'],
                'nom_joueur_adverse' => $joueur3['nom'],
                'club_adverse' => $joueur3['club'],
                'nom_joueur_impair' => $juge['nom'],
                'club_impair' => $juge['club'],
                'juge1' => $joueur1['nom'],
                'juge1_club' => $joueur1['club']
            ];
            $id_count++;

        } elseif (count($groupe) == 5) {
            list($joueur1, $joueur2, $joueur3, $joueur4, $juge) = $groupe;

            // Match 1: Joueur 1 vs Joueur 2, avec Joueur 3 comme juge
            $tours[] = [
                'id_tournoi' => $idTournoi,
                'id_match' => $idTournoi * 100 + 1,
                'id_count' => $id_count,
                'nom_joueur' => $joueur1['nom'],
                'club' => $joueur1['club'],
                'nom_joueur_adverse' => $joueur2['nom'],
                'club_adverse' => $joueur2['club'],
                'nom_joueur_impair' => $joueur3['nom'],
                'club_impair' => $joueur3['club'],
                'juge1' => $juge['nom'],
                'juge1_club' => $juge['club']
            ];
            $id_count++;

            // Match 2: Joueur 1 vs Joueur 3, avec Joueur 2 comme juge
            $tours[] = [
                'id_tournoi' => $idTournoi,
                'id_match' => $idTournoi * 100 + 2,
                'id_count' => $id_count,
                'nom_joueur' => $joueur1['nom'],
                'club' => $joueur1['club'],
                'nom_joueur_adverse' => $joueur3['nom'],
                'club_adverse' => $joueur3['club'],
                'nom_joueur_impair' => $joueur2['nom'],
                'club_impair' => $joueur2['club'],
                'juge1' => $joueur4['nom'],
                'juge1_club' => $joueur4['club']
            ];
            $id_count++;

            // Match 3: Joueur 1 vs Joueur 4, avec Joueur 5 comme juge
            $tours[] = [
                'id_tournoi' => $idTournoi,
                'id_match' => $idTournoi * 100 + 3,
                'id_count' => $id_count,
                'nom_joueur' => $joueur1['nom'],
                'club' => $joueur1['club'],
                'nom_joueur_adverse' => $joueur4['nom'],
                'club_adverse' => $joueur4['club'],
                'nom_joueur_impair' => $juge['nom'],
                'club_impair' => $juge['club'],
                'juge1' => $joueur3['nom'],
                'juge1_club' => $joueur3['club']
            ];
            $id_count++;

            // Match 4: Joueur 2 vs Joueur 4, avec Joueur 3 comme juge
            $tours[] = [
                'id_tournoi' => $idTournoi,
                'id_match' => $idTournoi * 100 + 4,
                'id_count' => $id_count,
                'nom_joueur' => $joueur2['nom'],
                'club' => $joueur2['club'],
                'nom_joueur_adverse' => $joueur4['nom'],
                'club_adverse' => $joueur4['club'],
                'nom_joueur_impair' => $joueur3['nom'],
                'club_impair' => $joueur3['club'],
                'juge1' => $joueur1['nom'],
                'juge1_club' => $joueur1['club']
            ];
            $id_count++;

            // Match 5: Joueur 2 vs Joueur 5, avec Joueur 3 comme juge
            $tours[] = [
                'id_tournoi' => $idTournoi,
                'id_match' => $idTournoi * 100 + 5,
                'id_count' => $id_count,
                'nom_joueur' => $joueur2['nom'],
                'club' => $joueur2['club'],
                'nom_joueur_adverse' => $juge['nom'],
                'club_adverse' => $juge['club'],
                'nom_joueur_impair' => $joueur1['nom'],
                'club_impair' => $joueur1['club'],
                'juge1' => $joueur4['nom'],
                'juge1_club' => $joueur4['club']
            ];
            $id_count++;

            // Match 6: Joueur 3 vs Joueur 4, avec Joueur 5 comme juge
            $tours[] = [
                'id_tournoi' => $idTournoi,
                'id_match' => $idTournoi * 100 + 6,
                'id_count' => $id_count,
                'nom_joueur' => $joueur3['nom'],
                'club' => $joueur3['club'],
                'nom_joueur_adverse' => $joueur4['nom'],
                'club_adverse' => $joueur4['club'],
                'nom_joueur_impair' => $joueur1['nom'],
                'club_impair' => $joueur1['club'],
                'juge1' => $joueur2['nom'],
                'juge1_club' => $joueur2['club']
            ];
            $id_count++;

            // Match 7: Joueur 3 vs Joueur 5, avec Joueur 4 comme juge
            $tours[] = [
                'id_tournoi' => $idTournoi,
                'id_match' => $idTournoi * 100 + 7,
                'id_count' => $id_count,
                'nom_joueur' => $joueur3['nom'],
                'club' => $joueur3['club'],
                'nom_joueur_adverse' => $joueur2['nom'],
                'club_adverse' => $joueur2['club'],
                'nom_joueur_impair' => $joueur4['nom'],
                'club_impair' => $joueur4['club'],
                'juge1' => $juge['nom'],
                'juge1_club' => $juge['club']
            ];
            $id_count++;

            // Match 8: Joueur 4 vs Joueur 5, avec Joueur 1 comme juge
            $tours[] = [
                'id_tournoi' => $idTournoi,
                'id_match' => $idTournoi * 100 + 8,
                'id_count' => $id_count,
                'nom_joueur' => $joueur4['nom'],
                'club' => $joueur4['club'],
                'nom_joueur_adverse' => $juge['nom'],
                'club_adverse' => $juge['club'],
                'nom_joueur_impair' => $joueur2['nom'],
                'club_impair' => $joueur2['club'],
                'juge1' => $joueur1['nom'],
                'juge1_club' => $joueur1['club']
            ];
            $id_count++;
        }
        $dernierIdTournoi++;
    }

    return $tours;
}

// Récupérer les joueurs inscrits
$sql = "SELECT id, nom, club FROM joueurs";
$result = $conn->query($sql);
if (!$result) {
    die('Erreur lors de la récupération des joueurs inscrits : ' . htmlspecialchars($conn->error));
}

$joueurs = $result->fetch_all(MYSQLI_ASSOC);



// Récupérer les derniers IDs utilisés
$lastIds = getLastId($conn);

// Appel de la fonction pour générer les tours avec tous les joueurs pour la session 2
$tours = genererToursSession4($joueurs, $matchs_joues, $lastIds);

// Préparer et exécuter la requête SQL pour insérer les tours dans la table tournoi_impairs
$insert_query = $conn->prepare("INSERT INTO tournoi_impairs (id_tournoi, id_match, id_count,nom_joueur, club, nom_joueur_adverse, club_adverse, nom_joueur_impair, club_impair, juge1, juge1_club) VALUES (?, ?, ?, ?,?, ?, ?, ?, ?, ?, ?)");

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


// Enregistrer les tours générés en session pour une utilisation ultérieure
$_SESSION['tours_session4'] = $tours;

// Rediriger vers la page eliminatoire_session2.php
header("Location: /bracketts/views_tour/T4_eliminatoire.php");
exit;

// Fermer la connexion à la base de données


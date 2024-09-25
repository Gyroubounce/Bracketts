
CREATE TABLE IF NOT EXISTS competitions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    tournoi VARCHAR(255) NOT NULL,
    lieu VARCHAR(255) NOT NULL,
    juge VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS joueurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    age INT NOT NULL,
    categorie VARCHAR(255) NOT NULL,
    division VARCHAR(255) NOT NULL,
    club VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS juges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    club VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS tournois (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_tournoi INT NOT NULL,
    id_match INT NOT NULL,
    nom_joueur VARCHAR(255) NOT NULL,
    club VARCHAR(255) NOT NULL,
    nom_joueur_adverse VARCHAR(255) NOT NULL,
    club_adverse VARCHAR(255) NOT NULL,
    juge1 VARCHAR(255) NOT NULL,
    juge1_club VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS tournoi_impairs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_tournoi INT NOT NULL,
    id_match INT NOT NULL,
    id_count INT NOT NULL,
    nom_joueur VARCHAR(255) NOT NULL,
    club VARCHAR(255) NOT NULL,
    nom_joueur_adverse VARCHAR(255) NOT NULL,
    club_adverse VARCHAR(255) NOT NULL,
    nom_joueur_impair VARCHAR(255) NOT NULL,
    club_impair VARCHAR(255) NOT NULL,
    juge1 VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS score_impairs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_tournoi INT NOT NULL,
    id_match INT NOT NULL,
    nom_joueur VARCHAR(255) NOT NULL,
    club_joueur VARCHAR(255) NOT NULL,
    nom_joueur_adverse VARCHAR(255) NOT NULL,
    club_joueur_adverse VARCHAR(255) NOT NULL,
    score1_joueur1 INT NOT NULL,
    score2_joueur1 INT NOT NULL,
    score3_joueur1 INT NOT NULL,
    score4_joueur1 INT NOT NULL,
    score5_joueur1 INT NOT NULL,
    score1_joueur2 INT NOT NULL,
    score2_joueur2 INT NOT NULL,
    score3_joueur2 INT NOT NULL,
    score4_joueur2 INT NOT NULL,
    score5_joueur2 INT NOT NULL,
    gagnant VARCHAR(255) NOT NULL,
    gagnant_club VARCHAR(255) NOT NULL,
    juge1 VARCHAR(255) NOT NULL,
    juge1_club VARCHAR(255) NOT NULL
);


CREATE TABLE IF NOT EXISTS score_t2_tours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_tournoi INT NOT NULL,
    id_match INT NOT NULL,
    nom_joueur VARCHAR(255) NOT NULL,
    club_joueur VARCHAR(255) NOT NULL,
    nom_joueur_adverse VARCHAR(255) NOT NULL,
    club_joueur_adverse VARCHAR(255) NOT NULL,
    score1_joueur1 INT NOT NULL,
    score2_joueur1 INT NOT NULL,
    score3_joueur1 INT NOT NULL,
    score4_joueur1 INT NOT NULL,
    score5_joueur1 INT NOT NULL,
    score1_joueur2 INT NOT NULL,
    score2_joueur2 INT NOT NULL,
    score3_joueur2 INT NOT NULL,
    score4_joueur2 INT NOT NULL,
    score5_joueur2 INT NOT NULL,
    gagnant VARCHAR(255) NOT NULL,
    gagnant_club VARCHAR(255) NOT NULL,
    juge1 VARCHAR(255) NOT NULL,
    juge1_club VARCHAR(255) NOT NULL
);


CREATE TABLE IF NOT EXISTS score_t3_tours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_tournoi INT NOT NULL,
    id_match INT NOT NULL,
    nom_joueur VARCHAR(255) NOT NULL,
    club_joueur VARCHAR(255) NOT NULL,
    nom_joueur_adverse VARCHAR(255) NOT NULL,
    club_joueur_adverse VARCHAR(255) NOT NULL,
    score1_joueur1 INT NOT NULL,
    score2_joueur1 INT NOT NULL,
    score3_joueur1 INT NOT NULL,
    score4_joueur1 INT NOT NULL,
    score5_joueur1 INT NOT NULL,
    score1_joueur2 INT NOT NULL,
    score2_joueur2 INT NOT NULL,
    score3_joueur2 INT NOT NULL,
    score4_joueur2 INT NOT NULL,
    score5_joueur2 INT NOT NULL,
    gagnant VARCHAR(255) NOT NULL,
    gagnant_club VARCHAR(255) NOT NULL,
    juge1 VARCHAR(255) NOT NULL,
    juge1_club VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS score_t4_tours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_tournoi INT NOT NULL,
    id_match INT NOT NULL,
    nom_joueur VARCHAR(255) NOT NULL,
    club_joueur VARCHAR(255) NOT NULL,
    nom_joueur_adverse VARCHAR(255) NOT NULL,
    club_joueur_adverse VARCHAR(255) NOT NULL,
    score1_joueur1 INT NOT NULL,
    score2_joueur1 INT NOT NULL,
    score3_joueur1 INT NOT NULL,
    score4_joueur1 INT NOT NULL,
    score5_joueur1 INT NOT NULL,
    score1_joueur2 INT NOT NULL,
    score2_joueur2 INT NOT NULL,
    score3_joueur2 INT NOT NULL,
    score4_joueur2 INT NOT NULL,
    score5_joueur2 INT NOT NULL,
    gagnant VARCHAR(255) NOT NULL,
    gagnant_club VARCHAR(255) NOT NULL,
    juge1 VARCHAR(255) NOT NULL,
    juge1_club VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS T2_classements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    club VARCHAR(255) NOT NULL,
    matchs_gagnes INT NOT NULL,
    points_gagnes INT NOT NULL,
    points_perdus INT NOT NULL
);


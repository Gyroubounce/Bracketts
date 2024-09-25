<?php
// Joueur.php

class Joueur {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function joueurExiste($nom) {
        $count = 0; // Initialiser la variable $count
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM joueurs WHERE nom = ?");
        if ($stmt) {
            $stmt->bind_param("s", $nom);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
        }
        return $count > 0;
    }
    

    public function enregistrerJoueur($nom, $age, $categorie, $division, $club, $email) {
        $stmt = $this->conn->prepare("INSERT INTO joueurs (nom, age, categorie, division, club, email) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sissss", $nom, $age, $categorie, $division, $club, $email);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function compterJoueurs() {
        $sql = "SELECT COUNT(*) AS count_joueurs FROM joueurs";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['count_joueurs'];
    }
}


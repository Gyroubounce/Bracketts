<?php

class Competition
{
    private $conn; // Connexion à la base de données
    public $id; // Identifiant unique de la compétition
    public $nom; // Nom de la compétition
    public $tournoi; // Type de tournoi
    public $lieu; // Lieu de la compétition
    public $juge; // Juge de la compétition

    // Constructeur pour initialiser la connexion à la base de données
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Méthode pour créer une nouvelle compétition dans la base de données
    public function create()
    {
        $stmt = $this->conn->prepare("INSERT INTO competitions (nom, tournoi, lieu, juge) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $this->nom, $this->tournoi, $this->lieu, $this->juge);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            error_log("Erreur lors de la création de la compétition: " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    // Méthode pour récupérer une compétition par ID
    public function read($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM competitions WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $competition = $result->fetch_assoc();
        $stmt->close();
        return $competition;
    }

    // Méthode pour mettre à jour une compétition
    public function update()
    {
        $stmt = $this->conn->prepare("UPDATE competitions SET nom = ?, tournoi = ?, lieu = ?, juge = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $this->nom, $this->tournoi, $this->lieu, $this->juge, $this->id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            error_log("Erreur lors de la mise à jour de la compétition: " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    // Méthode pour supprimer une compétition
    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM competitions WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            error_log("Erreur lors de la suppression de la compétition: " . $stmt->error);
            $stmt->close();
            return false;
        }
    }
}


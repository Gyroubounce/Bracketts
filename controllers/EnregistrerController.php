<?php
// EnregistrerController.php
require_once(__DIR__ . '/../models/Competition.php');

class EnregistrerController
{
    private $conn;
    private $competition;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->competition = new Competition($this->conn);
    }

    public function enregistrerCompetition()
    {
        if (isset($_POST['nom'], $_POST['tournoi'], $_POST['lieu'], $_POST['juge'])) {
            $this->competition->nom = $_POST['nom'];
            $this->competition->tournoi = $_POST['tournoi'];
            $this->competition->lieu = $_POST['lieu'];
            $this->competition->juge = $_POST['juge'];
      
            // Créer les tables de la base de données
           include(__DIR__ . '/../includes/setup.php');


            if ($this->competition->create()) {
                $competitionId = $this->conn->insert_id; // Récupérer l'ID de la compétition nouvellement créée
                header("Location: /bracketts/views/bienvenue.php?id=" . $competitionId);
                exit;
            } else {
                echo "Erreur lors de l'enregistrement de la compétition.";
            }
        } else {
            echo "Données du formulaire manquantes.";
        }
    }
}

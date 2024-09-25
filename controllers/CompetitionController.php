<?php
class CompetitionController
{
    private $conn;
    private $competition;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->competition = new Competition($this->conn);
    }

    public function afficherFormulaireInitialisation()
    {
        // Afficher le formulaire d'initialisation de la compétition
        require __DIR__ . '/../views/initialiser_competition.php';
    }
}

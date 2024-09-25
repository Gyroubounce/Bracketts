

<?php
session_start();

// Obtenir la connexion à la base de données
require_once(__DIR__ . '/../includes/db.php');
// Inclure le modèle Competition
require_once(__DIR__ . '/../models/Competition.php');

?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bracket à Double Élimination</title>

  <?php require_once(__DIR__ . '/../includes/header.php'); ?>
  <style>
    /* Styles généraux pour la structure du bracket */
    .theme {
      height: 100%;
      width: 100%;
      position: absolute;
    }

    .bracket {
      padding: 40px;
      display: flex;
      flex-direction: row;
      position: relative;
    }

    .column {
      display: flex;
      flex-direction: column;
      justify-content: space-around;
      align-items: center;
      margin-right: 40px; /* Espacement entre les colonnes */
    }

    .match {
      position: relative;
      display: flex;
      flex-direction: column;
      min-width: 240px;
      max-width: 240px;
      height: 62px;
      margin-bottom: 24px; /* Espacement entre les matchs */
    }

    .match .match-top {
      border-radius: 2px 2px 0 0;
      background-color: #2c3e50; /* Couleur de fond pour le haut du match */
      color: #ecf0f1; /* Couleur du texte */
    }

    .match .match-bottom {
      border-radius: 0 0 2px 2px;
      background-color: #34495e; /* Couleur de fond pour le bas du match */
      color: #ecf0f1; /* Couleur du texte */
    }

    .match .team {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      height: 100%;
      border: 1px solid #2980b9; /* Couleur de la bordure */
      background-color: #2980b9; /* Couleur de fond */
      color: #ecf0f1; /* Couleur du texte */
      position: relative;
    }

    .match .team span {
      padding: 0 8px; /* Espacement interne */
    }

    .match-lines {
      display: block;
      position: absolute;
      top: 50%;
      bottom: 0;
      margin-top: 0px;
      right: -1px; /* Ajuster le positionnement à droite */
    }

    .match-lines .line {
      background: #e74c3c; /* Couleur de la ligne */
      position: absolute;
    }

    .match-lines .line.one {
      height: 1px;
      width: 12px;
    }

    .match-lines .line.two {
      height: 44px;
      width: 1px;
      left: 11px; /* Ajuster la position à gauche pour le loser bracket */
    }

    .match-lines.alt {
      left: -12px; /* Ajuster la position des lignes alternatives pour le loser bracket */
    }

    .column:first-child .match-lines.alt {
      display: none; /* Masquer la ligne alternative pour le premier column */
    }

    .column:nth-child(even) .match-lines .line.two {
      transform: translate(0, -100%); /* Inverser la ligne pour le loser bracket */
    }

    .column:last-child .match-lines {
      display: none; /* Masquer les lignes pour le dernier column */
    }

    .column:last-child .match-lines.alt {
      display: block; /* Afficher la ligne alternative pour le dernier column */
    }

    .column:nth-child(2) .match-lines .line.two {
      height: 88px;
    }

    .column:nth-child(3) .match-lines .line.two {
      height: 175px;
    }

    .column:nth-child(4) .match-lines .line.two {
      height: 262px;
    }

    .column:nth-child(5) .match-lines .line.two {
      height: 349px;
    }

    /* Nouveaux styles pour aligner les colonnes des losers bracket horizontalement */
    .losers-column {
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: space-around;
    }

    .losers-column .round {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      margin: 0 20px; /* Espacement entre les rounds du loser bracket */
    }

    .winner-section {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      margin-left: 40px; /* Espacement entre la section vainqueur et le dernier column */
    }

    .winner-section .match {
      margin-bottom: 24px; /* Espacement entre les matchs */
    }

  </style>
</head>
<body>
<?php
  // Fonction pour générer un bracket à double élimination
  function genererDoubleEliminationBracket($participants) {
    $bracket = [
      'winners' => [],
      'losers' => []
    ];

    // Mélanger les participants pour des appariements aléatoires
    shuffle($participants);

    // Initialiser le tableau des vainqueurs avec les participants
    $round_matches = [];
    for ($i = 0; $i < count($participants); $i += 2) {
      $player1 = $participants[$i];
      $player2 = isset($participants[$i + 1]) ? $participants[$i + 1] : 'Exempt';
      $round_matches[] = ['player1' => $player1, 'player2' => $player2];
    }
    $bracket['winners'][] = $round_matches;

    // Générer les tours des vainqueurs et des perdants
    $remaining_winners = $participants;
    $losers_pool = [];

    // Noms des tours
    $round_names_winners = ['Huitième de finale', 'Quart de finale', 'Demi-finale', 'Finale'];
    $round_names_losers = ['Tour 2', 'Tour 3', 'Tour 4', 'Tour 5'];

    // Boucle pour les tours de winners bracket
    for ($round = 0; $round < 4; $round++) {
      $next_round_winners = [];
      $round_matches = [];

      for ($i = 0; $i < count($remaining_winners); $i += 2) {
        $player1 = $remaining_winners[$i];
        $player2 = isset($remaining_winners[$i + 1]) ? $remaining_winners[$i + 1] : 'Exempt';

        if ($player2 !== 'Exempt') {
          $round_matches[] = ['player1' => $player1, 'player2' => $player2];
          $winner = rand(0, 1) ? $player1 : $player2;
          $loser = ($winner == $player1) ? $player2 : $player1;
          $next_round_winners[] = $winner;
          $losers_pool[] = $loser;
        } else {
          $next_round_winners[] = $player1;
        }
      }

      // Vérifier si le tableau $round_matches est vide avant de l'ajouter
      if (!empty($round_matches)) {
        $bracket['winners'][] = ['round_name' => $round_names_winners[$round], 'matches' => $round_matches];
      }

      $remaining_winners = $next_round_winners;
    }

    // Boucle pour les tours de losers bracket
    $current_losers = array_splice($losers_pool, 0, 8);
    for ($round = 0; $round < 4; $round++) {
      $next_round_losers = [];
      $round_matches = [];

      for ($i = 0; $i < count($current_losers); $i += 2) {
        $player1 = $current_losers[$i];
        $player2 = isset($current_losers[$i + 1]) ? $current_losers[$i + 1] : 'Exempt';

        if ($player2 !== 'Exempt') {
          $round_matches[] = ['player1' => $player1, 'player2' => $player2];
          $next_round_losers[] = rand(0, 1) ? $player1 : $player2;
        } else {
          $next_round_losers[] = $player1;
        }
      }

      // Vérifier si le tableau $round_matches est vide avant de l'ajouter
      if (!empty($round_matches)) {
        $bracket['losers'][] = ['round_name' => $round_names_losers[$round], 'matches' => $round_matches];
      }

      $current_losers = $next_round_losers;
    }

    return $bracket;
  }

  // Exemple de participants
  $participants = [
    'Joueur 1', 'Joueur 2', 'Joueur 3', 'Joueur 4',
    'Joueur 5', 'Joueur 6', 'Joueur 7', 'Joueur 8',
    'Joueur 9', 'Joueur 10', 'Joueur 11', 'Joueur 12',
    'Joueur 13', 'Joueur 14', 'Joueur 15', 'Joueur 16'
  ];

  // Générer le bracket à double élimination
  $bracket = genererDoubleEliminationBracket($participants);
  ?>

  <div class="theme">
    <div class="bracket">
      <?php foreach ($bracket['winners'] as $round): ?>
        <div class="column">
          <?php if (isset($round['round_name'])): ?>
            <h3><?php echo $round['round_name']; ?></h3>
          <?php endif; ?>
          <?php if (!empty($round['matches'])): ?>
            <?php foreach ($round['matches'] as $match): ?>
              <div class="match">
                <div class="match-top team">
                  <span class="name"><?php echo $match['player1']; ?></span>
                </div>
                <div class="match-lines">
                  <div class="line one"></div>
                  <div class="line two"></div>
                </div>
                <div class="match-bottom team">
                  <span class="name"><?php echo $match['player2']; ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>

      <div class="column losers-column">
        <?php foreach (array_reverse($bracket['losers']) as $round): ?>
          <div class="round">
            <?php if (isset($round['round_name'])): ?>
              <h3><?php echo $round['round_name']; ?></h3>
            <?php endif; ?>
            <?php if (!empty($round['matches'])): ?>
              <?php foreach ($round['matches'] as $match): ?>
                <div class="match">
                  <div class="match-top team">
                    <span class="name"><?php echo $match['player1']; ?></span>
                  </div>
                  <div class="match-lines alt">
                    <div class="line one"></div>
                    <div class="line two"></div>
                  </div>
                  <div class="match-bottom team">
                    <span class="name"><?php echo $match['player2']; ?></span>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="winner-section">
        <h3>Vainqueur</h3>
        <div class="match">
          <div class="match-top team">
            <span class="name"><?php echo isset($bracket['winners'][4][0]['player1']) ? $bracket['winners'][4][0]['player1'] : ''; ?></span>
          </div>
          <div class="match-lines">
            <div class="line one"></div>
            <div class="line two"></div>
          </div>
          <div class="match-bottom team">
            <span class="name"><?php echo isset($bracket['winners'][4][0]['player2']) ? $bracket['winners'][4][0]['player2'] : ''; ?></span>
          </div>
        </div>
      </div>

    </div>
  </div>
</body>
</html>

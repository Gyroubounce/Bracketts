<?php
session_start();

// Obtenir la connexion à la base de données
require_once(__DIR__ . '/../includes/db.php');
// Inclure le modèle Competition
require_once(__DIR__ . '/../models/Competition.php');


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

    // Boucle pour les tours de winners bracket
    for ($round = 1; $round <= 4; $round++) {
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

        $bracket['winners'][] = $round_matches;
        $remaining_winners = $next_round_winners;
    }

    // Boucle pour les tours de losers bracket
    $current_losers = array_splice($losers_pool, 0, 8);
    for ($round = 1; $round <= 4; $round++) {
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

        $bracket['losers'][] = $round_matches;
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

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bracket à Double Élimination</title>

    <?php require_once(__DIR__ . '/../includes/header.php'); ?>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            padding: 20px;
        }

        .bracket {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            margin-top: 20px;
        }

        .round {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0 20px;
        }

        .match {
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            width: 200px;
            text-align: center;
            position: relative;
        }

        .match a {
            color: #333;
            text-decoration: none;
            font-weight: bold;
        }

        .match a:hover {
            text-decoration: underline;
        }

        .connector {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 2px;
            background-color: #ccc;
            z-index: -1;
        }

        .connector.left {
            left: -20px;
        }

        .connector.right {
            right: -20px;
        }

        .rounds-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .rounds-container .round {
            flex: 0 0 auto;
            margin: 0 10px;
        }

        h2 {
            margin-top: 0;
            text-align: center;
        }

        h3 {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Bracket à Double Élimination</h1>
    <div class="bracket">
        <div class="rounds-container">
            <div class="round">
                <h2>Winners Bracket</h2>
                <?php for ($round_num = 1; $round_num < count($bracket['winners']); $round_num++): ?>
                    <div class="round-content">
                        <h3>Tour <?= $round_num ?></h3>
                        <?php foreach ($bracket['winners'][$round_num] as $match): ?>
                            <div class="match">
                                <a href="#"><?= htmlspecialchars($match['player1']) ?></a>
                                <span class="connector right"></span>
                                <a href="#"><?= htmlspecialchars($match['player2']) ?></a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endfor; ?>
            </div>
            <div class="round">
                <h2>Losers Bracket</h2>
                <?php foreach ($bracket['losers'] as $round_num => $round): ?>
                    <div class="round-content">
                        <h3>Tour <?= $round_num + 1 ?></h3>
                        <?php foreach ($round as $match): ?>
                            <div class="match">
                                <a href="#"><?= htmlspecialchars($match['player1']) ?></a>
                                <span class="connector right"></span>
                                <a href="#"><?= htmlspecialchars($match['player2']) ?></a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>

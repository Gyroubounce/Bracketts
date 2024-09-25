<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($nomCompetition) ? htmlspecialchars($nomCompetition) : 'Tournoi de Ping Pong'; ?> - Inscription</title>
    <link rel="stylesheet" href="/bracketts/style.css">
</head>
<body>
    <header>
        <h1><?php echo isset($nomCompetition) ? htmlspecialchars($nomCompetition) : 'Tournoi de Ping Pong'; ?></h1>
            <nav>
                <ol>
                    <li class="menu"><a href="/bracketts/views/initialiser_competition.php">Compétitons</a>
                        <div>
                            <ol class="sub-menu">
                                <li><a href="/bracketts/views/inscription_joueurs.php">Inscription Joueurs</a></li>
                                <li><a href="/bracketts/views/inscription_juges.php">Inscription des Juges</a></li>
                                <li><a href="/bracketts/views/combinaisons.php">Combinaisons</a></li>
                            </ol>
                        </div>
                    </li>
                    <li class="menu"><a href="/bracketts/views/liste_joueurs.php">Liste des Inscrits</a>
                        <div>
                            <ol class="sub-menu">
                                <li><a href="/bracketts/views/affichage.php">Affichage</a></li>
                                <li><a href="/bracketts/views/chatgpt.php">chatgpt</a></li>
                                <li><a href="/bracketts/views/bienvenue.php?id=1">bienvenue</a></li>
                            </ol>
                        </div>
                    </li>
                    <li class="menu"><a href="/bracketts/views_tour/eliminatoire.php">Tableau éliminatoire</a>
                        <div>
                            <ol class="sub-menu">
                                <li><a href="/bracketts/views_tour/T2_eliminatoire.php">T2_eliminatoire</a></li>
                                <li><a href="/bracketts/views_tour/T3_eliminatoire.php">T3_eliminatoire</a></li>
                                <li><a href="/bracketts/views_tour/T4_eliminatoire.php">T4 eliminatoire</a></li>
                            </ol>
                        </div>
                    </li>
                    <li class="menu"><a href="/bracketts/views_tour/résultat.php">Résultats</a>
                        <div>
                            <ol class="sub-menu">
                                <li><a href="/bracketts/views_tour/T2_résultat.php">Résultat T2</a></li>
                                <li><a href="/bracketts/views_tour/T3_résultat.php">Résultat T3</a></li>
                                <li><a href="/bracketts/views_tour/T4_résultat.php">Résultat T4</a></li>
                            </ol>
                        </div>
                    </li>
                    <li class="menu"><a href="/bracketts/views_tour/classement.php">Classement</a> 
                        <div>
                            <ol class="sub-menu">
                                <li><a href="/bracketts/views_tour/T2_classement.php">Classment T2</a></li>
                                <li><a href="/bracketts/views_tour/T3_classement.php">Classment T3</a></li>
                                <li><a href="/bracketts/views_tour/T4_classement.php">Classment T4</a></li>
                            </ol>
                        </div>
                    </li>   
                    <li class="menu"><a href="/bracketts/views/bracket.php">Brackets</a>
                        <div>
                            <ol class="sub-menu">
                                <li><a href="/bracketts/views/test.php">Test</a></li>
                                <li><a href="/bracketts/views/koi.php">koi</a></li>
                                <li><a href="/bracketts/views/correctionsbis.php">correctionsbis</a></li>
                            </ol>
                        </div>
                    </li> 
                    <li class="menu"><a href="match_individuel.php">Correction Impair</a>
                        <div>
                            <ol class="sub-menu">
                                <li><a href="juges.php">Juges</a></li>
                                <li><a href="match_individuel.php">individuel</a></li>
                                <li><a href="inscription_juge.php">Inscription des Juges</a></li>
                            </ol>
                        </div>
                    </li>
                </ol>
            </nav>
    </header>
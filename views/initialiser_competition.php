<?php require_once(__DIR__ . '/../includes/header.php'); ?>

<main>
    <h2>Initialiser le tournoi</h2>
    <form action="index.php" method="POST">
        <label for="nom">Nom de la Compétition :</label><br>
        <input type="text" id="nom" name="nom" required><br>

        <label for="tournoi">Nom officiel :</label><br>
        <input type="text" id="tournoi" name="tournoi" required><br>

        <label for="lieu">Lieu :</label><br>
        <input type="text" id="lieu" name="lieu" required><br>

        <label for="juge">Option des juges :</label>
        <select name="juge" id="juge">
            <option value="">--Please choose an option--</option>
            <option value="fiche">Fiche d'inscription</option>
            <option value="selection">Sélection automatique</option>
            <option value="saisie">Saisie manuelle</option>
        </select>

        <input type="submit" value="Créer">
    </form>
</main>

<?php require_once(__DIR__ . '/../includes/footer.php'); ?>

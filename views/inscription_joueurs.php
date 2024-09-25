
<?php require_once(__DIR__ . '/../includes/header.php'); ?>

<main>
        <form action="/bracketts/controllers/JoueurController.php" method="POST">
            <label for="nom">Nom du joueur :</label><br>
            <input type="text" id="nom" name="nom" required><br>
            
            <label for="age">Âge :</label><br>
            <input type="number" id="age" name="age" required><br>

            <label for="categorie">Catégorie :</label><br>
            <input type="text" id="categorie" name="categorie" required><br>
            
            <label for="division">Division :</label><br>
            <input type="text" id="division" name="division" required><br>
            
            <label for="club">Club :</label><br>
            <input type="text" id="club" name="club" required><br>
            
            <label for="email">Email :</label><br>
            <input type="email" id="email" name="email" required><br>
                    
            <input type="submit" value="S'inscrire">
        </form>
    </main>



<?php require_once(__DIR__ . '/../includes/footer.php'); ?>
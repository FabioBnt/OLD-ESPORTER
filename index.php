<?php
    include './modele/Connexion.php';
    $connx = Connexion::getInstance();
    if (isset($_GET['SeDeconnecter'])) {
        $connx->seDeconnecter();
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css" />
    <title>E-Sporter Manager</title>
</head>
<body class="accueil">
    <!--Menu de navigation-->
    <header>
            <div class="menunav">
                <button class="buttonM" onclick="window.location.href='./index.php'">Accueil</button>
                <button class="buttonM" onclick="window.location.href='./page/ListeTournois.php'">Liste des Tournois</button>
                <button class="buttonM" onclick="window.location.href='./page/Classement.php'">Classement</button>
            </div>
            <div class="menucenter">
                <img class="logo" src="./img/logo header.png" alt="Logo Esporter">
            </div>
            <div class="menuright">  
                    <?php 
                        if($connx->getRole() === Role::Visiteur){
                            echo '<a href="./page/ConnexionPage.php" id="connexion">Se Connecter</a>';
                        }else{
                            echo '<div class="disconnect"><h3>Bonjour, '.$connx->getIdentifiant().'</h3>'.' <a href="index.php?SeDeconnecter=true" id="deconnexion">Deconnexion</a></div>';
                        }
                    ?>
            </div>      
    </header>
    <main class="accueilmain">
        <div class="mainA">
            <div class="titre">
                <h1> Gestionnaire d'une saison de compétition d'E-Sport </h1>
            </div>
            <div id="divbutton">
                <?php 
                        if($connx->getRole() == Role::Administrateur){
                            echo "<button class='buttonM' onclick="."window.location.href='./page/CreerTournoi.php';"." type='button'> Créer un tournoi </button>";
                        };
                    ?>
                <button class="buttonM" onclick="window.location.href = './page/ListeEquipe.php';" type="button"> Liste des équipes </button>
                <?php 
                        if($connx->getRole() == Role::Administrateur){
                            echo "<button class='buttonM' onclick="."window.location.href='./page/CreerEcurie.php';"." type='button'> Créer une écurie </button>";
                        };
                    ?>
            </div>
        </div>
    </main>
</body>
</html>
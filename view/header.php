


<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?php echo $page['title']; ?> - CPlay</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700|Saira:700" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.4.3/css/foundation.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
        <link rel="stylesheet" href="public/css/app.css" />
    </head>
    <body>
        <header>
            <div class="grid-x">
                <div class="small-12 medium-5 cell">
                    <div class="header-content">
                        <a href="list.php">Bibliothèque</a>
                        <a href="contest.php">Concours</a>
                        <a href="premium.php">Premium</a>
                    </div>
                </div>
                <div class="small-12 medium-2 cell text-center">
                    <div class="header-content main">
                        <h1>CPlay</h1>
                    </div>
                </div>
                <div class="small-12 medium-5 cell text-right">
                    <div class="header-content">

                        <?php

                        /*Ici, je change ma condition, je récupère mon tableau $_SESSION, et si la case userConnected renvoi true (j'ai demandé a ce que ce soit le cas si la connexion soit réussi), alors l'avatar, le pseudo et la possibilité d'aller sur la page logout pour se déconnecter s'afficheront. Sinon, mes pages login et register.*/

                        if( isset($_SESSION["userConnected"]) && $_SESSION["userConnected"]){ ?>
                            <img class="avatar" src="http://www.francesoir.fr/sites/francesoir/files/vice-versa-colere-francesoir_field_image_diaporama.jpg" />
                            <?php echo $_SESSION["user"]["username"]; ?>
                            <a href="logout.php">Déconnexion</a>
                        <?php }else{ ?>
                            <a href="register.php">Inscription</a>
                            <a href="login.php" class="login">Connexion</a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </header>

        <div class="container">

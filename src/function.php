<?php
function averageReview( $reviews ){
    $sum = 0; // met a 0 pour faire addition dessus
    foreach( $reviews as $review ){
        $sum += $review; // fait la somme de chaque note
    }
    return round( $sum / count( $reviews ), 1 ); // fait le calcul
}

function getGameType( $identifier ){
    return $GLOBALS['types'][ $identifier ];
}

function getShortDescription( $description, $size = 40 ){
    if( strlen( $description ) > $size ){
        return substr( $description, 0, $size ) . '...';
    }

    return $description;
}

function getShortReview( $review ){
    if( $review < 10 ){
        return 'star-o';
    }

    if( $review < 15 ){
        return 'star-half-o';
    }

    return 'star';
}

function needTriggerWarning( $type ){
    if( $type == "Horreur" || $type == "Adulte" ){
        return true;
    }

    return false;
}

function getFrenchDate( $date, $time = false ){
    $time = strtotime( $date );
    if( $time ){
        return date( 'd/m/Y - H:i', $time );
    }

    return date( 'd / m / Y', $time );
}


        /*Ma fonction permettant l'upload. D'abord je passe deux paramètres, file et config.
        uploadPicture( $_FILES['picture_file'] = $file, $uploadConfig = $config ); */


function uploadPicture( $file, $config ){
    if( $file['size'] > $config['max'] ){   //si le fichier est trop gros, je renvoi le tableau suivant
        return array(
            'status' => false,
            'message' => 'Le fichier est trop volumineux',
        );
    }


/*Ici je compare le type du fichier au tableau allows que j'ai défini, avec les types autorisés dedans*/
    if( !in_array( $file['type'], $config['allows'] ) ){    
        return array(
            'status' => false,
            'message' => 'Ce type de ficher n\'est pas autorisé',
        );
    }

    /*Si il n'y pas d'erreur, je rentre ici.*/
    /*avec $ext je récupére l'extension pour bien nommé le fichier*/
    /*Avec Name je défini un nom pour le fichier, ce sera media_ et l'extension récupérer*/
    /* avec $path je demande a ce que l'upload soit dans public/data/lenomquejeviensdegénérer*/

    $ext = pathinfo( $file['name'], PATHINFO_EXTENSION );
    $name = uniqid( 'media_' ) . '.' . $ext; //uniq it crée un nom unique
    $path = $config['path'] . $name;

    // si ça s'est bien passé, je récupére avec le tableau suivant le chemin.
    move_uploaded_file( $file['tmp_name'], $path );
    return array(
        'status' => true,
        'path' => $path,
    );
}


        /* s'il y a une erreur je l'affichage dans un ul, même chose avec success*/

function showError( $errors ){
    if( !empty( $errors ) ){ ?>
        <div class="small-12 cell">
            <div class="callout alert">
                <ul><?php echo $errors; ?></ul>
            </div>
        </div>
     <?php }
}
function showSuccess( $success ){
    if( !empty( $success ) ){ ?>
        <div class="small-12 cell">
            <div class="callout success">
                <ul><?php echo $success; ?></ul>
            </div>
        </div>
     <?php }
}

function addLog( $message ){
    $file = fopen( "./logs/" . date("Y-m-d") . ".log", "a+" );
    fputs($file, date("Y-m-d H:i:s") . " - " . $message . "\n" );
    fclose( $file );
}


/* DANS MA FONCTION LOGIN je met ma super variable session ou je défini userconnected en true et ou je stock un tableau contenant toute les données de l'user

dans la variable keychain, je stock le return de ma fonction generatecookiekeychain, la voir plus bas.

avec setcookie, je créer un cookie ! Donc quand l'utilisateur se connecte il aura ce cookie sur son ordinateur. Dans ce cookie, je stock le résultat de ma fonction generatecookiekeychain, qui est en fait l'id en claire et une chaine codé comportant plusieurs élément.

*/

function login ( $user ){
    $_SESSION["userConnected"] = true;
    $_SESSION["user"] = $user;

    $keychain = generateCookieKeychain ( $user ); // = $kc
    setcookie("autoauth", $keychain, time() + 15 * 24 * 3600, null, null, false, true );
}



/*Cette fonction va générer une chaine de caractère unique pour chaque
user qui va me permettre de l'identifier*/
function generateCookieKeyChain( $user ){
    $kc = $user['id'];
    $kc .= "____";
    $kc .= md5( $user["username"] . $user["email"] . $user["password"]);
    return $kc;
     
}

/*Donc, on a dans la fonction login, logé l'utilisateur et on lui a envoyé dans un cookie une chaine permettant unique. Quand il va quitter la page il ne sera plus logger, mais il aura toujours son cookie avec sa chaine unique dedans. On va utiliser celà pour le reconnecter. Direction, APP.PHP*/



/*a partir d'ici, fonctions pour générer des token*/

function generateResetToken(){
    return md5 ( uniqid( rand( ), true ) );
}


function checkToken($token, $id){

    $dbConfig = [
    'host' => 'localhost',
    'user' => 'cplay',
    'pass' => 'cplay',
    'name' => 'cplay',
];

/*
    $dsn = "mysql:host=" . $dbConfig['host'] . ";dbname=" . $dbConfig['name'] . ";charset=utf8";
    $db = new PDO( $dsn, $dbConfig['user'], $dbConfig['pass'] );*/
    global $db;
    
    $stmt = $db -> prepare('SELECT * FROM users WHERE id = ?');
    $stmt -> bindValue(1, htmlspecialchars( $id ));
    $stmt -> execute();

    if ($user = $stmt -> fetch() ) {

        $userToken = $user["reset_token"];
        $time = strtotime($user["reset_expire"]);

        if ($userToken === $token && time() < $time) {

            return true;
            
        }else{

            return false;
        }
    }


}
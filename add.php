<?php
$page = [
    'title' => 'Ajouter un jeu',
    'premium' => false,
    'admin' => true,
];

require_once 'src/app.php';

/*Pas de paramètre de l'exterieur (de l'utilisateur) donc QUERY
ces données sauront utile pour les listes du formulaire.*/

$stmt = $db->query( 'SELECT * FROM games_type' );
$types = $stmt->fetchAll();

$stmt = $db->query( 'SELECT * FROM editor' );
$editors = $stmt->fetchAll();

$stmt = $db->query( 'SELECT * FROM developer' );
$developers = $stmt->fetchAll();

/*création de deux variables vide dans lesquelles on va mettre des trucs.*/
$errors = "";
$success = "";

        /*Il faut savoir si le formulaire a déjà été envoyé, pour éviter une arrivé sur une erreur.
        On prend donc un champ text, comme name, et on vérifie qu'il y ai eu une valeur, même vide.
        Si bien que si il y a eu un envoi, même vide, la condition sera vérifié.*/


if( isset( $_POST['name'] ) ){

    /*on vérifie la présence de tout les formulaires, pour chaque erreur, on ajoute un message d'erreur dans la variable errors*/ 

    /*On utilise if plutôt que else if pour qu'il y ai une possibilité d'avoir plusieurs erreur (else if = sinon si, ne peux rentrer que dans une condition)*/

    /*le .= est une concaténation, permettra de concatener dans la variable error*/

    if( empty( $_POST['name'] ) ){
        $errors .= "<li>Vous devez renseigner le nom de votre jeu</li>";
    }

    if( empty( $_POST['description'] ) ){
        $errors .= "<li>Vous devez renseigner la description de votre jeu</li>";
    }

    if( empty( $_POST['type'] ) ){
        $errors .= "<li>Vous devez renseigner le type de votre jeu</li>";
    }

    if( empty( $_POST['editor'] ) ){
        $errors .= "<li>Vous devez renseigner l'éditeur de votre jeu</li>";
    }

    if( empty( $_POST['developer'] ) ){
        $errors .= "<li>Vous deveez renseigner le développeur de votre jeu</li>";
    }

        /*Si jamais le champ d'url est vide et qu'il y a une erreur sur l'upload, il va m'envoyer un message me disant d'ajouter un média*/

    if( empty( $_POST['picture_link'] ) && $_FILES['picture_file']['error'] != 0 ){
        $errors .= "<li>Vous deveez télécharger ou ajouter le lien d'un média pour votre jeu</li>";
    }

/*    Si errors est tjr une chaine de caractère vide, on rentre dans le if suivant, dans lequel on va préparer notre requête, un insert games avec tout les champ listé, avec les values qui sont des placeholder associés. Puis je prépare la requête. */

    if( empty( $errors ) ){
        $query = 'INSERT INTO games ( name, picture, type_id, description,
            release_date, press_review, player_review,
            developer_id, editor_id ) VALUES ( :name, :picture,
                :type, :description, :release_date, :press_review,
                :player_review, :developer, :editor )';


        /*On a ici des trucs envoyé par l'user, donc j'utilise un prepare pour me prémunir des différentes attaques*/

        $stmt = $db->prepare( $query );

            /*htmlspecialchars permet d'empêcher l'injection de code*/

        $stmt->bindValue( 'name', htmlspecialchars( $_POST['name'] ) );
        $stmt->bindValue( 'type', htmlspecialchars( $_POST['type'] ) );
        $stmt->bindValue( 'description', htmlspecialchars( $_POST['description'] ) );
        $stmt->bindValue( 'developer', htmlspecialchars( $_POST['developer'] ) );
        $stmt->bindValue( 'editor', htmlspecialchars( $_POST['editor'] ) );


        /*En dessous, les trois champs non obligatoire. J'injecte la valeur NULL si ils sont vides.*/
        /*condition ternaire= (condition) ? Ce qui se passe si c'est rempli : ici si c'est pas rempli*/
        /*Oublie du htmlspecialchars() mais toujours nécessaireeee*/

        $release_date = ( !empty( $_POST['release_date'] ) ) ? $_POST['release_date'] : NULL;
        $stmt->bindValue( 'release_date', $release_date );

        $press_review = ( !empty( $_POST['press_review'] ) ) ? $_POST['press_review'] : NULL;
        $stmt->bindValue( 'press_review', $press_review );

        $player_review = ( !empty( $_POST['player_review'] ) ) ? $_POST['player_review'] : NULL;
        $stmt->bindValue( 'player_review', $player_review );

        /*Ensuite, si le champ d'url est rempli je l'envoi, sinon j'appel ma fonction créer moi même,
        uploadPicture*/

        if( !empty( $_POST['picture_link'] ) ){
            
            $getImg = file_get_contents ($_POST['picture_link'] );
            $ext = pathinfo( $_POST['picture_link'], PATHINFO_EXTENSION );
            $name = 'public/data/' .  uniqid( 'media_' ) . '.' . $ext; 
            file_put_contents($name, $getImg);
            $stmt->bindValue( 'picture', $name );

        }else{
            $result = uploadPicture( $_FILES['picture_file'], $uploadConfig ); 

            //parametre $uploadconfig 
            // $_FILE super variable pour l'upload

            if( $result['status'] ){
                $stmt->bindValue( 'picture', $result['path'] );
            }else{
                $errors .= '<li>' . $result['message'] . '</li>'; /*affichage du tableau d'erreur*/
            }
        }
        /*Si aucune erreur, on prévient que c'est cool*/
        if( empty( $errors ) ){
            if( $stmt->execute() ){
                $success = "Votre jeu à été publié";
                addLog( "Ajout de jeu" . htmlspecialchars($_POST['name']) );
                
            }else{
                /*Si ça c'est pas bien passé, on concatène dans errors une nvl erreur*/
                $errors .= 'Une erreur est survenue lors de la publication';
                addLog("Erreur lors de l'insertion d'un jeu en DB");
            }
        }
    }
}

/*Inclusion du header (html après php)*/

require_once 'view/header.php';
?>

<h2>Ajouter un jeu</h2>

<form method="post" enctype="multipart/form-data" class="grid-x add">
    <?php echo showError( $errors ); ?>
    <?php echo showSuccess( $success ); ?>

    <div class="small-12 medium-6 cell">
        <input type="text" name="name" placeholder="Nom du jeu" />
        <textarea name="description" rows="6" placeholder="Description du jeu"></textarea>

        <input type="number" name="press_review" placeholder="Note de la presse" />
        <input type="number" name="player_review" placeholder="Note des joueurs" />
    </div>

					<!-- Construction des formulaires select -->

    <div class="small-12 medium-6 cell">
        <select name="type">
            <option selected disabled class="placeholder">Type du jeu</option>
            <?php foreach( $types as $type ){ ?>
                <option value="<?php echo $type['id']; ?>"><?php echo $type['genre']; ?></option>
            <?php } ?>
        </select>

        <select name="editor">
            <option selected disabled class="placeholder">Editeur du jeu</option>
            <?php foreach( $editors as $editor ){ ?>
                <option value="<?php echo $editor['id']; ?>"><?php echo $editor['name']; ?></option>
            <?php } ?>
        </select>

        <select name="developer">
            <option selected disabled class="placeholder">Développeur du jeu</option>
            <?php foreach( $developers as $developer ){ ?>
                <option value="<?php echo $developer['id']; ?>"><?php echo $developer['name']; ?></option>
            <?php } ?>
        </select>

        <input type="text" name="release_date" placeholder="Date de sortie" onfocus="this.type='date'" onblur="this.type='text'" />


										
										<!-- Upload -->
										
        <h3>Média du jeu</h3>
        <input type="file" name="picture_file" />
        <p class="separator"><span>OU</span></p>
        <input type="url" name="picture_link" placeholder="Lien vers le média" />
    </div>

    <input type="submit" value="Ajouter" class="button expanded" />
</form>

<?php
require_once 'view/footer.php';
?>

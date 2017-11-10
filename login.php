<?php

$page = [
    'title' => 'login',
    'premium' => false,
    'admin' => false,
];

require_once 'src/app.php';


/*A nouveau, je vais définir des variables vides success et errors pour afficher les messages. Sachant que j'utilise success pour tester la connection au début de l'exercice. A terme, la connection redirigera l'utilisateur sur une autre page. Je vais aussi utiliser isset a nouveau pour que l'utilisateur qui arrive sur la page n'est pas de message.*/
// en fait j'ai jarter sucess car on en avait pas besoin héhé.


$errors = "";



if ( isset( $_POST["email"] ) ) {


	/*Ici, avec checkMail je récupère dans ma base de donnée le mail que l'utilisateur a rentré dans le formulaire de connexion.*/

	$checkMail = $db->prepare('SELECT * FROM users WHERE email = ?');
	$checkMail -> bindValue(1, htmlspecialchars($_POST["email"]) );
	$checkMail -> execute();

/*Je vérifie dans le if suivant que fetch arrive a récupérer le mail rentré par l'utilisateur. Je stock aussi les résultat du fetch (donc la ligne tableau de l'utilisateur) dans la variable User*/


	if ( $user = $checkMail ->fetch() ) {

		/*Si le mail est trouvé dans ma base de donnée, je procède a la vérification du password avec la fonction password verify. Je met deux paramètre, le comparant et le comparé. Le comparé étant ce que je récupère de l'input "password", le comparant est la clé hashé du tableau user, qui est en fait le password encodé. La fonction va vérifier que les deux correspondent*/

		if (password_verify( $_POST["password"], $user["password"] ) ){

			/*J'APPELLE désormais une fonction login pour loger l'user, il faut s'y réferer dans function.php*/

			login( $user );

			header("location: list.php");
		}else{
			$errors .= "<li>Identifiants non reconnus</li>";
		}
	}else{
		$errors .= "<li>Identifiants non reconnus</li>";
	}
}

/*Si tout a fonctionné, l'utilisateur est maintenant connecté ! Je vais aller changer les valeurs en dur sur la page header*/


require_once 'view/header.php';
?>

 <h2>Connectez vous</h2>

 <form method="post" class="grid-x add">
		<?php echo showError( $errors ); ?>


     <div class="small-12 medium-3 cell">
         <input type="email" name="email" placeholder="votre mail">
         <input type="password" name="password" placeholder="Votre mot de passe" />
     </div>

     <input type="submit" value="Valider" class="button expanded" />
 </form>

<a href="password_recovery.php">Oubli du mot de passe</a>

<?php
require_once 'view/footer.php';
 ?>

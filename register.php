<?php
$page = [
    'title' => 'Inscription',
    'premium' => false,
    'admin' => false,
];

require_once 'src/app.php';



$success = "";
$errors = "";


/*Avec isset, je vérifie que le formulaire a déjà été envoyé pour ne pas envoyer de messages d'erreurs à l'utilisateur qui arrive sur la page pour la première fois*/
if( isset( $_POST['username'] ) ){

/*Dans isset, je fais mes vérifications, d'input en input.*/

	if( empty( $_POST['username'] ) ){ // si l'username est pas rempli
	    $errors .= "<li>Commenkon tinscri ss pseudo lol</li>";
	}

	if ( strlen( $_POST['username'] ) < 4 ) { // si l'username fait moins de 4 caractères
		$errors .= "<li>O moin 4 caracter dans le pseudo merci</li>";
	}

	if( empty( $_POST['email'] ) ){ // Si le mail est pas rempli
	    $errors .= "<li>i nou fo ton mail pr te spamHEU te contacté mci</li>";
	}

	if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
		// j'utilise une fonction qui vérifie si c'est un mail. Si elle retourne faux, j'envoi une erreur.

		$errors .= '<li>Wsh c pa une adress mail sa. Arret. Arret stp. C deja assé dur com sa au joure le joure. Jé perdu mon chien, épi de blé. Il é mor ds une moissonneuse batteuse  <img src="https://cdn.themysteriousworld.com/wp-content/uploads/2015/01/icelandicsheepdog.jpg"></li>';
	}

/*	Ici, je vais chercher dans ma base de donnée un mail grâce a un paramètre. Je décide que ce paramètre soit: "trouve un mail qui soit pareil que celui que la personne utilise pour s'inscrire"*/

	$checkMail = $db->prepare('SELECT * FROM users WHERE email = ?');
	$checkMail -> bindValue(1, $_POST["email"]);
	$checkMail -> execute();

/*	Si jamais fetch fonctionne, donc si je récupère un mail identique, j'envoi un erreur indiquant que ce mail est déjà utilisé..*/

	if ( $checkMail ->fetch() ) {
		$errors .="<li>Mai atten, se mail, JE LE CONNAI !!! IL EXIST DEJA !!!!!</li>";
	}

	if( empty( $_POST['password'] ) ){ // si password vide
	    $errors .= "<li>si tu mé pa ton mot de pass, tora pa de mo de pass</li>";
	}



	if ( $_POST['password'] !== $_POST['checkpassword']) { // si les champs password et vérification password ne sont pas pareil..
		$errors .= "<li>C PA LES MM PASSWORD DU CON !</li>";
	}


	if (is_numeric($_POST['password'])) { //avec is numéric, je vérifie si le password ne contient que des chiffres. Si c'est le cas, j'envoi une erreur.
		$errors .= "<li>ta cru 12345 sa iré ? mé au moin une letttre !!!!</li>" ;
	}

	if (strlen($_POST['password']) < 8) { // Si le password est trop court
		$errors .= "<li>O moin 8 caracter dans le mdp merci</li>";
	}


/*	Si ma variable errors, dans laquelle j'ai envoyé les erreurs pour les afficher, est vide, alors je procède a l'ajout de l'utilisateur dans ma base de donnée !
*/
	if (empty( $errors ) ) {
		$query = 'INSERT INTO users (username, email, password) VALUES ( :username, :email, :password)';
		$stmt = $db->prepare( $query );

		$stmt->bindValue('username', htmlspecialchars( $_POST['username'] ) );
		$stmt->bindValue('email', htmlspecialchars( $_POST['email'] ) );

		/*Avec la fonction password_hash, je vais faire en sorte que le password soit envoyé sous forme de clé encodé dans le tableau, pour ne pas avoir de mot de passe en claire dans ma base de donnée.*/
		$stmt->bindValue('password', password_hash( $_POST['password'], PASSWORD_DEFAULT ) );


/*Avec la condition suivante, si l'inscription dans la BDD s'est bien déroulé, j'envoi un message, et j'envoi une erreur si c'est pas le cas. */
		if ( $stmt ->execute() ) {
			$success .= "Bienvenu ds la mifa !";
		}else{
			$errors .= "<li>erreur dans l'inscription</li>";
			var_dump($stmt->errorInfo());
		}
	}
}

require_once 'view/header.php';
 ?>


<!-- Le HTML de mon formulaire. -->

 <h2>Inscrivez-vous</h2>

 <form method="post" class="grid-x add">
			<?php echo showError( $errors ); ?>
	     <?php echo showSuccess( $success ); ?>

     <div class="small-12 medium-3 cell">
         <input type="text" name="username" placeholder="votre pseudo" />
         <input type="text" name="email" placeholder="votre mail">
         <input type="password" name="password" placeholder="Votre mot de passe" />
         <input type="password" name="checkpassword" placeholder="confirmez votre MDP">
     </div>

     <input type="submit" value="Valider" class="button expanded" />
 </form>



 <?php require_once 'view/footer.php'; ?>

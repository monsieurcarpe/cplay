<?php

$page = [
    'title' => 'Oubli de mot de passe',
    'premium' => false,
    'admin' => false,
];

require_once 'src/app.php';




$errors = "";
$success ="";

if(isset( $_POST [ "email" ] ) ){
	if ( empty ( $_POST [ "email" ] ) ) {
		$errors .= "<li>MAIS ENFIN REMPLI LE CHAMP MAIL ON VA PAS T'ENVOYER TOUT LES MDP DE NOTRE BASE DE DONNEES</li>";
	}


	if ( empty($errors) ) {

		$stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
		$stmt -> bindValue(1, htmlspecialchars($_POST["email"]) );
		$stmt -> execute();

		if ( $user = $stmt -> fetch() ) {
			$success .= "<li>envoi d'un mail pour récupérer le mot de passe (vérifiez votre dossier SPAM, car on envoi aussi plein de mails pour acheter des albums de Mylène Farmer.. Easy money.</li>";

			$token = generateResetToken();
			$expire = time() + 24 * 60 * 60 ;

			$stmt = $db->prepare("UPDATE users SET reset_token = :reset_token, reset_expire = :reset_expire WHERE id = :id");
			$stmt -> bindValue( "reset_token" , $token );
			$stmt -> bindValue( "reset_expire", date( "Y/m/d/h/i", $expire) );
			$stmt -> bindValue("id", $user["id"]);
			$stmt -> execute();

			$content = "http://localhost/cplay/password_forget.php?token=".$token."&id=".$user["id"];
			mail($user["email"], "MDP", $content);
			var_dump($content);
			
		}else{
			$errors .="<li>Ce mail n'existe pas, et nous en sommes désolé. Vraiment. Je... Je sais pas trop pourquoi il n'existe pas. Je sais même pas si c'est important d'exister, putain. <br><br> (note, cette erreur a été écrite par Romain Duris.)</li>";
		}

	}

}

require_once 'view/header.php';
?>

<h2>BAH ALORS ON OUBLIE SON MOT DE PASSE ?????</h2>

<form method="post" class="grid-x add">
	<?php echo showError( $errors ); ?>
	<?php echo showSuccess( $success ); ?>

	<div class="small-12 medium-12 cell">
		<input type="email" name="email" placeholder="votre mail">
	</div>

	<input type="submit" value="ON VA GERER CA MA GUEULE" class="button expanded" />
</form>



<?php require_once 'view/footer.php'; ?>
<?php

$page = [
    'title' => 'Définir un nouveau mot de passe',
    'premium' => false,
    'admin' => false,
];

require_once 'src/app.php';




$form = false;
$errors = "";



if ( !isset($_POST["password"] ) ) {

$token = $_GET["token"];
$id = $_GET["id"];

$form = checkToken($token , $id);


} else {

	if ($_POST['password'] !== $_POST["checkPassword"]) {
		$errors .= "<li>mais non, les mots de passe ne correspondent point !</li>";
	}

	if( empty( $_POST['password'] ) ){ 
	    $errors .= "<li>si tu mé pa ton mot de pass, tora pa de mo de pass</li>";
	}

	if (is_numeric($_POST['password'])) { 
		$errors .= "<li>ta cru 12345 sa iré ? mé au moin une letttre !!!!</li>" ;
	}

	if (strlen($_POST['password']) < 8) { 
		$errors .= "<li>O moin 8 caracter dans le mdp merci</li>";
	}

	if ( empty( $errors ) ) {

		$formupload = checkToken($_POST["token"] , $_POST["id"] );

		if ($formupload) {
			
			$stmt = $db ->prepare("UPDATE users SET password = ? , reset_expire = NULL , reset_token = NULL WHERE id = ?");

			$stmt-> bindValue(1, password_hash($_POST["password"], PASSWORD_DEFAULT ));
			$stmt-> bindValue(2, $_POST["id"]);
			if ( $stmt->execute() ){
				header("location: login.php");
			}

		}else{
			$errors = "erreur";
		}
	}
}

require_once 'view/header.php'; ?>

<?php if ($form) { ?>

<h2> Définir un nouveau mot de passe </h2>
<form method="post" class="grid-x add">

	<?php echo showError( $errors ); ?>


	<div class="small-12 medium-12 cell">
		<input type="password" name="password" placeholder="Nouveau MDP">
		<input type="password" name="checkPassword" placeholder="confirmez nouveau MDP">
		<input type="hidden" name="token" value="<?php echo $_GET["token"]; ?>">
		<input type="hidden" name="id" value="<?php echo $_GET["id"]; ?>">
	</div>

	<input type="submit" value="Changez votre MDP" class="button expanded" />
</form>

<?php }else{ ?>
	<h2>Erreur, le lien n'est peut être plus valide. Réessayez.</h2>
<?php } ?>

<?php require_once 'view/footer.php'; ?>
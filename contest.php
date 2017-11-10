<?php
$page = [
    'title' => 'Concours',
    'premium' => true,
    'admin' => false,
];

require_once 'src/app.php';
require_once 'view/header.php';

/*Condition permettant de vérifier si l'utilisateur a tenté sa chance, 
et le cas échéant tester avec une autre condition (dedans) si il est gagnant*/



$stmt = $db->prepare("SELECT contest_time FROM users WHERE id = ?");
$stmt->bindValue(1 , 1);
$stmt ->execute();
$contestTime = strtotime( $stmt->fetch()["contest_time"] );

if ( time() - $contestTime < $contestConfig["delay"]) {
	$canPlay = false;	
}else{
	$canPlay = true;
}

/*Vérification afin de voir si l'user a déjà jouer*/

if ( isset( $_GET["tryout"] ) && $canPlay === true) {
	
	/*Vérification gagnant / perdant*/

	if ($_GET["tryout"] == rand( 1, 4 ) ) {

		$stmt = $db->prepare( "SELECT * FROM games 
			LEFT JOIN users_game ON games.id = users_game.game_id
			WHERE users_game.user_id != ? OR users_game.user_id IS NULL
			ORDER BY RAND() LIMIT 1");
		$stmt->bindValue(1, 1);
		$stmt->execute();
		$wonGame = $stmt->fetch();


		$insertGame = $db->prepare("INSERT INTO users_game (game_id, user_id) VALUES (:game_id ,:user_id)");
		$insertGame->bindValue("game_id", $wonGame["id"]);
		$insertGame->bindValue("user_id", $_SESSION["user"]["id"]);
		$insertGame->execute();

		$won = true;

	}else{ /*perdant*/
		
		$won = false;
	}

	/*Update de la date ou il a participé pour l'empêcher de jouer sans arrêt*/

	$upDate = $db->prepare("UPDATE users SET contest_time = NOW() where id = ?");
	$upDate-> bindValue(1, 1);
	$upDate-> execute();

}


?>
<?php	if( isset($_SESSION["userConnected"]) && $_SESSION["userConnected"]){ ?>


<h2>C un concours frr</h2>

<div class="grid-x contest">

	<?php if( !isset( $won ) ){ ?>
		<?php if ( $canPlay !== true) { ?>
			<div class="small-12 cell">
				<div class="callout warning">
					<i class="fa fa-warning"></i> Vous avez déjà joué cette semaine
				</div>
			</div>
		<?php } ?>

	    <?php for( $i = 0; $i < 12; $i++ ){ ?>

	        <div class="small-6 medium-3 cell">
	            <a href="?tryout=<?php echo rand(1,4); ?>" class="box <?php echo ( $canPlay !== true) ? "disable" : " "; ?>">?</a>
	        </div>

	<?php } ?>

    <?php }elseif( $won === true ){ ?>
    	<div class="small-12 cell">
    		<p>Bravo, vous avez gagné <?php echo $wonGame["name"]; ?> - <a href="game.php?id=<?php echo $wonGame["id"]?>"> voir la fiche du jeu</a></p>
    	</div>
    <?php }else{ ?>
    	<div class="small-12 cell">
    		<p>Vous avez perdu ! Try again next week !</p>
    	</div>
    <?php } ?>
</div>

<?php }else{ ?>

<h2>Connectez vous pour accéder au concours :) </h2>

<?php } ?>
<?php
require_once 'view/footer.php';
?>

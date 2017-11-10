<?php
require_once 'config.php';
require_once 'function.php';

session_start();

if (!isset($_COOKIE["lang"])) {

	setcookie("lang", "fr", time() + 365*24*3600, null, null, false, true);

}

if (!isset($_COOKIE["trash"])) {

	setcookie("trash", "THIS USER IS GARBAGE !!!!", time() + 365*24*3600, null, null, false, true);
	
}


$isConnected = false;
$isPremium = true;
$isAdmin = true;

$dsn = "mysql:host=" . $dbConfig['host'] . ";dbname=" . $dbConfig['name'] . ";charset=utf8";
$db = new PDO( $dsn, $dbConfig['user'], $dbConfig['pass'] );


if( $page['premium'] && ( !isset($_SESSION["userConnected"]) || !$_SESSION['user']['premium']) ){
    header('Location: premium.php');
    exit();
}

if( $page['admin'] && ( !isset($_SESSION["userConnected"]) || !$_SESSION['user']['admin']) ){
    echo "Vous devez être admin pour accéder a cette section";
    exit();
}

/*C'EST ICI QUE CA SE JOUE MES PTITS AMI.ES
Dans cette condition, on va vérifier si la session de connexion n'existe pas, donc si l'user est pas connecté, ET on va aussi vérifier si il a le cookie autoauth, que je lui ai donné si il s'est loggé il y quelque temps sur mon site. 

Si c'est le cas, on rentre dans la condition. Et la fête commence.

On stock dans $keychain le cookie autoauth, pour rappelle, il contient une chaine unique a l'utilisateur.

Avec explode, on va demander a PHP de séparer cette chaine en deux élément, ce qu'il y avant le "_____" et ce qu'il y a après. Ce qu'il y a avant, c'est le premier élement, donc [0]

Et cet élément là, c'est l'id. 

On va récupérer l'id comme on a fait pour vérifier si un mail existait déjà dans notre base, donc on demande a récupérer le même ID contenue dans userId. 

Si jamais on le trouve, on rentre dans la condition suivante puisque le fetch a fonctionné.

Puis on va comparer deux choses,
On va prendre keychain qui contient la chaine unique qui permet d'identifier un utilisateur. 
Et on va réutiliser notre fonction generatecookiekeychain sur le tableau user de l'utilisateur, qui est contenue dans $user.

Pour rappel, generatecookiekeychain() prend les infos de l'utilisateur et crée une chaine unique. Donc normalement, les deux chaines ayant pris les même info, elles seront identique. Si jamais c'est le cas, j'appelle ma fonction login.

Donc je vais le reloger automatiquement et lui redonner le cookie, donc le mettre a jour pour qu'il n'expire pas.

Simple. N'est ce pas ?*/

if ( !isset ( $_SESSION["userConnected"] ) && isset($_COOKIE["autoauth"] ) ) {
	$keychain = $_COOKIE["autoauth"];
	$userId = explode('____', $keychain )[0];

	$stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
	$stmt -> bindValue(1, htmlspecialchars($userId));
	$stmt -> execute();

	if ( $user = $stmt ->fetch() ) {
		if ($keychain === generateCookieKeyChain( $user )) {
			login( $user );
		}
	}
}


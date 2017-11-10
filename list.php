<?php
$page = [
    'title' => 'Bibliothèque',
    'premium' => false,
    'admin' => false,
];

require_once 'src/app.php';

$query = 'SELECT *, games.id AS gid FROM games LEFT JOIN games_type ON games.type_id = games_type.id';
if( !empty( $_GET['search'] ) ){
    $stmt = $db->prepare( $query . " WHERE games.name LIKE CONCAT('%', ? ,'%')" ); 
    /*like va ressembler au paramètre ?. %: il peut y avoir des paramètres avant ou après.
    La fonction concat permet de concatener le fait qu'il peut y avoir des caractères avant / après*/
    $stmt->bindValue( 1, $_GET['search'] );
    $stmt->execute();
}else{
    $stmt = $db->query( $query );
}

$games = $stmt->fetchAll();

require_once 'view/header.php';
?>

<h2>Bibliothèque</h2>

<p>
    <?php
    if( ( $counter = $stmt->rowCount() ) > 0 ){
        echo "Il y a actuellement " . $counter . " jeu(x) dans la bibliothèque";
    }else{
        echo "Il n'y a actuellement aucun jeu dans la bibliothèque";
    }
    ?>
</p>

<form method="get" class="grid-x search-box">
    <div class="small-11 cell">
        <input type="search" name="search" placeholder="Rechercher..." class="search-box_input" />
    </div>
    <div class="small-1 cell">
        <button type="submit" class="search-box_button">
            <i class="fa fa-search"></i>
        </button>
    </div>
</form>

<div class="grid-x games">
    <?php foreach( $games as $key => $game ){
        if( !empty( $_GET['search'] ) && stripos( $game['name'], $_GET['search'] ) === false ){
        continue;
        }
        ?>

        <div class="small-12 medium-4 cell text-center">
            <a href="game.php?id=<?php echo $game['gid']; ?>" class="item right" data-tooltip title="<?php echo getShortDescription( $game['description'], 80 ) ?>">
                <h3><?php echo $game['name']; ?></h3>
                <div class="picture" style="background-image:url('<?php echo $game['picture']; ?>');">
                    <?php if( !empty( $game[ 'press_review' ] ) && !empty( $game[ 'player_review' ] ) ){ ?>
                        <div class="shortReview">
                            <?php $reviews = [ $game[ 'press_review' ], $game[ 'player_review' ] ]; ?>
                            <i class="fa fa-<?php echo getShortReview( averageReview( $reviews ) ); ?>"></i>
                        </div>
                    <?php } ?>
                    <div class="type">
                        <?php echo $game['genre']; ?>
                    </div>
                </div>
            </a>
        </div>

    <?php } ?>
</div>

<?php
require_once 'view/footer.php';
?>

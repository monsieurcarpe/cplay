<?php
$page = [
    'title' => 'Jeu',
    'premium' => false,
    'admin' => false,
];

require_once 'src/app.php';

if( !isset( $_GET['id'] ) ){
    header( 'Location: list.php' );
    exit();
}

$query = 'SELECT *, developer.name AS developer, editor.name AS editor, games.name AS gname FROM games ';
$query .= 'LEFT JOIN games_type ON games.type_id = games_type.id ';
$query .= 'LEFT JOIN developer ON games.developer_id = developer.id ';
$query .= 'LEFT JOIN editor ON games.editor_id = editor.id ';
$query .= 'WHERE games.id = ?';
$stmt = $db->prepare( $query );
$stmt->bindValue( 1, $_GET['id'] );
$stmt->execute();
$game = $stmt->fetch();

/*sur un fetch all, il aurait renvoyé un double tableau. 
Le fetch récupère seulement le premier résultat. (une ligne)
Si il y a un deuxième fetch, il va passer après le dernier jeu et il n'y a plus rien, donc NULL
*/

if( !empty( $game[ 'press_review' ] ) && !empty( $game[ 'player_review' ] ) ){
    $reviews = [ $game[ 'press_review' ], $game[ 'player_review' ] ];
    $game[ 'globalReview' ] = averageReview( $reviews );
}else{
    $game[ 'globalReview' ] = '-';
}

$errors = "";
$success = "";
if( isset( $_POST['content'] ) ){
    if( empty( $_POST['content'] ) ){
        $errors .= "Votre commentaire doit avoir du contenu";
    }

    if( empty( $errors ) ){
        $stmt = $db->prepare( 'INSERT INTO comment ( user_id, game_id, content, publishedAt ) VALUES ( :user, :game, :content, NOW() )' ); 

        //NOW() = fonction sql qui donne le datetime de maintenant
        
        $stmt->bindValue( 'user', $_SESSION["user"]["id"] );
        $stmt->bindValue( 'game', $_GET['id'] );
        $stmt->bindValue( 'content', $_POST['content'] );

        if( $stmt->execute() ){
            $success = "Votre commentaire à été publié";
        }else{
            $errors .= 'Une erreur est survenue lors de la publication';
        }
    }
}

$stmt = $db->prepare('SELECT * FROM comment LEFT JOIN users ON comment.user_id = users.id WHERE comment.game_id = ? ORDER BY publishedAt DESC');
$stmt->bindValue( 1, $_GET['id'] );
$stmt->execute();
$comments = $stmt->fetchAll();

require_once 'view/header.php';
?>

<div class="grid-x game">
    <div class="small-12 medium-9 cell content">
        <h2><?php echo strtoupper( $game['gname'] ); ?></h2>
        <span class="type"><?php echo $game['genre']; ?></span>

        <?php if( needTriggerWarning( $game['genre'] ) ){ ?>
            <div class="alert callout">
                <i class="fa fa-warning"></i> Certains contenus de ce jeu peuvent heurter la sensibilité des plus fragiles
            </div>
        <?php } ?>

        <div class="grid-x more">
            <div class="small-12 medium-4 text-center">
                <i class="fa fa-calendar"></i>
                <?php if( !empty( $game['release_date'] ) ){
                    echo getFrenchDate( $game['release_date'] );
                }else{ ?>
                    Pas encore annoncée
                <?php } ?>
            </div>
            <div class="small-12 medium-4 text-center">
                <i class="fa fa-paper-plane"></i>
                <?php echo $game['editor']; ?>
            </div>
            <div class="small-12 medium-4 text-center">
                <i class="fa fa-code"></i>
                <?php echo $game['developer']; ?>
            </div>
        </div>

        <p><?php echo $game['description']; ?></p>

        <div class="grid-x reviews">
            <div class="small-12 medium-4 text-center">
                <div class="review">
                    <h3>Note presse</h3>
                    <?php echo ( !empty( $game['press_review'] ) ) ? $game['press_review'] : '-'; ?>
                </div>
            </div>
            <div class="small-12 medium-4 text-center">
                <div class="review">
                    <h3>Note joueur(s)</h3>
                    <?php echo ( !empty( $game['player_review'] ) ) ? $game['player_review'] : '-'; ?>
                </div>
            </div>
            <div class="small-12 medium-4 text-center">
                <div class="review">
                    <h3>Note globale</h3>
                    <?php echo $game['globalReview']; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="small-12 medium-3 cell picture">
        <img src="<?php echo $game['picture'] ?>" />
    </div>
</div>

<div class="grid-x comment">
    <?php echo showError( $errors ); ?>
    <?php echo showSuccess( $success ); ?>

 <?php  if( isset($_SESSION["userConnected"]) && $_SESSION["userConnected"]){ ?>
    <form method="post" class="small-12 cell">
        <textarea name="content"></textarea>
        <input type="submit" value="Publier" class="button" />
    </form>
<?php }else{ ?>

    <h3>Connectez vous pour laisser un commentaire :)</h3>
<?php } ?>

    <?php foreach( $comments as $comment ){ ?>
        <div class="small-12 cell">
            <hr>
            <span class="name"><?php echo $comment['username']; ?> </span><span class="time"><?php echo getFrenchDate( $comment['publishedAt'] ); ?></span><br/>
            <?php echo $comment['content']; ?>
        </div>
    <?php } ?>

</div>

<?php
require_once 'view/footer.php';
?>

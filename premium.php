<?php
$page = [
    'title' => 'Premium',
    'premium' => false,
    'admin' => false,
];

require_once 'src/app.php';
require_once 'view/header.php';
?>


<h1>Bah Alors, on est pas premium ?</h1>
<br>
<div class="grid-x grid-padding-x">
  <div class="cell small-4 text-center"><h2 class="subheader">Abonnement a 100e / mois </h2><img src="https://afghanistan-parsa.org/wp-content/uploads/2013/03/paypaldonatenow.png"> .</div>
  <div class="cell small-4 text-center"><h2 class="subheader">Abonnement a 150e / mois</h2> <img src="https://afghanistan-parsa.org/wp-content/uploads/2013/03/paypaldonatenow.png"> </div>
  <div class="cell small-4 text-center"><h2 class="subheader">Abonnement a 46584e / mois</h2> <img src="https://afghanistan-parsa.org/wp-content/uploads/2013/03/paypaldonatenow.png"> </div>
</div>

<?php
require_once 'view/footer.php';
?>

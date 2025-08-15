<?php
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
require_once 'SessionManager.php';

$sessionManager = new SessionManager();

$orders = $sessionManager->returnOrders();

$currency = array(
    'EUR' => '&euro;',
    'USD' => '&dollar;',
  );


?>
<!DOCTYPE html>
<html lang="en">

<head>
         <meta charset="UTF-8">
         <meta name="viewport" content="width=device-width, initial-scale=1.0">
         <link rel="stylesheet" href="style.css">
         <title>Dashboard</title>
</head>

<body>


  <a href="/">Ga naar domein winkel</a>
<div class="order-container">

<?php foreach($orders as $key => $value) {
         $totalPrice = 0;
         ?>
    <div class="order-card">
        <h3>Bestelling: #<?= htmlspecialchars($key) ?></h3>
        <?php foreach($value as $order) {
         $totalPrice += $order['price'];
         ?>
            <ul class="order-list">
                <li>Domein: <?= htmlspecialchars($order['domain']) ?></li>
                <li>Prijs: <span class="price"><?= $currency[$order['currency']] . htmlspecialchars($order['price']) ?></span></li>
            </ul>
        <?php 
         //Voeg BTW toe aan de totale prijs
      $totalPriceWithBTW = number_format(round($totalPrice * 1.21, 1), 2);
      $BTWOnly = $totalPriceWithBTW - $totalPrice;
} ?>
         <p>Prijs: <?= $currency[$order['currency']] . $totalPrice ?></p>
      <p>BTW: <?= $currency[$order['currency']] .  $BTWOnly ?></p>
      <h3>Totaal: <?= $currency[$order['currency']] .  $totalPrice ?></h3>
    </div>
<?php } ?>

</div>

</body>


</html>
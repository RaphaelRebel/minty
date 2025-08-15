<?php
session_start();

//Ik heb niet veel ervaring met webshops, dus ik ga ervan uit dat er een logische ID voor een bestelling zou zijn. Voor nu gebruik ik uniqid() en vernieuw ik die na elke bestelling. Graag leer ik er meer over.
$_SESSION['key'] ??= uniqid();

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
require_once 'GetDomains.php';
require_once 'SessionManager.php';

$user = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];
$db = $_ENV['DATABASE'];


//Ik zou de URL anders ophalen wegens veiligheid redenen, maar omdat ik niet weet welk domein er straks word gebruikt, heb ik het voor de gemak opgehaald via $_SERVER['SERVER_NAME'].
//Andere (veilige) opties:
//Laravel: request()->getHost()
// Wordpress: $urlparts = wp_parse_url(home_url()); $domain = $urlparts['host'];
// Of ENV. maar dan moet de host (jij) dat aanpassen in .env bestand en ik heb liever dat je dit kan opstarten zonder gedoe.
$server_name = $_SERVER['SERVER_NAME'] ?? 'localhost';
$server_port = $_SERVER['SERVER_PORT'] ?? '80';
$server_host = $server_name . ':' . $server_port;

$sessionManager = new SessionManager();


$sessionManager->manageAction();



// $db = $sessionManager->connectToDB($server_host, $user, $password, $db);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="description" content="Dit is de Minty Media opdracht domein zoeker die gemaakt is door Raphael Rebel.">
  <meta name="keywords" content="Minty Media, opdracht, domein zoeker">
  <meta name="author" content="Raphael Rebel">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Minty Media opdracht domein zoeker</title>
</head>

<body>
  <a href="/?page=dashboard">Ga naar dashboard</a>
  <form name="form" action="" method="get">
    <label for="domain">Domein</label><br>
    <input type="text" name="domain" id="domain">
  </form>
  <!-- call getDomains in PHP -->
  <?php



  $domains = new getDomains();

  // $GET is geen veilige manier om data te krijgen, inplaats daarvan gebruik ik filter_input
  $domain = filter_input(INPUT_GET, 'domain');
  $domain = str_replace(" ", "", $domain);
  // $_SESSION['search_domain'] = $domain;
  echo "Domein: " . htmlspecialchars($domain) . "<br>";
  $data = $domains->getDomains($domain);

  // $data = json_decode($data, true);
  
  //Ik ben nog geen product tegengekomen zonder EURO prijs, maar anders zou ik het zo erin stoppen: 
  $currency = array(
    'EUR' => '&euro;',
    'USD' => '&dollar;',
  );


  ?>
  <div class="domain-layout">
    <?php
    foreach ($data as $domain) {
      if (!empty($domain['domain']) && !str_starts_with($domain['domain'], 'null') && !str_starts_with($domain['domain'], ".")) {


        $domainCode = json_encode($domain);
        $domainCode = base64_encode($domainCode);

        $product = $domain['price']['product'];
        ?>
        <div>
          <ul>
            <form action="" method="post">
              <input type="hidden" name="action" value="add">
              <input type="hidden" name="code" value="<?= $domainCode ?>">
              <li>Domein: <?= $domain['domain'] ?></li>
              <li>Status: <?= $domain['status'] ?></li>
              <li>Prijs: <?= $currency[$product['currency']] . htmlspecialchars($product['price']) ?></li>
              <?php if ($domain['status'] !== "active") { ?>
                <div class="cart-action"><input type="submit" value="Toevoegen" /></div><?php } else {
                ?>
                <div class="cart-action">Niet beschikbaar</div><?php
              } ?>
            </form>
          </ul>

          <?php if ($_SESSION['added'][$domainCode] ?? false) {
            echo "<p>Dit domein is toegevoegd aan de winkelwagen.</p>";
          } ?>
        </div>

        <?php



      }
      ;
    }


    ?>


  </div>
  <!-- Maak een winkelwagen aan -->
  <div class="cart-overview">
    <h2>Winkelwagen: </h2>
    <ul>
      <?php

      $totalPrice = 0;


      if ($_SESSION['added']) {
        
        foreach ($_SESSION['added'] as $bitDomain => $value) {
          $domainDecoded = base64_decode($bitDomain);
          if ($domainDecoded === false) {
            continue; // skip invalid base64
          }
          $domainDecoded = json_decode($domainDecoded, true);

          if (!is_array($domainDecoded) || empty($domainDecoded['price']['product']['price'])) {
            continue;
          }

          $totalPrice += $domainDecoded['price']['product']['price'];
          ?>
          <div>
            <li><?= htmlspecialchars($domainDecoded['domain']) ?>
              <p>
                <?= $currency[$domainDecoded['price']['product']['currency']] . htmlspecialchars($domainDecoded['price']['product']['price']) ?>
              </p>
            </li>

            <form action="" method="post">
              <input type="hidden" name="action" value="remove_single">
              <input type="hidden" name="code" value="<?= $bitDomain ?>">
              <input type="submit" value="Verwijder Domein">
            </form>
          </div>
          <?php
        }

      }

      //Voeg BTW toe aan de totale prijs
      $totalPriceWithBTW = number_format(round($totalPrice * 1.21, 1), 2);
      $BTWOnly = $totalPriceWithBTW - $totalPrice;
      ?>
    </ul>
    <div>
      <p>Prijs: <?= $totalPrice ?></p>
      <p>BTW: <?= $BTWOnly ?></p>
      <h3>Totaal: <?= $totalPrice ?></h3>
      <form action="" method="post">

        <input type="hidden" name="action" value="create">
        <input type="submit" value="Bestellen">
      </form>


    </div>

    <form action="" method="post">

      <input type="hidden" name="action" value="remove">
      <input type="submit" value="Winkelwagen leegmaken">
    </form>
  </div>

</body>

</html>
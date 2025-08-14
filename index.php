<?php
session_start();
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
require_once 'getDomains.php';

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

$db = new mysqli($server_name, $user, $password, $db) or die("Unable to connect to server");

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="description" content="Dit is de Minty Media opdracht domein zoeker die gemaakt is door Raphael Rebel.">
  <meta name="keywords" content="Minty Media, opdracht, domein zoeker">
  <meta name="author" content="Raphael Rebel">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Minty Media opdracht domein zoeker</title>
</head>

<body>
  <?php
  //check of er een code word toegevoegd (code van domein + tdl bit-64)
  $addedCode = [];

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $code = $_POST['code'];

    if ($code !== '') {
      $_SESSION['added'] ??= [];
      $_SESSION['added'][$code] = true;

      $addedCode[] = $code;


      //Redirectie
      session_write_close();
      header('Location: ' . $_SERVER['REQUEST_URI']);
      exit;
    }

  } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove') {

    $removeAdded = $_SESSION['added'];

    //Verwijder domeinen
    if ($removeAdded) {

      foreach ($removeAdded as $key => $value) {
        unset($removeAdded[$key]);
      }

      $_SESSION['added'] = $removeAdded;

    }
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
  } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove_single') {

    $code = $_POST['code'];

    //Verwijder domeinen
    if ($_SESSION['added']) {

      unset($_SESSION['added'][$code]);


    }
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
  }
  ?>
  <form name="form" action="" method="get">
    <input type="text" name="domain" id="domain">
  </form>
  <!-- call getDomains in PHP -->
  <?php



  $domains = new getDomains();

  // $GET is geen veilige manier om data te krijgen, inplaats daarvan gebruik ik filter_input
  $domain = filter_input(INPUT_GET, 'domain');
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
  <div style="display: flex;  flex-flow: row wrap;">
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
                <div class="cart-action"><input type="submit" value="Toevoegen" /></div><?php } ?>
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
  <div>
    <h2>Winkelwagen: </h2>
    <ul>
      <?php

      $totalPrice = 0;

      foreach ($_SESSION['added'] as $bitDomain => $value) {
        $domainDecoded = base64_decode($bitDomain);
        $domainDecoded = json_decode($domainDecoded, true);

        $totalPrice += $domainDecoded['price']['product']['price'];
        ?>
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
        <?php
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
    </div>

    <form action="" method="post">

      <input type="hidden" name="action" value="remove">
      <input type="submit" value="Winkelwagen leegmaken">
    </form>
  </div>

</body>

</html>
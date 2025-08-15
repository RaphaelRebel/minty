<?php

class SessionManager
{
         protected $user;
         protected $password;
         protected $db;
         protected $server_name;
         protected $server_host;
         //in deze class beheer ik zoveel mogelijk session functionaliteiten
         public function __construct()
         {

                  $this->user = $_ENV['DB_USER'];
                  $this->password = $_ENV['DB_PASSWORD'];
                  $this->db = $_ENV['DATABASE'];

                  //Ik zou de URL anders ophalen wegens veiligheid redenen, maar omdat ik niet weet welk domein er straks word gebruikt, heb ik het voor de gemak opgehaald via $_SERVER['SERVER_NAME'].
                  //Andere (veilige) opties:
                  //Laravel: request()->getHost()
                  // Wordpress: $urlparts = wp_parse_url(home_url()); $domain = $urlparts['host'];
                  // Of ENV. maar dan moet de host (jij) dat aanpassen in .env bestand en ik heb liever dat je dit kan opstarten zonder gedoe.
                  $this->server_name = $_SERVER['SERVER_NAME'] ?? 'localhost';
                  $server_port = $_SERVER['SERVER_PORT'] ?? '80';
                  $this->server_host = $this->server_name . ':' . $server_port;


         }

         public function connectToDB($host, $user, $password, $db)
         {
                  $db = new mysqli(
                           $host,
                           $user,
                           $password,
                           $db
                  );

                  if ($db->connect_error) {
                           die('Connection failed: ' . $db->connect_error);
                  }

                  return $db;
         }


         public function returnOrders()
         {
                  $db = $this->connectToDB($this->server_name, $this->user, $this->password, $this->db);
                  $sqlOrders = "SELECT * FROM orders";
                  $sqlOrdersOverview = "SELECT * FROM orders_overview";
                  $resultOrders = $db->query($sqlOrders);
                  $resultsOrdersOverview = $db->query($sqlOrdersOverview);

                  $orders = [];

                  if ($resultsOrdersOverview->num_rows > 0) {
                           while ($row = $resultsOrdersOverview->fetch_assoc()) {
                                    if (!empty($row)) {
                                             $orders[$row['id']] = [];
                                    }
                           }
                  }

                  if ($resultOrders->num_rows > 0) {
                           while ($row = $resultOrders->fetch_assoc()) {
                                    if (!empty($row)) {
                                             $orders[$row['parent_id']][] = $row;
                                    }
                           }
                  } else {
                           echo "0 results";
                  }

                  foreach ($orders as $key => $value) {
                           if(empty($value)){
                                    unset($orders[$key]);
                           }
                  }

                  $db->close();

                  return $orders;
         }

         public function manageAction()
         {
                  $action = $_POST['action'] ?? null;

                  if ($action === null) {
                           return;
                  }

                  //Check wat de actie is, gebasseerd daarop, functies aanzetten (functies staan onder deze functie)

                  switch ($action) {
                           case 'add':
                                    $this->addAction();
                                    break;
                           case 'remove':
                                    $this->removeAction();
                                    break;
                           case 'remove_single':
                                    $this->removeSingleAction();
                                    break;
                           case 'create':
                                    $this->createAction();
                                    break;

                  }

                  return;
         }

         private function addAction()
         {
                  $code = $_POST['code'] ?? '';

                  if ($code !== '') {
                           $_SESSION['added'] ??= [];
                           $_SESSION['added'][$code] = true;

                           $addedCode[] = $code;


                           //Redirectie
                           session_write_close();
                           header('Location: ' . $_SERVER['REQUEST_URI']);
                           exit;
                  }
         }

         private function removeAction()
         {
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
         }

         private function removeSingleAction()
         {
                  $code = $_POST['code'] ?? '';

                  //Verwijder domeinen
                  if ($_SESSION['added']) {

                           unset($_SESSION['added'][$code]);


                  }
                  header('Location: ' . $_SERVER['REQUEST_URI']);
                  exit;
         }

         private function createAction()
         {


                  $db = $this->connectToDB($this->server_name, $this->user, $this->password, $this->db);



                  $orderedProducts = $_SESSION['added'];
                  $orderTitle = $_SESSION['key'];


                  //Check of de titel al bestaat in de database, zodat er geen duplicates zijn
                  $checkOverview = $db->prepare("SELECT id FROM orders_overview WHERE title = ?");
                  $checkOverview->bind_param("s", $orderTitle);
                  $checkOverview->execute();
                  $checkOverview->store_result();


                  if ($checkOverview->num_rows > 0) {
                           var_dump($orderTitle);
                           echo "Title already exists in orders_overview.";
                  }


                  $createOrderOverviewSQL = $db->prepare("INSERT INTO orders_overview (title) VALUES (?)");
                  $createOrderOverviewSQL->bind_param("s", $orderTitle);

                  if ($createOrderOverviewSQL->execute()) {
                           echo "New orders_overview row created with ID: " . $createOrderOverviewSQL->insert_id;
                  } else {
                           echo "Error: " . $createOrderOverviewSQL->error;
                  }


                  foreach ($orderedProducts as $key => $value) {
                           $key_decoded = base64_decode($key);
                           $key_decoded = json_decode($key_decoded);

                           $parentId = $createOrderOverviewSQL->insert_id;
                           $domain = $key_decoded->domain;
                           $price = intval($key_decoded->price->product->price);
                           $status = $key_decoded->status;
                           $currency = $key_decoded->price->product->currency;

                           $createRow = $db->prepare("INSERT INTO orders (parent_id, domain, status, price, currency) VALUES (?, ?, ?, ?, ?)");
                           $createRow->bind_param(
                                    "issds",
                                    $parentId,
                                    $domain,
                                    $status,
                                    $price,
                                    $currency
                           );

                           $createRow->execute();
                  }

                  header('Location: ' . $_SERVER['REQUEST_URI']);

                  //Reset mandje en sessie key
                  $_SESSION['added'] = [];
                  $_SESSION['key'] = uniqid();
                  exit;
         }

         public function isAction($action)
         {
                  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === $action) {
                           return true;
                  }

                  return false;
         }


}
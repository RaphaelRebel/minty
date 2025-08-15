<?php
require __DIR__ . '/vendor/autoload.php';
class GetDomains
{

         private $sessionActive = false;
         //Get API info
         private $apiUrl;
         private $apiKey;
         //Voeg parameters toe. In verband met tijd, zal ik het in het script stoppen.
         private $params = array(
                  'with_price' => true,
         );
         private $tdls = array(
                  'com',
                  'net',
                  'org',
                  'io',
                  'co',
                  'nl',
                  'amsterdam',
                  'dev',
                  'shop',
                  'info',
         );

         public function __construct()
         {


                  $this->apiUrl = $_ENV['API_URL'] ?? '';
                  $this->apiKey = $_ENV['API_RULE'] . ' ' . $_ENV['API_KEY'] ?? '';
                  if ($this->apiUrl === '' || $this->apiKey === '') {
                           throw new RuntimeException('API_URL or API_KEY not set');
                  }

                  if (empty($_SESSION['domains'])) {
                           $_SESSION['domains'] = [];
                  }
         }

         public function callApi($method, $domain = 'rebootz')
         {
                  try {
                           //Voeg hier de data toe.. Naam, extensions, ect..
                           $data = array();

                           foreach ($this->tdls as $tdl) {
                                    $data[] = array(
                                             "name" => $domain,
                                             "extension" => $tdl
                                    );
                           }


                           //start cUrl
                           $curl = curl_init();

                           //Voeg cURL opties toe (Voor nu, alleen POST..)
                           switch ($method) {
                                    case 'POST':
                                             curl_setopt($curl, CURLOPT_POST, 1);

                                             // if($this->params){
                                             //          curl_setopt($curl, CURLOPT_POSTFIELDS, $this->params);
                                             // }

                                             if ($data) {
                                                      curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                                             }
                                             break;
                                    default:
                                             return json_encode(['error' => 'Wrong method applied.']);
                           }


                           //Add URL
                           curl_setopt($curl, CURLOPT_URL, $this->apiUrl);
                           //Add headers
                           $headers = [
                                    'Authorization: ' . $this->apiKey,
                                    'Content-Type: application/json',
                                    'Accept: application/json',
                           ];

                           curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

                           //Zorg ervoor dat de response wordt teruggegeven
                           curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                           $result = curl_exec($curl);




                           return $result;
                  } catch (Exception $e) {
                           // Handle exception
                           return json_encode(['error' => 'An error occurred while fetching the API: ' . $e->getMessage()]);
                  }

         }

         public function getDomains($domain): bool|string|array
         {
                  try {
                           if (session_status() !== PHP_SESSION_ACTIVE) {

                                    session_start();
                           }

                           // var_dump(`'SID', session_id(), array_keys($_SESSION['domains'] ?? []));
                           if ($_SESSION['domains'][$domain] ?? '' !== '') {
                                    return $_SESSION['domains'][$domain];
                           }
                           $json = $this->callApi('POST', $domain);
                           $data = json_decode($json, true);
                           $_SESSION['domains'][$domain] = $data;
                           if (json_last_error() !== JSON_ERROR_NONE) {
                                    throw new RuntimeException('Invalid JSON from API: ' . json_last_error_msg());
                           }


                           // if you immediately redirect after this in your controller:
                           // session_write_close();

                           return $data;

                  } catch (Exception $e) {
                           // Handle exception
                           return json_encode(['error' => 'An error occurred while fetching domains: ' . $e->getMessage()]);
                  }
         }

}
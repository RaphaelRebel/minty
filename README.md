Gebruik de REST API om data op te halen/versturen
Laat tenminste 10 verschillende tld's zien met prijzen (op basis van end-user input)
In de web pagina aangeven of het domein beschikbaar is of niet.
Maak een winkelwagen waar domeinen aan toegevoegd kunnen worden
Wanneer tld niet beschikbaar is moet het niet mogelijk zijn om die toe te kunnen voegen aan de winkelmand
Domeinen weer uit winkelmand kunnen halen
Bereken het subtotaal + btw en laat deze op de checkout pagina zien.
Voeg een bestelling toe aan een database (klik hier voor meer info)
Een lijst met bestellingen.

Bronnen:

https://www.uptimia.com/questions/how-to-add-custom-headers-to-a-php-curl-request#:~:text=Tip%3A%20Handling%20Authorization%20Headers,ch%2C%20CURLOPT_HTTPHEADER%2C%20%24headers)%3B

https://stackoverflow.com/questions/9802788/call-a-rest-api-in-php

https://www.php.net/manual/en/function.curl-exec.php#:~:text=curl_exec(CurlHandle%20%24handle%20)%3A%20string,for%20the%20session%20are%20set.

https://phppot.com/php/simple-php-shopping-cart/ 

Ik heb last gehad van een bug dat de sessie een reset gaf. Dat bleek uitendelijk 
  <?php session_abort(); ?> te zijn

  Ik heb voor het gemak van de gebruiker de .env erin gehouden, maar ik zou het normaal gesproken in .gitignore doen
  
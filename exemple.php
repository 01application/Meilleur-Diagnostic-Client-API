<?php
/**
 * API Meilleur Diagnostic
 * Exemple d'envoi de lead via PHP 5 CURL
 */

/**
 * Le XML à générer en fonction de vos infos
 * Encodage UTF-8 pour les textes
 */
$body = '<?xml version="1.0" encoding="utf-8"?>
<lead>
<code_postal>51000</code_postal>
<ville>Châlons en Champagne</ville>
<email>test@example.com</email>
<telephone>0123456789</telephone>
<nom>Jean Dupont</nom>
<bien>0</bien>
<transaction>2</transaction>
<surface>123</surface>
<pieces>5</pieces>
<construction>1</construction>
<diagnostic>
<amiante>1</amiante>
<plomb>0</plomb>
<gaz>0</gaz>
<ernt>1</ernt>
<dpe>0</dpe>
<habitabilite>0</habitabilite>
<termites>0</termites>
<carrez>1</carrez>
<electricite>0</electricite>
<boutin>0</boutin>
</diagnostic>
<origine>TEST</origine>
</lead>';

/**
 * Requête et stockage de la réponse
 */
// En Test:
$url = 'http://api.meilleurdiagnostic.com/lead/test.xml';
// En Prod :
//$url = 'http://api.meilleurdiagnostic.com/lead/add.xml';

/**
 * Création de la requête CURL
 * Important Header Content-Type annonce du XML (sinon réponse 412)
 */
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt ($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);
curl_close($ch);

/**
 * On interprète la réponse en XML
 */
$reponse = new SimpleXMLElement($result);

if(!$reponse->error) {
  // OK
	echo '<p>Demande prise en compte, merci.</p>';
} elseif($reponse->error->code == 412) {
  // Erreur 412
  // Certains champs obligatoires sont manquants ou invalides
	$erreur = explode(' ', $reponse->error->message);
	$erreurListe = explode('|', $erreur[2]);

	echo '<p>Veuillez v&eacute;rifier les champs suivant.</p>';
	echo '<pre>';
	print_r($erreurListe);
} else {
  // Erreur 500
  // A priori ponctuel, demander de réessayer
	echo '<p>Une erreur s\'est produite. Veuillez r&eacute;essayer, merci.</p>';
}

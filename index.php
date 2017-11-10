<?php

error_reporting(E_ALL); ini_set('display_errors', '1');

require "MySQLConnector.class.php";

function languageAnalyze($text)
{

	/* Your google cloud API key */
	$api_key = "";

	/* Fields to query google language API 
	* It may vary depending feature you want, here we only want to analyze entity sentiment
	*/
	$fields = array(
		'encodingType' => 'UTF8',
		'document' => array('type'=>'PLAIN_TEXT', 'content' => $text),
		'features' => array(
	  "extractSyntax" => false,
	  "extractEntities" => false,
	  "extractDocumentSentiment" => false,
	  "extractEntitySentiment" => true,
	  "classifyText" => false)
	);

	/* Init cURL */
	$ch = curl_init();

	/* Some other request
		curl_setopt($ch,CURLOPT_URL, "https://language.googleapis.com/v1/documents:analyzeEntities?key=$api_key");
		curl_setopt($ch,CURLOPT_URL, "https://language.googleapis.com/v1/documents:analyzeEntitySentiment?key=$api_key");
	*/
	curl_setopt($ch,CURLOPT_URL, "https://language.googleapis.com/v1/documents:annotateText?key=$api_key");
	/* JSON is mandatory for google cloud API */
	curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: application/json' ));
	curl_setopt($ch,CURLOPT_POST, count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($fields));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	/* Execute cURL */
	$result = curl_exec($ch);
	$entities = json_decode($result);

	/* Close cURL */
	curl_close($ch);

	return $entities;
}

$mysql = new MySQLConnector();
$rows = $mysql->query("SELECT * from comments");
foreach($rows as $row)
{
	/* Analyze each comment */
	$entities = languageAnalyze($row['comment']);

	foreach($entities->entities as $e)
	{
		/* Store analysis in database */
//		$mysql->queryNoReturn("INSERT into entities (id_comment, entity, salience, sentiment) VALUES ('".$row['id']."', '".$e->name."', '".$e->salience."', '".$e->sentiment->score."')");
	}
}

/* Query the most used entities in comments */
$rows = $mysql->query("SELECT entity, count(*) as number  FROM entities GROUP BY entity ORDER BY number DESC");

echo "Most used entities :<br/>";
echo "<ul>";
foreach($rows as $row)
{
	echo "<li>".$row['entity']."(".$row['number'].")</li>";	
}
echo "</ul>";

echo "<br/><br/>";
echo "Finding out negative comment to fix them<br/>";

?>

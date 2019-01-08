<?php

error_reporting(0);

if (!isset($argv[1])) {
	die('Usage: php lichess.php [path to file]');
}

$content = file_get_contents($argv[1]);
if ($content === false) {
	exit(1);
}

$extractedJson = extractJson($content);
if ($extractedJson === false) {
	exit(1);
}

$decodedJson = json_decode($extractedJson);
if ($decodedJson === null) {
	exit(1);
}

echo generateOutput($decodedJson);

function generateOutput($decodedJson)
{
	$output = [
		getId($decodedJson),
		getRating($decodedJson),
		getVote($decodedJson),
		getFen($decodedJson),
	];
	
	return implode("\t", $output);
}

function extractJson($content)
{
	preg_match('/lichess\.puzzle\ \=\ \{\ data\:\ (.+?)\,\ pref\:\ \{\"/', $content, $matches);
	return $matches[1] ?? false;
}

function getFen($decodedJson)
{
	$treePartsCount = count($decodedJson->game->treeParts);
	return $decodedJson->game->treeParts[$treePartsCount - 1]->fen ?? exit(1);
}

function getId($decodedJson)
{
	return $decodedJson->puzzle->id ?? exit(1);
}

function getRating($decodedJson)
{
	return $decodedJson->puzzle->rating ?? exit(1);
}

function getVote($decodedJson)
{
	return $decodedJson->puzzle->vote ?? exit(1);
}

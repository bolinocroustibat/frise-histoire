﻿<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Content-Type, Depth, User-Agent, X-File-Size, 
    X-Requested-With, If-Modified-Since, X-File-Name, Cache-Control");
header('Content-Type: application/json; charset=utf-8'); 
include('connex.php');

// The PHP json_encode function convert UTF-8 strings to hexadecimal entities.
// JSON_UNESCAPED_UNICODE is available on PHP Version 5.4 or later.
// The following code is for Version 5.3 :
function raw_json_encode($input) {
    return preg_replace_callback(
        '/\\\\u([0-9a-zA-Z]{4})/',
        function ($matches) {
            return mb_convert_encoding(pack('H*',$matches[1]),'UTF-8','UTF-16');
        },
        json_encode($input)
    );
}

// En entrée : un ID de parent
// en sortie : l'ensemble des période et des évènements qui sont dedans, en JSON
$parent_id = $_GET['parent_id'];
// echo 'ID de période parente : '.$parent_id.'<br/><br/>';

$data = array();

// construction du tableau des évènements
$req = $bdd->query('SELECT id, parent_period_id, date, name, description, image FROM frise_evenements WHERE parent_period_id='.$parent_id);
$rep = $req->fetchAll(PDO::FETCH_ASSOC); // Ou PDO:FETCH_NUM si on veut des clefs qui ne soient que des chiffres
$data['events']=$rep;

// Ajout, à la suite du tableau, des périodes
$req = $bdd->query('SELECT id, parent_period_id, has_child, start, end, name, description,color FROM frise_periodes WHERE parent_period_id='.$parent_id);
$rep = $req->fetchAll(PDO::FETCH_ASSOC); // Ou PDO:FETCH_NUM si on veut des clefs qui ne soient que des chiffres
$data['periods']=$rep;  // on ajoute les trois tableaux

// dans chaque objet "période" du JSON, un booléen qui dit si il a des enfants ou pas

$data=raw_json_encode($data);
print_r($data);

?>
<?php 
//Ligne nécessaire à l'utilisation de la superglobale de session
ob_start();
session_start();

//Connection a la base de donnée grace a l'objet PDO
$bdd = new PDO('mysql:host=mysql;dbname=hobby-horse;charset=utf8', 'root', 'root');

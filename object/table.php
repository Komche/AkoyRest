<?php
    header('Content-Type: application/json');

    include_once('../config/connexion.php');

    $database = new Connexion();
    $db = $database->getConnection();
<?php
    // required headers
header("Access-Control-Allow-Origin: http://localhost/AkoyRest/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Content-Type: application/json');

include_once('../config/connexion.php');
include_once('../model/table.php');

$database = new Configuration();
$db = $database->getConnection();
$config = $database->getConfig();
$table_key = 0;

if (!empty($_GET['id'])) {
    $id_val = $_GET['id'];
    // die($id);
} else {
    $id_val = null;
}


$data = json_decode(file_get_contents('php://input'), true);
$table_name = explode("/", $_SERVER['REDIRECT_URL']);

foreach ($table_name as $i => $value) {
    if (in_array($value, $config['tables'])) {
        $table_key = $i;
        $table_name[$table_key] = $value;
    }
}

if (in_array($table_name[$table_key], $config['tables'])) {

    $current_table = $table_name[$table_key];
    $id = $config['tables'][$current_table]['id'][0];
    $table_field = array();
    $table_field_ = $config['tables'][$current_table];
    foreach ($table_field_ as $key => $value) {
        if (is_int($key)) {
            $table_field[] = $value;
        }
    }
    $table = new Table($db, $table_name[$table_key], $table_field, $data, $id, $id_val, $config['jwt'], $config['key']);
} else {
    $table = new Table($db, $table_name[$table_key], $table_field, $data, $id, $id_val);
    $table->throwError(503, "$table_name[$table_key] n'existe pas dans la liste des tables de cette base de donnée", true);
}

if (array_key_exists('required', $config['tables'][$current_table])) {
    $required = $config['tables'][$current_table]['required'];
}else{
    $required = null;
}


$request_method = $_SERVER['REQUEST_METHOD'];

switch ($request_method) {
    case 'GET':
        header("Access-Control-Allow-Methods: GET");
        if (!empty($id_val)) {
            echo $table->getData();
        } else {
            echo $table->getData();
        }
        break;

    case 'POST':
        header("Access-Control-Allow-Methods: POST");
        echo $table->insert($required);
        break;

    case 'PUT':
        header("Access-Control-Allow-Methods: PUT");
        
        if (!empty($id_val)) {
            echo $table->update();
        }else {
            $table->throwError(503, "Vous avez oublié de donner l'identifiant de la table à modifier", true);
        }
        break;
    case 'DELETE':
        header("Access-Control-Allow-Methods: DELETE");
        $id = intval($_GET['id']);
        echo $table->delete();
        break;

    default:
        header('HTTP/1.0 405 Method Not Allowed');
        break;
}

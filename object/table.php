<?php
    // required headers
header("Access-Control-Allow-Origin: http://localhost/AkoyRest/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Content-Type: application/json');

include_once('../config/Configuration.php');
include_once('../model/table.php');
include_once('../model/function.php');
include_once('../model/HttpRequest.php');

// get url request values
$request = new HttpRequest();
$request->setBaseUrl($_SERVER[ 'SCRIPT_NAME']);
$request->createRequest();
$url_request =   $request->getParameters();

// get configuration values
$database = new Configuration();
$db = $database->getConnection();
$config = $database->getConfig();
$table_key = 0;

//get input data
$data = json_decode(file_get_contents('php://input'), true);

// check if table_1 existe in database
if (in_array($url_request['table_1'], $config['tables'])) {
    $current_table = $url_request['table_1'];
}else {
    die('ha');
    Table::throwError(null, $url_request['table_1']." n'existe dans la base de donnée", true);
}

// check if property_1 existe in database
$table_field = getSimpleArray($config['tables'][$current_table]);
if (!empty(trim($url_request['property_1']))) {
    if (in_array($url_request['property_1'], $table_field)) {
        $property = $url_request['property_1'];
    } else {
        Table::throwError(null, $url_request['property_1'] . " n'existe dans la table $current_table", true);
    }
}else {
    $property = null;
}

// check  val_1 existe 
$table_field = getSimpleArray($config['tables'][$current_table]);
if (!empty($url_request[ 'val_1'])) {
    $val = $url_request['val_1'];
} else {
    $val = null;
}

// create instance of Table
$table = new Table($db, $current_table, $table_field, $data, $property, $val, $config['jwt'], $config['key']);

// check required field
if (array_key_exists('required', $config['tables'][$current_table])) {
    $required = $config['tables'][$current_table]['required'];
}else{
    $required = null;
}

// get method
$request_method = $_SERVER['REQUEST_METHOD'];

switch ($request_method) {
    case 'GET':
        header("Access-Control-Allow-Methods: GET");
        echo $table->getData();
        break;

    case 'POST':
        header("Access-Control-Allow-Methods: POST");
        echo $table->insert($required);
        break;

    case 'PUT':
        header("Access-Control-Allow-Methods: PUT");
        
        if (!empty($val)) {
            echo $table->update();
        }else {
            $table->throwError(503, "Vous avez oublié de donner l'identifiant de la table à modifier", true);
        }
        break;
    case 'DELETE':
        header("Access-Control-Allow-Methods: DELETE");
        $property = $_GET[ 'property'];
        echo $table->delete();
        break;

    default:
        header('HTTP/1.0 405 Method Not Allowed');
        break;
}

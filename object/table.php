<?php
    header('Content-Type: application/json');

    include_once('../config/connexion.php');
    include_once('../model/table.php');

    $database = new Connexion();
    $db = $database->getConnection();
    $config = $database->getConfig();

    if (!empty($_GET['id'])) {
        $id = $_GET['id'];
       // die($id);
    } else {
        $id=null;
    }
    

    $data = json_decode(file_get_contents('php://input'),true);
    
    $table_name = explode("/", $_SERVER['REDIRECT_URL']); 
    if (in_array($table_name[3], $config['tables'])) {
        $table = new Table($db,$table_name[3],null,null,'id',$id);
    } else {
        die("$table_name[3] n'existe pas dans la liste des tables de cette base de donnée ");
    }
    
    
    

    $request_method = $_SERVER['REQUEST_METHOD'];

    switch ($request_method) {
        case 'GET':
            if(!empty($id)){
                echo $table->getData();
            }else{
                echo $table->getData();
            }
            break;
        
        case 'POST':
            insert('vols',['ville_depart', 'ville_arriver', 'nb_heure_vols', 'prix'],$data);
            echo $table->insert();
            break;

        case 'PUT':
            $id = intval($_GET['id']);
            update('vols',['ville_depart', 'ville_arriver', 'nb_heure_vols', 'prix'],$data, 'id', $id);
            break;
        case 'DELETE':
            $id = intval($_GET['id']);
            delete('vols', 'id', $id);
            break;
        
        default:
            header('HTTP/1.0 405 Method Not Allowed');
            break;
    }
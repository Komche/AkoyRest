<?php
    header('Content-Type: application/json');

    include_once('../config/connexion.php');

    $database = new Connexion();
    $db = $database->getConnection();

    $data = json_decode(file_get_contents('php://input'),true);

    

    $request_method = $_SERVER['REQUEST_METHOD'];

    switch ($request_method) {
        case 'GET':
            if(!empty($_GET['id'])){
                $id = intval($_GET['id']);
                get_employees($id);
            }else{
                get_employees();
            }
            break;
        
        case 'POST':
            insert('vols',['ville_depart', 'ville_arriver', 'nb_heure_vols', 'prix'],$data);
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
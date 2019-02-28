<?php

    include_once('../config/connexion.php');

    $database = new Connexion();
    $db = $database->getConnection();

    class Table {

        private $table;
        private $fields = [];
        private $values = [];
        private $id;
        private $db;

        public function __construct($db) {
            $this->db = $db;
        }

        public function getData($table, $id=null, $id_val=null)
        {
            $query = "SELECT * FROM $table ";
            if ($id!=null && $id_val!=null) {
                $query .= "WHERE $id=:$id LIMIT 1";

                $req = $this->db->prepare($query);
                $req->execute(['id'=>$id]);
                if ($result = $req->fetch(PDO::FETCH_ASSOC)) {
                    header('Content-Type: application/json');
                    return json_encode($result);
                }
            }else{
                $req = $this->db->query($query);
                if ($result = $req->fetchAll(PDO::FETCH_ASSOC)) {
                    header('Content-Type: application/json');
                    return json_encode($result);
                }
            }       
            
            
        }


        
    }
    

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
            $data = json_decode(file_get_contents('php://input'),true);
            insert('vols',['ville_depart', 'ville_arriver', 'nb_heure_vols', 'prix'],$data);
            break;

        case 'PUT':
            $id = intval($_GET['id']);
            $data = json_decode(file_get_contents('php://input'),true);
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


    function insert($table, $fields = [], $values = [])
    {
        global $db;
        if (count($fields) > 0) {
            $total = count($fields) - 1;
            $sql = "INSERT INTO $table(";
            foreach ($fields as $key => $field) {
                if ($total != $key) {
                    $sql .= $field . ", ";
                } else {
                    $sql .= $field . ") ";
                }
            }
            $sql .= "VALUES(";
            foreach ($fields as $key => $field) {
                if ($total != $key) {
                    $sql .= ":$field, ";
                } else {
                    $sql .= ":$field)";
                }
            }

            $req = $db->prepare($sql);
            if ($req->execute($values)) {
                $result = array("status"=>1,
                                "message"=> "Enregistrement ajouté avec succès");
            } else {
                $result = array("status"=>0,
                                "message"=> "Enregistrement échouer");
            }

            header('Content-Type: application/json');
            echo json_encode($result);
            
        }
    }

    function update($table, $fields = [], $values = [], $id, $id_val)
    {
        global $db;
        if (count($fields) > 0) {
            $total = count($fields) - 1;
            $sql = "UPDATE $table SET ";
            foreach ($fields as $key => $field) {
                if ($total != $key) {
                    $sql .= "$field=:$field, ";
                } else {
                    $sql .= "$field=:$field ";
                }
            }
            $sql .= "WHERE $id=:$id";
            
            $values[$id] = $id_val;
            //echo($sql); print_r($values); die();

            $req = $db->prepare($sql);
            if ($req->execute($values)) {
                $result = array("status"=>1,
                                "message"=> "Enregistrement modifier avec succès");
            } else {
                $result = array("status"=>0,
                                "message"=> "modification échouer");
            }

            header('Content-Type: application/json');
            echo json_encode($result);
            
        }
    }

    function delete($table, $id, $id_val)
    {
        global $db;
        $sql = "DELETE FROM $table WHERE $id=$id_val";

        if ($db->exec($sql)) {
            $result = array("status"=>1,
                            "message"=> "Enregistrement supprimer avec succès");
        } else {
            $result = array("status"=>0,
                            "message"=> "suppression échouer");
        }

        header('Content-Type: application/json');
        echo json_encode($result);
    }
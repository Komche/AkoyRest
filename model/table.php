<?php
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
                    return json_encode($result);
                }
            }else{
                $req = $this->db->query($query);
                if ($result = $req->fetchAll(PDO::FETCH_ASSOC)) {
                    return json_encode($result);
                }
            }                              
        }

    function insert($table, $fields = [], $values = [])
    {
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

            $req = $this->db->prepare($sql);
            if ($req->execute($values)) {
                $result = array("status"=>1,
                                "message"=> "Enregistrement effectué avec succès");
            } else {
                $result = array("status"=>0,
                                "message"=> "Enregistrement échoué");
            }
            return json_encode($result);
            
        }
    }

    function update($table, $fields = [], $values = [], $id, $id_val)
    {
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

            $req = $this->db->prepare($sql);
            if ($req->execute($values)) {
                $result = array("status"=>1,
                                "message"=> "Enregistrement modifié avec succès");
            } else {
                $result = array("status"=>0,
                                "message"=> "modification échouée");
            }

            return json_encode($result);
            
        }
    }

        function delete($table, $id, $id_val)
        {
            $sql = "DELETE FROM $table WHERE $id=$id_val";

            if ($this->db->exec($sql)) {
                $result = array("status"=>1,
                                "message"=> "Enregistrement supprimer avec succès");
            } else {
                $result = array("status"=>0,
                                "message"=> "suppression échouée");
            }

            return json_encode($result);
        }
        
    }
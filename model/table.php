<?php
    class Table {

        private $table;
        private $fields = [];
        private $values = [];
        private $id;
        private $id_val;
        private $db;
        private $results = [];

        public function __construct($db, $table, $fields = [null], $values = [null], $id=null, $id_val=null) {
            $this->db = $db;
            $this->table = $table;
            $this->fields = $fields;
            $this->values = $values;
            $this->id = $id;
            $this->id_val = $id_val;   
            $this->results['error'] = false;
            $this->results['message'] = "Tout s'est bien déroulé";         
        }

        public function getData()
        {
            $query = "SELECT * FROM $this->table ";
            if ($this->id!=null && $this->id_val!=null) {
                //die($this->id_val);
                $query .= "WHERE $this->id=:$this->id LIMIT 1";

                $req = $this->db->prepare($query);
                //var_dump([$this->id=>intval($this->id_val)]); die();
                $req->execute([$this->id=>intval($this->id_val)]);
                if ($results = $req->fetch(PDO::FETCH_ASSOC)) {
                    http_response_code(200);
                    return json_encode($results);
                }else {
                    http_response_code(404);
                    $results['error'] = true;
                    $results['message'] = "Une erreur s'est produite ou enregistrement non trouvé";
                    return json_encode($results);
                }
            }else{
                $req = $this->db->query($query);
                if ($result = $req->fetchAll(PDO::FETCH_ASSOC)) {
                    http_response_code(200);
                    return json_encode($result);
                }
            }                              
        }

    public function insert()
    {
        if (count($this->fields) > 0) {
            $total = count($this->fields) - 1;
            $sql = "INSERT INTO $this->table(";
            foreach ($this->fields as $key => $field) {
                if ($total != $key) {
                    $sql .= $field . ", ";
                } else {
                    $sql .= $field . ") ";
                }
            }
            $sql .= "VALUES(";
            foreach ($this->fields as $key => $field) {
                if ($total != $key) {
                    $sql .= ":$field, ";
                } else {
                    $sql .= ":$field)";
                }
            }

            //s'il existe un champs password il sera crypter
            if($this->values['password']){
                $this->values['password'] = password_hash($this->values['password'], PASSWORD_BCRYPT);
            }

            $req = $this->db->prepare($sql);
            if (!empty($this->values)) {
                if ($req->execute($this->values)) {
                    http_response_code(201);
                    $results['error'] = false;
                    $results['message'] = "Enregistrement effectué avec succès";
                } else {
                    http_response_code(503);
                    $results['error'] = true;
                    $results['message'] = "Enregistrement échoué";
                }
            } else {
                http_response_code(400);
                $results['error'] = true;
                $results['message'] = "Un ou plusieurs champs mal renseigner";
            }
            
            
            return json_encode($results);
            
        }
    }

    public function update()
    {
        if (count($this->fields) > 0) {
            $total = count($this->fields) - 1;
            $sql = "UPDATE $this->table SET ";
            foreach ($this->fields as $key => $field) {
                if ($total != $key) {
                    $sql .= "$field=:$field, ";
                } else {
                    $sql .= "$field=:$field ";
                }
            }
            $sql .= "WHERE $this->id=:$this->id";
            
            $this->values[$this->id] = $this->id_val;

            $req = $this->db->prepare($sql);
            if ($req->execute($this->values)) {
                http_response_code(200);
                $results['error'] = false;
                $results['message'] = "Enregistrement modifié avec succès";
            } else {
                http_response_code(503);
                $results['error'] = true;
                $results['message'] = "modification échouée";
            }

            return json_encode($results);
            
        }
    }

        public function delete()
        {
            $sql = "DELETE FROM $this->table WHERE $this->id=?";
            $del = $this->db->prepare($sql);
            if ($del->execute([$this->id_val])) {
                http_response_code(200);
                $results['error'] = false;
                $results['message'] = "Enregistrement supprimer avec succès";
            } else {
                http_response_code(503);
                $results['error'] = true;
                $results['message'] = "suppression échouée";
            }

            return json_encode($results);
        }
        
    }
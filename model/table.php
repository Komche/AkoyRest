<?php
include_once '../lib/php-jwt/src/BeforeValidException.php';
include_once '../lib/php-jwt/src/ExpiredException.php';
include_once '../lib/php-jwt/src/SignatureInvalidException.php';
include_once '../lib/php-jwt/src/JWT.php';
use \Firebase\JWT\JWT;

class Table
{

    private $table;
    private $fields = [];
    private $values = [];
    private $id;
    private $id_val;
    private $db;
    private $results = [];
    private $playload;
    private $key;

    public function __construct($db, $table, $fields = [null], $values = [null], $id = null, $id_val = null, $playload = null, $key = null)
    {
        $this->db = $db;
        $this->table = $table;
        $this->fields = $fields;
        $this->values = $values;
        $this->id = $id;
        $this->id_val = $id_val;
        $this->playload = $playload;
        $this->key = $key;
        $this->results['error'] = false;
        $this->results['message'] = "Tout s'est bien déroulé";
    }

    public function getData()
    {
        $query = "SELECT * FROM $this->table ";
        if ($this->id != null && $this->id_val != null) {
            //die($this->id_val);
            $query .= "WHERE $this->id=:$this->id LIMIT 1";

            $req = $this->db->prepare($query);

            $req->execute([$this->id => intval($this->id_val)]);
            if ($this->results = $req->fetch(PDO::FETCH_ASSOC)) {
                if ($this->playload != null) {
                    $this->playload['data'] = $this->results;
                    $token = JWT::encode($this->playload, $this->key);
                    $this->throwError(200, "Succues : $token");
                } else {
                    http_response_code(200);

                    return json_encode($this->results);
                }
            } else {
                $this->throwError(404, "Une erreur s'est produite ou enregistrement non trouvé", true);
            }
        } else {
            $req = $this->db->query($query);
            if ($this->results = $req->fetchAll(PDO::FETCH_ASSOC)) {
                if ($this->playload !== null) {
                    $this->playload['data'] = $this->results;
                    $token = JWT::encode($this->playload, $this->key);
                    $this->throwError(200, "Sucuess : $token");
                } else {
                    http_response_code(200);

                    return json_encode($this->results);
                }
            }
        }
    }

    public function insert($required = null)
    {


        if ($required !== null) {
            //print_r($this->values); die();
            foreach ($this->values as $key => $value) {

                for ($i = 0; $i < count($required); $i++) {
                    if ($required[$i] == $key) {
                        unset($required[$i]);

                        $required[$key] = $value;
                    }
                }
            }
            $this->is_not_empty($required);
        }
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
            if ($this->values['password']) {
                $this->values['password'] = password_hash($this->values['password'], PASSWORD_BCRYPT);
            }

            //s'il existe un champ email il sera verifer s'il est au bon format
            if ($this->values['email']) {
                if (!filter_var($this->values['email'], FILTER_VALIDATE_EMAIL))
                    $this->throwError(503, "Cette adresse email n'est pas au bon format", true);
            }

            $req = $this->db->prepare($sql);
            if (!empty($this->values)) {
                if ($req->execute($this->values)) {
                    $this->throwError(201, "Enregistrement effectué avec succès");
                } else {
                    $this->throwError(503, "Enregistrement échoué", true);
                }
            } else {
                $this->throwError(400, "Un ou plusieurs champs mal renseigner", true);
            }
        }
    }

    public function update()
    {

        if (count($this->values) > 0) {
            $temp  = array_keys($this->values);
            $last_key = end($temp);
            $sql = "UPDATE $this->table SET ";
            foreach ($this->values as $key => $field) {
                if ($last_key != $key) {
                    $sql .= "$key=:$key, ";
                } else {
                    $sql .= "$key=:$key ";
                }
            }
            $sql .= "WHERE $this->id=:$this->id";

            //s'il existe un champs password il sera crypter
            if ($this->values['password']) {
                $this->values['password'] = password_hash($this->values['password'], PASSWORD_BCRYPT);
            }

            $this->values[$this->id] = $this->id_val;

            $req = $this->db->prepare($sql);
            if ($req->execute($this->values)) {
                $this->throwError(200, "Enregistrement modifié avec succès");
            } else {
                $this->throwError(503, "modification échouée", true);
            }
        }
    }

    public function delete()
    {
        if ($this->is_not_use($this->table, $this->id, $this->id_val)) {
            $this->throwError(503, "Cet enregistrement n'existe pas", true);
        }
        $sql = "DELETE FROM $this->table WHERE $this->id=?";
        $del = $this->db->prepare($sql);
        if ($del->execute([$this->id_val])) {
            $this->throwError(200, "Enregistrement supprimer avec succès");
        } else {
            $this->throwError(503, "Suppression échouée", true);
        }
    }

    public function is_not_empty($fields = [])
    {
        if (count($fields) != 0) {
            foreach ($fields as $key => $field) {
                if (empty($field) && trim($field) == "") {
                    $this->throwError(503, "$key est vide");
                    return false;
                }
            }
            return true;
        }
    }

    public function lastID($table, $fields)
    {
        $sql = "SELECT $fields FROM $table ORDER BY $fields DESC LIMIT 1;";
        $req = $this->db->query($sql);

        if ($res = $req->fetch()) {
            return $res[$fields];
        }
    }

    public function is_not_use($table, $field, $value)
    {
        $sql = "SELECT * FROM $table WHERE $field=:value";

        $req = $this->db->prepare($sql);
        $req->execute(array('value' => $value));
        if ($req->fetch()) {
            return false;
        } else {
            return true;
        }
    }


    public function throwError($code = null, $message, $is_error = false)
    {
        http_response_code($code);
        $this->results['error'] = $is_error;
        $this->results['message'] = $message;
        echo json_encode($this->results);
        die();
    }

    public function test($ha)
    {
        echo $ha;
    }
}


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="main.css">
    <title>AkoyRest</title>
</head>

<body>
    <form class="my-form" action="index.php" method="post">
        <div class="form-control">
            <label for="db_name">Nom de la base de nom</label>
            <input type="text" name="db_name">
        </div>
        <div class="form-control">
            <label for="host">Nom du hote</label>
            <input type="text" name="host">
        </div>
        <div class="form-control">
            <label for="username">Utilisateur de la base de donnée</label>
            <input type="text" name="username">
        </div>
        <div class="form-control">
            <label for="password">mot de passe</label>
            <input type="password" name="password">
        </div>
        <input type="submit" value="Valider">
    </form>
</body>

</html>


<?php
//echo file_get_contents('config/Configuration.php'); die();
require_once('config/Configuration.php');
$database = new Configuration();
global $db;
$db = $database->getConnection();

if (isset($_POST)) {
    extract($_POST);

    $file = fopen("config/var.php", "w+");
    $txt = '<?php
        //Pour la connexion à la base de donnée
        $this->config["host"] = \'' . $host . '\';
        $this->config["db_name"] = \'' . $db_name . '\';
        $this->config["username"] = \'' . $username . '\';
        $this->config["password"] = \'' . $password . '\';';


    fwrite($file, $txt);
    fclose($file);

    $tables_list = getTables();

    if (is_array($tables_list) || is_object($tables_list)) {
        $txt = '$this->config["tables"] = [';
        foreach ($tables_list as $tables) {
            $txt .= '\'' . $tables . '\',';
        }
        $txt .= '];';
        file_put_contents("config/var.php", "\n" . $txt . PHP_EOL, FILE_APPEND | LOCK_EX, null);

        foreach ($tables_list as $tables) {
            $fields = getFields($tables);
            if (is_array($fields) || is_object($fields)) {
                (empty($text_field)) ? $text_field = '$this->config[\'tables\'][\'' . $tables . '\'] = [' : $text_field .= '$this->config[\'tables\'][\'' . $tables . '\'] = [';
                (empty($text_field_id)) ? $text_field_id = '$this->config[\'tables\'][\'' . $tables . '\'][\'id\'] = [' : $text_field_id .= '$this->config[\'tables\'][\'' . $tables . '\'][\'id\'] = [';
                $text_field = '$this->config[\'tables\'][\'' . $tables . '\'] = [';
                foreach ($fields as $key => $field) {
                    if ($key == 0) {
                        $text_field_id .= '\'' . $field . '\'';
                        $text_field .= '\'' . $field . '\',';
                    } else {
                        $text_field .= '\'' . $field . '\',';
                    }
                }
                $text_field .= '];';
                $text_field_id .= '];';
                file_put_contents("config/var.php", "\n" . $text_field . PHP_EOL, FILE_APPEND | LOCK_EX, null);
                file_put_contents("config/var.php", "\n" . $text_field_id . PHP_EOL, FILE_APPEND | LOCK_EX, null);
            }
        }
    } else {
        die("Aucune table trouvé dans votre base de donnée");
    }
}

function getTables()
{
    if (isset($_POST)) {
        extract($_POST);
    }
    global $db;
    $sql = "SHOW TABLES";
    $req = $db->prepare($sql);
    $req->execute();
    while ($res = $req->fetch()) {
        $data[] = $res["Tables_in_$db_name"];
    }
    return $data;
}

function getFields($table)
{
    global $db;
    $sql = "DESCRIBE $table";
    $req = $db->query($sql);
    while ($res = $req->fetch()) {
        $data[] = $res['Field'];
    }
    return $data;
}
?>
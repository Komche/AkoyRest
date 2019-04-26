<?php
class Configuration
{
    private $config = array();
    public $conn;

    public function __construct()
    {
        //Pour la connexion à la base de donnée
        // $this->config['host'] = 'localhost';
        // $this->config['db_name'] = 'akoybiz';
        // $this->config['username'] = 'root';
        // $this->config['password'] = '';
        include_once 'var.php';
        //information pour Json Web Token
        $this->config['jwt'] = null;
        // [
        //     'iss' => "localhost", // celui qui emet le JWT
        //     'aud' => "localhost", // celui qui va recevoir
        //     'iat' => time(), // l'heure de l'émission
        //     'exp' => time()+120, // l'heure de l'expiration
        //     'data'=>[] // donnée à envoyer
        // ];
       $this->config['key']= null; //vous seul devrait connaitre cette cle;

        //Infromation des tables de la base de donnée
        //$this->config['tables'] = ['category', 'chats', 'city', 'comments', 'country', 'files', 'profile', 'publication', 'roles', 'users', 'user_akoy'];

        //Information des attribut de la base de donnée
        /* NB: 
            1. si vous souhaitez crypter un de vos champs, par exemple le champ mot de passe il faut 
            le nomer 'password' 
            
            2. Si vous souhaitez verifier si une adresse mail est valid il faut nommer le champs 'email' */
        // $this->config['tables']['vols'] = ['ville_depart', 'ville_arriver', 'nb_heure_vols', 'prix'];
        // $this->config['tables']['products'] = ['name', 'description', 'price', 'category_id'];
        // $this->config['tables']['categories'] = ['name', 'description'];
        // $this->config['tables']['users'] = ['firstname', 'lastname', 'email', 'password'];

        /*
        * jointure entre les tables
        * on va lister les champs à afficher des deux tables joint
        */
        //$this->config['tables']['products']['categories']= ['name', 'description', 'price', 'name as cat_name' ];

        //les champs requis d'une table
        //$this->config['tables']['users']['required'] = ['email', 'password'];

        //print_r($this->config['tables']); die();
    }

    //$mydatabase['name'] = "api_db";


    public function getConnection()
    {
        $this->conn = null;
        $host = $this->config['host'];
        $db_name = $this->config['db_name'];

        try {
            $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
            $this->conn = new PDO("mysql:host=$host;dbname=$db_name", $this->config['username'], $this->config['password'], $pdo_options);
            $this->conn->exec("set names utf8");
        } catch (Exception $e) {
            echo "Erreur de connection: $e->getMessage()";
        }

        return $this->conn;
    }

    public function getConfig()
    {
        return $this->config;
    }
}

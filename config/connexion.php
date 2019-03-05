<?php
    class Connexion
    {
        private $config = array();
        public $conn;

        public function __construct() {
            //Pour la connexion à la base de donnée
            $this->config['host'] = 'localhost';
            $this->config['db_name'] = 'api_db';
            $this->config['username'] = 'root';
            $this->config['password'] = '';

            //Infromation des tables de la base de donnée
            $this->config['tables'] = ['vols', 'products', 'categories','users'];

            //Information des attribut de la base de donnée
            $this->config['tables']['vols'] = ['ville_depart', 'ville_arriver', 'nb_heure_vols', 'prix'];
            $this->config['tables']['products'] = ['name', 'description', 'price', 'category_id'];
            $this->config['tables']['categories'] = ['name', 'description'];
            $this->config['tables']['users'] = ['firstname', 'lastname', 'email', 'password'];

            //information des IDs de la base de donnée
            $this->config['tables']['vols']['id'] = ['id'];
            $this->config['tables']['products']['id'] = ['id'];
            $this->config['tables']['categories']['id'] = ['id'];
            $this->config['tables']['users']['id'] = ['id'];

        }

        //$mydatabase['name'] = "api_db";


        public function getConnection()
        {
            $this->conn = null;
            $host = $this->config['host'];
            $db_name = $this->config['db_name'];

            try {
                $this->conn = new PDO("mysql:host=$host;dbname=$db_name", $this->config['username'], $this->config['password']);
                $this->conn->exec("set names utf8");
            } catch (PDOException $exception) {
                echo "Erreur de connection: $exception->getMessage()";
            }

            return $this->conn;
        }

        public function getConfig(){
            return $this->config;
        }
    }
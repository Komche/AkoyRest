<?php
        //Pour la connexion à la base de donnée
        $this->config["host"] = 'localhost';
        $this->config["db_name"] = 'MarketApp';
        $this->config["username"] = 'root';
        $this->config["password"] = '';
$this->config["tables"] = ['category','owner','product','roles','shop','users',];

$this->config['tables']['category'] = ['label',];

$this->config['tables']['category']['id'] = ['id_category'];

$this->config['tables']['owner'] = ['last_name','frist_name','email','phone_number','nif','rccm','user',];

$this->config['tables']['category']['id'] = ['id_category'];$this->config['tables']['owner']['id'] = ['id_owner'];

$this->config['tables']['product'] = ['label','description','likes','times','url','category','shop',];

$this->config['tables']['category']['id'] = ['id_category'];$this->config['tables']['owner']['id'] = ['id_owner'];$this->config['tables']['product']['id'] = ['id_product'];

$this->config['tables']['roles'] = ['types','read_role','write_role','user',];

$this->config['tables']['category']['id'] = ['id_category'];$this->config['tables']['owner']['id'] = ['id_owner'];$this->config['tables']['product']['id'] = ['id_product'];$this->config['tables']['roles']['id'] = ['id_roles'];

$this->config['tables']['shop'] = ['label','owner',];

$this->config['tables']['category']['id'] = ['id_category'];$this->config['tables']['owner']['id'] = ['id_owner'];$this->config['tables']['product']['id'] = ['id_product'];$this->config['tables']['roles']['id'] = ['id_roles'];$this->config['tables']['shop']['id'] = ['id_shop'];

$this->config['tables']['users'] = ['last_name','first_name','email','phone_number','password_user','code',];

$this->config['tables']['category']['id'] = ['id_category'];$this->config['tables']['owner']['id'] = ['id_owner'];$this->config['tables']['product']['id'] = ['id_product'];$this->config['tables']['roles']['id'] = ['id_roles'];$this->config['tables']['shop']['id'] = ['id_shop'];$this->config['tables']['users']['id'] = ['id_user'];

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Db
 *
 * @author семья
 */
class Model {
    private $con;
    
    public function __construct($config){
        $this->con = new DB($config['DBHost'], $config['DBPort'], $config['DBName'], $config['DBUser'], $config['DBPassword']);  
    }
    
    public function log($type, $description)
    {
        $params = array(
            "type" => $type,
            "description" => $description
        );
        return $this->con->query("INSERT INTO log (type, description) VALUES (:type, :description)", $params);
    }    
    
    public function registration($username, $firstname, $lastname, $id)
    {
        $res = 0;
        $params = array(
            "login" => $username,
            "firstname" => $firstname,
            "lastname" => $lastname,
            "tid" => $id
        );
        if(!$this->is_exists($username, $firstname, $lastname, $id))
        {
            $res = $this->con->query("INSERT IGNORE INTO users (login, firstname, lastname, tid) VALUES (:login, :firstname, :lastname, :tid)", $params);
            $this->log('registration','Пользователь - '.implode(': ',$params).' авторизовался в боте.');
        }
        else
        {
           $res = 1; 
        }
        return $res;
    }
    
    public function getUser($tid)
    {
        $params = array(
            "tid" => $tid
        );
        $res = $this->con->query("select * from users where tid = :tid", $params);
        return isset($res) ? $res : null;
    }
    
    public function is_exists($id)
    {
        $res = 0;
        $params = array(
            "tid" => $id
        );
        $res = $this->con->query("select 1 from users where tid = :tid", $params);
        return isset($res[0]) && isset($res[0][1]) ? $res[0][1] : 0;
    } 

    public function getCitys()
    {
        $res = $this->con->query("select * from citys");
        return isset($res) ? $res : null;
    }

    public function getCityForName($name)
    {
        $params = array(
            "name" => $name
        );
        $res = $this->con->query("select * from citys where name = :name", $params);
        return isset($res[0]) && isset($res[0]['id'])? $res[0]['id'] : 0;
    }

    public function getCountrys()
    {
        $res = $this->con->query("select * from countrys");
        return isset($res) ? $res : null;
    }

    public function getCitysForCountry($country_name)
    {
        $params = array(
            "country_name" => $country_name
        );
        $res = $this->con->query("select s.* from citys s join countrys c on c.id = s.country_id and c.name = :country_name", $params);
        return isset($res) ? $res : null;
    }
    
    public function is_city_user($tid)
    {
        $res = 0;
        $params = array(
            "tid" => $tid
        );
        $res = $this->con->query("select 1 from users where tid = :tid and city_id is not null", $params);
        return isset($res[0]) && isset($res[0][1]) ? $res[0][1] : 0;
    }

    public function is_city($name)
    {
        $res = 0;
        $params = array(
            "name" => $name
        );
        $res = $this->con->query("select 1 from citys where name = :name", $params);
        return isset($res[0]) && isset($res[0][1]) ? $res[0][1] : 0;
    }

    public function is_country($name)
    {
        $res = 0;
        $params = array(
            "name" => $name
        );
        $res = $this->con->query("select 1 from countrys where name = :name", $params);
        return isset($res[0]) && isset($res[0][1]) ? $res[0][1] : 0;
    }
    
    public function setCityUser($tid, $city_id)
    {       
        $res = 0;
        $params = array(
            "tid" => $tid,
            "city_id" => $city_id
        );
        $this->con->query("UPDATE users set city_id = :city_id where tid = :tid", $params);
        return $res;
    }
}

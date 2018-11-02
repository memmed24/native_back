<?php

class DB{
    public $db;
    private $host = "localhost";
    private $database = "personal";
    private $user = "root";
    private $pass = "";
    public function __construct(){
        try{
            $this->db = new PDO("mysql:host=".$this->host.";dbname=".$this->database, $this->user, $this->pass);
        }catch (PDOException $exception){
            echo $exception->getMessage();
        }
    }
}
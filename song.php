<?php

require_once 'db.php';

class Song {
    private $db;
    public function __construct(){
        $this->db = new DB();
    }

    public function get($get){

    }
}
$song = new Song();
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $song->get($_GET);
}elseif ($_SERVER['REQUEST_METHOD'] == 'POST'){

}
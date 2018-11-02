<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, personal_user_token");

class Includer {
    private $dir = "C:\\xampp\\htdocs\\personal";
    private $sorting_order = 2;

    public function __construct(){
        $files = scandir($this->dir, $this->sorting_order);
        $i = 0;

        foreach ($files as $key => $value){
            if($i !== 0 && $i !== 1 && $i !== 2 && $value !== "includer.php" && $value !== 'notes.php' && $value !== 'messages.php'){
                require_once $value;
            }
            $i++;
        }
    }
}


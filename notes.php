<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, personal_user_token");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

include_once "db.php";
include_once "helper.php";

class Notes {

    private $db;
    private $user;
    private $token;

    public function __construct(){
        $this->db = new DB();
        $headers = getallheaders();
        $this->token = isset($headers['personal_user_token']) ? $headers['personal_user_token'] : null;
        if($this->token !== null){
            $stmt = $this->db->db->prepare("SELECT * FROM users WHERE user_token = :token");
            $stmt->bindParam(':token', $this->token);
            $stmt->execute();
            $this->user = $stmt->fetchObject();

            if($this->user == false){
                echo Helper::error('Unknown');
                die();
            }else{
                $this->user->user_password = null;
            }
        }else{
            echo Helper::error("Unknown");
            die();
        }
    }

    public function post(){
        $content = trim(file_get_contents("php://input"));
        $_POST = json_decode($content, true);

        $stmt = $this->db->db->prepare("INSERT INTO notes (note_header, note_text, user_id, created_at) VALUES (:note_header, :note_text, :user_id, :created_at)");
        $time = $this->time();
        $stmt->bindParam(":note_header",$_POST['header']);
        $stmt->bindParam(":note_text",$_POST['text']);
        $stmt->bindParam(":user_id",$this->user->user_id);
        $stmt->bindParam(":created_at",$time);

        $stmt->execute();

        $note = [
            'note_id' => $this->db->db->lastInsertId(),
            'note_header' => $_POST['header'],
            'note_text' => $_POST['text'],
            'user_id' => $this->user->user_id,
            'created_at' => $time
        ];



        $data = [
            'status' => 200,
            'data' => $note
        ];

        echo json_encode($data);

    }

    public function get(){
        $query = "SELECT * FROM notes WHERE user_id = :user_id";

        if(isset($_GET['order'])){

        }
        $stmt = $this->db->db->prepare($query);
        $stmt->bindParam(':user_id', $this->user->user_id);
        $stmt->execute();
        $notes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $data = [
            'status' => 200,
            'data' => $notes
        ];
        echo json_encode($data);
    }

    public function delete($id){
        $query = "DELETE FROM notes WHERE note_id = :note_id";
        $stmt = $this->db->db->prepare($query);
        $stmt->bindParam(':note_id', $id);
        $stmt->execute();
        $data = [
            'status' => 200,
            'messages' => 'removed'
        ];
        echo json_encode($data);

    }

    private function time(){
        $time = time();
        $time = date('Y-m-d H:i:s', $time);
        return $time;
    }


}
$notes = new Notes();
if($_SERVER['REQUEST_METHOD'] == 'POST'):
    $notes->post();
elseif($_SERVER['REQUEST_METHOD'] == 'GET'):
    $notes->get();
elseif($_SERVER['REQUEST_METHOD'] == 'DELETE'):
    $notes->delete($_GET['id']);
endif;
<?php
/**
 * Created by PhpStorm.
 * User: memme
 * Date: 7/14/2018
 * Time: 2:58 PM
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, personal_user_token");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");


include_once "db.php";
include_once "helper.php";
class Messages{

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

    public function get(){

    }

    public function post(){
        $content = trim(file_get_contents("php://input"));
        $_POST = json_decode($content, true);
        $to = $_POST['message_to'];
        $stmt = $this->db->db->prepare("INSERT INTO messages (message_from, message_to, message_text) VALUES (:from, :to, :text)");
        $stmt->bindParam(':from', $this->user->user_id);
        $stmt->bindParam(':to', $to);
        $stmt->bindParam(':text', $_POST['message_text']);

        $stmt->execute();
        $data = [
            'status' => 200,
            'data' => [
                'message_from' => $this->user->user_id,
                'message_to' => $_POST['message_to'],
                'message_text' => $_POST['message_text']
            ]
        ];

        echo json_encode($data);
    }


}
$message = new Messages();
if($_SERVER['REQUEST_METHOD'] == 'GET'){

}else if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message->post();
}
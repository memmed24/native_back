<?php
header("Access-Control-Allow-Origin: *");

include_once "includer.php";

class Index{

    public function __construct(){
        $includer = new Includer();
    }

}

$index = new Index();


class Url{

    private $db;

    public function __construct(){
        $this->db = new db();
        switch ($_SERVER['REQUEST_METHOD']){
            case 'GET':
                $this->get(isset($_GET['type']) ? $_GET['type'] : null);
                break;
            case 'POST':
                $this->post(isset($_GET['type']) ? $_GET['type'] : null);
                break;
        }
    }

    public function get($page = null){
        switch ($page){
            case null:
                $this->index();
                break;
            case 'song':
                $query = "SELECT * FROM SONGS";
                if(isset($_GET['limit'])){
                    $query .= " LIMIT ".$_GET['limit'];
                }
                $stmt = $this->db->db->prepare($query);
                $stmt->execute();
                echo json_encode($stmt->fetchAll(PDO::FETCH_OBJ));
                break;
        }

    }

    /**
     * @param null $page
     */
    public function post($page = null){
        switch ($page){
            case null:
                $this->index();
                break;
            case 'song':
                $stmt = $this->db->db->prepare("INSERT INTO SONGS (music_title, music_cover) VALUES (:title, :cover)");
                $stmt->bindParam(':title', $_POST['title']);
                $stmt->bindParam(':cover', $_POST['cover']);
                $stmt->execute();
                break;
            case 'register':
                $content = trim(file_get_contents("php://input"));

                $_POST = json_decode($content, true);



                $stmt = $this->db->db->prepare("INSERT INTO users (user_name, user_surname, user_username, user_token, user_password) VALUES (:user_name, :surname, :username, :token, :password)");
                $stmt->bindParam(':user_name', $_POST['name']);
                $stmt->bindParam(':surname', $_POST['surname']);
                $stmt->bindParam(':username', $_POST['username']);
                $token = $this->token();
                $stmt->bindParam(':token', $token);
                $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
                $stmt->bindParam(':password', $password);
                $stmt->execute();
                $data = [
                    'status' => 200,
                    'data' => [
                        'user_name' => $_POST['name'],
                        'user_surname' => $_POST['surname'],
                        'user_username' => $_POST['username'],
                        'user_token' => $token
                    ]
                ];
                echo json_encode($data);
                break;
            case 'login':
                $content = trim(file_get_contents("php://input"));

                $_POST = json_decode($content, true);

                $stmt = $this->db->db->prepare("SELECT * FROM users WHERE user_username = :username");
                $stmt->bindParam(':username', $_POST['username']);
                $stmt->execute();
                $user = $stmt->fetchObject();
                
                if($user == false){
                    echo json_encode($this->errors('WRONG_PASSWORD'));
                    die();
                }

                if(password_verify($_POST['password'], $user->user_password)):
                    $token = $this->token();
                    $stmt2 = $this->db->db->prepare("UPDATE users SET user_token = :token WHERE user_username = :username");
                    $stmt2->bindParam(':token', $token);
                    $stmt2->bindParam(':username', $_POST['username']);
                    $stmt2->execute();
                    $user->user_token = $token;
                    $user->user_password = null;
                    $data = [
                        'status' => 200,
                        'data' => $user
                    ];
                    echo json_encode($data);
                else:
                    echo json_encode($this->errors('WRONG_PASSWORD'));
                endif;
                break;
            case 'logout':
                $headers = getallheaders();
                $token = $headers['personal_user_token'];

                $stmt = $this->db->db->prepare("UPDATE users SET user_token = null WHERE user_token = :token");
                $stmt->bindParam(":token", $token);
                $stmt->execute();
                $user = $stmt->fetchObject();
                $data = [
                    'status' => 200,
                    'messages' => 'Log out'
                ];
                echo json_encode($data);
                break;
        }

    }

    private function token(){
        $token = md5(uniqid(rand(), true));
        return $token;
    }

    private function errors($error){
        switch ($error){
            case 'WRONG_PASSWORD':
                $data = [
                    'status' => 300,
                    'messages' => 'Wrong password or username'
                ];
                break;
            default:
                $data = [];
                break;
        }
        return $data;
    }


    public function index(){
        echo "asd";
    }

}
$url = new Url();
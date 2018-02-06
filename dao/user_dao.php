<?php

require_once(DB_CONNECTION);
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/user_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/utils/datatables_helper.class.php' );

class user_dao {
  private $conn;
  
  public function __construct(){
      $this->conn = new keopsdb();
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  }
  function getUser($email) {
    try {
      $user = new user_dto();
      $query = $this->conn->prepare("SELECT * FROM USERS WHERE email = ?;");
      $query->bindParam(1, $email);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $user->id = $row['id'];
        $user->name = $row['name'];
        $user->email = $row['email'];
        $user->creation_date = $row['creation_date'];
        $user->role = $row['role'];
        $user->password = $row['password'];
        $user->active = $row['active'];
      }
      $this->conn->close_conn();
      return $user;
    } catch (Exception $ex) {
      throw new Exception("Error in user_dao::getUser : " . $ex->getMessage());
    }
  }
  
  function getUserById($id) {
    try {
      $user = new user_dto();
      
      $query = $this->conn->prepare("SELECT * FROM USERS WHERE id = ?;");
      $query->bindParam(1, $id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $user->id = $row['id'];
        $user->name = $row['name'];
        $user->email = $row['email'];
        $user->creation_date = $row['creation_date'];
        $user->role = $row['role'];
        $user->active = $row['active'];
      }
      $this->conn->close_conn();
      return $user;
    } catch (Exception $ex) {
      throw new Exception("Error in user_dao::getUserById : " . $ex->getMessage());
    }
  }

  function getUserPassword($email){
    try {
      $query = $this->conn->prepare("SELECT password FROM USERS WHERE email LIKE ?;");
      $query->bindParam(1, $email);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      $password = $query->fetch();
      return $password["password"];
      
    } catch (Exception $ex) {
      throw new Exception("Error in user_dao::getUserPassword : " . $ex->getMessage());
    }
  
  }
  
  function getUsers() {
    try {
      $users = array();
      $query = $this->conn->prepare("SELECT id, name, email, creation_date, role, active FROM USERS");
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $user = new user_dto();
        $user->id = $row['id'];
        $user->name = $row['name'];
        $user->email = $row['email'];
        $user->creation_date = $row['creation_date'];
        $user->role = $row['role'];
        $user->active = $row['active'];
        $users[] = $user;
      }
      $this->conn->close_conn();
      return $users;
    } catch (Exception $ex) {
      throw new Exception("Error in user_dao::getUsers : " . $ex->getMessage());
    }
  }

  function newUser($user_dto){
    try {
      error_log("HOLA");
      $query = $this->conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?);");
      $query->bindParam(1, $user_dto->name);
      $query->bindParam(2, $user_dto->email);
      $query->bindParam(3, $user_dto->password);
      $query->execute();
      error_log("HOLA 2");
      //$query->setFetchMode(PDO::FETCH_ASSOC);
      $user_dto->id = $this->conn->lastInsertId();
      error_log("HOLA 3");
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $user_dto->id = -1;
      error_log($ex->getMessage());
      return false;
    }
  }
 
  function getDatatablesUsers($request) {
    try {
      $columns = array(
          array( 'db' => 'id', 'dt' => 0 ),
          array( 'db' => 'name', 'dt' => 1 ),
          array( 'db' => 'email', 'dt' => 2 ),
          array(
              'db'        => 'creation_date',
              'dt'        => 3,
              'formatter' => function( $d, $row ) {
                  return date( 'd/m/Y', strtotime($d));
              }
          ),
          array( 'db' => 'role', 'dt' => 4 ),
          array( 'db' => 'active', 'dt' => 5)
      );

      return json_encode(DatatablesProcessing::simple( $request, $this->conn, "users", "id", $columns ));
    } catch (Exception $ex) {
      throw new Exception("Error in user_dao::getDatatablesUsers : " . $ex->getMessage());
    }
  }
}

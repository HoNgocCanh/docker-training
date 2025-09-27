<?php

require_once 'BaseModel.php';

class UserModel extends BaseModel {

    public function findUserById($id) {
        $sql = 'SELECT * FROM users WHERE id = '.$id;
        $user = $this->select($sql);

        return $user;
    }

    public function findUser($keyword) {
        $sql = 'SELECT * FROM users WHERE user_name LIKE %'.$keyword.'%'. ' OR user_email LIKE %'.$keyword.'%';
        $user = $this->select($sql);

        return $user;
    }

    /**
     * Authentication user
     * @param $userName
     * @param $password
     * @return array
     */
    public function auth($userName, $password) {
        // Clean username trước khi query
        $cleanUsername = XSSProtection::clean($userName);
        $md5Password = md5($password);
        
        $sql = 'SELECT * FROM users WHERE name = "' . 
            mysqli_real_escape_string(self::$_connection, $cleanUsername) . 
            '" AND password = "' . $md5Password . '"';

        $user = $this->select($sql);
        return $user;
    }

    /**
     * Delete user by id
     * @param $id
     * @return mixed
     */
    public function deleteUserById($id) {
        $sql = 'DELETE FROM users WHERE id = '.$id;
        return $this->delete($sql);

    }

    /**
     * Update user
     * @param $input
     * @return mixed
     */
    public function updateUser($input) {
        $sql = 'UPDATE users SET 
                 name = "' . mysqli_real_escape_string(self::$_connection, $input['name']) .'", 
                 password="'. md5($input['password']) .'"
                WHERE id = ' . $input['id'];

        $user = $this->update($sql);

        return $user;
    }

    /**
     * Insert user
     * @param $input
     * @return mixed
     */
    public function insertUser($input) {
    $sql = "INSERT INTO `app_web1`.`users` (`name`, `fullname`, `email`, `type`, `password`) VALUES (" .
        "'" . mysqli_real_escape_string(self::$_connection, $input['name']) . "', " .
        "'" . mysqli_real_escape_string(self::$_connection, $input['fullname']) . "', " .
        "'" . mysqli_real_escape_string(self::$_connection, $input['email']) . "', " .
        "'" . mysqli_real_escape_string(self::$_connection, $input['type']) . "', " .
        "'" . md5($input['password']) . "')";

    $user = $this->insert($sql);

    return $user;
    }

    /**
     * Search users
     * @param array $params
     * @return array
     */
    public function getUsers($params = []) {
        //Keyword
        if (!empty($params['keyword'])) {
            $keyword = mysqli_real_escape_string(self::$_connection, $params['keyword']);
            $sql = 'SELECT * FROM users WHERE name LIKE "%' . $keyword . '%"';
            // Debug query
            error_log("SQL Query: " . $sql);
            $users = $this->select($sql);
        } else {
            $sql = 'SELECT * FROM users';
            $users = $this->select($sql);
        }

        return $users;
    }
}
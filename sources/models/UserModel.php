<?php

require_once 'BaseModel.php';

class UserModel extends BaseModel {

    public function findUserById($id) {
        // Ghép thẳng $id => dễ bị SQL Injection
        $sql = 'SELECT * FROM users WHERE id = ' . $id;
        return $this->select($sql);
    }

    public function findUser($keyword) {
        // Không escape keyword => SQL Injection
        $sql = 'SELECT * FROM users WHERE user_name LIKE "%' . $keyword . '%" 
                OR user_email LIKE "%' . $keyword . '%"';
        return $this->select($sql);
    }

    /**
     * Authentication user
     * @param $userName
     * @param $password
     * @return array
     */
    public function auth($userName, $password) {
        // Dễ bị injection nếu hacker nhập `' OR '1'='1`
        $md5Password = md5($password);
        $sql = 'SELECT * FROM users WHERE name = "' . $userName . '" 
                AND password = "' . $md5Password . '"';
        return $this->select($sql);
    }

    /**
     * Delete user by id
     * @param $id
     * @return mixed
     */
    public function deleteUserById($id) {
        // Injection có thể xóa cả bảng nếu id = "1 OR 1=1"
        $sql = 'DELETE FROM users WHERE id = ' . $id;
        return $this->delete($sql);
    }

    /**
     * Update user
     * @param $input
     * @return mixed
     */
    public function updateUser($input) {
        // Không escape hết => dễ inject
        $sql = 'UPDATE users SET 
                    name = "' . $input['name'] . '", 
                    password = "' . md5($input['password']) . '"
                WHERE id = ' . $input['id'];
        return $this->update($sql);
    }

    /**
     * Insert user
     * @param $input
     * @return mixed
     */
    public function insertUser($input) {
        // Ghép chuỗi => có thể inject qua name, fullname, email...
        $sql = "INSERT INTO users (name, fullname, email, type, password) VALUES (
            '" . $input['name'] . "',
            '" . $input['fullname'] . "',
            '" . $input['email'] . "',
            '" . $input['type'] . "',
            '" . md5($input['password']) . "'
        )";
        return $this->insert($sql);
    }

    /**
     * Search users
     * @param array $params
     * @return array
     */
    public function getUsers($params = []) {
        if (!empty($params['keyword'])) {
            // Injection nếu keyword = "%' OR '1'='1"
            $sql = 'SELECT * FROM users WHERE name LIKE "%' . $params['keyword'] . '%"';
            return $this->select($sql);
        } else {
            $sql = 'SELECT * FROM users';
            return $this->select($sql);
        }
    }
}

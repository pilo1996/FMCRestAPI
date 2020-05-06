<?php

include 'functions.php';

    class DBOperations{
        private $conn;

        function __construct(){
            require_once dirname(__FILE__).'/DBConnect.php';
            $db = new DBConnect();
            $this->conn = $db->connect();
        }

        public function createUser($email, $nome, $password){
            if(!$this->emailExists($email)){
                $sql = "INSERT INTO users (email, nome, password, validated, token) VALUES (?,?,?,?,?);";
                $stmt = $this->conn->prepare($sql);
                $token = generateToken();
                $hashed_password = encryptPassword($password);
                $validated = 1;
                $stmt->bind_param("sssis", $email, $nome, $hashed_password, $validated, $token);
                $res = $stmt->execute();
                if($res)
                    return USER_CREATED;
                else
                    return USER_FAILURE;
            }
            else
                return USER_EXISTS;
        }

        public function userLogin($email, $password){
            if($this->emailExists($email)){
                if(password_verify($password, $this->getUserPasswordByEmail($email))){
                    return USER_AUTHENTICATED;
                }else{
                    return USER_PASSWORD_MISMATCH;
                }
            }else{
                return USER_NOT_FOUND;
            }
        }

        private function getUserPasswordByEmail($email){
            $sql = "SELECT password FROM users WHERE email=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($password);
            $stmt->fetch();
            return $password;
        }

        private function getUserPasswordByID($id){
            $sql = "SELECT password FROM users WHERE id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($password);
            $stmt->fetch();
            return $password;
        }

        public function getUserByEmail($email){
            $sql = "SELECT * FROM users WHERE email=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($id, $email, $nome, $password, $validated, $token, $profile_pic, $selected_device);
            $stmt->fetch();
            $user = array();
            $user['userID'] = $id;
            $user['email'] = $email;
            $user['nome'] = $nome;
            $user['plainPassword'] = $password;
            $user['isValidated'] = $validated;
            $user['profile_pic'] = $profile_pic;
            $user['selectDeviceID'] = $selected_device;
            return $user;
        }

        public function getUserByID($id){
            $sql = "SELECT * FROM users WHERE id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $stmt->bind_result($id, $email, $nome, $password, $validated, $token, $profile_pic, $selected_device);
            $stmt->fetch();
            $user = array();
            $user['id'] = $id;
            $user['email'] = $email;
            $user['nome'] = $nome;
            $user['password'] = $password;
            $user['validated'] = $validated;
            $user['token'] = $token;
            $user['profile_pic'] = $profile_pic;
            $user['selected_device'] = $selected_device;
            return $user;
        }

        public function updateUser($id, $nome, $profile_pic){
            $sql = "UPDATE users SET nome = ?, profile_pic = ? WHERE id = ?;";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssi", $nome, $profile_pic, $id);
            if($stmt->execute())
                return true;
            else
                return false;
        }

        public function updateSelectedDevice($userID, $deviceID){
            $sql = "UPDATE users SET selected_device=? WHERE id = ?;";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $deviceID, $userID);
            if($stmt->execute())
                return true;
            else
                return false;
        }

        public function updateUserPassword($id, $newPassword, $currentPassword){
            if(password_verify($currentPassword, $this->getUserPasswordByID($id))) {
                $sql = "UPDATE users SET password = ? WHERE id = ?;";
                $stmt = $this->conn->prepare($sql);
                $newPassword = encryptPassword($newPassword);
                $stmt->bind_param("si", $newPassword, $id);
                if($stmt->execute())
                    return PASSWORD_CHANGED;
                else
                    return PASSWORD_NOT_CHANGED;
            }
            else
                return PASSWORD_MISMATCH;
        }

        public function emailExists($email){
            $sql = "SELECT email FROM users WHERE email=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }

        public function userIDExists($id){
            $sql = "SELECT email FROM users WHERE id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }

        public function getEmailbyID($id){
            $sql = "SELECT email FROM users WHERE id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $email = $stmt->get_result()->fetch_row();
            return $email[0];
        }

        public function getAllPositionsByUserID($id){
            $sql = "SELECT * FROM locations WHERE id=?;";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($positionID, $deviceID, $userID, $traduzione, $lat, $lon, $dayTime, $dateTime);
            $positions = array();
            while($stmt->fetch()){
                $position = array();
                $position['positionID'] = $positionID;
                $position['deviceID'] = $deviceID;
                $position['userID'] = $userID;
                $position['address'] = $traduzione;
                $position['latitude'] = $lat;
                $position['longitude'] = $lon;
                $position['dayTime'] = $dayTime;
                $position['dateTime'] = $dateTime;
                return $position;
                array_push($positions, $position);
            }
            return $positions;
        }

        public function getPositionsByDeviceUserID($userID, $deviceID){
            $sql = "SELECT * FROM locations WHERE user_fk=? AND device_fk=?;";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $userID, $deviceID);
            $stmt->execute();
            $stmt->bind_result($positionID, $deviceID, $userID, $traduzione, $lat, $lon, $dayTime, $dateTime);
            $positions = array();
            while($stmt->fetch()){
                $position = array();
                $position['positionID'] = $positionID;
                $position['deviceID'] = $deviceID;
                $position['userID'] = $userID;
                $position['address'] = $traduzione;
                $position['latitude'] = $lat;
                $position['longitude'] = $lon;
                $position['dayTime'] = $dayTime;
                $position['dateTime'] = $dateTime;
                array_push($positions, $position);
            }
            return $positions;
        }

        public function addDevice($nomeDevice, $uuid, $ownerID){
            if($this->userIDExists($ownerID)){
                if(!$this->isDeviceAlreadyRegistered($uuid, $ownerID)){
                    $sql = "INSERT INTO devices (nome_device, uuid_device, ownerFk, ownerEmail) VALUES (?,?,?,?)";
                    $stmt = $this->conn->prepare($sql);
                    $emailbyID = $this->getEmailbyID($ownerID);
                    $stmt->bind_param("ssis", $nomeDevice, $uuid, $ownerID, $emailbyID);
                    $res = $stmt->execute();
                    if($res)
                        return DEVICE_ADDED;
                    else
                        return DEVICE_FAILURE;
                }else{
                    return DEVICE_ALREADY_REGISTERED;
                }
            }
            else
                return USER_NOT_FOUND;
        }

        public function updateDevice($newDeviceName, $uuid, $ownerID){
            if($this->userIDExists($ownerID)){
                if($this->isDeviceAlreadyRegistered($uuid, $ownerID)){
                    $sql = "UPDATE devices SET nome_device=?, ownerEmail=? WHERE uuid_device=? AND ownerFk=?;";
                    $stmt = $this->conn->prepare($sql);
                    $emailbyID = $this->getEmailbyID($ownerID);
                    $stmt->bind_param("sssi", $newDeviceName, $emailbyID, $uuid, $ownerID);
                    $res = $stmt->execute();
                    if($res)
                        return DEVICE_UPDATED;
                    else
                        return DEVICE_UPDATE_FAILURE;
                }else{
                    return DEVICE_NOT_FOUND;
                }
            }
            else
                return USER_NOT_FOUND;
        } //deprecated

        private function isDeviceAlreadyRegistered($uuid, $ownerID){
            $sql = "SELECT id_device FROM devices WHERE ownerFk=? AND uuid_device=?;";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("is", $ownerID, $uuid);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }

        public function getDeviceByUuidAndOwnerID($uuid, $ownerID){
            $sql = "SELECT * FROM devices WHERE ownerFk=? AND uuid_device=?;";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("is", $ownerID, $uuid);
            $stmt->execute();
            $data = $stmt->get_result()->fetch_row();
            $device = array();
            $device['id'] = $data[0];
            $device['name'] = $data[1];
            $device['uuid'] = $data[2];
            $device['ownerID'] = $data[3];
            $device['email'] = $data[4];
            return $device;
        }

        public function getDeviceByID($deviceID){
            $sql = "SELECT * FROM devices WHERE id_device=?;";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $deviceID);
            $stmt->execute();
            $stmt->bind_result($id_device, $nome_device, $uuid_device, $ownerFk, $ownerEmail);
            $device = array();
            $device['id'] = $id_device;
            $device['name'] = $nome_device;
            $device['uuid'] = $uuid_device;
            $device['ownerID'] = $ownerFk;
            $device['email'] = $ownerEmail;
            return $device;
        }

        public function saveDeviceToFavorites($userID, $deviceID){
            if($this->userIDExists($userID)){
                if(!$this->isDeviceAlreadyBookmarked($userID, $deviceID) && $this->getDeviceOwnerID($deviceID) != $userID){
                    $sql = "INSERT INTO favorites (user_fk, device_fk) VALUES (?,?);";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bind_param("ii", $userID, $deviceID);
                    $res = $stmt->execute();
                    if($res)
                        return DEVICE_ADDED_TO_FAVORITES;
                    else
                        return DEVICE_FAILURE;
                }else{
                    return DEVICE_ALREADY_REGISTERED;
                }
            }
            else
                return USER_NOT_FOUND;
        }

        private function getDeviceOwnerID($deviceID){
            $sql = "SELECT ownerFk FROM devices WHERE id_device=?;";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $deviceID);
            $stmt->execute();
            if($stmt->num_rows > 0){
                $stmt->bind_result($ownerID);
                $stmt->fetch();
                return $ownerID;
            }else
                return -1;
        }

        private function isDeviceAlreadyBookmarked($userID, $deviceID){
            $sql = "SELECT * FROM favorites WHERE user_fk=? AND device_fk=?;";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $userID, $deviceID);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }

        public function getAllDevicesRegistered($userID){
            $sql = "SELECT * FROM devices WHERE ownerFk=?;";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $userID);
            $stmt->execute();
            $stmt->bind_result($id_device, $nome_device, $uuid_device, $ownerFk, $ownerEmail);
            $devices = array();
            while($stmt->fetch()){
                $device = array();
                $device['id'] = $id_device;
                $device['name'] = $nome_device;
                $device['uuid'] = $uuid_device;
                $device['ownerID'] = $ownerFk;
                $device['email'] = $ownerEmail;
                array_push($devices, $device);
            }
            return $devices;
        }

        public function getAllDevicesBookmarked($userID){
            $sql = "SELECT * FROM devices WHERE id_device = (SELECT device_fk FROM favorites WHERE user_fk=?);";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $userID);
            $stmt->execute();
            $stmt->bind_result($id_device, $nome_device, $uuid_device, $ownerFk, $ownerEmail);
            $devices = array();
            while($stmt->fetch()){
                $device = array();
                $device['id'] = $id_device;
                $device['name'] = $nome_device;
                $device['uuid'] = $uuid_device;
                $device['ownerID'] = $ownerFk;
                $device['email'] = $ownerEmail;
                array_push($devices, $device);
            }
            return $devices;
        }

        public function getAllSavedDevices($userID){
            $registered = $this->getAllDevicesRegistered($userID);
            $bookmarked = $this->getAllDevicesBookmarked($userID);
            $saved = array_merge($registered, $bookmarked);
            return $saved;
        }

        public function deletePositionsByDevice($userID, $deviceID){
            $sql = "DELETE FROM locations WHERE device_fk=? AND user_fk=?;";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $deviceID, $userID);
            if($stmt->execute())
                return true;
            else
                return false;
        }

        public function deletePositionByID($id_location){
            $sql = "DELETE FROM locations WHERE id_location=?;";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id_location);
            if($stmt->execute())
                return true;
            else
                return false;
        }

        public function deleteDeviceByID($deviceID){
            $sql = "DELETE FROM devices WHERE id_device=?;";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $deviceID);
            if($stmt->execute())
                return true;
            else
                return false;
        }

        public function deleteBookmarkedDevice($userID, $deviceID){
            $sql = "DELETE FROM favorites WHERE user_fk=? AND device_fk=?;";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $userID, $deviceID);
            if($stmt->execute())
                return true;
            else
                return false;
        }

        public function addPosition($userID, $deviceID, $traduzione, $lat, $lon, $dayTime, $dateTime){
                $sql = "INSERT INTO locations (device_fk, user_fk, via, latitudine, longitudine, dayTime, dateTime) VALUES (?,?,?,?,?,?,?);";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("iisddss", $deviceID, $userID, $traduzione, $lat, $lon, $dayTime, $dateTime);
                $res = $stmt->execute();
                if($res){
                    $sql = "SELECT * FROM locations WHERE device_fk=? AND user_fk=? AND dayTime=?";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bind_param("iis", $deviceID, $userID, $dayTime);
                    $stmt->execute();
                    $data = $stmt->get_result()->fetch_row();
                    $position = array();
                    $position['positionID'] = $data[0];
                    $position['deviceID'] = $data[1];
                    $position['userID'] = $data[2];
                    $position['address'] = $data[3];
                    $position['latitude'] = $data[4];
                    $position['longitude'] = $data[5];
                    $position['dayTime'] = $data[6];
                    $position['dateTime'] = $data[7];
                    return $position;
                }
                else
                    return null;
        }

        public function uploadImage($userID, $img_reference){
            $dest_dir = 'C:/xampp/htdocs/profilepics/';
            $imgPath = null;
            $target_file = $dest_dir . uniqid() . '.'.$img_reference;
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $sql = "UPDATE users SET profile_pic = ? WHERE id = ?;";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("si", $target_file, $userID);
                if($stmt->execute()){
                    /*
                    $url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
                    $url .= $_SERVER['SERVER_NAME'];
                    $url .= $_SERVER['REQUEST_URI'];
                    $imgPath =  dirname($url).'/profilepics/';*/
                    $imgPath = 'http://camoli.ns0.it/profilepics/';
                }
            }
            return $imgPath;
        }
    }

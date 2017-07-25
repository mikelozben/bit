<?php
namespace Models;

use components\MysqlConnector;

/**
 * User model.
 */
class User extends BaseUser 
{
    /**
     * User id
     * @var integer|null
     */
    public $id = null;
    
    /**
     * Username
     * @var string
     */
    public $username;
    
    /**
     * Password
     * @var string
     */
    public $password;
    
    /**
     * User current balance
     * @var integer
     */
    public $balance;

    /**
     * Creates User instance by given associative array.
     * 
     * @param array $row
     * @return \Models\User|null
     */
    private static function createFromRow(array $row)
    {
        $arrRowFields = ['id', 'username', 'password', 'balance'];
        
        $objUser = new User();
        
        foreach ($arrRowFields as $rowFieldName) {
            if (!array_key_exists($rowFieldName, $row)) {
                return null;
            }
            $objUser->{$rowFieldName} = $row[$rowFieldName];
        }
        
        return $objUser;
    }
    
    /**
     * Provides User instance by given user id.
     * 
     * @param integer $userId
     * @return \Models\User|null
     * @throws \Exception
     */
    public static function loadById($userId)
    {
        $connection = MysqlConnector::get();
        $intUserId = intval($userId);
        
        if ( !is_int($intUserId) || (0 >= $intUserId) ) {
            throw new \Exception('Failed to find user: invalid parameter');
        }
        $sql = "SELECT * FROM `" . static::$tableName . "` WHERE (`id`={$intUserId})";
        if ($result = $connection->db->query($sql)) {
            if ($row = $result->fetch_assoc()) {
                return User::createFromRow($row);
            }
        } else {
            throw new \Exception($connection->db->error);
        }
        return null;
    }
    
    /**
     * Provides User instance by given username.
     * 
     * @param string $username
     * @return \Models\User|null
     * @throws \Exception
     */
    public static function loadByUsername($username)
    {
        if ( is_string($username) ) {
            $connection = MysqlConnector::get();

            $username = mysqli_real_escape_string($connection->db, $username);
            $sql = "SELECT * FROM `" . static::$tableName . "` WHERE (`username`='{$username}') LIMIT 1";
            if ($result = $connection->db->query($sql)) {
                if ($row = $result->fetch_assoc()) {
                    return User::createFromRow($row);
                }
            } else {
                throw new \Exception($connection->db->error);
            }
        }
        
        return null;
    }
    
    /**
     * Generates password hash.
     * 
     * @param string $strPassword
     * @return string
     */
    public static function generatePasswordHash($strPassword)
    {
        return password_hash($strPassword, PASSWORD_DEFAULT);
    }
    
    /**
     * Verifies password.
     * 
     * @param string $strPassword
     * @return boolean
     */
    public function verifyPassword($strPassword)
    {
        return password_verify($strPassword, $this->password);
    }
    
    /**
     * Makes transaction, increasing or decreasing current user balance.
     * 
     * @param float $amount
     * @return float
     * @throws \Exception
     */
    public function makeTransaction($amount)
    {
        if ( !is_float($amount) ) {
            throw new \Exception('Failed to process transaction: invalid amount');
        } elseif (null === $this->id) {
            throw new \Exception('Failed to process transaction: user invalid');
        }
        
        //float form amount to bigint database amount 
        $amount = ceil($amount*100);
        if (0 == $amount) {
            throw new \Exception('Failed to process transaction: zero amount');
        }
        
        $sql = "CALL user_make_amount({$this->id}, {$amount}, @status, @balance, @error)";
        $connection = MysqlConnector::get();
        if ($result = $connection->db->query($sql)) {
            /**
             * This looping on next_result is necessary,
             * cause stored procedure may send more than one response,
             * and this cause error width message 
             * "Commands out of sync; you can't run this command now"
             */
            while ($connection->db->next_result()) {
                $connection->db->store_result();
            }

            $sqlQData = "SELECT @status AS 'status', @balance AS 'balance', @error AS 'error'";
            if ($result = $connection->db->query($sqlQData)) {
                if ($row = $result->fetch_assoc()) {
                    if (isset($row['balance']) && isset($row['status']) && isset($row['error'])) {
                        if ( '1' === $row['status'] ) {
                            return intval($row['balance']);
                        } else {
                            throw new \Exception('Failed to process transaction : ' . $row['error']);
                        }
                    }
                    
                }
            } else {
                throw new \Exception('Failed to process transaction : ' . $connection->db->error);
            }
        } else {
            throw new \Exception('Failed to process transaction : ' . $connection->db->error);
        }
        throw new \Exception('Failed to process transaction');
    }
}

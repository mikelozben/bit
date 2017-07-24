<?php
namespace Models;

/**
 * Base User model.
 */
class BaseUser
{
    /**
     * Model table name
     * @var string
     */
    static $tableName = 'user';
    
    /**
     * User id
     * @var integer
     */
    public $id;
    
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
     * Static method for loading user from session.
     * 
     * @return \Models\BaseUser|null|null
     */
    public static function loadCurrentUser()
    {
        $objCurrentUser = null;
        if (isset($_SESSION['user.id'])) {
            $objCurrentUser = static::loadById($_SESSION['user.id']);
        }
        
        return $objCurrentUser;
    }
    
    /**
     * Static method for saving user to session. If parameter is null
     * clears session user data.
     * 
     * @param \Models\BaseUser|null $objCurrentUser
     * @throws \Exception
     */
    public static function saveCurrentUser($objCurrentUser = null)
    {
        if (null === $objCurrentUser) {
            unset($_SESSION['user.id']);
        } elseif ($objCurrentUser instanceof BaseUser) {
            if (null !== $objCurrentUser->id) {
                $_SESSION['user.id'] = $objCurrentUser->id;
            } else {
                throw new \Exception('Failed to login: given user invalid');
            }
        }
    }
    
    /**
     * Loads user model by given id.
     * 
     * @param integer $userId
     * @return \Models\BaseUser|null
     */
    public static function loadById($userId)
    {
        return null;
    }
}

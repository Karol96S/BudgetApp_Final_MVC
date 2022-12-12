<?php

namespace App\Models;

use PDO;
use \App\Token;

class RememberedLogin extends \Core\Model
{
    /**
     * Find a remembered login model by the token
     */
    public static function findByToken($token)
    {
        $token = new Token($token);
        $token_hash = $token->getHash();

        $sql = 'SELECT * FROM remembered_logins
                WHERE token_hash = :token_hash';
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $token_hash, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Get the user model associated with this remembered login
     */
    public function getUser()
    {
        return User::findByID($this->user_id);
    }

    /**
     * See if remember token has expired or not
     */
    public function hasExpired()
    {
        return strtotime($this->expires_at) < time();
    }

    /**
     * Delete this model
     */
    public function delete()
    {
        $sql = 'DELETE FROM remembered_logins
                WHERE token_hash = :token_hash';
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $this->token_hash, PDO::PARAM_STR);

        $stmt->execute();
    }
}
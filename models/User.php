<?php

require 'Model.php';

/**
 * Class for work with user
 */
class User extends Model
{
    protected $table = 'users';

    /**
     * @var array $fields user fields
     */
    protected $fields = [
        'id',
        'email',
        'password',
        'name',
        'birthdate',
        'avatar',
    ];

    /**
     * Create new user
     *
     * @return bool success
     */
    public function create()
    {
        $pdo = $this->connection;

        $userQuery = $pdo->prepare("
            INSERT INTO users (email, password, name, birthdate, avatar)
            VALUES (:email, :password, :name, :birthdate, :avatar)
        ");

        // $id = $this->escape($this->id);
        $email = $this->escape($this->email);
        $password = $this->escape($this->password);
        $name = $this->escape($this->name);
        $birthdate = $this->escape($this->birthdate);
        $avatar = $this->escape($this->avatar);

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));

        $userQuery->bindParam(':email', $email);
        $userQuery->bindParam(':password', $hashedPassword);
        $userQuery->bindParam(':name', $name);
        $userQuery->bindParam(':birthdate', $birthdate);
        $userQuery->bindParam(':avatar', $avatar);

        return $userQuery->execute();
    }
}

<?php

require_once 'Model.php';

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
        'last_login_at',
        'birthdate',
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
            INSERT INTO " . $this->table . " (email, password, name, last_login_at, birthdate)
            VALUES (:email, :password, :name, :last_login_at, :birthdate)
        ");

        $email = $this->email;
        $name = $this->name;
        $lastLoginAt = date("Y-m-d H:i:s");
        $birthdate = $this->birthdate ?? date("Y-m-d H:i:s");
        $password = $this->password;

        $userQuery->bindParam(':email', $email);
        $userQuery->bindParam(':password', $password);
        $userQuery->bindParam(':name', $name);
        $userQuery->bindParam(':last_login_at', $lastLoginAt);
        $userQuery->bindParam(':birthdate', $birthdate);

        return $userQuery->execute();
    }

    public static function isEmailTaken($email)
    {
        $pdo = DB::getInstance();
        $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM users WHERE email=?");
        $stmt->execute(array($email));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $email_count = $row["count"];
        }
        return $email_count > 0;
    }
}

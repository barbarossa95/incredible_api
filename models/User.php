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
        'lat',
        'long',
        'country_code',
        'age_limit_max',
        'age_limit_min'
    ];

    /**
     * Create new user
     *
     * @return bool success
     */
    public function update()
    {
        $pdo = $this->connection;

        $userQuery = $pdo->prepare("
            UPDATE " . $this->table . " SET email=:email, password=:password, name=:name, last_login_at=:last_login_at, birthdate=:birthdate
            WHERE id=:id
        ");

        $email = $this->email;
        $name = $this->name;
        $lastLoginAt = $this->last_login_at;
        $birthdate = $this->birthdate;
        $password = $this->password;
        $id = $this->id;

        $userQuery->bindParam(':email', $email);
        $userQuery->bindParam(':password', $password);
        $userQuery->bindParam(':name', $name);
        $userQuery->bindParam(':last_login_at', $lastLoginAt);
        $userQuery->bindParam(':birthdate', $birthdate);
        $userQuery->bindParam(':id', $id);

        return $userQuery->execute();
    }


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
        $birthdate = $this->birthdate;
        $password = $this->password;

        $userQuery->bindParam(':email', $email);
        $userQuery->bindParam(':password', $password);
        $userQuery->bindParam(':name', $name);
        $userQuery->bindParam(':last_login_at', $lastLoginAt);
        $userQuery->bindParam(':birthdate', $birthdate);

        $success = $userQuery->execute();

        if ($success) {
            $this->id = self::findByEmail($email, 'id')->id;
        }

        return $success;
    }

    public static function isEmailTaken($email)
    {
        $pdo = DB::getInstance();
        $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM users WHERE email=?");
        $stmt->execute([$email]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $email_count = $row["count"];
        }
        return $email_count > 0;
    }

    public static function findByEmail($email, $fields = '*')
    {
        $pdo = DB::getInstance();

        $stmt = $pdo->prepare("SELECT " . $fields . " FROM users WHERE email=?");
        $stmt->execute([$email]);
        if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return null;
        }

        return self::fill($row);
    }

    public static function findById($id, $fields = '*')
    {
        $pdo = DB::getInstance();

        $stmt = $pdo->prepare("SELECT " . $fields . " FROM users WHERE id=?");
        $stmt->execute([$id]);
        if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return null;
        }

        return self::fill($row);
    }
}

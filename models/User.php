<?php

require_once 'Model.php';
require_once 'Settings.php';
require_once 'UserInterest.php';

/**
 * Class for work with user
 */
class User extends Model
{
    const SEARCH_FILTER_WORLD = 'world';
    const SEARCH_FILTER_COUNTRY = 'country';
    const SEARCH_FILTER_NEAR = 'near';

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
        $pdo = self::connection();

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
        $pdo = self::connection();

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

    /**
     * Finc users with given email count
     *
     * @param string $email
     * @return bool
     */
    public static function isEmailTaken($email)
    {
        $pdo = self::connection();
        $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM users WHERE email=?");
        $stmt->execute([$email]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $email_count = $row["count"];
        }
        return $email_count > 0;
    }

    /**
     * Find user by email value
     *
     * @param integer $email
     * @return self|null
     */
    public static function findByEmail($email)
    {
        return self::findBy('email', $email);
    }

    /**
     * Find user by id value
     *
     * @param integer $id
     * @return self|null
     */
    public static function findById($id)
    {
        return self::findBy('id', $id);
    }

    /**
     * Get User search settings
     *
     * @return Settings[]
     */
    public function getSettings()
    {
        $settings = [];

        $pdo = self::connection();
        $stmt = $pdo->prepare("SELECT * FROM settings_interests WHERE user_id=?");
        $stmt->execute([$this->id]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[] = Settings::fill($row);
        }

        return $settings;
    }

    /**
     * Get User interests
     *
     * @return UserInterest[]
     */
    public function getInterests()
    {
        $settings = [];

        $pdo = self::connection();
        $stmt = $pdo->prepare("SELECT * FROM user_interests WHERE user_id=?");
        $stmt->execute([$this->id]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[] = UserInterest::fill($row);
        }

        return $settings;
    }
}

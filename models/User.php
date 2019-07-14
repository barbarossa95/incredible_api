<?php

namespace Models;

use PDO;

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
        'age_limit_min',
        'age_search_max',
        'age_search_min',
        'location'
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
            $this->initSettings();
        }

        return $success;
    }

    /**
     * Init user settings
     *
     * @return void
     */
    public function initSettings()
    {
        $interests = array_map(
            function ($item) {
                return $item->slug;
            },
            Interest::getAll()
        );

        foreach ($interests as $interest) {
            $userInterest = new UserInterest;
            $userInterest->user_id = $this->id;
            $userInterest->interest_slug = $interest;
            $userInterest->value = true;
            $userInterest->create();

            $searchInterest = new Settings;
            $searchInterest->user_id = $this->id;
            $searchInterest->interest_slug = $interest;
            $searchInterest->create();
        }
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

    /**
     * Build from subqueries
     *
     * @return array
     */
    private function feedSubQueries()
    {
        $queryParams = [
            'lat' => $this->lat,
            'long' => $this->long,
        ];

        $subQueries = [
            User::SEARCH_FILTER_WORLD => "
                SELECT id, email, name, last_login_at, country_code, (YEAR(CURRENT_TIMESTAMP) - YEAR(birthdate)) as age,
                (6371.008 *(acos(cos(radians(:lat)) * cos(radians(`lat`)) * cos(radians(`long`) - radians(:long)) + sin(radians(:lat)) * sin(radians(`lat`))))) as distance,
                (3) as priority
                FROM users
            ",
            User::SEARCH_FILTER_COUNTRY => "
                SELECT id, email, name, last_login_at, country_code, (YEAR(CURRENT_TIMESTAMP) - YEAR(birthdate)) as age,
                (6371.008 *(acos(cos(radians(:lat)) * cos(radians(`lat`)) * cos(radians(`long`) - radians(:long)) + sin(radians(:lat)) * sin(radians(`lat`))))) as distance,
                (2) as priority
                FROM users
                where country_code=:country_code
            ",
            User::SEARCH_FILTER_NEAR => "
                SELECT id, email, name, last_login_at, country_code, (YEAR(CURRENT_TIMESTAMP) - YEAR(birthdate)) as age,
                (6371.008 *(acos(cos(radians(:lat)) * cos(radians(`lat`)) * cos(radians(`long`) - radians(:long)) + sin(radians(:lat)) * sin(radians(`lat`))))) as distance,
                (1) as priority
                FROM users
                having distance < 50
            ",
        ];

        $locationFilter = $this->location;

        $queryFrom = "(" . $subQueries[User::SEARCH_FILTER_WORLD] . ")";
        switch ($locationFilter) {
            case User::SEARCH_FILTER_COUNTRY:
                $queryParams['country_code'] = $this->country_code;
                $queryFrom = "(" . $subQueries[User::SEARCH_FILTER_COUNTRY]
                    . ") union distinct " . $queryFrom;
                break;
            case User::SEARCH_FILTER_NEAR:
                $queryParams['country_code'] = $this->country_code;
                $queryFrom = "(" . $subQueries[User::SEARCH_FILTER_NEAR]
                    . ") union distinct ("
                    . $subQueries[User::SEARCH_FILTER_COUNTRY]
                    . ") union distinct $queryFrom";
                break;
        }

        return [$queryFrom, $queryParams];
    }

    /**
     * Build where clause filters
     *
     * @return array
     */
    private function feedWhereClause()
    {
        $queryParams = [];

        $where = "WHERE id<>:ownId ";
        $queryParams['ownId'] = $this->id;

        if (!empty($limitMax = $this->age_search_max)) {
            $where .= "AND T.age < :age_search_max";
            $queryParams['age_search_max'] = $limitMax;
        }

        if (!empty($limitMin = $this->age_search_min)) {
            $where .= "AND T.age > :age_search_min";
            $queryParams['age_search_min'] = $limitMin;
        }

        $settingsInterests = $this->getSettings();

        foreach ($settingsInterests as $interest) {
            if ($interest->value === null) continue;

            $queryParams[$interest->interest_slug] = $interest->value;
            $where .= "
                AND EXISTS (
                    SELECT i.user_id
                    FROM user_interests AS i
                    WHERE i.interest_slug = '" . $interest->interest_slug . "'
                    AND i.value = :" . $interest->interest_slug . "
                    AND i.user_id = T.id
                )
            ";
        }

        $where .= "
            AND EXISTS (
                SELECT u.age_limit_min, u.age_limit_max, (YEAR(CURRENT_TIMESTAMP) - YEAR(:birthdate)) as age
                FROM users AS u
                WHERE u.id = T.id
                HAVING (ISNULL(u.age_limit_min) OR age > u.age_limit_min)
                AND (ISNULL(u.age_limit_max) OR age < u.age_limit_max)
            )
        ";
        $queryParams['birthdate'] = $this->birthdate;

        return [$where, $queryParams];
    }

    /**
     * Get list of users based on settings
     *
     * @return array|null
     */
    public function getUsersFeed($offset, $limit = 10)
    {
        $pdo = self::connection();

        list($queryFrom, $queryParamsFrom) = $this->feedSubQueries();

        list($whereClause, $queryParamsWhere) = $this->feedWhereClause();

        $queryParams = array_merge($queryParamsFrom, $queryParamsWhere);

        $query = "
        SELECT DISTINCT id, email, name, last_login_at, age, distance, country_code
        FROM ($queryFrom) as T
        $whereClause
        ORDER BY
        T.priority ASC,
        last_login_at DESC
        LIMIT $offset,$limit
        ";

        $stmt = $pdo->prepare($query);
        $stmt->execute($queryParams);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        response(200, $rows);
        return;
    }
}

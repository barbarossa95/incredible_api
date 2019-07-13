<?php

namespace Models;

/**
 * Class for work with user settings
 */
class Settings extends Model
{
    protected $table = 'settings_interests';

    /**
     * @var array $fields user fields
     */
    protected $fields = [
        'id',
        'user_id',
        'interest_slug',
        'value',
    ];

    /**
     * Update user settings
     *
     * @return bool success
     */
    public function update()
    {
        $pdo = self::connection();

        $settingsQuery = $pdo->prepare("
            UPDATE " . $this->table . " SET value=:value
            WHERE user_id=:user_id and interest_slug=:interest_slug
        ");

        $userId = $this->user_id;
        $interestSlug = $this->interest_slug;
        $value = $this->value;

        $settingsQuery->bindParam(':user_id', $userId);
        $settingsQuery->bindParam(':interest_slug', $interestSlug);
        $settingsQuery->bindParam(':value', $value);

        return $settingsQuery->execute();
    }


    /**
     * Create new user settings
     *
     * @return bool success
     */
    public function create()
    {
        $pdo = self::connection();

        $settingsQuery = $pdo->prepare("
            INSERT INTO " . $this->table . " (email, password, name, last_login_at, birthdate)
            VALUES (:email, :password, :name, :last_login_at, :birthdate)
        ");

        $userId = $this->user_id;
        $interestSlug = $this->interest_slug;
        $value = $this->value;

        $settingsQuery->bindParam(':user_id', $userId);
        $settingsQuery->bindParam(':interest_slug', $interestSlug);
        $settingsQuery->bindParam(':value', $value);

        return $settingsQuery->execute();
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this->update();
    }
}

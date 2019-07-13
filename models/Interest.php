<?php

namespace Models;

use PDO;

/**
 * Class for work with user settings
 */
class Interest extends Model
{
    protected $table = 'interests';

    /**
     * @var array $fields user fields
     */
    protected $fields = [
        'slug',
        'name',
        'description',
    ];

    public static function getAll()
    {
        $interests = [];

        $pdo = self::connection();
        $stmt = $pdo->prepare("SELECT * FROM interests");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $interests[] = self::fill($row);
        }
        return $interests;
    }
}

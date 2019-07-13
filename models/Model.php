<?php

require $_SERVER['DOCUMENT_ROOT'] . '/database/connection.php';

class Model
{
    /**
     * @var string table name
     */
    protected $table = '';

    /**
     * @var array table fields
     */
    protected $fields = [];

    /**
     * @var array table data
     */
    protected $data = [];

    /**
     * Default class constructor
     */
    public function __construct()
    { }

    /**
     * Helper function
     * @param string $input escaped string
     * @return string
     */
    public function escape($input)
    {
        $pdo =  $this->connection;

        return $pdo->quote($input);
    }

    /**
     * Setter for table fields
     *
     * @param string $name field name
     * @param string $value field new value
     */
    public function __set($name, $value)
    {
        if (!in_array($name, $this->fields)) return;

        $this->data[$name] = $value;
    }

    /**
     * Getter for table fields
     *
     * @param string $name field name
     * @return mixed field value
     */
    public function __get($name)
    {
        return in_array($name, $this->fields) ?
            $this->data[$name] : null;
    }

    /**
     * Static call for get PDO connection
     *
     * @return PDO
     */
    public static function connection()
    {
        return DB::getInstance();
    }

    /**
     * Find by function
     *
     * @param string $field searchable field
     * @param string $value needle value
     * @param self|null
     */
    public static function findBy($field, $value)
    {
        $class = get_called_class();
        $pdo = self::connection();

        $stmt = $pdo->prepare("SELECT * FROM users WHERE " . $field . "=?");
        $stmt->execute([$value]);
        if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return null;
        }

        return $class::fill($row);
    }

    /**
     * Static function for filling model by data
     *
     * @param array $data
     * @return self
     */
    public static function fill($data)
    {
        $class = get_called_class();
        $instance = new $class;

        foreach ($data as $key => $value) {
            $instance->$key = $value;
        }

        return $instance;
    }
}

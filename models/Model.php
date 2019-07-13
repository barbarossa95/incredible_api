<?php

require $_SERVER['DOCUMENT_ROOT'] . '/database/connection.php';

class Model
{
  /**
   * PDOP Connection
   */
  protected $connection;

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
  {
    $this->connection = DB::getInstance();
  }

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
   * @return string field value
   */
  public function __get($name)
  {
    return in_array($name, $this->fields) ?
      $this->data[$name] : null;
  }
}

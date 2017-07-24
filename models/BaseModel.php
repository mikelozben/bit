<?php
namespace Models;

use components\MysqlConnector;

/**
 * Base model.
 */
class BaseModel
{
    protected $connection;
    static $tableName = 'table';
    
    /**
     * Constructor.
     * @param array $config
     */
    public function __construct(array $config) {
        $this->connection = MysqlConnector::get($config);
    }
}

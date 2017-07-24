<?php
namespace components;

/**
 * Connector component for mysql.
 */
class MysqlConnector
{
    /**
     * Singletone instance
     * @var MysqlConnector|null
     */
    public static $instance = null;
    
    /**
     * Database connection
     * @var resource|null
     */
    public $db = null;

    /**
     * Constructor.
     * 
     * @param array $config
     * @throws \Exception
     */
    public function __construct(array $config) {
        $this->db = mysqli_connect(
            $config['db']['host'], 
            $config['db']['username'], 
            $config['db']['password'], 
            $config['db']['database'],
            $config['db']['port']
        );

        if (mysqli_connect_errno()) {
            throw new \Exception('Database connection failed');
        }

        MysqlConnector::$instance = $this;
    }

    /**
     * Method for getting singletone instance.
     * 
     * @param array $config
     * @return MysqlConnector
     */
    public static function get(array $config = [])
    {
        if ( null === MysqlConnector::$instance ) {
            MysqlConnector::$instance = new MysqlConnector($config);
        }

        return MysqlConnector::$instance;
    }
}

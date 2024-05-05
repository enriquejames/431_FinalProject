<?php

class DataSource
{

    private $host =  null;
    private $user = null;
    private $pass = null;
    private $db = null;
    private $creds = null;
    public $writePath = null;

    public function __construct($credentials = null)
    {
        $this->creds =  ($credentials ?? __DIR__ . "\mysql_credentials.json");
    }

    // Private function that establishes the MySQL credential values. 
    private function mysqlSetup()
    {
        $settings = array();

        try {

            if (is_array($this->creds)) {
                $settings = $this->creds;
            } else {
                $path = $this->creds;

                $json = file_get_contents($path);

                $settings = json_decode($json, true);
            }

            $this->host = $settings['host'] ?? null;

            $this->user = $settings['username'] ?? null;

            $this->pass = $settings['password'] ?? null;

            $this->db = $settings['schema'] ?? null;
        } catch (exception $e) {

            exit($e->getMessage());
        }
    }

    // Use if your data source is JSON files
    public function JSON($array = true)
    {
        $path = $this->creds;
        $json = file_get_contents($path);
        $this->writePath = $path;
        return json_decode($json, $array); // Return PHP Object or Array
    }

    // Use if your data source is MySQL and you want to use PDO
    public function PDO()
    {
        $this->mysqlSetup();

        $charset = 'utf8mb4';
        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $pdo = new PDO($dsn, $this->user, $this->pass, $options);

        return $pdo;
    }

    // Use if your data source is MySQL and you want to use MySQLI
    public function mysqli()
    {
        $this->mysqlSetup();

        $mysqli = new mysqli($this->host, $this->user, $this->pass, $this->db);

        return $mysqli;
    }
}


//data_source.php only take responsible to connect Database
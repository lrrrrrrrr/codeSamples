<?php

namespace classes;

use PDO;
use PDOException;
use RuntimeException;

class Connection
{

    const LOCAL_HOST = 'localhost';
    const DEFAULT_USER = 'root';
    const DEFAULT_PASSWORD = '';

    /** @var PDO|null $pdo */
    private $pdo;
    /** @var  string $host */
    private $host;
    /** @var  string $dbUser */
    private $dbUser;
    /** @var  string $dbPassword */
    private $dbPassword;
    /** @var  $dbName $dbName */
    private $dbName;

    /**
     * Connection constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $config = array_merge([
            'host' => self::LOCAL_HOST,
            'dbUser' => self::DEFAULT_USER,
            'dbPassword' => self::DEFAULT_PASSWORD,
            'dbName' => null,
        ], $config);

        $this->host = $config['host'];
        $this->dbUser = $config['dbUser'];
        $this->dbPassword = $config['dbPassword'];
        $this->dbName = $config['dbName'];
    }

    /**
     * @return PDO|null
     * @throws \Exception
     */
    private function createPdoInstance(): ?\PDO
    {
        if ($this->pdo === null) {
            try {
                $this->pdo = new PDO('mysql:host=' .
                    $this->host. ';dbname=' . $this->dbName, $this->dbUser, $this->dbPassword,
                    array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
            } catch (PDOException $e) {
                throw $e;
            }
        }

        return $this->pdo;
    }

    /**
     * Opens PDO connection
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function open(): void
    {
        if (null !== $this->pdo) {
            return;
        }
        try {
            $this->pdo = $this->createPdoInstance();
        } catch (\PDOException $e) {
            throw new RuntimeException($e->getMessage(),(int)$e->getCode());
        }
    }

    /**
     * Close PDO connection
     */
    public function close(): void
    {
        $this->pdo = null;
    }

    public function createCommand($sql, array $properties = []): Command
    {
        return new Command($this->pdo, $sql, $properties);
    }

}
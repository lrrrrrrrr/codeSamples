<?php

namespace classes;

/**
 * Class Application
 * @package classes
 */
class Application
{
    const FIRST_VISITOR = 1;
    const VISITORS_TABLE = 'visitors';

    private $db;
    private $client;
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;

        if (!isset($this->config['imageFile'])) {
            throw new \RuntimeException('No image filename in configuration!');
        }
    }

    /**
     * Runs the application.
     * This is the main entrance of an application.
     * @throws \Exception
     */
    public function run(): void
    {
        $date = (new \DateTime())->format('Y-m-d H:i:s');

        if ($currentVisitor = $this->isVisitorExists([
            'ip_address' => $this->getClient()->getIpAddress(),
            'page_url' => $this->getClient()->getRequestUrl(),
            'user_agent' => $this->getClient()->getUserAgent(),
        ])) {
            $this->updateRow([
                ':id' => $currentVisitor['id'],
                ':view_date' => $date,
                ':views_count' => isset($currentVisitor['views_count']) ? ++$currentVisitor['views_count'] : self::FIRST_VISITOR,
            ]);
        } else {
            $this->addToDb([
                ':ip_address' => $this->getClient()->getIpAddress(),
                ':user_agent' => $this->getClient()->getUserAgent(),
                ':view_date' => $date,
                ':page_url' => $this->getClient()->getRequestUrl(),
                ':views_count' => self::FIRST_VISITOR,
            ]);
        }
        $this->renderImage();
    }

    /**
     * @param array $params e.g. [
     * 'ip_address' => '127.0.0.1',
     * 'page_url' => 'http://localhost/index1.html',
     * 'user_agent' => 'Mozilla',
     * ]
     * @return array|null
     * @throws \Exception
     */
    public function isVisitorExists(array $params): ?array
    {
        $result = $this->getDb()->createCommand(
            'SELECT * FROM `visitors` WHERE 
                    `ip_address` = :ip_address 
                AND `page_url` = :page_url 
                AND `user_agent` = :user_agent', $params)->get();
        if (!$result) {
            return null;
        }
        return $result;
    }

    private function updateRow($params): bool
    {
        return $this->getDb()->createCommand(null, $params)->update(self::VISITORS_TABLE);
    }

    /**
     * @param array $params e.g. [
     * ':ip_address' => '127.0.0.1',
     * ':user_agent' => 'Mozilla',
     * ':view_date' => '2017-09-06 11:15:00',
     * ':page_url' => 'http://localhost/index1.html',
     * ':views_count' => 1
     * ]
     * @return int
     * @throws \Exception
     */
    private function addToDb(array $params): int
    {
        return $this->getDb()->createCommand(null, $params)->add(self::VISITORS_TABLE);
    }

    public function renderImage(): void
    {
        $fp = fopen($this->config['imageFile'], 'rb');

        header('Content-type: image/jpeg');
        header('Content-Length: ' . filesize($this->config['imageFile']));

        fpassthru($fp);
    }

    /**
     * @return Connection
     * @throws \Exception
     * @throws \RuntimeException
     */
    public function getDb(): Connection
    {
        if (!isset($this->config['db'])) {
            throw new \RuntimeException('Missing db config');
        }

        if (!$this->db) {
            try {
                $this->db = new Connection($this->config['db']);
                $this->db->open();
            } catch (\RuntimeException $e) {
                echo $e->getMessage();
            }
        }

        return $this->db;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        if (!$this->client) {
            $this->client = new Client();
        }

        return $this->client;
    }

}
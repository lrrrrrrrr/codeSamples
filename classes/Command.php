<?php

namespace classes;

use PDO;

class Command
{
    private $db;
    private $sql;
    private $properties;

    public function __construct(PDO $db, $sql, array $properties = [])
    {
        $this->db = $db;
        $this->sql = $sql;
        $this->properties = $properties;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $sth = $this->db->prepare($this->sql, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $sth->execute($this->properties);
        return $sth->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        $sth = $this->db->prepare($this->sql, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $sth->execute($this->properties);
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    public function execute(): int
    {
        return $this->db->exec($this->sql);
    }

    public function update($table): bool
    {
        $sth = $this->db->prepare(
            'UPDATE `' . $table . '` SET `view_date` = :view_date, `views_count` = :views_count WHERE `id` = :id;',
            [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);

        return $sth->execute($this->properties);
    }

    /**
     * @param $table
     * @return int
     */
    public function add($table): int
    {
        $sth = $this->db->prepare(
            'INSERT INTO `' . $table . '` (`ip_address`, `user_agent`, `view_date`, `page_url`, `views_count`) 
                      VALUES (:ip_address, :user_agent, :view_date, :page_url, :views_count);', [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);

        return $sth->execute($this->properties);
    }

}
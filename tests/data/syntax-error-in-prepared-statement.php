<?php

namespace SyntaxErrorInPreparedStatementMethodRuleTest;

use staabm\PHPStanDba\Tests\Fixture\Connection;
use staabm\PHPStanDba\Tests\Fixture\PreparedStatement;

class Foo
{
    public function syntaxError(Connection $connection)
    {
        $connection->preparedQuery('SELECT email adaid WHERE gesperrt freigabe1u1 FROM ada', []);
    }

    public function syntaxErrorInConstruct()
    {
        $stmt = new PreparedStatement('SELECT email adaid WHERE gesperrt freigabe1u1 FROM ada', []);
    }

    public function syntaxErrorOnKnownParamType(Connection $connection, int $i, bool $bool)
    {
        $connection->preparedQuery('
            SELECT email adaid
            WHERE gesperrt = ? AND email LIKE ?
            FROM ada
            LIMIT        1
        ', [$i, '%@example.com']);

        $connection->preparedQuery('
            SELECT email adaid
            WHERE gesperrt = ? AND email LIKE ?
            FROM ada
            LIMIT        1
        ', [$bool, '%@example.com']);
    }

    public function noErrorOnMixedParams(Connection $connection, $unknownType)
    {
        $connection->preparedQuery('
            SELECT email, adaid
            FROM ada
            WHERE gesperrt = ? AND email LIKE ?
            LIMIT        1
        ', [1, $unknownType]);
    }

    public function noErrorOnPlaceholderInLimit(Connection $connection, int $limit)
    {
        $connection->preparedQuery('
            SELECT email, adaid
            FROM ada
            WHERE gesperrt = ?
            LIMIT        ?
        ', [1, $limit]);

        $connection->preparedQuery('
            SELECT email, adaid
            FROM ada
            WHERE gesperrt = :gesperrt
            LIMIT        :limit
        ', [':gesperrt' => 1, ':limit' => $limit]);
    }

    public function noErrorOnPlaceholderInOffsetAndLimit(Connection $connection, int $offset, int $limit)
    {
        $connection->preparedQuery('
            SELECT email, adaid
            FROM ada
            WHERE gesperrt = ?
            LIMIT        ?,  ?
        ', [1, $offset, $limit]);

        $connection->preparedQuery('
            SELECT email, adaid
            FROM ada
            WHERE gesperrt = :gesperrt
            LIMIT   :offset,     :limit
        ', [':gesperrt' => 1, ':offset' => $offset, ':limit' => $limit]);
    }

    public function preparedParams(Connection $connection)
    {
        $connection->preparedQuery('SELECT email, adaid FROM ada WHERE gesperrt = ?', [1]);

        $connection->preparedQuery('
            SELECT email, adaid
            FROM ada
            WHERE gesperrt = ? AND email LIKE ?
            LIMIT        1
        ', [1, '%@example%']);
    }

    public function preparedNamedParams(Connection $connection)
    {
        $connection->preparedQuery('SELECT email, adaid FROM ada WHERE gesperrt = :gesperrt', ['gesperrt' => 1]);
    }
}

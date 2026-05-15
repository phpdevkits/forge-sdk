<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Enums;

/**
 * Database engines Forge can install on a server.
 *
 * Mirrors the `DatabaseType` enum in Forge's OpenAPI spec.
 */
enum DatabaseType: string
{
    case Mysql = 'mysql';
    case Mysql8 = 'mysql8';
    case Mysql84 = 'mysql84';
    case Mysql9 = 'mysql9';
    case Mariadb = 'mariadb';
    case Mariadb106 = 'mariadb106';
    case Mariadb1011 = 'mariadb1011';
    case Mariadb114 = 'mariadb114';
    case Postgres = 'postgres';
    case Postgres13 = 'postgres13';
    case Postgres14 = 'postgres14';
    case Postgres15 = 'postgres15';
    case Postgres16 = 'postgres16';
    case Postgres17 = 'postgres17';
    case Postgres18 = 'postgres18';
}

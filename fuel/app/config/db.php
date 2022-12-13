<?php
/**
 * Fuel is a fast, lightweight, community driven PHP 5.4+ framework.
 *
 * @package    Fuel
 * @version    1.9-dev
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2019 Fuel Development Team
 * @link       https://fuelphp.com
 */

/**
 * -----------------------------------------------------------------------------
 *  Global database settings
 * -----------------------------------------------------------------------------
 *
 *  Set database configurations here to override environment specific
 *  configurations
 *
 */

return array(
    'development' => array(
        'type'          => 'mysqli',
        'connection'    => array(
            'socket'    => '/Applications/MAMP/tmp/mysql/mysql.sock',
            'hostname'  => '127.0.0.1',
            'port'      => '3306',
            'database'  => 'fueldb_twitter_dev',
            'username'  => 'root',
            'password'  => getenv()['MYSQL_ROOT_PASSWORD'],
            'persistent'=> false,
            'compress'  => false,
        ),
        'identifier'    => '`',
        'table_prefix'  => '',
        'charset'       => 'utf8',
        'enable_cache'  => true,
        'profiling'     => false,
        'readonly'      => false,
    ),
    'production' => array(
        'type'          => 'mysqli',
        'connection'    => array(
            'socket'    => '/Applications/MAMP/tmp/mysql/mysql.sock',
            'hostname'  => '127.0.0.1',
            'port'      => '3306',
            'database'  => 'fueldb_twitter_dev',
            'username'  => 'root',
            'password'  => getenv()['MYSQL_ROOT_PASSWORD'],
            'persistent'=> false,
            'compress'  => false,
        ),
        'identifier'    => '`',
        'table_prefix'  => '',
        'charset'       => 'utf8',
        'enable_cache'  => true,
        'profiling'     => false,
        'readonly'      => false,
    ),
    'test' => array(
        'type'          => 'mysqli',
        'connection'    => array(
            'socket'    => '/Applications/MAMP/tmp/mysql/mysql.sock',
            'hostname'  => '127.0.0.1',
            'port'      => '3306',
            'database'  => 'fueldb_twitter_test',
            'username'  => 'root',
            'password'  => getenv()['MYSQL_ROOT_PASSWORD'],
            'persistent'=> false,
            'compress'  => false,
        ),
        'identifier'    => '`',
        'table_prefix'  => '',
        'charset'       => 'utf8',
        'enable_cache'  => true,
        'profiling'     => false,
        'readonly'      => false,
    )
);

<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/28/17
 * Time: 5:48 AM
 */

namespace Modules\Helpers;


use Modules\Database;

abstract class Entities
{
    protected $db;
    public static $inTransaction;
    public static $entityTransactionKeys;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    static function verify()
    {
        if (!static::$inTransaction) return null;
        if (!empty(static::$entityTransactionKeys))
            foreach (static::$entityTransactionKeys as $key)
                static::remove_entity( $key );
        Database::getConnection()->rollBack();
    }

    static function commit()
    {
        if (!Database::getConnection()->commit()) return static::verify();
        static::$inTransaction = false;
        static::$entityTransactionKeys = [];
    }

    static function beginTransaction($tag_id, $dependant = null)
    {
        static::$inTransaction = true;
        $key = self::new_entity( $tag_id, $dependant );
        Database::getConnection()->beginTransaction();
        return $key;
    }

    static function new_entity($tag_id, $dependant)
    {
        $db = Database::getConnection();
        do {
            try {
                $stmt = $db->prepare( 'INSERT INTO StatsCoach.entity (entity_pk, entity_fk) VALUE (?,?)' );
                $stmt->execute( [$stmt = Bcrypt::genRandomHex(), $dependant] );
            } catch (\PDOException $e) {
                $stmt = false;
            }
        } while (!$stmt);
        $db->prepare( 'INSERT INTO StatsCoach.entity_tag (entity_id, user_id, tag_id, creation_date) VALUES (?,?,?,?)' )->execute( [$stmt, (!empty($_SESSION['id']) ? $_SESSION['id'] : $stmt), $tag_id, time()] );
        static::$entityTransactionKeys[] = $stmt;
        return $stmt;
    }

    static function remove_entity($id)
    {
        if (!Database::getConnection()->prepare( 'DELETE FROM StatsCoach.entity WHERE entity_pk = ?' )->execute( [$id] ))
            throw new \Exception( "Bad Entity Delete $id" );
    }

    public function fetch_object($sql, ...$execute)
    {
        $stmt = $this->db->prepare( $sql );
        $stmt->setFetchMode( \PDO::FETCH_CLASS, \stdClass::class );
        if (!$stmt->execute( $execute )) return false;
        $stmt = $stmt->fetchAll();  // user obj
        return (is_array( $stmt ) && count( $stmt ) == 1 ? $stmt[0] : $stmt);
    }

    public function fetch_classes($sql, ...$execute)
    {
        $stmt = $this->db->prepare( $sql );
        $stmt->setFetchMode( \PDO::FETCH_CLASS, \stdClass::class );
        if (!$stmt->execute( $execute )) return false;
        return $stmt->fetchAll();  // user obj
    }

    public function fetch_into_current_class($array)
    {
        $object = get_object_vars( $this );
        foreach ($array as $key => $value)
            if (array_key_exists( $key, $object ))
                $this->$key = $value;
    }

    public function fetch_as_array_object($sql, ...$execute)
    {
        $stmt = $this->db->prepare( $sql );
        $stmt->setFetchMode( \PDO::FETCH_CLASS, Skeleton::class );
        $stmt->execute( $execute );
        return $stmt->fetchAll();  // user obj
    }

    public function fetch_to_global($sql, $execute)
    {
        $stmt = $this->db->prepare( $sql );
        $stmt->setFetchMode( \PDO::FETCH_CLASS, Carbon::class );
        $stmt->execute( $execute );
        return $stmt->fetchAll();  // user obj

    }

}
<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/29/17
 * Time: 10:38 PM
 */

namespace Tables;


use Modules\Entities;
use Modules\Error\PublicAlert;
use Modules\Interfaces\iEntity;

class Photos extends Entities implements iEntity
{
    static function get(&$object, $id)
    {
        if (!($object instanceof \stdClass))
            throw new \Exception( 'Invalid Object Passed' );
        $object->photo = [];

        $sql = 'SELECT * FROM StatsCoach.entity_photos WHERE parent_id = ? OR photo_id = ?LIMIT 1';
        $stmt = self::fetch_classes( $sql, $id, $id );

        //sortDump($object);

        foreach ($stmt as $item => $value)
            if (is_object( $value ))
                $object->photo[$value->photo_id] = $value;

        return $object;
    }

    static function all(&$object, $id)
    {
        $sql = 'SELECT * FROM StatsCoach.entity_photos WHERE parent_id = ?';
        $object->photo = static::fetch_classes( $sql, $id );
    }

    static function range(&$object, $id, $argv)
    {
        // TODO: Implement range() method.
    }

    static function add(&$object, $id, $argv)
    {
        $photo_id = static::beginTransaction( Entities::ENTITY_PHOTOS, $id );
        $sql = 'REPLACE INTO StatsCoach.entity_photos (parent_id, photo_id, user_id, photo_path, photo_description) VALUES (:parent_id, :photo_id, :user_id, :photo_path, :photo_description)';
        $stmt = self::database()->prepare( $sql );
        $stmt->bindValue( ':parent_id', $id );
        $stmt->bindValue( ':photo_id', $photo_id );
        $stmt->bindValue( ':user_id', $_SESSION['id'] );
        $stmt->bindValue( ':photo_path', $argv['photo_path'] );
        $stmt->bindValue( ':photo_description', $argv['photo_description'] );
        if (!$stmt->execute())
            throw new PublicAlert( 'Sorry, we could not process your request.', 'danger' );
        return static::commit();
    }

    static function remove(&$object, $id)
    {
        $sql = 'DELETE * FROM StatsCoach.entity_photos WHERE photo_id = ?';
        if (array_key_exists( $id, $object->photos ))
            unset( $object->photos[$id] );    // I may not need the array_key_exists
        return self::database()->prepare( $sql )->execute( [$id] );
    }

}
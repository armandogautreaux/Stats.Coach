<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 8/2/17
 * Time: 12:46 PM
 */

namespace Tables;

use Model\User;
use Modules\Entities;
use Modules\Interfaces\iEntity;
use Modules\Helpers\Pipe;

class Messages extends Entities implements iEntity
{
    static function get(&$object, $id)
    {
        $to_user = $object->user_id ?? false;
        if (!$to_user) throw new \Exception( 'Cannot get messages from a non-user.' );
        $object->messages = self::fetch_classes( 'SELECT * FROM StatsCoach.user_messages INNER JOIN StatsCoach.entity_tag ON entity_id = message_id WHERE 
                    StatsCoach.user_messages.to_user_id = ? AND StatsCoach.entity_tag.user_id = ? OR 
                    StatsCoach.user_messages.to_user_id = ? AND StatsCoach.entity_tag.user_id = ?', $id, $_SESSION['id'], $_SESSION['id'], $id );
        return $object;
    }


    static function all(&$object, $id)   // signed in user
    {
        $stmt = self::database()->prepare( 'SELECT user_id, to_user_id FROM StatsCoach.user_messages INNER JOIN StatsCoach.entity_tag ON entity_id = message_id WHERE 
                    StatsCoach.user_messages.to_user_id = ? OR StatsCoach.entity_tag.user_id = ?' );
        $stmt->execute( [$id, $id] );
        $stmt = $stmt->fetchAll();

        $users = array();
        foreach ($stmt as $message => $userid)
            foreach ($userid as $user => $id)
                if (!array_key_exists( $id, $users ))
                    $users[$id] = $id;

        foreach ($users as $key => $val) {
            new User( $val );
        }
    }

    static function range(&$object, $id, $argv)
    {
        // TODO: Implement range() method.
    }

    static function add(&$object, $id, $argv)   // id is the user to be sent to
    {
        $message_id = self::beginTransaction( Entities::USER_MESSAGES, $_SESSION['id'] );
        $sql = 'INSERT INTO StatsCoach.user_messages (message_id, to_user_id, message) VALUES (:message_id, :user_id, :message)';
        $stmt = self::database()->prepare( $sql );
        $stmt->bindValue( ':message_id', $message_id );
        $stmt->bindValue( ':user_id', $id );
        $stmt->bindValue( ':message', $argv );
        return $stmt->execute() ? self::commit( function () use ($id) {
            // Update there browser
            Pipe::send( 'Messages/', $id );
            Pipe::send( 'Messages/' . $_SESSION['id'] . '/', $id );

            // Update My View
            Pipe::send( 'Messages/', $_SESSION['id'] );
            Pipe::send( 'Messages/' . $id . '/', $_SESSION['id'] );
        } ) : self::verify( 'Failed to send your message.' );
    }

    static function remove(&$object, $id)
    {
        $sql = 'DELETE * FROM StatsCoach.entity_location WHERE entity_id = ?';
        if (self::database()->prepare( $sql )->execute( [$id] )) {
            unset( $object->location );
            return true;
        }
        return false;
    }

}
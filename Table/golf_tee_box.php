<?php
namespace Table;


use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;

class golf_tee_box extends Entities implements iRest
{
    const PRIMARY = [
    'course_id',
    ];

    const COLUMNS = [
    'course_id','tee_box','distance','distance_color','distance_general_slope','distance_general_difficulty','distance_womens_slope','distance_womens_difficulty','distance_out','distance_in','distance_tot',
    ];

    const BINARY = [
    'course_id',
    ];

    /**
     * @param array $return
     * @param string|null $primary
     * @param array $argv
     * @return bool
     */
    public static function Get(array &$return, string $primary = null, array $argv) : bool
    {
        if (isset($argv['limit'])){
            if ($argv['limit'] !== '') {
                $pos = strrpos($argv['limit'], "><");
                if ($pos !== false) { // note: three equal signs
                    substr_replace($argv['limit'],',',$pos, 2);
                }
                $limit = ' LIMIT ' . $argv['limit'];
            } else {
                $limit = '';
            }
        } else {
            $limit = ' LIMIT 100';
        }

        $get = isset($argv['select']) ? $argv['select'] : self::COLUMNS;
        $where = isset($argv['where']) ? $argv['where'] : [];

        $sql = '';
        foreach($get as $key => $column){
            if (!empty($sql)) {
                $sql .= ', ';
            }
            if (in_array($column, self::BINARY)) {
                $sql .= "HEX($column) as $column";
            } else {
                $sql .= $column;
            }
        }

        $sql = 'SELECT ' .  $sql . ' FROM statscoach.golf_tee_box';

        $pdo = Database::database();

        if ($primary === null) {
            if (!empty($where)) {
                $build_where = function (array $set, $join = 'AND') use (&$pdo, &$build_where) {
                    $sql = '(';
                    foreach ($set as $column => $value) {
                        if (is_array($value)) {
                            $build_where($value, $join === 'AND' ? 'OR' : 'AND');
                        } else {
                            if (in_array($column, self::BINARY)) {
                                $sql .= "($column = UNHEX(" . $pdo->quote($value) . ")) $join ";
                            } else {
                                $sql .= "($column = " . $pdo->quote($value) . ") $join ";
                            }
                        }
                    }
                    return substr($sql, 0, strlen($sql) - (strlen($join) + 1)) . ')';
                };
                $sql .= ' WHERE ' . $build_where($where);
            }
        } else {
            $primary = $pdo->quote($primary);
            $sql .= ' WHERE  course_id=UNHEX(' . $primary .')';
        }

        $sql .= $limit;

        $return = self::fetch($sql);

        /**
        *   The next part is so every response from the rest api
        *   formats to a set of rows. Even if only one row is returned.
        *   You must set the third parameter to true, otherwise '0' is
        *   apparently in the self::COLUMNS
        */

        if ($primary === null && count($return) && in_array(array_keys($return)[0], self::COLUMNS, true)) {  // You must set tr
            $return = [$return];
        }        if ($primary === null && count($return) && in_array(array_keys($return)[0], self::COLUMNS, true)) {  // You must set tr
            $return = [$return];
        }

        return true;
    }

    /**
    * @param array $argv
    * @return bool|mixed
    */
    public static function Post(array $argv)
    {
        $sql = 'INSERT INTO statscoach.golf_tee_box (course_id, tee_box, distance, distance_color, distance_general_slope, distance_general_difficulty, distance_womens_slope, distance_womens_difficulty, distance_out, distance_in, distance_tot) VALUES ( UNHEX(:course_id), :tee_box, :distance, :distance_color, :distance_general_slope, :distance_general_difficulty, :distance_womens_slope, :distance_womens_difficulty, :distance_out, :distance_in, :distance_tot)';
        $stmt = Database::database()->prepare($sql);
            $course_id = $id = isset($argv['course_id']) ? $argv['course_id'] : self::new_entity('golf_tee_box');
            $stmt->bindParam(':course_id',$course_id, \PDO::PARAM_STR, 16);
            
                $tee_box = $argv['tee_box'];
                $stmt->bindParam(':tee_box',$tee_box, \PDO::PARAM_STR, 1);
                    $stmt->bindValue(':distance',$argv['distance'], \PDO::PARAM_STR);
                    
                $distance_color = $argv['distance_color'];
                $stmt->bindParam(':distance_color',$distance_color, \PDO::PARAM_STR, 10);
                    
                $distance_general_slope = isset($argv['distance_general_slope']) ? $argv['distance_general_slope'] : null;
                $stmt->bindParam(':distance_general_slope',$distance_general_slope, \PDO::PARAM_STR, 4);
                    $stmt->bindValue(':distance_general_difficulty',isset($argv['distance_general_difficulty']) ? $argv['distance_general_difficulty'] : null, \PDO::PARAM_STR);
                    
                $distance_womens_slope = isset($argv['distance_womens_slope']) ? $argv['distance_womens_slope'] : null;
                $stmt->bindParam(':distance_womens_slope',$distance_womens_slope, \PDO::PARAM_STR, 4);
                    $stmt->bindValue(':distance_womens_difficulty',isset($argv['distance_womens_difficulty']) ? $argv['distance_womens_difficulty'] : null, \PDO::PARAM_STR);
                    
                $distance_out = isset($argv['distance_out']) ? $argv['distance_out'] : null;
                $stmt->bindParam(':distance_out',$distance_out, \PDO::PARAM_STR, 7);
                    
                $distance_in = isset($argv['distance_in']) ? $argv['distance_in'] : null;
                $stmt->bindParam(':distance_in',$distance_in, \PDO::PARAM_STR, 7);
                    
                $distance_tot = isset($argv['distance_tot']) ? $argv['distance_tot'] : null;
                $stmt->bindParam(':distance_tot',$distance_tot, \PDO::PARAM_STR, 10);
        
        return $stmt->execute() ? $id : false;

    }

    /**
    * @param array $return
    * @param string $primary
    * @param array $argv
    * @return bool
    */
    public static function Put(array &$return, string $primary, array $argv) : bool
    {
        foreach ($argv as $key => $value) {
            if (!in_array($key, self::COLUMNS)){
                unset($argv[$key]);
            }
        }

        $sql = 'UPDATE statscoach.golf_tee_box ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

        if (isset($argv['course_id'])) {
            $set .= 'course_id=UNHEX(:course_id),';
        }
        if (isset($argv['tee_box'])) {
            $set .= 'tee_box=:tee_box,';
        }
        if (isset($argv['distance'])) {
            $set .= 'distance=:distance,';
        }
        if (isset($argv['distance_color'])) {
            $set .= 'distance_color=:distance_color,';
        }
        if (isset($argv['distance_general_slope'])) {
            $set .= 'distance_general_slope=:distance_general_slope,';
        }
        if (isset($argv['distance_general_difficulty'])) {
            $set .= 'distance_general_difficulty=:distance_general_difficulty,';
        }
        if (isset($argv['distance_womens_slope'])) {
            $set .= 'distance_womens_slope=:distance_womens_slope,';
        }
        if (isset($argv['distance_womens_difficulty'])) {
            $set .= 'distance_womens_difficulty=:distance_womens_difficulty,';
        }
        if (isset($argv['distance_out'])) {
            $set .= 'distance_out=:distance_out,';
        }
        if (isset($argv['distance_in'])) {
            $set .= 'distance_in=:distance_in,';
        }
        if (isset($argv['distance_tot'])) {
            $set .= 'distance_tot=:distance_tot,';
        }

        if (empty($set)){
            return false;
        }

        $sql .= substr($set, 0, strlen($set)-1);

        $db = Database::database();

        
        $primary = $db->quote($primary);
        $sql .= ' WHERE  course_id=UNHEX(' . $primary .')';

        $stmt = $db->prepare($sql);

        if (isset($argv['course_id'])) {
            $course_id = 'UNHEX('.$argv['course_id'].')';
            $stmt->bindParam(':course_id', $course_id, \PDO::PARAM_STR, 16);
        }
        if (isset($argv['tee_box'])) {
            $tee_box = $argv['tee_box'];
            $stmt->bindParam(':tee_box',$tee_box, \PDO::PARAM_STR, 1);
        }
        if (isset($argv['distance'])) {
            $stmt->bindValue(':distance',$argv['distance'], \PDO::PARAM_STR);
        }
        if (isset($argv['distance_color'])) {
            $distance_color = $argv['distance_color'];
            $stmt->bindParam(':distance_color',$distance_color, \PDO::PARAM_STR, 10);
        }
        if (isset($argv['distance_general_slope'])) {
            $distance_general_slope = $argv['distance_general_slope'];
            $stmt->bindParam(':distance_general_slope',$distance_general_slope, \PDO::PARAM_STR, 4);
        }
        if (isset($argv['distance_general_difficulty'])) {
            $stmt->bindValue(':distance_general_difficulty',$argv['distance_general_difficulty'], \PDO::PARAM_STR);
        }
        if (isset($argv['distance_womens_slope'])) {
            $distance_womens_slope = $argv['distance_womens_slope'];
            $stmt->bindParam(':distance_womens_slope',$distance_womens_slope, \PDO::PARAM_STR, 4);
        }
        if (isset($argv['distance_womens_difficulty'])) {
            $stmt->bindValue(':distance_womens_difficulty',$argv['distance_womens_difficulty'], \PDO::PARAM_STR);
        }
        if (isset($argv['distance_out'])) {
            $distance_out = $argv['distance_out'];
            $stmt->bindParam(':distance_out',$distance_out, \PDO::PARAM_STR, 7);
        }
        if (isset($argv['distance_in'])) {
            $distance_in = $argv['distance_in'];
            $stmt->bindParam(':distance_in',$distance_in, \PDO::PARAM_STR, 7);
        }
        if (isset($argv['distance_tot'])) {
            $distance_tot = $argv['distance_tot'];
            $stmt->bindParam(':distance_tot',$distance_tot, \PDO::PARAM_STR, 10);
        }

        if (!$stmt->execute()){
            return false;
        }

        $return = array_merge($return, $argv);

        return true;

    }

    /**
    * @param array $remove
    * @param string|null $primary
    * @param array $argv
    * @return bool
    */
    public static function Delete(array &$remove, string $primary = null, array $argv) : bool
    {
        return \Table\carbon::Delete($remove, $primary, $argv);
    }
}
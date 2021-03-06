<?php
namespace Table;


use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;

class golf_course extends Entities implements iRest
{
    const PRIMARY = [
    'course_id',
    ];

    const COLUMNS = [
    'course_id','course_name','course_holes','course_phone','course_difficulty','course_rank','box_color_1','box_color_2','box_color_3','box_color_4','box_color_5','course_par','course_par_out','course_par_in','par_tot','course_par_hcp','course_type','course_access','course_handicap','pga_professional','website',
    ];

    const BINARY = [
    'course_id','course_name',
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

        $sql = 'SELECT ' .  $sql . ' FROM statscoach.golf_course';

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
        $sql = 'INSERT INTO statscoach.golf_course (course_id, course_name, course_holes, course_phone, course_difficulty, course_rank, box_color_1, box_color_2, box_color_3, box_color_4, box_color_5, course_par, course_par_out, course_par_in, par_tot, course_par_hcp, course_type, course_access, course_handicap, pga_professional, website) VALUES ( UNHEX(:course_id), :course_name, :course_holes, :course_phone, :course_difficulty, :course_rank, :box_color_1, :box_color_2, :box_color_3, :box_color_4, :box_color_5, :course_par, :course_par_out, :course_par_in, :par_tot, :course_par_hcp, :course_type, :course_access, :course_handicap, :pga_professional, :website)';
        $stmt = Database::database()->prepare($sql);
            $course_id = $id = isset($argv['course_id']) ? $argv['course_id'] : self::new_entity('golf_course');
            $stmt->bindParam(':course_id',$course_id, \PDO::PARAM_STR, 16);
            
                $course_name = $argv['course_name'];
                $stmt->bindParam(':course_name',$course_name, \PDO::PARAM_STR, 16);
                    
                $course_holes = isset($argv['course_holes']) ? $argv['course_holes'] : '18';
                $stmt->bindParam(':course_holes',$course_holes, \PDO::PARAM_STR, 2);
                    $stmt->bindValue(':course_phone',$argv['course_phone'], \PDO::PARAM_STR);
                    
                $course_difficulty = isset($argv['course_difficulty']) ? $argv['course_difficulty'] : null;
                $stmt->bindParam(':course_difficulty',$course_difficulty, \PDO::PARAM_STR, 10);
                    
                $course_rank = isset($argv['course_rank']) ? $argv['course_rank'] : null;
                $stmt->bindParam(':course_rank',$course_rank, \PDO::PARAM_STR, 5);
                    
                $box_color_1 = isset($argv['box_color_1']) ? $argv['box_color_1'] : null;
                $stmt->bindParam(':box_color_1',$box_color_1, \PDO::PARAM_STR, 10);
                    
                $box_color_2 = isset($argv['box_color_2']) ? $argv['box_color_2'] : null;
                $stmt->bindParam(':box_color_2',$box_color_2, \PDO::PARAM_STR, 10);
                    
                $box_color_3 = isset($argv['box_color_3']) ? $argv['box_color_3'] : null;
                $stmt->bindParam(':box_color_3',$box_color_3, \PDO::PARAM_STR, 10);
                    
                $box_color_4 = isset($argv['box_color_4']) ? $argv['box_color_4'] : null;
                $stmt->bindParam(':box_color_4',$box_color_4, \PDO::PARAM_STR, 10);
                    
                $box_color_5 = isset($argv['box_color_5']) ? $argv['box_color_5'] : null;
                $stmt->bindParam(':box_color_5',$box_color_5, \PDO::PARAM_STR, 10);
                    $stmt->bindValue(':course_par',$argv['course_par'], \PDO::PARAM_STR);
                    
                $course_par_out = $argv['course_par_out'];
                $stmt->bindParam(':course_par_out',$course_par_out, \PDO::PARAM_STR, 2);
                    
                $course_par_in = $argv['course_par_in'];
                $stmt->bindParam(':course_par_in',$course_par_in, \PDO::PARAM_STR, 2);
                    
                $par_tot = $argv['par_tot'];
                $stmt->bindParam(':par_tot',$par_tot, \PDO::PARAM_STR, 2);
                    
                $course_par_hcp = isset($argv['course_par_hcp']) ? $argv['course_par_hcp'] : null;
                $stmt->bindParam(':course_par_hcp',$course_par_hcp, \PDO::PARAM_STR, 4);
                    
                $course_type = isset($argv['course_type']) ? $argv['course_type'] : null;
                $stmt->bindParam(':course_type',$course_type, \PDO::PARAM_STR, 30);
                    
                $course_access = isset($argv['course_access']) ? $argv['course_access'] : null;
                $stmt->bindParam(':course_access',$course_access, \PDO::PARAM_STR, 120);
                    $stmt->bindValue(':course_handicap',$argv['course_handicap'], \PDO::PARAM_STR);
                    $stmt->bindValue(':pga_professional',$argv['pga_professional'], \PDO::PARAM_STR);
                    $stmt->bindValue(':website',$argv['website'], \PDO::PARAM_STR);
        
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

        $sql = 'UPDATE statscoach.golf_course ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

        if (isset($argv['course_id'])) {
            $set .= 'course_id=UNHEX(:course_id),';
        }
        if (isset($argv['course_name'])) {
            $set .= 'course_name=UNHEX(:course_name),';
        }
        if (isset($argv['course_holes'])) {
            $set .= 'course_holes=:course_holes,';
        }
        if (isset($argv['course_phone'])) {
            $set .= 'course_phone=:course_phone,';
        }
        if (isset($argv['course_difficulty'])) {
            $set .= 'course_difficulty=:course_difficulty,';
        }
        if (isset($argv['course_rank'])) {
            $set .= 'course_rank=:course_rank,';
        }
        if (isset($argv['box_color_1'])) {
            $set .= 'box_color_1=:box_color_1,';
        }
        if (isset($argv['box_color_2'])) {
            $set .= 'box_color_2=:box_color_2,';
        }
        if (isset($argv['box_color_3'])) {
            $set .= 'box_color_3=:box_color_3,';
        }
        if (isset($argv['box_color_4'])) {
            $set .= 'box_color_4=:box_color_4,';
        }
        if (isset($argv['box_color_5'])) {
            $set .= 'box_color_5=:box_color_5,';
        }
        if (isset($argv['course_par'])) {
            $set .= 'course_par=:course_par,';
        }
        if (isset($argv['course_par_out'])) {
            $set .= 'course_par_out=:course_par_out,';
        }
        if (isset($argv['course_par_in'])) {
            $set .= 'course_par_in=:course_par_in,';
        }
        if (isset($argv['par_tot'])) {
            $set .= 'par_tot=:par_tot,';
        }
        if (isset($argv['course_par_hcp'])) {
            $set .= 'course_par_hcp=:course_par_hcp,';
        }
        if (isset($argv['course_type'])) {
            $set .= 'course_type=:course_type,';
        }
        if (isset($argv['course_access'])) {
            $set .= 'course_access=:course_access,';
        }
        if (isset($argv['course_handicap'])) {
            $set .= 'course_handicap=:course_handicap,';
        }
        if (isset($argv['pga_professional'])) {
            $set .= 'pga_professional=:pga_professional,';
        }
        if (isset($argv['website'])) {
            $set .= 'website=:website,';
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
        if (isset($argv['course_name'])) {
            $course_name = 'UNHEX('.$argv['course_name'].')';
            $stmt->bindParam(':course_name', $course_name, \PDO::PARAM_STR, 16);
        }
        if (isset($argv['course_holes'])) {
            $course_holes = $argv['course_holes'];
            $stmt->bindParam(':course_holes',$course_holes, \PDO::PARAM_STR, 2);
        }
        if (isset($argv['course_phone'])) {
            $stmt->bindValue(':course_phone',$argv['course_phone'], \PDO::PARAM_STR);
        }
        if (isset($argv['course_difficulty'])) {
            $course_difficulty = $argv['course_difficulty'];
            $stmt->bindParam(':course_difficulty',$course_difficulty, \PDO::PARAM_STR, 10);
        }
        if (isset($argv['course_rank'])) {
            $course_rank = $argv['course_rank'];
            $stmt->bindParam(':course_rank',$course_rank, \PDO::PARAM_STR, 5);
        }
        if (isset($argv['box_color_1'])) {
            $box_color_1 = $argv['box_color_1'];
            $stmt->bindParam(':box_color_1',$box_color_1, \PDO::PARAM_STR, 10);
        }
        if (isset($argv['box_color_2'])) {
            $box_color_2 = $argv['box_color_2'];
            $stmt->bindParam(':box_color_2',$box_color_2, \PDO::PARAM_STR, 10);
        }
        if (isset($argv['box_color_3'])) {
            $box_color_3 = $argv['box_color_3'];
            $stmt->bindParam(':box_color_3',$box_color_3, \PDO::PARAM_STR, 10);
        }
        if (isset($argv['box_color_4'])) {
            $box_color_4 = $argv['box_color_4'];
            $stmt->bindParam(':box_color_4',$box_color_4, \PDO::PARAM_STR, 10);
        }
        if (isset($argv['box_color_5'])) {
            $box_color_5 = $argv['box_color_5'];
            $stmt->bindParam(':box_color_5',$box_color_5, \PDO::PARAM_STR, 10);
        }
        if (isset($argv['course_par'])) {
            $stmt->bindValue(':course_par',$argv['course_par'], \PDO::PARAM_STR);
        }
        if (isset($argv['course_par_out'])) {
            $course_par_out = $argv['course_par_out'];
            $stmt->bindParam(':course_par_out',$course_par_out, \PDO::PARAM_STR, 2);
        }
        if (isset($argv['course_par_in'])) {
            $course_par_in = $argv['course_par_in'];
            $stmt->bindParam(':course_par_in',$course_par_in, \PDO::PARAM_STR, 2);
        }
        if (isset($argv['par_tot'])) {
            $par_tot = $argv['par_tot'];
            $stmt->bindParam(':par_tot',$par_tot, \PDO::PARAM_STR, 2);
        }
        if (isset($argv['course_par_hcp'])) {
            $course_par_hcp = $argv['course_par_hcp'];
            $stmt->bindParam(':course_par_hcp',$course_par_hcp, \PDO::PARAM_STR, 4);
        }
        if (isset($argv['course_type'])) {
            $course_type = $argv['course_type'];
            $stmt->bindParam(':course_type',$course_type, \PDO::PARAM_STR, 30);
        }
        if (isset($argv['course_access'])) {
            $course_access = $argv['course_access'];
            $stmt->bindParam(':course_access',$course_access, \PDO::PARAM_STR, 120);
        }
        if (isset($argv['course_handicap'])) {
            $stmt->bindValue(':course_handicap',$argv['course_handicap'], \PDO::PARAM_STR);
        }
        if (isset($argv['pga_professional'])) {
            $stmt->bindValue(':pga_professional',$argv['pga_professional'], \PDO::PARAM_STR);
        }
        if (isset($argv['website'])) {
            $stmt->bindValue(':website',$argv['website'], \PDO::PARAM_STR);
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
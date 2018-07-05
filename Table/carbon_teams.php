<?php

namespace Table;

use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;

class carbon_teams extends Entities implements iRest
{
    const PRIMARY = "team_id";

    const COLUMNS = [
    'team_id','team_coach','parent_team','team_code','team_name','team_rank','team_sport','team_division','team_school','team_district','team_membership','team_photo',
    ];

    const BINARY = [
    'team_id','team_coach','parent_team','team_photo',
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

        $sql = 'SELECT ' .  $sql . ' FROM statscoach.carbon_teams';

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
        } else if (!empty(self::PRIMARY)){
            $sql .= ' WHERE ' . self::PRIMARY . '=UNHEX(' . $pdo->quote($primary) . ')';
        }

        $sql .= $limit;

        $return = self::fetch($sql);

        return true;
    }

    /**
    * @param array $argv
    * @return bool|mixed
    */
    public static function Post(array $argv)
    {
        $sql = 'INSERT INTO statscoach.carbon_teams (team_id, team_coach, parent_team, team_code, team_name, team_rank, team_sport, team_division, team_school, team_district, team_membership, team_photo) VALUES ( :team_id, :team_coach, :parent_team, :team_code, :team_name, :team_rank, :team_sport, :team_division, :team_school, :team_district, :team_membership, :team_photo)';
        $stmt = Database::database()->prepare($sql);
            $team_id = $id = self::new_entity('carbon_teams');
            $stmt->bindParam(':team_id',$team_id, \PDO::PARAM_STR, 16);
            
                $team_coach = isset($argv['team_coach']) ? $argv['team_coach'] : null;
                $stmt->bindParam(':team_coach',$team_coach, \PDO::PARAM_STR, 16);
                    
                $parent_team = isset($argv['parent_team']) ? $argv['parent_team'] : null;
                $stmt->bindParam(':parent_team',$parent_team, \PDO::PARAM_STR, 16);
                    
                $team_code = isset($argv['team_code']) ? $argv['team_code'] : null;
                $stmt->bindParam(':team_code',$team_code, \PDO::PARAM_STR, 225);
                    
                $team_name = isset($argv['team_name']) ? $argv['team_name'] : null;
                $stmt->bindParam(':team_name',$team_name, \PDO::PARAM_STR, 225);
                    
                $team_rank = isset($argv['team_rank']) ? $argv['team_rank'] : '0';
                $stmt->bindParam(':team_rank',$team_rank, \PDO::PARAM_STR, 11);
                    
                $team_sport = isset($argv['team_sport']) ? $argv['team_sport'] : 'Golf';
                $stmt->bindParam(':team_sport',$team_sport, \PDO::PARAM_STR, 225);
                    
                $team_division = isset($argv['team_division']) ? $argv['team_division'] : null;
                $stmt->bindParam(':team_division',$team_division, \PDO::PARAM_STR, 225);
                    
                $team_school = isset($argv['team_school']) ? $argv['team_school'] : null;
                $stmt->bindParam(':team_school',$team_school, \PDO::PARAM_STR, 225);
                    
                $team_district = isset($argv['team_district']) ? $argv['team_district'] : null;
                $stmt->bindParam(':team_district',$team_district, \PDO::PARAM_STR, 225);
                    
                $team_membership = isset($argv['team_membership']) ? $argv['team_membership'] : null;
                $stmt->bindParam(':team_membership',$team_membership, \PDO::PARAM_STR, 225);
                    
                $team_photo = isset($argv['team_photo']) ? $argv['team_photo'] : null;
                $stmt->bindParam(':team_photo',$team_photo, \PDO::PARAM_STR, 16);
        
        return $stmt->execute() ? $id : false;

    }

    /**
    * @param array $return
    * @param string $id
    * @param array $argv
    * @return bool
    */
    public static function Put(array &$return, string $id, array $argv) : bool
    {
        foreach ($argv as $key => $value) {
            if (!in_array($key, self::COLUMNS)){
                unset($argv[$key]);
            }
        }

        $sql = 'UPDATE statscoach.carbon_teams ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

        if (isset($argv['team_id'])) {
            $set .= 'team_id=UNHEX(:team_id),';
        }
        if (isset($argv['team_coach'])) {
            $set .= 'team_coach=UNHEX(:team_coach),';
        }
        if (isset($argv['parent_team'])) {
            $set .= 'parent_team=UNHEX(:parent_team),';
        }
        if (isset($argv['team_code'])) {
            $set .= 'team_code=:team_code,';
        }
        if (isset($argv['team_name'])) {
            $set .= 'team_name=:team_name,';
        }
        if (isset($argv['team_rank'])) {
            $set .= 'team_rank=:team_rank,';
        }
        if (isset($argv['team_sport'])) {
            $set .= 'team_sport=:team_sport,';
        }
        if (isset($argv['team_division'])) {
            $set .= 'team_division=:team_division,';
        }
        if (isset($argv['team_school'])) {
            $set .= 'team_school=:team_school,';
        }
        if (isset($argv['team_district'])) {
            $set .= 'team_district=:team_district,';
        }
        if (isset($argv['team_membership'])) {
            $set .= 'team_membership=:team_membership,';
        }
        if (isset($argv['team_photo'])) {
            $set .= 'team_photo=UNHEX(:team_photo),';
        }

        if (empty($set)){
            return false;
        }

        $set = substr($set, 0, strlen($set)-1);

        $sql .= $set . ' WHERE ' . self::PRIMARY . "='$id'";

        $stmt = Database::database()->prepare($sql);

        if (isset($argv['team_id'])) {
            $team_id = 'UNHEX('.$argv['team_id'].')';
            $stmt->bindParam(':team_id', $team_id, \PDO::PARAM_STR, 16);
        }
        if (isset($argv['team_coach'])) {
            $team_coach = 'UNHEX('.$argv['team_coach'].')';
            $stmt->bindParam(':team_coach', $team_coach, \PDO::PARAM_STR, 16);
        }
        if (isset($argv['parent_team'])) {
            $parent_team = 'UNHEX('.$argv['parent_team'].')';
            $stmt->bindParam(':parent_team', $parent_team, \PDO::PARAM_STR, 16);
        }
        if (isset($argv['team_code'])) {
            $team_code = $argv['team_code'];
            $stmt->bindParam(':team_code',$team_code, \PDO::PARAM_STR, 225 );
        }
        if (isset($argv['team_name'])) {
            $team_name = $argv['team_name'];
            $stmt->bindParam(':team_name',$team_name, \PDO::PARAM_STR, 225 );
        }
        if (isset($argv['team_rank'])) {
            $team_rank = $argv['team_rank'];
            $stmt->bindParam(':team_rank',$team_rank, \PDO::PARAM_STR, 11 );
        }
        if (isset($argv['team_sport'])) {
            $team_sport = $argv['team_sport'];
            $stmt->bindParam(':team_sport',$team_sport, \PDO::PARAM_STR, 225 );
        }
        if (isset($argv['team_division'])) {
            $team_division = $argv['team_division'];
            $stmt->bindParam(':team_division',$team_division, \PDO::PARAM_STR, 225 );
        }
        if (isset($argv['team_school'])) {
            $team_school = $argv['team_school'];
            $stmt->bindParam(':team_school',$team_school, \PDO::PARAM_STR, 225 );
        }
        if (isset($argv['team_district'])) {
            $team_district = $argv['team_district'];
            $stmt->bindParam(':team_district',$team_district, \PDO::PARAM_STR, 225 );
        }
        if (isset($argv['team_membership'])) {
            $team_membership = $argv['team_membership'];
            $stmt->bindParam(':team_membership',$team_membership, \PDO::PARAM_STR, 225 );
        }
        if (isset($argv['team_photo'])) {
            $team_photo = 'UNHEX('.$argv['team_photo'].')';
            $stmt->bindParam(':team_photo', $team_photo, \PDO::PARAM_STR, 16);
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
        $sql = 'DELETE FROM statscoach.carbon_teams ';

        foreach($argv as $column => $constraint){
            if (!in_array($column, self::COLUMNS)){
                unset($argv[$column]);
            }
        }

        if ($primary === null) {
            /**
            *   While useful, we've decided to disallow full
            *   table deletions through the rest api. For the
            *   n00bs and future self, "I got chu."
            */
            if (empty($argv)) {
                return false;
            }
            $sql .= ' WHERE ';
            foreach ($argv as $column => $value) {
                if (in_array($column, self::BINARY)) {
                    $sql .= " $column =UNHEX(" . Database::database()->quote($value) . ') AND ';
                } else {
                    $sql .= " $column =" . Database::database()->quote($value) . ' AND ';
                }
            }
            $sql = substr($sql, 0, strlen($sql)-4);
        } else if (!empty(self::PRIMARY)) {
            $sql .= ' WHERE ' . self::PRIMARY . '=UNHEX(' . Database::database()->quote($primary) . ')';
        }

        $remove = null;

        return self::execute($sql);
    }
}
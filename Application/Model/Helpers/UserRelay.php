<?php

/*
The user relay runs through out Database connection code, no PDO is actually run here
Do not use try catch, as it is not needed

Email needs to be edited in function "register"
*/

namespace Model\Helpers;

use Controller\User;
use PDO;
use Controller\User as Users;
use Modules\Helpers\Skeleton;
use Modules\Helpers\Bcrypt;
use Modules\Database;
use Psr\Singleton;

class UserRelay
{
    use Singleton;

    private $db;


    public $user_id;
    public $user_username;
    public $user_full_name;
    public $user_first_name;
    public $user_last_name;
    public $user_profile_pic;
    public $user_cover_photo;
    public $user_birth_date;
    public $user_gender;
    public $user_bio;
    public $user_rank;
    public $user_password;
    public $user_email;
    public $user_email_code;
    public $user_email_confirmed;
    public $user_generated_string;
    public $user_membership;
    public $user_deactivated;
    public $user_creation_date;
    public $user_ip;


    public function __construct()
    {
        $this->db = Database::getConnection();  // establish a connection

        //This would be run on first request after logging in. Class will be sterilised
        if ($this->user_id = Users::loggedIn() && !isset($this->user_username))
            $this->userProfile( $this->user_id );   // populates the global and
    }

    public function __wakeup()
    {
        $this->db = Database::getConnection();

        if (Users::loggedIn()) {
            // every server request after login

            if (empty($this->user_full_name)) {
                $this->user_full_name = $this->user_first_name . ' ' . $this->user_last_name;
            }

            if (isset($this->username) && !array_key_exists( 'username', $GLOBALS )) {
                foreach (get_class_vars( $this ) as $key => $var)
                    $GLOBALS[$key] = $this->$key = $var;
            }
        }
    }

    public function __sleep()
    {
        return array('user_username', 'user_first_name', 'user_last_name', 'user_profile_pic', 'user_cover_photo', 'user_birth_date', 'user_gender', 'user_bio', 'user_rank', 'user_email', 'user_email_code', 'user_email_confirmed', 'user_generated_string', 'user_membership', 'user_deactivated', 'user_creation_date', 'user_ip');
    }

    public function profileData($id)
    {
        // TODO - I dont think I need this anymore.. but idk
        $sql = "SELECT user_username, user_first_name, user_last_name, user_gender, user_bio, user_profile_pic, user_cover_photo, user_email, user_creation_date
                      FROM `users` WHERE `user_id`= ?";

        $stmt = $this->db->prepare( $sql );
        $stmt->execute( array($id) );
        return $stmt->fetch();
    }


    private function userProfile($id = null)
    {


        $this->user_id = ($id == null ? $id = User::loggedIn() : $id);

        
        if (isset($this->username)) {
            // this should have been done in the wake up routine
            // TODO - check if this actually matters
            if (!array_key_exists( 'username', $GLOBALS )) {
                foreach (get_class_vars( $this ) as $key => $var)
                    $GLOBALS[$key] = $var;
            }
            return true;
        }

        // In theory this request is only called once per session.
        $sql = 'SELECT * FROM users WHERE `user_id` = ?';
        $stmt = $this->db->prepare( $sql );
        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        $stmt->execute( array($id) );
        $data = $stmt->fetch();

        foreach ($data as $key => $val)
            $GLOBALS[$key] = $this->{$key} = $val;

        // Reconfig variables for dynamic path, and easy templating
        $GLOBALS['user_profile_pic'] = $this->user_profile_pic = SITE_ROOT . $this->user_profile_pic;
        $GLOBALS['user_cover_photo'] = $this->user_cover_photo = SITE_ROOT . $this->user_cover_photo;
        $GLOBALS['user_full_name'] = $this->user_full_name = $this->user_first_name . ' ' . $this->user_last_name;
        
    }
 
    public function fetch_info($what, $field, $value)
    {
        $allowed = array('user_id', 'user_profile_pic', 'user_username', 'user_full_name', 'user_first_name', 'user_last_name', 'user_gender', 'user_bio', 'user_email');

        if (!in_array( $what, $allowed, true ) || !in_array( $field, $allowed, true ))
            throw new \InvalidArgumentException;

        $sql = "SELECT $what FROM `users` WHERE $field = ?";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( array($value) );
        return $stmt->fetch();

    } // Returns only one value from the db

    public function register($username, $password, $email, $firstName, $lastName)
    {
        $time = time();
        $ip = $_SERVER['REMOTE_ADDR']; // getting the users IP address
        $email_code = $email_code = uniqid( 'code_', true ); // Creating a unique string.
        $crypt = Bcrypt::genHash( $password );

        try {
            $sql = "INSERT INTO users (`user_username`, `user_password`, `user_email`, `user_ip`, `user_creation_date`, `user_email_code`, `user_first_name`, `user_last_name`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $this->db->prepare( $sql )->execute( array($username, $crypt, $email, $ip, $time, $email_code, $firstName, $lastName) );

            mail( $email, 'Please activate your account', "Hello $firstName ,
            \r\nThank you for registering with us. 
            \r\n Username :  $username 
            \r\n Password :  $password 
            \r\n Please visit the link below so we can activate your account:\r\n\r\n
             http://www.Stats.Coach/Activate/$email/$email_code/
             \r\n\r\n--" . SITE_ROOT );

        } catch (\Exception $e) {
            echo $e->getMessage();

            throw new \Exception( "Sorry, we were unable to create this account. Please try again." );
        }
        return true;
    }

    public function activate($email, $email_code)
    {
        $sql = "SELECT COUNT(user_id) FROM users WHERE user_email = ? AND user_email_code = ? AND user_email_confirmed = ?";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( array($email, $email_code, '0') );

        if ($stmt->fetch() > 0) {
            $sql = "UPDATE `users` SET `user_email_confirmed` = 1 WHERE `user_email` = ?";
            return $this->db->prepare( $sql )->execute( array($email) );
        }
        throw new \Exception( 'Sorry, we have failed to activate your account. Please contact us for further assistance.' );

    }

    public function login($username, $password)
    {
        $sql = "SELECT `user_password`, `user_id` FROM `users` WHERE `user_username` = ?";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( array($username) );
        $data = $stmt->fetch();

        // using the verify method to compare the password with the stored hashed password.
        if (Bcrypt::verify( $password, $data['user_password'] ) === true) {
            return $data['user_id'];    // returning the user's id.
        }
        throw new \Exception ( 'Sorry, the username and password combination you have entered is invalid.' );
    }

    public function update_user($first_name, $last_name, $gender, $bio, $image_location, $id)
    {
        return $this->db->prepare( "UPDATE users SET user_first_name = ?, user_last_name = ?, user_gender = ?, user_bio = ?, user_profile_pic = ? WHERE user_id = ?" )
            ->execute( array($first_name, $last_name, $gender, $bio, $image_location, $id) );
    }

    public function change_password($user_id, $password)
    {   /* Two create a Hash you do */
        $password_hash = Bcrypt::genHash( $password );

        $stmt = $this->db->prepare( "UPDATE users SET user_password = ? WHERE user_id = ?" );
        return $stmt->execute( array($password_hash, $user_id) );

    }

    public function recover($email, $generated_string)
    {
        if ($generated_string == 0) {
            return false;
        } else {
            $stmt = $this->db->prepare( "SELECT COUNT(`user_id`) FROM `users` WHERE `user_email` = ? AND `user_generated_string` = ?" );
            $stmt->execute( array($email, $generated_string) );

            if ($stmt->fetch()) {   // a row exists

                $username = self::fetch_info( 'user_username', 'user_email', $email ); // getting username for the use in the email.
                $user_id = self::fetch_info( 'user_id', 'user_email', $email ); // getting username for the use in the email.

                // We want to keep things standard and use the user's id for most of the operations. Therefore, we use id instead of email.

                $charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $generated_password = substr( str_shuffle( $charset ), 0, 10 );

                $this->change_password( $user_id, $generated_password );

                $stmt = $this->db->prepare( "UPDATE `users` SET `user_generated_string` = 0 WHERE `user_id` = ?" );
                $stmt->execute( array($user_id) );

                mail( $email, 'Your password', "Hello " . $username . ",\n\nYour your new password is: " . $generated_password . "\n\n
                               Please change your password once you have logged in using this password.\n\n-Lil Richard" );

            } else {
                return false;
            }
        }
    }

    public function confirm_recover($email)
    {
        $first_name = $this->fetch_info( 'first_name', 'email', $email );   // returns 1 value

        $unique = uniqid( '', true );
        $random = substr( str_shuffle( 'AdfsBCDEFGHIJKLMNOPQRSTUVWXYZ' ), 0, 10 );

        $generated_string = $unique . $random;          // a random and unique string

        $stmt = $this->db->prepare( "UPDATE `users` SET `user_generated_string` = ? WHERE `user_email` = ?" );
        $stmt->execute( array($generated_string, $email) );

        mail( $email, 'Recover Password', "Hello " . $first_name . ",\r\nPlease click the link below:\r\n\r\n
            " . SITE_ROOT . "recover/" . $email . "/" . $generated_string . "/\r\n\r\n 
            We will generate a new password for you and send it back to your email.\r\n\r\n
            --" . SITE_ROOT );
        return true;
    }

    public function user_exists($username)
    {
        $sql = 'SELECT COUNT(user_id) FROM users WHERE user_username = ?';
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( array($username) );
        $sql = $stmt->fetchColumn();
        return $sql;
    }

    public function email_exists($email)
    {
        $sql = "SELECT COUNT(user_id) FROM `users` WHERE `user_email`= ?";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( array($email) );
        $sql = $stmt->fetchColumn();
        return $sql;
    }

    public function email_confirmed($username)
    {
        $sql = "SELECT COUNT(user_id) FROM users WHERE user_username= ? AND user_email_confirmed = ?";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( array($username, 1) );
        if ($stmt->fetch())
            return true;
        throw new \Exception( 'Sorry, you need to activate your account. Please check your email!' );
    }

    public function get_users()
    {
        $sql = "SELECT * FROM users ORDER BY user_creation_date DESC";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute();
        return $stmt->fetchAll();
    }

}












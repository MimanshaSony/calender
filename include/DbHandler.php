<?php


class DbHandler {

    private $conn;

    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
	}
	
 function createUser($name, $email, $code, $mobile, $password) {
        $response = array();

        // First check if user already existed in db
        if (!$this->isUserExists($email)) {
           
			 // insert query
            $stmt = $this->conn->prepare("INSERT INTO users(name, email, code, mobile, password) values(?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiis", $name, $email, $code, $mobile, $password);

            $result = $stmt->execute();

            $stmt->close();

            // Check for successful insertion
            if ($result) {
                // User successfully inserted
                return USER_CREATED_SUCCESSFULLY;
            } else {
                // Failed to create user
                return USER_CREATE_FAILED;
            }
        } else {
            // User with same email already existed in the db
            return USER_ALREADY_EXISTED;
        }

        return $response;
    }
	
 function checkLogin($email,$password) {
        // fetching user by email
		
        $stmt = $this->conn->prepare("SELECT password FROM users WHERE email = ? AND password=? ");
        $stmt->bind_param("ss", $email,$password);
        $stmt->execute();
        $stmt->bind_result($password);
        $stmt->store_result();

       if ($stmt->num_rows > 0) {
            // Found user with the email
            $stmt->fetch();
             $stmt->close();

                // User password is correct
                return TRUE;

        } else {
            $stmt->close();

            // user not existed with the email
            return FALSE;
        }
    }
	
 function getTask($email,$new_password,$confirm_password) {
	
     // fetching user by email
        $stmt = $this->conn->prepare("SELECT email FROM users WHERE email = ? ");

        $stmt->bind_param("s", $email);

        $stmt->execute();

       // $stmt->bind_result($password_hash);

        $stmt->store_result();
       if ($stmt->num_rows > 0) {
            // Found user with the email
            // Now verify the password
			
            $stmt->fetch();

            $stmt->close();
			  $db = new DbConnect();
        $this->conn = $db->connect();
        $stmt = $this->conn->prepare("UPDATE users SET password=? WHERE email=?");
        $stmt->bind_param("ss",$new_password,$email);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
		
        $stmt->close();
		if($num_affected_rows>0){
			return true;
		}else{
			return false;
		}
				
        } else {
            $stmt->close();

            // user not existed with the email
            return FALSE;
        }
		}
		
public function create_events($Event_name, $start_date, $start_time, $end_date, $end_time, $month, $year, $add_notes, $imageUrl) {
	
		$response = array ();
		
		$stmt = $this->conn->prepare("INSERT INTO create_events(Event_name, start_date, start_time, end_date, end_time, month, year, add_notes, imageUrl) values(?, ?, ?, ?, ?, ?, ?, ?, ?)" );
		
		$stmt->bind_param ( "sisississ", $Event_name, $start_date, $start_time, $end_date, $end_time, $month, $year, $add_notes, $imageUrl);
		
		$result = $stmt->execute ();
		
		$stmt->close ();
		
		if ($result) {
			return USER_CREATED_SUCCESSFULLY;
			
		 }else {
			return USER_CREATE_FAILED;
		}
		return $response;
	}
	
/*public function getRepeatTask($repeat) {
	
		$response = array ();
		
		$stmt = $this->conn->prepare("INSERT INTO my_repeat(repeat) values(?)" );
		
		$stmt->bind_param ( "s", $repeat);
		
		$result = $stmt->execute ();
		
		$stmt->close ();
		
		 if ($result) {
        	
             return TRUE;
            }
         else {
            return FALSE;
        }
	}*/
	
		
function changename($email,$name,$new_name) {
	
     // fetching user by email
        $stmt = $this->conn->prepare("SELECT name FROM users WHERE email = ? AND name=? ");

        $stmt->bind_param("ss", $email,$name);

        $stmt->execute();

        $stmt->store_result();
       if ($stmt->num_rows > 0) {
            // Found user with the email
			
            $stmt->fetch();

            $stmt->close();
			
		    $db = new DbConnect();
        $this->conn = $db->connect();
        $stmt = $this->conn->prepare("UPDATE users SET name=? WHERE email=?");
        $stmt->bind_param("ss",$new_name,$email);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
		
        $stmt->close();
		if($num_affected_rows>0){
			return true;
		}else{
			return false;
		}
				
        } else {
            $stmt->close();

            // user not existed with the email
            return FALSE;
        }
		}
		
function changepassword($email,$password,$new_password,$confirm_password) {
	
     // fetching user by email
        $stmt = $this->conn->prepare("SELECT password FROM users WHERE email = ? AND password=? ");

        $stmt->bind_param("ss", $email,$password);

        $stmt->execute();

        $stmt->store_result();
       if ($stmt->num_rows > 0) {
            // Found user with the email
			
            $stmt->fetch();

            $stmt->close();
			
		    $db = new DbConnect();
        $this->conn = $db->connect();
        $stmt = $this->conn->prepare("UPDATE users SET password=? WHERE email=?");
        $stmt->bind_param("ss",$new_password,$email);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
		
        $stmt->close();
		if($num_affected_rows>0){
			return true;
		}else{
			return false;
		}
				
        } else {
            $stmt->close();

            // user not existed with the email
            return FALSE;
        }
		}
		
function changenumber($email,$number,$new_number) {
	
     // fetching user by email
        $stmt = $this->conn->prepare("SELECT number FROM users WHERE email = ? AND number=? ");

        $stmt->bind_param("ss", $email,$number);

        $stmt->execute();

        $stmt->store_result();
       if ($stmt->num_rows > 0) {
            // Found user with the email
			
            $stmt->fetch();

            $stmt->close();
			
		    $db = new DbConnect();
        $this->conn = $db->connect();
        $stmt = $this->conn->prepare("UPDATE users SET number=? WHERE email=?");
        $stmt->bind_param("ss",$new_number,$email);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
		
        $stmt->close();
		if($num_affected_rows>0){
			return true;
		}else{
			return false;
		}
				
        } else {
            $stmt->close();

            // user not existed with the email
            return FALSE;
        }
		}
		
public function createTask($day, $events, $created_at) {
    
        $stmt = $this->conn->prepare("INSERT INTO day(day, events, created_at) VALUES(?, ?, ?)");
        $stmt->bind_param("iss", $day, $events, $created_at);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
        	
             return TRUE;
            }
         else {
            return FALSE;
        }
    }
	
	
public function getAllUserEvents($start_date) {
        $stmt = $this->conn->prepare("SELECT  ce.Event_name, ce.start_date, ce.start_time, ce.end_date, ce.end_time from create_events ce WHERE ce.start_date = ?");
        $stmt->bind_param("i", $start_date);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }
	
public function getAllEvents($start_date) {
        $stmt = $this->conn->prepare("SELECT  ce.Event_name from create_events ce WHERE ce.start_date = ?");
        $stmt->bind_param("s", $start_date);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }
	
public function getAllDay($Event_name) {
        $stmt = $this->conn->prepare("SELECT  ce.start_date, ce.end_date from create_events ce WHERE ce.Event_name = ?");
        $stmt->bind_param("s", $Event_name);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }
	
public function getAllMonthEvents($month) {
        $stmt = $this->conn->prepare("SELECT ce.start_date from create_events ce WHERE ce.month = ?");
        $stmt->bind_param("s", $month);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }
	
public function getAllyearEvents($year) {
        $stmt = $this->conn->prepare("SELECT ce.start_date from create_events ce WHERE ce.year = ?");
        $stmt->bind_param("s", $year);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }
	
public function useDevice($task) {
    
        $stmt = $this->conn->prepare("INSERT INTO button(task) VALUES(?)");
        $stmt->bind_param("s", $task);
        $result = $stmt->execute();
        $stmt->close();
		return $result;
        
    }
	
	public function doRepeat($cus) {
        $stmt = $this->conn->prepare("SELECT * from mee WHERE cus = ?");
        $stmt->bind_param("s", $cus);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }
	
	public function startReminder($note) {
        $stmt = $this->conn->prepare("SELECT * from sta WHERE note = ?");
        $stmt->bind_param("s", $note);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }
	public function language($lan) {
        $stmt = $this->conn->prepare("SELECT * from language WHERE lan = ?");
        $stmt->bind_param("s", $lan);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }

	
	public function customReminder($bell) {
        $stmt = $this->conn->prepare("SELECT * from cus_rem WHERE bell = ?");
        $stmt->bind_param("s", $bell);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }
	
	function custom($start_date, $start_time, $end_date, $end_time) {
        $response = array();

			 // insert query
            $stmt = $this->conn->prepare("INSERT INTO custom(start_date, start_time, end_date, end_time) values(?, ?, ?, ?)");
            $stmt->bind_param("isis", $start_date, $start_time, $end_date, $end_time);

            $result = $stmt->execute();

            $stmt->close();

            // Check for successful insertion
            if ($result) {
                // User successfully inserted
                return USER_CREATED_SUCCESSFULLY;
            } else {
                // Failed to create user
                return USER_CREATE_FAILED;
            }
         

        return $response;
    }
	
	
	
	  function isUserExists($email) {
        $stmt = $this->conn->prepare("SELECT password from users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

     function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT name, email, code, mobile FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            // $user = $stmt->get_result()->fetch_assoc();
            $stmt->bind_result($name, $email, $code, $mobile);
            $stmt->fetch();
            $user = array();
            $user["name"] = $name;
            $user["email"] = $email;
			$user["code"] = $code;
			$user["mobile"] = $mobile;
            
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }
	 
}

	
?>
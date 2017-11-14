<?php

require_once '../include/DbHandler.php';
require '.././libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

function authenticate(\Slim\Route $route) {
    // Getting request headers
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();
}
	

$app->post('/signup', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('name', 'email', 'code', 'mobile', 'password'));

            $response = array();

            // reading post params
            $name = $app->request->post('name');
            $email = $app->request->post('email');
			$code = $app->request->post('code');
			$mobile = $app->request->post('mobile');
            $password = $app->request->post('password');

            // validating email address
            validateEmail($email);
			validateName($name);
			validateMobile($mobile);
			validatePassword($password);

            $db = new DbHandler();
			
            $res = $db->createUser($name, $email, $code, $mobile, $password);
            if ($res == USER_CREATED_SUCCESSFULLY) {
                $response["error"] = false;
                $response["message"] = "You are successfully registered";
            } else if ($res == USER_CREATE_FAILED) {
                $response["error"] = true;
                $response["message"] = "Oops! An error occurred while registereing";
            } else if ($res == USER_ALREADY_EXISTED) {
               
                $response["message"] = "Sorry, this email already existed";
            }
            // echo json response
            echoRespnse(201, $response);
        });
		
$app->post('/login', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('email','password'));

            // reading post params
            $email = $app->request()->post('email');
            $password = $app->request()->post('password');
            $response = array();

            $db = new DbHandler();
			
            // check for correct email and password
            if ($db->checkLogin($email, $password)) {
                // get the user by email
                $user = $db->getUserByEmail($email);

                if ($user != NULL) {
                    $response["error"] = false;
                    $response['name'] = $user['name'];
                    $response['email'] = $user['email'];
                 
                } else {
                                                                                                                                                                                                                     // unknown error occurred
                    $response['error'] = true;
                    $response['message'] = "An error occurred. Please try again";
                }
            } else {
                // user credentials are wrong
                $response['error'] = true;
                $response['message'] = 'Login failed. Incorrect credentials';
            }

            echoRespnse(200, $response);
        });
		
$app->put('/forgetPassword',  function() use ($app) {
	verifyRequiredParams(array('email','new_password','confirm_password'));
           
            $response = array();
			
			$email = $app->request->put('email');
			$new_password = $app->request->put('new_password');
            $confirm_password = $app->request->put('confirm_password');
			
			if($new_password==$confirm_password){
            $db = new DbHandler();

            // fetch task
            $result = $db->getTask($email,$new_password,$confirm_password);

             if ($result == TRUE) {
                $response["error"] = false;
                $response["message"] = "Updated Succesfully ";
            }else{
                $response["error"] = true;
                $response["message"] = "Error Occured";
            }
			}
			else{
				$response["error"] = true;
				$response["message"] = "new_password confirm_password not matched";
			}
            // echo json response
            echoRespnse(201, $response);
        });
		
$app->post('/save', function() use ($app) {
	
	verifyRequiredParams(array('Event_name', 'start_date', 'start_time', 'end_date', 'end_time', 'month', 'year', 'add_notes'));
	
	     $response = array();
	        
			$Event_name= $app->request->post('Event_name');
	        $start_date= $app->request->post('start_date');
            $start_time = $app->request->post('start_time');
			$end_date= $app->request->post('end_date');
			$end_time= $app->request->post('end_time');
			$month= $app->request->post('month');
			$year= $app->request->post('year');
			$add_notes= $app->request->post('add_notes');
			
			validateStart($start_date);
			validateEnd($end_date);
			validateStartTime($start_time);
			validateEndTime($end_time);
			validateMonth($month);
			//validateYear($year);
			
	 if ((!isset($_FILES['file']))) {
        $result = array('success' => '0',
            'code' => '500',
            'message'=> 'Error: No files uploaded');
        echoRespnse(500,json_encode($result));
        return;
		
    }else {
		
         $tmp_name = $_FILES["file"]["tmp_name"];
         $name = $_FILES["file"]["name"];

        if (!file_exists("../images")) {
            mkdir("../images", 0777, true);
        }

        $fullFileName =  strtotime("now").'_'.$name;
        $saved = move_uploaded_file($tmp_name, "../images/$fullFileName");
		$fullpath= "/images/$fullFileName";
		$imageUrl = "http://$_SERVER[HTTP_HOST]/images/$fullpath";	 
       
	}
	
	        $db = new DbHandler();
			$res = $db->create_events($Event_name, $start_date, $start_time, $end_date, $end_time, $month, $year, $add_notes, $imageUrl);
			
			 if ($res == USER_CREATED_SUCCESSFULLY) {
                $response["error"] = false;
                $response["message"] = "Event is successfully created";
            } else if ($res == USER_CREATE_FAILED) {
                $response["error"] = true;
                $response["message"] = "Oops! An error occurred while creating ";
            }
	         echoRespnse(201, $response);
     });
	 
/* $app->post('/DoNotRepeat', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('repeat'));

            $response = array();

            // reading post params
            $repeat = $app->request->post('repeat');
            
            $db = new DbHandler();
			
        $result = $db->getRepeatTask($repeat);
	 //if ($db->getRepeatTask($repeat)) {
		if ($result==true)	{
            switch ($result) {
    case "Daily":
       $response["error"] = false;
       $response["message"] = "Repeat Daily ";
        break;
    case "Weekly":
        $response["error"] = false;
        $response["message"] = "Repeat Weekly ";
        break;
    case "Monthly":
        $response["error"] = false;
        $response["message"] = "Repeat Monthly ";
        break;
	case "Yearly":
       $response["error"] = false;
       $response["message"] = "Repeat Yearly ";
        break;
   
}
	}
	
else{
	$response["error"] = true;
    $response["message"] = "Error Occured ";
}
            // echo json response
            echoRespnse(201, $response);
        });*/
		
$app->get('/search', 'authenticate', function() {
	
	            if(isset($_GET["event"])){
				$Event_name =($_GET["event"]);
			}
            $response = array();
            $db = new DbHandler();

            // fetching all user tasks
            $result = $db->getAllDay($Event_name);

            $response["error"] = false;
            $response["tasks"] = array();

            // looping through result and preparing tasks array
            while ($task = $result->fetch_assoc()) {
                $tmp = array();
				$tmp["start_date"] = $task["start_date"];
				$tmp["end_date"] = $task["end_date"];
                
                array_push($response["tasks"], $tmp);
            }

            echoRespnse(200, $response);
        });
		
		
		
$app->get('/searchByDate', 'authenticate', function() {
	
	            if(isset($_GET["date"])){
				$start_date =($_GET["date"]);
			}
            $response = array();
            $db = new DbHandler();

            // fetching all user tasks
            $result = $db->getAllEvents($start_date);

            $response["error"] = false;
            $response["tasks"] = array();

            // looping through result and preparing tasks array
            while ($task = $result->fetch_assoc()) {
                $tmp = array();
				$tmp["Event_name"] = $task["Event_name"];
                
                array_push($response["tasks"], $tmp);
            }

            echoRespnse(200, $response);
        });
	 
		
$app->get('/dayEvents', 'authenticate', function() {
            if(isset($_GET["start_date"])){
				$start_date = intval($_GET["start_date"]);
			}
            $response = array();
            $db = new DbHandler();

            // fetching all user tasks
            $result = $db->getAllUserEvents($start_date);

            $response["error"] = false;
            $response["tasks"] = array();

            // looping through result and preparing tasks array
            while ($events = $result->fetch_assoc()) {
                $tmp = array();
				$tmp["Event_name"] = $events["Event_name"];
                $tmp["start_date"] = $events["start_date"];
                $tmp["start_time"] = $events["start_time"];
                $tmp["end_date"] = $events["end_date"];
				$tmp["end_time"] = $events["end_time"];
                array_push($response["tasks"], $tmp);
            }

            echoRespnse(200, $response);
			
        });
		
$app->post('/Use_Device', 'authenticate', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('task'));

            $response = array();
            $task = $app->request->post('task');
            $db = new DbHandler();

            // creating new task
            $result = $db->useDevice( $task);
            if ($result == TRUE) {
            	
                $response["message"] = "TRUE";
                echoRespnse(201, $response);
            } else {
                $response["message"] = "FALSE";
                echoRespnse(200, $response);
            }          
        });
		
		
$app->get('/monthEvents', 'authenticate', function() {
            if(isset($_GET["month"])){
				$month = ($_GET["month"]);
			}
            $response = array();
            $db = new DbHandler();

            // fetching all user tasks
            $result = $db->getAllMonthEvents($month);

            $response["error"] = false;
            $response["tasks"] = array();

            // looping through result and preparing tasks array
            while ($events = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["start_date"] = $events["start_date"];
                array_push($response["tasks"], $tmp);
            }

            echoRespnse(200, $response);
			
        });
		
		
$app->get('/yearEvents', 'authenticate', function() {
            if(isset($_GET["year"])){
				$year = ($_GET["year"]);
			}
            $response = array();
            $db = new DbHandler();

            // fetching all user tasks
            $result = $db->getAllyearEvents($year);

            $response["error"] = false;
            $response["tasks"] = array();

            // looping through result and preparing tasks array
            while ($events = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["start_date"] = $events["start_date"];
                array_push($response["tasks"], $tmp);
            }

            echoRespnse(200, $response);
			
        });
		
		
$app->put('/changeName',  function() use ($app) {
	verifyRequiredParams(array('email','name','new_name'));
           
            $response = array();
			
            $email = $app->request->put('email');
			$name = $app->request->put('name');
			$new_name = $app->request->put('new_name');
            $db = new DbHandler();

            // fetch task
            $result = $db->changename($email,$name,$new_name);

             if ($result == TRUE) {
                $response["error"] = false;
                $response["message"] = "Updated Succesfully ";
            }else{
                $response["error"] = true;
                $response["message"] = "Error Occured";
            }
            // echo json response
            echoRespnse(201, $response);
        });
		
$app->put('/changeNumber',  function() use ($app) {
	verifyRequiredParams(array('email','mobile','new_number'));
           
            $response = array();
			
            $email = $app->request->put('email');
			$mobile = $app->request->put('mobile');
			$new_number = $app->request->put('new_number');
            $db = new DbHandler();

            // fetch task
            $result = $db->changenumber($email,$mobile,$new_number);

             if ($result == TRUE) {
                $response["error"] = false;
                $response["message"] = "Updated Succesfully ";
            }else{
                $response["error"] = true;
                $response["message"] = "Error Occured";
            }
            // echo json response
            echoRespnse(201, $response);
        });
		
$app->put('/changePassword',  function() use ($app) {
	verifyRequiredParams(array('email','password','new_password','confirm_password'));
           
            $response = array();
			
            $email = $app->request->put('email');
			$password = $app->request->put('password');
			$new_password = $app->request->put('new_password');
			$confirm_password = $app->request->put('confirm_password');
            $db = new DbHandler();
             
			if($new_password==$confirm_password)
			{
            // fetch task
            $result = $db->changepassword($email,$password,$new_password,$confirm_password);

             if ($result == TRUE) {
                $response["error"] = false;
                $response["message"] = "Password Updated Succesfully ";
            }else{
                $response["error"] = true;
                $response["message"] = "Error Occured";
            }
            // echo json response
            echoRespnse(201, $response);
			}
			else{
			     $response["error"] = true;
                $response["message"] = "new_password and confirm_password not matched";	
				echoRespnse(201, $response);
			}
			
        });
		
$app->post('/custom', function() use ($app) {
            
            verifyRequiredParams(array('start_date', 'start_time', 'end_date', 'end_time'));

            $response = array();

            // reading post params
            $start_date= $app->request->post('start_date');
            $start_time = $app->request->post('start_time');
			$end_date= $app->request->post('end_date');
			$end_time= $app->request->post('end_time');
			
			validateStart($start_date);
			validateEnd($end_date);

            $db = new DbHandler();
			
            $res = $db->custom($start_date, $start_time, $end_date, $end_time);
            if ($res == USER_CREATED_SUCCESSFULLY) {
                $response["error"] = false;
                $response["message"] = "You are successfully customized";
            } else if ($res == USER_CREATE_FAILED) {
                $response["error"] = true;
                $response["message"] = "Oops! An error occurred while customizing";
            } 
            // echo json response
            echoRespnse(201, $response);
        });
		

$app->get('/doRepeat', 'authenticate', function() {
            if(isset($_GET["repeat"])){
				$cus = ($_GET["repeat"]);
			}
            $response = array();
            $db = new DbHandler();

            // fetching all user tasks
            $result = $db->doRepeat($cus);

            $response["error"] = false;
            $response["tasks"] = array();

            // looping through result and preparing tasks array
            while ($events = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["cus"] = $events["cus"];
                array_push($response["tasks"], $tmp);
            }

            echoRespnse(200, $response);
			
        });	
		
$app->get('/customReminder', 'authenticate', function() {
            if(isset($_GET["CustomReminder"])){
				$bell = ($_GET["CustomReminder"]);
			}
            $response = array();
            $db = new DbHandler();

            // fetching all user tasks
            $result = $db->customReminder($bell);

            $response["error"] = false;
            $response["tasks"] = array();

            // looping through result and preparing tasks array
            while ($events = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["bell"] = $events["bell"];
                array_push($response["tasks"], $tmp);
            }

            echoRespnse(200, $response);
			
        });	
		
$app->get('/startReminder', 'authenticate', function() {
            if(isset($_GET["StartReminder"])){
				$note = ($_GET["StartReminder"]);
			}
            $response = array();
            $db = new DbHandler();

            // fetching all user tasks
            $result = $db->startReminder($note);

            $response["error"] = false;
            $response["tasks"] = array();

            // looping through result and preparing tasks array
            while ($events = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["note"] = $events["note"];
                array_push($response["tasks"], $tmp);
            }

            echoRespnse(200, $response);
			
        });	
		
$app->get('/language', 'authenticate', function() {
            if(isset($_GET["Language"])){
				$lan = ($_GET["Language"]);
			}
            $response = array();
            $db = new DbHandler();

            // fetching all user tasks
            $result = $db->language($lan);

            $response["error"] = false;
            $response["tasks"] = array();

            // looping through result and preparing tasks array
            while ($events = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["lan"] = $events["lan"];
                array_push($response["tasks"], $tmp);
            }

            echoRespnse(200, $response);
			
        });	
		
		
function verifyRequiredParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }
    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoRespnse(400, $response);
        $app->stop();
    }
}

/**
 * Validating email address
 */
function validateEmail($email) {
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Email address is not valid';
        echoRespnse(400, $response);
        $app->stop();
  }
}

function validateName($name) {
    $app = \Slim\Slim::getInstance();
    if (preg_match("/[^a-zA-Z'-]/", $name)){
        $response["error"] = true;
        $response["message"] = 'name is not valid';
        echoRespnse(400, $response);
        $app->stop();
  }
}

function validateMobile($mobile) {
    $app = \Slim\Slim::getInstance();
   // if ($mobile < 10 || $mobile > 10) {   
	   if (!preg_match("/^[7-9]{1}[0-9]{9}$/", $mobile)){
        $response["error"] = true;
        $response["message"] = "Mobile No. is not valid, Please Enter 10 Digit Mobile No.";
        echoRespnse(400, $response);
        $app->stop();
	   }
}

function validateStart($start_date) {
    $app = \Slim\Slim::getInstance();	
	   if (!preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])$/",$start_date)){
        $response["error"] = true;
        $response["message"] = "start_date is not valid.";
        echoRespnse(400, $response);
        $app->stop();
	   }
}

function validateEnd($end_date) {
    $app = \Slim\Slim::getInstance();	
	   if (!preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])$/",$end_date)){
        $response["error"] = true;
        $response["message"] = "end_date is not valid.";
        echoRespnse(400, $response);
        $app->stop();
	   }
}

function validatePassword($password) {
    $app = \Slim\Slim::getInstance();	
	   if(!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,12}$/', $password)) {
        $response["error"] = true;
        $response["message"] = "the password does not meet the requirements!. It has to be a number, a letter or one of the following: !@#$% and there have to be 8-12 characters ";
        echoRespnse(400, $response);
        $app->stop();
	   }
}

function validateStartTime($start_time) {
    $app = \Slim\Slim::getInstance();	
	   if(!preg_match("/(2[0-4]|[01][1-9]|10):([0-5][0-9])/", $start_time)) {
        $response["error"] = true;
        $response["message"] = "start_time is not valid.";
        echoRespnse(400, $response);
        $app->stop();
	   }
}

function validateEndTime($end_time) {
    $app = \Slim\Slim::getInstance();	
	   if(!preg_match("/(2[0-4]|[01][1-9]|10):([0-5][0-9])/", $end_time)) {
        $response["error"] = true;
        $response["message"] = "end_time is not valid.";
        echoRespnse(400, $response);
        $app->stop();
	   }
}

function validateMonth($month) {
    $app = \Slim\Slim::getInstance();	
	   if(!preg_match("/(0[1-9]|1[012])/", $month)) {
        $response["error"] = true;
        $response["message"] = "month is not valid.";
        echoRespnse(400, $response);
        $app->stop();
	   }
}

/*function validateYear($year) {
    $app = \Slim\Slim::getInstance();	
	   if(!preg_match("/(19|20)[0-9]{2}/", $year)) {
        $response["error"] = true;
        $response["message"] = "year is not valid.";
        echoRespnse(400, $response);
        $app->stop();
	   }
}*/


function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}

$app->run();
?>
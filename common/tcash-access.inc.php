<?php

//Generic function to return a TRUE if user is logged in, or FALSE if not logged in
//Checks are made to ensure that the user/password combination is still valid
function userIsLoggedIn()
{

    if (!isset($_SESSION['loggedIn']))
	{
        //ensure the email & password were completed
		if (!isset($_POST["user"]) or $_POST["user"] == "" or
			!isset($_POST["password"]) or $_POST["password"] == "")
		{
			$_SESSION["loginError"] = "Please fill in both fields.";
			return FALSE;
		}

        
		//scramble the password
		$password = md5($_POST["password"] . "TCASH");

		//Match user/password
		if (databaseContainsUser($_POST["user"], $password))
		{
			//user/password combination is OK
			$remVal = (isset($_POST['rememberme']) and $_POST['rememberme']=='remember' ? 'YES' : 'NO'); 
			setUserParams($_POST['user'], $password, $remVal);
		  	return TRUE;
		}
		else
		{
			//user/password combination is INVALID
			clearUserParams();
			$_SESSION["loginError"] = "The specified email address or password was incorrect.";
			return FALSE;
		}
	}

	//Even if the LoggedIn session variable is set, we still need to
	//check that the user record hasn't been deleted since the user logged in...
	safesessionstart();
	if (isset($_SESSION["loggedIn"]))
	{
		return databaseContainsUser($_SESSION["user"], $_SESSION["password"]);
	}
	else
	{
		return FALSE;
	}

}

function createSessionFromCookie()
{
	//session is not logged in... do we have a cookie set?
	if (isset($_COOKIE['session']))
	{
		return databaseContainsSession($_COOKIE['session'], $_COOKIE['uaddr']);
	}
	else
	{
		return FALSE;
	}
}


//Generic function to set the user SESSION parameters if login is successful
function setUserParams($user, $password, $remember)
{
	safesessionstart();
	$_SESSION["loggedIn"] = TRUE;
	$_SESSION["user"] = $user;
	$_SESSION["password"] = $password;
	$_SESSION['userIsAdmin'] = userHasRole('Admin');

	//If user ticked "remember me" store a cookie & record token in SQL against the user
	if ($remember == 'YES')
	{
		srand((double)microtime()*1000000);
		$unique = rand();
		$sessionHash = md5($user . $unique . $_SERVER['REMOTE_ADDR'] . 'MTNX');

		//Attempt to store in SQL 
		if (storeCookieToDB($sessionHash, $user, $_SERVER['REMOTE_ADDR']))
		{
			//If successful, create browser cookies
			setcookie("uaddr", $unique, time() + 3600 * 24 * 30, '/tcash/');
			setcookie("session", $sessionHash, time() + 3600 * 24 * 30, '/tcash/');
		}
		else
		{
			//Failed to store in SQL. Clear parameters & fail.
			clearUserParams();			
			$error = 'Error storing remember-me values';
			include $_SERVER['DOCUMENT_ROOT'] . '/includes/error.html.php';
			exit();
		}
	}
	else
	{
		//login is still successful, but not remembered.
	}
	unset($_SESSION["loginError"]);
}


//Generic function to store the cookie session hash into the SQL database
function storeCookieToDB($sessionHash, $user, $ipaddr)
{
	//look for any current sessions with exact same session id
	try
	{
		$pdo = tcashConnect();	
		$sql = 'SELECT count(*) AS cnt FROM session WHERE sessionid = :sid';
		$s = $pdo->prepare($sql);
		$s->bindValue(':sid', $sessionHash);
		$s->execute();
	}
	catch (PDOException $e)
	{
		$error = 'Error locating existing sessions for this user';
		include $_SERVER['DOCUMENT_ROOT'] . "/includes/error.html.php";
		exit();		
	}

	//only add a new session record if the exact same session token is not already present
	$row = $s->fetch();

	if ($row['cnt'] == 0)
	{
		try
		{
			$pdo = tcashConnect();	
			$sql = "INSERT INTO session (userid, location, sessionid) VALUES (:user, :loc, :sid)";
			$s = $pdo->prepare($sql);
			$s->bindValue(":user", $user);
			$s->bindValue(":loc", $ipaddr);
			$s->bindValue(":sid", $sessionHash);
			$s->execute();
		}
		catch (PDOException $e)
		{
			$error = "Error storing session information ";
			include $_SERVER['DOCUMENT_ROOT'] . "/includes/error.html.php";
			exit();
		}
	}

	return TRUE;
}


//Generic function to clear the cookie session from the SQL database
function clearCookieFromDB($session)
{
	try
	{
		$pdo = tcashConnect();	
		$sql = 'DELETE FROM session WHERE sessionid = :sid';
		$s = $pdo->prepare($sql);
		$s->bindValue(':sid', $session);
		$s->execute();
	}
	catch (PDOException $e)
	{
		$error = "Error clearing session information";
		include $_SERVER['DOCUMENT_ROOT'] . "/includes/error.html.php";
		exit();
	}
	
	return TRUE;
}



//Generic function to clear the user paramters if login is unsuccessful, or logout is successful
function clearUserParams()
{
	safesessionstart();

	//If session cookie is set, clear them all down, including removing from SQL database
	if (isset($_COOKIE["session"]))
	{
		clearCookieFromDB($_COOKIE['session']);
		setcookie("session", null, -1, '/tcash/');
		setcookie("uaddr", null, -1, '/tcash/');
	}

	//Clear rest of session variables
	unset($_SESSION["loggedIn"]);
	unset($_SESSION["user"]);
	unset($_SESSION["password"]);
	unset($_SESSION["userIsAdmin"]);
	return TRUE;
}


//Generic function to log a user out of the application...
function userLogOut()
{
	//If user clicked log out...
	if (isset($_GET["logout"]))
	{
		if (clearUserParams())
		{
			return TRUE;
		}
	}
	else
	{
		return FALSE;
	}
}


//Generic function to connect to the tcash database and search for the 
//username/password combination. Returns TRUE if found, or FALSE if not.
function databaseContainsUser($user, $password)
{
	try
	{
		$pdo = tcashConnect();	
		$sql = "SELECT COUNT(*) FROM user WHERE id = :user AND password = :password";
		$s = $pdo->prepare($sql);
		$s->bindValue(":user", $user);
		$s->bindValue(":password", $password);
		$s->execute();
	}
	catch (PDOException $e)
	{
		return FALSE;
	}

	$row = $s->fetch();

	if ($row[0] > 0)
	{
		return TRUE;
	}
	else
	{	
		return FALSE;
	}
}

//Generic function to connect to the tcash database and search for the 
//session value. Returns TRUE if found, or FALSE if not.
function databaseContainsSession($session, $unique)
{
	//Search for session based on session id supplied
	try
	{
		$pdo = tcashConnect();	
		$sql = "SELECT s.userid, s.location, u.password FROM session s " . 
				"INNER JOIN user u ON u.id = s.userid WHERE s.sessionid = :sid";
		$s = $pdo->prepare($sql);
		$s->bindValue(":sid", $session);
		$s->execute();
	}
	catch (PDOException $e)
	{
		return FALSE;
	}

	$row = $s->fetch();
	
	//if we have found a valid session row, we will try to restore it.
	if (!is_null($row['userid']) and !is_null($row['location']))
	{

		//first, check that the session data is valid based on:
		//The USERID returned from SQL + the UNIQUEID in a cookie + the current IPADDR of the browser + a CONSTANT
		if ($session == md5($row['userid'] . $unique . $_SERVER['REMOTE_ADDR'] . 'MTNX'))		
		{
			$_SESSION['loggedin'] = TRUE;	
			$_SESSION['user'] = $row['userid'];
			$_SESSION['password'] = $row['password'];
			$_SESSION['userIsAdmin'] = userHasRole('Admin');
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	else
	{	
		//Have not found a valid session record.
		return FALSE;
	}
}


//Generic function for checking if a particular user has a particular role
function userHasRole($role)
{
	try
	{
	  $pdo = tcashConnect();	
	  $sql = "SELECT COUNT(*) FROM userrole " . 
		  "WHERE userrole.userid = :user AND userrole.role = :role ";
	  $s = $pdo->prepare($sql);
	  $s->bindValue(":user", $_SESSION["user"]);
	  $s->bindValue(":role", $role);
	  $s->execute();
	}
	catch (PDOException $e)
	{
		$error = "Error searching for user roles.";
		include $_SERVER['DOCUMENT_ROOT'] . '/includes/error.html.php';
		exit();
	}

	$row = $s->fetch();

	if ($row[0] > 0)
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}


//Generic function for registering users with myHappyPlace
function userRegistration()
{
	if (isset($_POST["action"]) and $_POST["action"] == "register")
	{

		//first, check all fields were completed
		if (!isset($_POST['user']) or $_POST['user'] == '' 
			or !isset($_POST['fullname']) or $_POST['fullname'] == '' 
			or !isset($_POST['email']) or $_POST['email'] == '' 
			or !isset($_POST['password']) or $_POST['password'] == '' 
			or !isset($_POST['passwordcheck']) or $_POST['passwordcheck'] == '')
		{
			$_SESSION['registerError'] = 'You must complete all fields in the registration form';
			return FALSE;
		}

		//then, check if the two passwords submitted are the same
		if ($_POST['password'] != $_POST['passwordcheck'])
		{
			$_SESSION['registerError'] = 'The two supplied passwords did not match';
			return FALSE;
		}

		//Now, check there isn't already a user with that name
		try
		{
			$pdo = tcashConnect();	
			$sql = 'SELECT count(id) AS cnt FROM user WHERE id = :user';
			$s = $pdo->prepare($sql);
			$s->bindValue(':user', $_POST['user']);
			$s->execute();
		}
		catch (PDOException $e)
		{
			$_SESSION['registerError'] = 'An error occurred whilst verifying the ' . 
											'availability of the requested user name.';
			return FALSE;
		}

		//How many existing users with that name did we find? >0? 
		$row = $s->fetch();
		if ($row['cnt'] > 0)
		{
			$_SESSION['registerError'] = 'A user with this name already exists; try a different name.';
			return FALSE;
		}

		//then, hash the password
		$hashPW = md5($_POST['password'] . 'tcash');
		
		//next, add the user to the database
		try
		{
			$pdo = tcashConnect();				
			$sql = 'INSERT INTO user (id, fullname, password, email) VALUES (:user, :fname, :pword, :email)';
			$s = $pdo->prepare($sql);
			$s->bindValue(':user', $_POST['user']);
			$s->bindValue(':fname', $_POST['fullname']);
			$s->bindValue(':pword', $hashPW);
			$s->bindValue(':email', $_POST['email']);
			$s->execute();		
		}
		catch (PDOException $e)
		{
			$_SESSION['registerError'] = 'Error storing registration details. Try again later.';
			return FALSE;
		}

		//now ensure the user has appropriate group memberships
		try {
			//add new group with same name as user
			$pdo = tcashConnect();
			$sql = 'INSERT INTO hpgroup (id, description, moderator, sortorder) ' . 
					'VALUES (:id, :desc, :mod, :sort) ';
			$s = $pdo->prepare($sql);
			$s->bindValue(':id', $_POST['user']);
			$s->bindValue(':desc', $_POST['user'] . ' personal group');
			$s->bindValue(':mod', $_POST['user']);
			$s->bindValue(':sort', 3);
			$s->execute();

			//add relationship between user and their personal group
			$sql = 'INSERT INTO usergroup (userid, groupid) VALUES (:user, :group)';
			$s = $pdo->prepare($sql);			
			$s->bindValue(':user', $_POST['user']);
			$s->bindValue(':group', $_POST['user']);
			$s->execute();

			//add relationship between user and the Everyone group
			$s = $pdo->prepare($sql);			
			$s->bindValue(':user', $_POST['user']);
			$s->bindValue(':group', 'Everyone');
			$s->execute();

		} catch (PDOException $e) {
			$_SESSION['registerError'] = 'Error setting up default groups. Contact admin.';
			return FALSE;
		}

		//finally, set the login parameters so the new user is now logged on
		//but only if we can now find the user in the database, like usual
		if (databaseContainsUser($_POST["user"], $hashPW))
		{
			//user/password combination is OK
			setUserParams($_POST['user'], $hashPW, 'YES');
		  	return TRUE;
		}
		else
		{
			//user/password combination is INVALID
			clearUserParams();
			$_SESSION["loginError"] = "The registration failed. Try again later.";
			return FALSE;
		}
		return TRUE;
	}
}

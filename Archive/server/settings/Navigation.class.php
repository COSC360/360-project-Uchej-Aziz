<?php 

class Navigation {
	protected $titles = array(
		"" => array("Main"),
		"login" => array("Login"),
		"register" => array("Register", "Register Confirm"),
		"logout" => array("Logout"),
		"restore" => array("Restore", "Restore Confirm"),
		"t" => array("Thread", "Create Thread", "Create Post"),
		"account" => array("Account", "Account Edit"),
		"notifications" => array("Notifications"),
		"admin" => array("Admin Dashboard", "Admin"),
		"search" => array("Search")
	);
	protected $url = array();

	public function __construct() {
    $action = @htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8');
    $action = filter_var($action, FILTER_SANITIZE_URL);
    
    $action = substr($action, 1);
    $url = explode("/", $action);
		$this->url = $url;
	}

	public function getTitle() : string {
		
		if (count($this->url) == 1) {
			if (isset($this->titles[$this->url[0]][0]))
				return $this->titles[$this->url[0]][0];
		} else if (count($this->url) == 2) {
			if (isset($this->titles[$this->url[0]][0]))
				if (is_numeric($this->url[1]) && $this->url[0] == "account")
					return $this->titles[$this->url[0]][0];
				else if (isset( $this->titles[$this->url[0]][1]))
					return $this->titles[$this->url[0]][1];
				else "Page Not Found";
		} else if (count($this->url) == 3) {
			if (isset($this->titles[$this->url[0]][2])) { 
				if (is_numeric($this->url[2]) && $this->url[0] == "t") {
					return $this->url[0].'/'.$this->url[1].' Post';
				}
				return $this->titles[$this->url[0]][2];
			}
			else "Page Not Found";
		}
		
		return 'Page Not Found';
	}

	public function show() : string {

		$auth = isset($_SESSION['IS_AUTHORIZED']);
		$admin = isset($_SESSION['IS_ADMIN']);

		if (count($this->url) == 1) {

			switch ($this->url[0]) {
				case "login": {
					if (!$auth)
						return PUBLIC_DIRECTORY.'/login.php';
					return PUBLIC_DIRECTORY.'/layout/main.php';
				};
				case "register": {
					if (!$auth)
						return PUBLIC_DIRECTORY.'/register.php';
					return PUBLIC_DIRECTORY.'/layout/main.php';
				};
				case "restore": {
					if (!$auth)
						return PUBLIC_DIRECTORY.'/restore.php';
					return PUBLIC_DIRECTORY.'/layout/main.php';
				};
				case "account": {
					if (!$auth)
						return PUBLIC_DIRECTORY.'/login.php';
					return PUBLIC_DIRECTORY.'/account.php';
				};
				case "notifications": {
					if (!$auth)
						return PUBLIC_DIRECTORY.'/login.php';
					return PUBLIC_DIRECTORY.'/notifications.php';
				};
				case "search": {
					return PUBLIC_DIRECTORY.'/search.php';
				}
				case "": {
					return PUBLIC_DIRECTORY.'/layout/main.php';
				}
				case "logout": {
					if (!$auth)
						return PUBLIC_DIRECTORY.'/login.php';
					session_destroy();
					return PUBLIC_DIRECTORY.'/layout/main.php';
				}
				case "admin": {
					if (!$auth)
						return PUBLIC_DIRECTORY.'/login.php';
					else if ($admin && !$_SESSION['IS_ADMIN'])
						return PUBLIC_DIRECTORY.'/login.php';
					else 
						return PUBLIC_DIRECTORY.'/admin.php';
				}
				default: return PUBLIC_DIRECTORY.'/error.php';
			}
		} else if (count($this->url) == 2) {

			switch ($this->url[0]) {
				case "register": {
					if (!$auth)
						return PUBLIC_DIRECTORY.'/register-confirm.php';
					return PUBLIC_DIRECTORY.'/layout/main.php';
				};
				case "restore": {
					if (!$auth)
						return PUBLIC_DIRECTORY.'/restore-confirm.php';
					return PUBLIC_DIRECTORY.'/layout/main.php';
				};
				case "t": {
					if ($this->url[1] == "create") {
						if (!$auth)
							return PUBLIC_DIRECTORY.'/login.php';
						return PUBLIC_DIRECTORY.'/thread-create.php';
					} else if ($this->url[1] != "create") {
						return PUBLIC_DIRECTORY.'/thread.php';
					}
					return PUBLIC_DIRECTORY.'/layout/main.php';
				}
				case "admin": {
					if (!$auth)
						return PUBLIC_DIRECTORY.'/login.php';
					else if ($admin && !$_SESSION['IS_ADMIN'])
						return PUBLIC_DIRECTORY.'/login.php';
					else if ($this->url[1] == "users")
						return PUBLIC_DIRECTORY.'/admin-users.php';
					else if ($this->url[1] == "threads")
						return PUBLIC_DIRECTORY.'/admin-threads.php';
					return PUBLIC_DIRECTORY.'/layout/main.php';
				};
				case "account": {
					if (!$auth)
						return PUBLIC_DIRECTORY.'/login.php';
					if (!is_numeric($this->url[1]))
						return PUBLIC_DIRECTORY.'/account-settings.php';
					return PUBLIC_DIRECTORY.'/account.php';
				}
				default: return PUBLIC_DIRECTORY.'/error.php';
			}
		} else if (count($this->url) == 3) {
			
			switch ($this->url[0]) {
				case "t": {
					if ($this->url[2] == "create-post") {
						if (!$auth)
							return PUBLIC_DIRECTORY.'/login.php';
						return PUBLIC_DIRECTORY.'/post-create.php';
					} else if (is_numeric($this->url[2])) {
						if (!$auth) {
							return PUBLIC_DIRECTORY.'/login.php';
						}
						return PUBLIC_DIRECTORY.'/post.php';
					}
				}
				default: return PUBLIC_DIRECTORY.'/error.php';
			}
		}
		return PUBLIC_DIRECTORY.'/error.php';
	}
}

?>
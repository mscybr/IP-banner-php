# IP-banner-php
a simple PHP IP banner class for repeated requests at sensitive areas. 




# How to use:
**Step 1:** require the file
```
require_once 'ip_banner.php';
```
**Step 2:** connect to a database using PDO
```
    $db = new PDO(DB_dsn, DB_user, DB_password);
```
**Step 3:** create a new instance of the ip_banner using the PDO object
```
$banner = new ip_banner($db);
```
**Step 4:** start tracking request in sensitive areas
```
// a login request here
$banned = $banner->track_request_attempts();

if( $banned ){
  die("your IP address has been blocked");
}

```
parameters for the method 'track_request_attempts'
| Parameter                 | Type          | Default       | Description   |	
| :------------------------ |:-------------:| :-------------| :-------------|
| attempts_seperation	       |	INT         |    0          |  an additional variable to seperate attempts ( for example : 0 is for user login attempts, 1 is for admin login attempts )             |
| max_attempts	       |	INT         |    10          |      maximum attempts before banning        |
| check_duration	       |	INT         |    10          |      duration in minutes for which the maximum attempts occur     |
| ban_duration	       |	INT         |    10          |      duration in minutes for which to ban the ip address     |





# Track multiple requests:
tracking multiple requests can be done by using the attempts_seperation parameter, passing different integers will seperate the request trackers from each others
```
// a login request here
$banned_login = $banner->track_request_attempts(0);
if( $banned_login ){
  die("your IP address has been blocked");
}


// a register request here
$banned_register = $banner->track_request_attempts(1);
if( $banned_register ){
  die("your IP address has been blocked");
}

more requests and trackers....
```


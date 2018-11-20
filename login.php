<?php session_start(); ?>
<?php
header("content-type:text/html;charset=utf-8");
if(!isset($_POST["submit"])) {
	echo "Permission denied";
}

$username = $_POST["username"];
$password = $_POST["password"];

// echo "<br/>".$username."-".$password;

include('connect.php');

global $db;
$sql =<<<EOF
      SELECT * FROM lunch_user WHERE name='$username' AND password='$password';
EOF;

$ret = $db->query($sql);

$success = false;
while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
	$_SESSION['userid'] = $row['id'];
	$_SESSION['username'] = $row['name'];
	$success = true;
	break;
   }

	if($success) {
		header("location:main_page.php");
	} else {
		echo "<script> {window.alert('Error');location.href='index.html'} </script>";
	}
   $db->close();
?>
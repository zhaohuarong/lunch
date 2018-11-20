<?php session_start(); ?> 
<html>
<body>
<?php 
ini_set('date.timezone','Asia/Shanghai');
header("content-type:text/html;charset=utf-8");
?>

Welcome <?php echo $_SESSION['username']."---".$_SESSION['userid']."<br/>"; ?>
<form method="post" action="main_page.php">
	<input type="submit" name="y" value="点餐"/>
	<input type="submit" name="n" value="取消" />
</form>
<br/>

<?php
include('connect.php');
global $db;
if(in_array("点餐", $_POST))
{
	// insert db
	// echo "yyyyyyyyyyyyyyyyyy<br/>";
	insert_ret($_SESSION['userid'], 1);
}
else if(in_array("取消", $_POST))
{
	// insert db
	// echo "nnnnnnnnnnnnnn<br/>";
	insert_ret($_SESSION['userid'], 0);
}
else
{
	//echo "000000<br/>";
}

//operation 为1表示点餐，为0表示取消点餐
function insert_ret($user_id, $operation)
{
	$t=time();
	$t_string = date("Y-m-d H:i:s",$t);
	//echo $user_id."-".$t_string;

	$sql =<<<EOF
      INSERT INTO lunch_ret (user_id,time,operation) VALUES ($user_id, '$t_string', $operation);
EOF;
	global $db;
	$ret = $db->exec($sql);
	if(!$ret)
	{
		echo $db->lastErrorMsg();
	}
	else
	{
		//echo "insert created successfully\n";
	}


	$sql =<<<EOF
      UPDATE lunch_user SET status = $operation where ID=$user_id;
EOF;
	$ret = $db->exec($sql);
	if(!$ret)
	{
		echo $db->lastErrorMsg();
	}
	else
	{
		//echo $db->changes(), "updated successfully\n";
	}
}

$current_id = $_SESSION['userid'];
$sql =<<<EOF
      SELECT * FROM lunch_user;
EOF;
$ret = $db->query($sql);

$count = 0;
$user_name_array = array();
$user_status_array = array();
while($row = $ret->fetchArray(SQLITE3_ASSOC) )
{
	$user_name_array[$row['id']]=$row['name'];
	$user_status_array[$row['id']]=$row['status'];
	if($row['status'] == 1)
	{
		$count ++;
	}
}

echo "<font color=red size=5>总计：".$count."</font>";

echo "<table border='1'>";
foreach($user_name_array as $x=>$x_value)
{
	$status_text = "<font color='red'><B>未点</B></font>";
	if($user_status_array[$x] == 1)
	{
		$status_text = "<font color='green'><B>已点</B></font>";
	}
	echo "<tr>";
	echo "<td>$x</td><td>$x_value</td><td>$status_text</td>";
	echo "</tr>";
}
echo "</table>";


/////////////////

$sql =<<<EOF
      SELECT * FROM lunch_ret ORDER BY time DESC LIMIT 0,16
EOF;
$ret = $db->query($sql);
$history_array = array();
$i = 0;
while($row = $ret->fetchArray(SQLITE3_ASSOC) )
{
	$history_array[$i] = array($row['id'], $row['user_id'], $row['time'], $row['operation']);
	$i ++;
}

echo "<br/><h3>最新动态</h3>";
echo "<table border='1'>";
for($i = 0; $i < count($history_array); $i ++)
{
	echo "<tr>";
	$array_item = $history_array[$i];
	for($j = 0; $j < count($array_item); $j ++)
	{
		$text = "";
		if($j == 0)
		{
			continue;
		}
		else if($j == 1)
		{
			$text = $user_name_array[$array_item[$j]];
		}
		else if($j == 3)
		{
			if($array_item[$j] == 0)
			{
				$text = "取消";
			}
			else if($array_item[$j] == 1)
			{
				$text = "点餐";
			}
		}
		else
		{
			$text = $array_item[$j];
		}
		echo "<td>";
		echo $text;
		echo "</td>";
	}
	echo "</tr>";
}
echo "</table>";

$db->close();
?>
</body>
</html>
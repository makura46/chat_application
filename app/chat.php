<?php

include('./database.php');

session_start();

if (empty($_SESSION['room_name'])) {
	header('Location: ./signin.php');
	exit();
}

if (isset($_POST['button']) && !empty($_POST['chat'])) {
	$room = $_SESSION['room_name'];
	$speaker_id = $_SESSION['ID'];
	$speaker_name = $_SESSION['NAME'];
	$text = $_POST['chat'];
	$date = date('Y-m-d');
	$time = date('H:i:s');
	$pdo = DB_connection();
	$sql = 'INSERT INTO ' . $room . ' (speaker_id, speaker_name, log, speak_date, speak_time) VALUES (?, ?, ?, ?, ?)';
	$stmt = $pdo->prepare($sql);
	$bool = $stmt->execute(array($speaker_id, $speaker_name, $text, $date, $time));
	if (!$bool) {
		echo 'error';
	}
	DB_kill();	// データベース切断
	header('Location: ./chat.php');		// 更新した時のデータの再送をしないため
}


?>

<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="css/chat.css">
		<meta charset="utf-8">
		<title><?php echo $_SESSION['your']; ?></title>
	</head>
	<body>
		<div id="contents">
		<?php
		$room = $_SESSION['room_name'];
		$pdo = DB_connection();

		$sql = 'SELECT * FROM ' . $room . ' ORDER BY id DESC';
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			if ($row['speaker_id'] == $_SESSION['ID']) {
?>
	<div align="right">
	<div align="left" id="my"><font size="4"><?php echo htmlspecialchars($row['log'], ENT_QUOTES); ?></font><br><font size="2"><?php echo $row['speaker_name'] . " " . $row['speak_date'] . " " . $row['speak_time']; ?></font></div>		<!-- 自分が発言したものは右に -->
	</div>
<?php
			} else {
?>
	<div align="left">	
	<div align="left" id="you"><font size="4"><?php echo htmlspecialchars($row['log'], ENT_QUOTES); ?></font><br><font size="2"><?php echo $row['speaker_name'] . " " . $row['speak_date'] . " " . $row['speak_time']; ?></font></div>		<!-- 相手の発言は左に -->
	</div>
<?php
			}
		}
		DB_kill();
?>
		</div>
		<div id="footer">
		<div><form action="" method="post"><input type="text" name="chat" id="form"><input type="submit" value="送信" name="button" id="button"></form></div>	<!-- 送信フォーム -->
		<div id="foot"><form action="./menu.php" method="post"><input type="submit" value="チャット相手を選ぶ" id="return"></form></div>	<!-- 送信フォーム -->
		</div>
	</body>
</html>

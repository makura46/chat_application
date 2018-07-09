<?php

include('./database.php');

session_start();

if (!isset($_SESSION['ID'])) {
	header('Location: ./signin.php');
	exit();
}

if (isset($_POST['check']) && !empty($_POST['yours'])) {
	$chat_room = "";
	if (strcmp($_SESSION['ID'], $_POST['yours']) < 0) {
		$chat_room = $_SESSION['ID'] . '_' . $_POST['yours'];
	} else if (strcmp($_SESSION['ID'], $_POST['yours']) > 0) {
		$chat_room = $_POST['yours'] . '_' . $_SESSION['ID'];
	} else {
		echo error;
		exit();
	}
	$pdo = DB_connection();
	$stmt = $pdo->prepare('SELECT chat_name FROM chat WHERE chat_name = ?');	// 一回でもチャットしたかどうか
	$stmt->execute(array($chat_room));
	if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$stmt = $pdo->prepare('INSERT INTO chat (chat_name, chat_table_name) VALUES (?, ?)');
		$bool = $stmt->execute(array($chat_room, $chat_room . '_table'));
		if (!$bool) {
			$_SESSION['error'] = 'first_error';
			header('Location: ./esc.php');
			exit();
		}
		$sql = 'CREATE TABLE ' . $chat_room . '_table (id BIGINT PRIMARY KEY AUTO_INCREMENT, speaker_id VARCHAR(255) NOT NULL, speaker_name VARCHAR(255) NOT NULL, log TEXT NOT NULL, speak_date DATE NOT NULL, speak_time TIME NOT NULL)';
		$stmt = $pdo->query($sql);
		if (!$stmt) {		// クエリが失敗した場合
			$_SESSION['error'] = 'second error';
			header('Location: ./esc.php');
			exit();
		}
	}
	$stmt = $pdo->prepare('SELECT * FROM user_info WHERE user_id = ?');
	$stmt->execute(array($_POST['yours']));
	if ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		$_SESSION['your'] = $row['user_name'];
	$_SESSION['room_name'] = $chat_room . '_table';
	DB_kill();
	header('Location: ./chat.php');
	exit();
}


?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>チャット相手選択</title>
	</head>
	<body>
		<center>
			<form action="" method="post">
				<fieldset style="display: inline;">
					<legend>誰とチャットする？</legend>
					<select name="yours" size="5">
<?php 
$pdo = DB_connection();		// データベース接続
$stmt = $pdo->prepare('SELECT * FROM user_info');
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {		// 登録されているユーザを表示
	if ($row['user_id'] != $_SESSION['ID']) {	// 自分を除く
		echo '<option value="' . $row['user_id'] . '">'. $row['user_name'] . '</option>';
	}
}
DB_kill();		// データベース切断
?>
					</select><br>
					<input type="submit" name="check" value="チャット開始">

				</fieldset>
			</form>
			<form action="./signout.php" method="post">
				<input type="submit" value="ログアウト">
			</form>
		</center>
	</body>
</html>

<?php
include('./database.php');

session_start();
if (isset($_SESSION['ID'])) {	// セッションがあればメニュー画面へ
	header('Location: ./menu.php');
	exit();
}

$errorMessage = '';	// エラーメッセージ

if (isset($_POST['submit'])) {
	if (empty($_POST['user_id']) || empty($_POST['user_password']) || empty($_POST['user_pass']) || empty($_POST['user_name'])) {
		$errorMessage = '入力されていない項目があります';
	} else if ($_POST['user_password'] != $_POST['user_pass']) {
		$errorMessage = 'パスワードが一致しません';
	} else {
		$user_id = htmlspecialchars($_POST['user_id'], ENT_QUOTES);	 // 攻撃を回避
		$user_password = password_hash(htmlspecialchars($_POST['user_password'], ENT_QUOTES), PASSWORD_DEFAULT);		// 攻撃を回避
		$user_name = htmlspecialchars($_POST['user_name'], ENT_QUOTES);	// 攻撃を回避

		$pdo = DB_connection();		// データベースに接続

		$stmt = $pdo->prepare('SELECT * FROM user_info WHERE user_id = ?');
		$stmt->execute(array($user_id));

		if (!($stmt->fetch(PDO::FETCH_ASSOC))) {
			$stmt = $pdo->prepare('INSERT INTO user_info VALUES (?, ?, ?, ?)');
			$datetime = date('Y/m/d H:i:s');	// 現在時刻を取得

			$stmt->execute(array($user_id, $user_password, $user_name, $datetime));
			$errorMessage = '登録できました';

		} else {
			$errorMessage = 'そのログインIDはすでに登録されています';
		}

		DB_kill();	// データベース切断
	}
}

?>

<html>
	<head>
		<meta charset="utf-8">
		<title>新規作成</title>
	</head>
	<body>
		<center>
			<form action="" method="post">
				<fieldset style="display: inline;">
					<legend>新規作成</legend>
					<div><font color="red"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
					<table>
						<tr>
						<td style="text-align: right;"><label for="user_id">ログインID:</label></td>
						<td><input type="text" id="user_id" name="user_id"></td>
						</tr>
						<tr>
						<td style="text-align: right;"><label for="user_password">パスワード:</label></td>
						<td><input type="password" id="user_password" name="user_password"></td>
						</tr>
						<tr>
						<td style="text-align: right;"><label for="user_pass">パスワード（確認用）:</label></td>
						<td><input type="password" id="user_pass" name="user_pass"></td>
						</tr>
						<tr>
						<td style="text-align: right;"><label for="user_name">ユーザ名:</label></td>
						<td><input type="text" id="user_name" name="user_name"></td>
						</tr>
					</table>
					<input type="submit" name="submit" value="新規作成">
				</fieldset>
			</form>
			<form action="./signin.php" method="post">
				<input type="submit" value="ログイン画面へ">
			</form>
		</center>
	</body>
</html>


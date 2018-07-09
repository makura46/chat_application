<?php
include('./database.php');

session_start();
if (isset($_SESSION['ID'])) { // セッションが残っていればメニュー画面へ
	header('Location: ./menu.php');
	exit();
}

$errorMessage = ''; // エラーメッセージ

if (isset($_POST['signin'])) {
	if (empty($_POST['user_id']) || empty($_POST['user_password'])) {
		$errorMessage = 'ユーザ名かパスワードが空白です';
	} else {
		// 入力値を変数に格納
		$user_id = htmlspecialchars($_POST['user_id'], ENT_QUOTES);
		$user_password = htmlspecialchars($_POST['user_password'], ENT_QUOTES);

		$pdo = DB_connection(); // データベースに接続
		$stmt = $pdo->prepare('SELECT * FROM user_info WHERE user_id = ?');
		$stmt->execute(array($user_id));
		if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			if ($row['user_id'] == $user_id && password_verify($user_password, $row['user_password'])) { // ユーザIDとパスワードが一致した場合
				$_SESSION['ID'] = $user_id;
				$_SESSION['NAME'] = $row['user_name'];
				header('Location: ./menu.php');
				exit();
			} else {	// ユーザIDとパスワードが一致しない場合
				$errorMessage = 'ユーザIDかパスワードが間違っています';
			}
		} else {	// ユーザIDが存在しない場合
			$errorMessage = 'ユーザIDかパスワードが間違っています';
		}
		DB_kill($pdo); // データベース切断
	}
}



?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>signin</title>
	</head>
	<body>
		<center>
			<form action="" method="post">
				<fieldset style="display: inline;">
					<legend>ログイン</legend>
					<div>
					<font color="red"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font>
					</div>
					<table>
						<tr>
						<td style="text-align: right;"><label for="user_id">ログインID:</label></td>
						<td><input type="text" id="user_id" name="user_id"></td> <!-- ログインID入力フォーム  -->
						</tr>
						<tr>
						<td style="text-align: right;"><label for="user_password">パスワード:</label></td>
						<td><input type="password" id="user_password" name="user_password"></td> <!-- パスワード入力フォーム -->
						</tr>
					</table>
					<input type="submit" name="signin" value="送信"> <!-- 送信 -->
				</fieldset>
			</form>
			<form action="./signup.php" method="post">
				<input type="submit" value="新規作成"> <!-- ユーザ作成 -->
			</form>
		</center>
	</body>
</html>


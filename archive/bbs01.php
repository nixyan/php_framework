<?php
/**
 * Legacy PHP
 * 一つのファイルに全ての処理がつまっているためメンテナンス性が低く、
 * 複数人での作業にも向かない
 * 次のステップでは、ロジックとビューの分離を行う
 */

//データベースに接続
//ホスト名(Dockerのコンテナ名)、ユーザ名、パスワード、データベース名
$link = mysqli_connect('db', 'root', 'passw0rd', 'oneline_bbs');
if (!$link) {
  die('データベースに接続できません:' . mysql_error());
  echo 'データベース接続エラー';
}
//mysql_select_db('oneline_bbs', $link);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  //名前が正しく入力されているかチェック
  $name = null;
  if (!isset($_POST['name']) || !strlen($_POST['name'])) {
    $errors['name'] = '名前を入力してください';
  } else if (strlen($_POST['name'] > 40)) {
    $errors['name'] = '名前は40文字以内で入力してください';
  } else {
    $name = $_POST['name'];
  }
  //ひとことが正しく入力されているかチェック
  $comment = null;
  if (!isset($_POST['comment']) || !strlen($_POST['comment'])) {
    $errors['comment'] = 'ひとことを入力してください';
  } else if (strlen($_POST['comment'] > 200)) {
    $errors['comment'] = 'ひとことは200文字以内で入力してください';
  } else {
     $comment = $_POST['comment'];
  }

  //エラーがなければ保存
  if (count($errors) === 0) {
    $sql = "INSERT INTO post (name, comment, created_at) VALUES ('"
    . mysqli_real_escape_string($link, $name) . "', '"
    . mysqli_real_escape_string($link, $comment) . "', '"
    . date('Y-m-d H:i:s') . "')";

    //保存する
    mysqli_query($link, $sql);
    //var_dump(mysqli_error($link));

    header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
  }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>ひとこと掲示板</title>
</head>
<body>
  <h1>ひとこと掲示板</h1>
  <form action="bbs01.php" method="post">
    <?php if (count($errors)) { ?>
      <ul class="error_list">
        <?php foreach ($errors as $error) { ?>
          <li>
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
          </li>
        <?php } ?>
      </ul>
    <?php } ?>
    名前: <input type="text" name="name" /><br>
    ひとこと： <input type="text" name="comment" size="60"><br>
    <input type="submit" name="submit" value="送信">
  </form>

  <?php
  //投稿された内容を取得するSQLを作成して結果を取得
  $sql = 'SELECT * FROM post ORDER BY created_at DESC';
  $result = mysqli_query($link, $sql);
  ?>
  <?php if ($result !== false && mysqli_num_rows($result)) { ?>
    <ul>
      <?php while ($post = mysqli_fetch_assoc($result)) { ?>
        <li>
        <?= htmlspecialchars($post['name'], ENT_QUOTES, 'UTF-8') ?>
        <?= htmlspecialchars($post['comment'], ENT_QUOTES, 'UTF-8') ?>
        - <?= htmlspecialchars($post['created_at'], ENT_QUOTES, 'UTF-8') ?>
        </li>
      <?php } ?>
    </ul>
  <?php } ?>

  <?php
  //取得結果を解放して接続を閉じる
  mysqli_free_result($result);
  mysqli_close($link);
  ?>
</body>
</html>
<?php
/**
 * Logic-View PHP
 * リクエストに対する処理を行う部分をロジック、
 * 表示に関わる処理をビューと呼ぶ。
 * この2つの役割に大別することで可読性やメンテナンス性の向上につながる
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

//投稿された内容を取得するSQLを作成して結果を取得
$sql = 'SELECT * FROM post ORDER BY created_at DESC';
$result = mysqli_query($link, $sql);

$posts = [];
if ($result !== false && mysqli_num_rows($result)) {
  while ($post = mysqli_fetch_assoc($result)) {
    $posts[] = $post;
  }
}

include 'views/bbs_view.php';
?>
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
  <form action="bbs.php" method="post">
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
  <?php if (count($posts) > 0) { ?>
    <ul>
      <?php foreach ($posts as $post) { ?>
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
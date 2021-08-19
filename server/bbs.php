<html>

<head>
  <title>vamb掲示板_練習</title>
  <style type=text/css>
    div#header{background-color:#e0ffff;}
  </style>
  <!-- bootstrap CDN -->
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>

<body>
<div class="container">
<div id="header">

<h1>vamb@ch</h1>
<h4 style="color : ff8c00">一旦放置</h4>

<h2>投稿するところ</h2>
<form action="bbs.php" method="post" role="form">
  <div class="form-group">
    <label class="control-label">名前</label>
    <input type="text" name="user_name" class="form-control" placeholder="名前"/>
  </div>
  <div class="form-group">
    <label class="control-label">投稿内容</label>
    <!--<input type="text" name="content" class="form-control" placeholder="投稿内容"/>-->
    <textarea name="content" class="form-control" placeholder="それってあなたの感想ですよね？"></textarea>
  </div>
  <button type="submit" name="submit_btn" class="btn btn-primary">投稿</button>
</form>

<?php
// データベースへ接続
$dbs = "mysql:host=127.0.0.1;dbname=lesson;charset=utf8";
$db_user = "root";
$db_pass = "Seikimatu-4869";
$pdo = new PDO($dbs, $db_user, $db_pass);

//セッションスタート
//session_start();

// 変数の設定
$content = $_POST["content"];
$u_name = $_POST["user_name"];
$delete_id = $_POST["delete_id"];

//投稿内容がからじゃないかつ投稿ボタンが押されたらデータベースへのデータの挿入
if( isset($_POST["submit_btn"]) ){
  if(!empty($content)){ //空じゃなけりゃ
    echo "投稿されました。一番下";
    var_dump($_POST);
  
    //データベースへ挿入
    $sql  = "INSERT INTO bbs (content, updated_at, user_name) VALUES (:content, NOW(), :user_name);";
    $stmt = $pdo->prepare($sql);
    $stmt -> bindValue(":content", $content, PDO::PARAM_STR);
    $stmt -> bindValue(":user_name", $u_name, PDO::PARAM_STR);
    $stmt -> execute();

  }else{  //空ならエラー
    echo "投稿に失敗（投稿内容が空です。）";
  }
}

// データベースのデータの削除
$sql = "DELETE FROM bbs WHERE ip = :delete_id;";
$stmt = $pdo->prepare($sql);
$stmt -> bindValue(":delete_id", $delete_id, PDO::PARAM_STR);
$stmt -> execute();

// データベースからのデータの取得
$sql = "SELECT * FROM bbs ORDER BY updated_at $order;";
$stmt = $pdo->prepare($sql);
$stmt -> execute();

// 取得したデータをテーブルで表示

//投稿メッセージが削除されたら削除メッセージを表示
if($delete_id != NULL){
  echo "投稿が削除されました。: ".$delete_id;
}
?>

<h2>投稿リスト</h2>
<table class="table">
<tr>
  <th>No.</th>
  <!--<th>id</th>-->
  <th>投稿者名</th>
  <th>日時</th>
  <th>投稿内容</th>
  <th></th>
</tr>

<?php
while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
  $i++;
  //偶数番目のテーブルの背景を変更
  if($i%2 == 0){
?>
<tr bgcolor="#afeeee">
<?php
  }else{
?>
<tr>
<?php
  }
?>
  <td><?php echo "$i"; ?></td>
  <!--<td><?php echo "$row[ip]"; ?></td>-->
  <td><?php echo "$row[user_name]"; ?></td>
  <td><?php echo "$row[updated_at]"; ?></td>
  <td><?php echo "$row[content]"; ?></td>
  <td>
    <form action="bbs.php" method="post" role="form">
      <button type="submit" class="btn btn-danger">削除</button>
      <div class="form-group">
        <input type="hidden" name="delete_id" value="<?php echo "$row[ip]"; ?>" class="form-control"/>
      </div>
    </form>
  </td>
</tr>
<?php
}
?>
</table>
</div>
</div>
</body>
</html>

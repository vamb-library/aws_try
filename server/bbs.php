<!DOCTYPE html>
<html>
<head>
  <title>vamb掲示板_練習</title>
  <style type=text/css>
    div#header{background-color:#e0ffff;}
  
    .success_message {
      margin: 20px;
      /*margin-bottom: 20px;*/
      padding: 10px;
      /*color: #48b400;*/
      color: #008b8b;
      border-radius: 10px;
      border: 2px solid #00b3b3;
    }

    .error_message {
      margin-top: 10px;
      margin-left: 20px;
      margin-right: 20px;
      padding: 10px;
      color: #ef072d;
      /*list-style-type: none;*/
      border-radius: 10px;
      border: 1px solid #ff5f79;
    }

    .delete_message {
      margin: 20px;
      padding: 10px;
      color: #ff0000;
      border: 3px double #ff4500;
      border-radius: 5px;
    }

    .success_message,
    .error_message {
    font-size: 86%;
    line-height: 1.6em;
    }
  </style>
  
  <!-- bootstrap CDN -->
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>

<body>
<div class="container">
<div id="header">

<h1>vamb@ch</h1>
<h4 style="color: #ff8c00">ちょっと頑張ってみよう_</h4>

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
$options = array(
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
);

try{
  $pdo = new PDO($dbs, $db_user, $db_pass, $options);
}catch(PDOException $ex){  //接続が失敗したとき
  $error_message[] = $ex->getMessage();
}

// 変数の設定
$content = $_POST["content"];
$u_name = $_POST["user_name"];
$delete_id = $_POST["delete_id"];
$error_message = array();   //エラー文をここに格納
$res = null;

//投稿ボタンが押されたらデータベースへのデータの挿入開始
if( isset($_POST["submit_btn"]) ){
  //名前が空ならエラー文を記録
  if(empty($u_name)){
    $error_message[] = "名前を入力せよ";
    //echo "名前を入力しよ<br />";
  }

  //投稿内容が空ならエラー文
  if(empty($content)){
    $error_message[] = "投稿内容を入力せよ";
    //echo "投稿内容を入力せよ<br />";
  }

  if(empty($error_message)){ //エラー文がなければ
    //var_dump($_POST);

    try{
      //トランザクション開始
      $pdo->beginTransaction();

      //データベースへ挿入
      $sql  = "INSERT INTO bbs (content, updated_at, user_name) VALUES (:content, NOW(), :user_name);";
      $stmt = $pdo->prepare($sql);
      $stmt -> bindValue(":content", $content, PDO::PARAM_STR);
      $stmt -> bindValue(":user_name", $u_name, PDO::PARAM_STR);
      $stmt -> execute();

      //コミット
      $res = $pdo->commit();
      //$res = 0;
    }catch(Exception $ex){ //コミットまで何らかのエラーがあればロールバック
      $pdo->rollBack();
      echo "失敗したrollback". $ex->getMessage();
    }

    if($res){
      //データベースに挿入できたら成功メッセージを格納
      $suc_meg = "投稿されました。一番下";
      //echo "投稿されたよ";
    }else{
      $error_message[] = "投稿内容の書き込みに失敗しました";
      //echo "書き込みに失敗";
    }
  }else{  //エラー分があれば
    $error_message[] = "投稿に失敗しました";
    //echo "投稿に失敗しました。";
  }
}

// データベースのデータの削除
try{
  $pdo -> beginTransaction();

  $sql = "DELETE FROM bbs WHERE ip = :delete_id;";
  $stmt = $pdo->prepare($sql);
  $stmt -> bindValue(":delete_id", $delete_id, PDO::PARAM_STR);
  $stmt -> execute();

  $res = $pdo -> commit();
  //$res = 0;
}catch(Exception $ex){
  $pdo -> rollBack();
  $error_message[] = $ex -> getMessage();
  echo "削除に失敗rollBack". $ex -> getMessage();
}
if($res){
  $del_meg = "投稿が削除されました。: ". $delete_id. "<br /> この事象は管理者に報告され、
  操作元を特定します。<br /> って書いたらビビるよなぁ。";
}else{
  $error_message[] = "削除に失敗しました。";
}

// データベースからのデータの取得
$sql = "SELECT * FROM bbs ORDER BY updated_at $order;";
$stmt = $pdo->prepare($sql);
$stmt -> execute();

?>

<?php
//システムメッセージ

//投稿メッセージがdbに正常に入力されたときのメッセージ
if(!empty($suc_meg)){
?>
<p class = "success_message"><?php echo $suc_meg; ?></p>
<?php
}
//何らかのエラーを検出したときのメッセージ
if(!empty($error_message)){
  foreach($error_message as $err_value){?>
    <p class = "error_message">
      <?php echo $err_value; ?>
    </p>
  <?php
  }
  ?>
<!-- <ul class = "error_message">
  ?php foreach( $error_message as $err_value){?>
    <li> ?php echo $err_value; ?> </li>
  ?php
  }
  ?>
</ul> -->
<?php
}

//投稿メッセージが削除されたときのメッセージ
if(!empty($delete_id)){
?>  
<p class = "delete_message"> <?php echo $del_meg ?></p>
<?php
}

// 取得したデータをテーブルで表示
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

<!DOCTYPE html>
<html lang="en">
<head>
  <title>vamb掲示板_練習</title>

  <!-- bootstrap CDN 3.3-->
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>


  <!-- bootstrap CDN 5.0->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" 
  rel="stylesheet" 
  integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" 
  crossorigin="anonymous">
-->
  
  <style type=text/css>
    div#header{background-color:#e0ffff;}

    td{
      border-bottom: solid 2px #c71585;
    }

    th{
      border-bottom: solid 2px #c71585;
    }

    textarea{
      margin: 10px;
      resize: vertical;
      max-height: 200px;
      min-height: 35px;
    }

    .bar1{
      display: block;
      width: 80%;
      height: 1px;
      position: relative;
      left: -70px;
      background-color: #696969;
      /*border: none;*/
    }
  
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

  
</head>

<body>
<div class="container-fluid">
<div id="header">

<h1>vamb@ch</h1>
<h4 style="color: #ff8c00;">ちょっと頑張ってみよう_</h4>
<hr class = "bar1">最近の変更内容 (21. 9/20)</hr>
<ul style = "list-style-type: disc">
  <li>システムメッセージを追加</li>
    <ul style = "list-style-type: none">
      <li>投稿時、投稿エラー時、削除時のメッセジ</li>
    </ul>

  <li>投稿リストを上が最新になるようにした</li>
  <li>改行を反映を反映するようにした</li>
  <li>空白のみでは投稿不可にした</li>
</ul>
<hr class = "bar1">次</hr>
<ul style = "list-style-type: disc">
  <li>ログイン機能</li>
  <li>デザイン（bootstrap）</li>
  <li>オートリロード</li>
</ul>

<h2>投稿するところ</h2>
<form style = "background-color:#b0c4de; border: none; border-radius: 5px;" 
action="bbs.php" method="post" role="form">
  <div class="form-group" style = "width: 30%;">
    <label class="control-label" style = "margin: 10px;">名前</label>
    <input type="text" name="user_name" class="form-control" style = "margin: 10px;" placeholder="名前"/>
  </div>
  <div class="form-group" style = "width: 90%">
    <label class="control-label" style = "margin: 10px;">投稿内容</label>
    <!--<input type="text" name="content" class="form-control" placeholder="投稿内容"/>-->
    <textarea name="content" class="form-control" maxlength = "300" placeholder="それってあなたの感想ですよね？"></textarea>
  </div>
  <button type="submit" name="submit_btn" class="btn btn-primary" style = "margin: 10px;">投稿</button>
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
  $error_message[] = "DBの接続に失敗 ". $ex->getMessage();
}

// 変数の設定
//名前と投稿内容は空白や制御文字を無視するように設定する
$content = preg_replace("/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u", "", $_POST["content"]);
$u_name = preg_replace("/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u", "", $_POST["user_name"]);
$delete_id = $_POST["delete_id"];
$error_message = array();   //各エラー文をここに格納していく
$res = null;

//セッションスタート
session_start();

//投稿ボタンが押されたらデータベースへのデータの挿入開始
if( isset($_POST["submit_btn"]) ){
  //名前が空ならエラー文を記録
  if(empty($u_name)){
    $error_message[] = "名前を入力せよ";
  }else{  //入力されていればそれをセッションに保存する。
    $_SESSION['u_name'] = $u_name;
  }

  //投稿内容が空ならエラー文
  if(empty($content)){
    $error_message[] = "投稿内容を入力せよ";
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
      $error_message[] = "DBの挿入に失敗 ". $ex->getMessage();
    }

    if($res){
      //データベースに挿入できたら成功メッセージを格納
      $suc_meg = "投稿されました。一番下";
    }else{
      $error_message[] = "投稿内容の書き込みに失敗しました";
    }
  }else{  //エラー分があれば
    $error_message[] = "投稿に失敗しました";
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
  $del_meg = "投稿が削除されました。: ". $delete_id. "<br /> この事象は管理者に報告されます。
  <br /> (嘘です無理です。)";
}else{
  $error_message[] = "削除に失敗しました。";
}

// データベースからのデータの取得
$order = "DESC";  //降順
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
<table class="table" style="font: normal medium ヒラギノ角ゴシック" >
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
  <th><?php echo "$i"; ?></th>
  <!--<td>?php echo "$row[ip]"; ?></td>-->
  <td><?php echo "$row[user_name]"; ?></td>
  <td><?php echo "$row[updated_at]"; ?></td>
  <td><?php echo nl2br(htmlspecialchars("$row[content]")); ?></td>
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

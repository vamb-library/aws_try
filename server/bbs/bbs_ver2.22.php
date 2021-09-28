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
?>

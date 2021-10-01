<?php
    //データベースへの接続
    $bbs_DBaccess = "bbs_database.php";
    require($bbs_DBaccess); 
?>

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
    div#header{
        background-color:#e0ffff;
        border-radius: 10px;
    }

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
    tr:hover{
        background-color: #ffe6f0;
    }
  </style>

  
</head>

<body>
<div class="container-fluid">
<div id="header">

<h1>vamb@ch</h1>
<h4 style="color: #ff8c00;">ちょっと頑張ろう＿＿</h4>
<hr class = "bar1">最近の変更内容 (21. 9/28)</hr>
<ul style = "list-style-type: disc">
  <li>名前を入力して投稿したら、名前が保存されるようにした。</li>
    <ul style = "list-style-type: none">
      <li>連続して投稿する際、名前を省略するようにした</li>
    </ul>
  <li>ちょっとしたデザイン改良？</li>
</ul>
<hr class = "bar1">次</hr>
<ul style = "list-style-type: disc">
  <li>ログイン機能</li>
  <li>デザイン（bootstrap）</li>
  <li>オートリロード</li>
</ul>

<h2>投稿するところ</h2>
<form style = "background-color:#b0c4de; border: none; border-radius: 5px;" action="" method="post" role="form">
  <div class="form-group" style = "width: 30%;">
    <label class="control-label" style = "margin: 10px;">名前</label>
    <input type="text" name="user_name" class="form-control" style = "margin: 10px;" maxlength="15"
        value="<?php if(!empty($_SESSION['u_name'])) echo htmlspecialchars($_SESSION['u_name'], ENT_QUOTES, "UTF-8"); ?>" 
        placeholder="名前"/>
  </div>
  <div class="form-group" style = "width: 90%">
    <label class="control-label" style = "margin: 10px;">投稿内容</label>
    <!--<input type="text" name="content" class="form-control" placeholder="投稿内容"/>-->
    <textarea name="content" class="form-control" maxlength = "300" placeholder="それってあなたの感想ですよね？"></textarea>
  </div>
  <button type="submit" name="submit_btn" class="btn btn-primary" style = "margin: 10px;">投稿</button>
</form>

<?php
    //システムメッセージの表示
    $bbs_system_message = "bbs_sys_meg.php";
    require($bbs_system_message); 
?>

<h2>投稿リスト</h2>
<table class="table" style="font: normal medium ヒラギノ角ゴシック; border-collapse: separate;" >
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
  <td><?php echo nl2br(htmlspecialchars("$row[content]", ENT_QUOTES, "UTF-8")); ?></td>
  <td style="text-align: center; vertical-align: bottom;">
    <form action="" method="post" role="form">
      <button type="submit" class="btn btn-danger" style="margin-bottom: -10px;">削除</button>
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
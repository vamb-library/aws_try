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
<html>
<?php
  session_id($_COOKIE['PHPSESSID']);
  session_start();
?>
<?php echo $_SESSION['NAME']; ?>
hoge

</html>

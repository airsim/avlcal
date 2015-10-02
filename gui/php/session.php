<?php
  session_register ("count");
  $count++;
?>

<html>

<body>


 Hello visitor, you have seen this page <?php echo $count; ?> times.<p>

 <php?
 # the <?=SID?> is necessary to preserve the session id
 # in the case that the user has disabled cookies
 ?>

 To continue, <a href="session.php">click here</a>

</body>
</html>
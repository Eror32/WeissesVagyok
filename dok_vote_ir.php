<?php
  session_start();
  include("kapcsolat.php") ;

  $dpostid = intval($_POST['dpostid']); 
  $vuid = $_SESSION['uid'];
  $vchoice = $_POST[$dpostid];
  $exists = mysqli_query($adb, "SELECT * FROM votelog WHERE vuid = $vuid AND vdpostid = $dpostid");

  if (mysqli_num_rows($exists) == 0) mysqli_query($adb, "
      INSERT INTO votelog (vdpostid,  vuid,    vchoice) 
      VALUES              ($dpostid, $vuid, '$vchoice')");
  else mysqli_query($adb, "
      UPDATE votelog SET vchoice = '$vchoice' 
      WHERE vuid = $vuid AND vdpostid = $dpostid");

  print("<script>window.parent.location.reload();</script>");
?>
<?php
    session_start();
    include("kapcsolat.php"); 

    $textid = mysqli_fetch_array(mysqli_query($adb, "SELECT MAX(dtextid) FROM dok WHERE dpostid = $_POST[dpostid]"))[0] + 1;
    $text = isset($_POST['commentInput']) ? trim($_POST['commentInput']) : '';
    if ($text == '') die();

    mysqli_query( $adb , "
    INSERT INTO dok (	      dpostid ,   dtextid ,           duid ,  dtext   , dtime ,	dstatus ) 
    VALUES          ( $_POST[dpostid] ,   $textid , $_SESSION[uid] , '$text'  , NOW() ,     'A' )
    ");
    print("<script>setTimeout(function() {window.parent.location.reload()}, 100)</script>");
?>
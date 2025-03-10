<?php
  session_start();
  include("kapcsolat.php");
  
  $postid = mysqli_fetch_array(mysqli_query($adb, "SELECT MAX(dpostid) FROM dok"))[0] + 1;

  //eseménykezelés
  $event= $_POST['add-event'];
  $eventEnd = $_POST['event-date'] ?? NULL;

  if ($event == 'igen'){
    $event = 'I';
    if ($eventEnd == NULL) die( "<script> alert('Adjon meg egy lejárati dátumot!') </script>" ) ;
  }
  else $event = 'N';

  //beviteli szöveg
  $text = trim(preg_replace('/[ ]{2,}/', ' ', $_POST['dtext']));
  if ($text=="") die( "<script> alert('A közzétételhez írjon valamit a mezőbe!') </script>" ) ;

  //feltöltött fájl kezelése
  $files = [];

  if (!empty($_FILES['dok-post-files']['name'][0])) {
    foreach ($_FILES['dok-post-files']['name'] as $fi => $fileName) {
      $fileTmpName = $_FILES['dok-post-files']['tmp_name'][$fi];
      $fileSize = $_FILES['dok-post-files']['size'][$fi];

      //2MB max
      if ($fileSize >= 2000000) die("<script>alert('A(z) ".$fi + 1 .". fájl mérete túl nagy, kérem távolítsa el a listából! (max 2MB)')</script>");

      //prefix: dok-postid-file
      $filePrefix = "dok-".$postid."-".basename($fileName);
      if (move_uploaded_file($fileTmpName, 'dok-files/'.$filePrefix)) $files[] = $filePrefix;
    }
  }
  $dfile = implode(';', $files);

  //szavazati opciók
  $inputCount = 0;
  $dvote = "";
  while(isset($_POST['input'.$inputCount])){
    $inputText = trim(preg_replace('/[ ]{2,}/', ' ', $_POST['input'.$inputCount]));

    if ($inputText!="") $dvote = $dvote . $inputText . ";";
    $inputCount++;
  }
  $dvote = substr($dvote, 0, -1) . ' ';
  
  //feltöltés
  $eventEnd = $eventEnd ? "'" . mysqli_real_escape_string($adb, $eventEnd) . "'" : "NULL";
  mysqli_query( $adb , "
    INSERT INTO dok (	dpostid , dtextid ,           duid   ,	dtext   , dfile    , dvote    ,  devent  , deventEnd ,	dtime ,	dstatus ) 
    VALUES          ( $postid ,       0 , '$_SESSION[uid]' , '$text'  , '$dfile' , '$dvote' , '$event' , $eventEnd , NOW() ,     'A' )
  ");
  print("<script>setTimeout(function() {window.parent.location.reload()}, 100)</script>");
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Weiss Diákönkormányzat</title>
    <link rel="stylesheet" href="dok.css">
    <script src="dok.js" defer></script>
</head>
<body style="margin:0px; min-width: 1210px;">

    <div id="dok-search">
        <form method="GET" style="background: transparent; box-shadow: none;">
            <input type="text" name="search" placeholder="Keresés...">
            <span onclick="this.closest('form').submit()"><img src="images/magnifying-glass.png"></span>
        </form>
    </div>

    <div id="dok-container">
        <div class="dok-content">
            <?php
                $search = isset($_GET['search']) ? trim($_GET['search']) : '';
                $sql = "SELECT *, DATE_FORMAT(dtime, '%Y-%m-%d %H:%i') AS postedTime FROM dok WHERE dtextid = 0 AND dstatus = 'A'";

                if (!empty($search)) {
                    print('<h2 style="margin-left: 10px;">Találatok a következőre: '.htmlspecialchars($search).'</h2>');
                    $sql .= " AND dtext LIKE '%$search%'";
                }

                //Közzététel
                else if ($user['ustatusz']=='D'){
                    print('
                        <form action="dok_post_ir.php" method="post" enctype="multipart/form-data" target="kisablak" style="background: transparent; box-shadow: none;">
                            <dialog id="voteWindow">
                                <div>
                                    <h2>Opciók megadása:</h2>
                                    <p id="votes-container">
                                        <input type="text" name="input0" maxlength=19><br>
                                    </p>
                                    <br><br>
                                    <p style ="font-size: 11px;"><u>Megjegyzés:</u><br>
                                    <i>A „Kész” gombbal kiléphet. Ha nem szeretne szavazást létrehozni, vagy ha nincs szüksége egyes mezőkre, hagyja őket üresen!</i></p>
                                    <label id="closeVote">Kész</label>
                                    <label id="addVote">Új opció</label>
                                </div>
                            </dialog>

                            <textarea name="dtext" onclick="this.style.height=`215px`"></textarea>
                            
                            <label for="dok-post-vote"" onclick="Vote()">Szavazás létrehozása</label><input type="button" name="dok-post-vote" style="display: none">

                            <label for="post-file">Fájl csatolása</label><input type="file" name="dok-post-files[]" id="post-file" multiple style="display: none">
                            <ol id="file-list"></ol>');
                            
                                
                            print('
                            <dl>
                                <dt>Szeretné eseményként kezelni? </dt>
                                <dd style="margin-left: 132px"> Igen <input type="radio" id="dok-event-add" name="add-event" value="igen"> </dd>
                                <dd style="margin-left: 130px" id="event-date" hidden> Dátum: <input type="date" id="dok-event-date" name="event-date"></dd>
                                <dd style="margin-left: 130px"> Nem <input type="radio" id="dok-event-add" name="add-event" value="nem" checked> </dd>
                            </dl>

                            <label for="dok-post-send">Közzététel</label><input type="submit" id="dok-post-send" style="display: none">
                            <hr>
                        </form>
                    ');
                }
                $sql .= " ORDER BY dpostid DESC";
                $query = mysqli_query($adb, $sql);
                $user = mysqli_fetch_array(mysqli_query($adb, "SELECT * FROM user WHERE uid='$_SESSION[uid]'"));  
                
                while ($dok = mysqli_fetch_assoc($query)) {

                    $puser = mysqli_fetch_array(mysqli_query($adb, "SELECT * FROM user WHERE uid='{$dok['duid']}' AND ustatusz!='I' ")); 
                    
                    $punick = $puser['unick'] ?? "Törölt felhasználó";
                    if ($puser['uprofkepnev']=="") $pupic ="images/alapkep.jfif";
                    else $pupic = "profilkepek/".$puser['uprofkepnev'];
                    
                    //nl2br = formázás (sortörés)
                    //htmlspecialchars = HTML tag elleni védelem
                    print('
                        <a href="#" style="margin: 10px;">
                        <img src="'.$pupic.'"><b> '.$punick.' </b></a><i style="font-size: 11px; color: grey;">'.$dok['postedTime'].'</i>
                    ');

                    //if ($_SESSION['uid'] == $dok['duid']) 

                    print('
                        <div class="posted">
                        <p>
                            '.nl2br(htmlspecialchars($dok['dtext'])).'
                        </p>'
                    );

                    //szavazás
                    if ($dok['dvote']!=" "){
                        $votes= explode(';', $dok['dvote']);    
                        print('<form action="dok_vote_ir.php" method="post" target="kisablak">');
                        print('<input type="hidden" name="dpostid" value="'.$dok['dpostid'].'">');
                        
                        //szavat kiírás
                        $voted= mysqli_fetch_array(mysqli_query($adb, "SELECT * FROM votelog WHERE vuid='{$_SESSION['uid']}' AND vdpostid='{$dok['dpostid']}' ORDER BY vid DESC"));
                        for($i=0; $i < count($votes); $i++){
                            $voteCount= mysqli_fetch_array(mysqli_query($adb, "SELECT COUNT(*) AS vote_count
                                                                                FROM votelog v1
                                                                                JOIN (
                                                                                    SELECT vuid, vdpostid, MAX(vid) AS max_vid
                                                                                    FROM votelog
                                                                                    GROUP BY vuid, vdpostid
                                                                                ) v2 
                                                                                ON v1.vuid = v2.vuid 
                                                                                AND v1.vdpostid = v2.vdpostid 
                                                                                AND v1.vid = v2.max_vid
                                                                                WHERE v1.vdpostid = '$dok[dpostid]' 
                                                                                AND v1.vchoice = '$votes[$i]'"));
                            $identifier= $dok['dpostid'] . $i;

                            if ($voted && $voted['vchoice'] == $votes[$i]){
                                print('
                                    <label for="'.$identifier.'" style="line-height: 16px; background-color: rgba(0, 0, 0, 0.05); border-radius: 16px; padding: 6px;">
                                    <input type="radio" name="'.$dok['dpostid'].'" id="'.$identifier.'" value="'.$votes[$i].'" checked>'.htmlspecialchars($votes[$i]).' <i style="float:right; font-size: 12px;">'.$voteCount[0].' szavazó</i></label>
                                ');
                            } 

                            else{ 
                                print('
                                    <label for="'.$identifier.'" style="line-height: 16px; background-color: rgba(0, 0, 0, 0.05); border-radius: 16px; padding: 6px;">
                                    <input type="radio" name="'.$dok['dpostid'].'" id="'.$identifier.'" value="'.$votes[$i].'" onchange="this.form.submit()">'.htmlspecialchars($votes[$i]).'<i style="float:right; font-size: 12px;">'.$voteCount[0].' szavazó</i></label>
                                ');
                            }
                        }
                        print('</form>');
                    }
                    print('</div>');
                    
                    //feltöltött fájl kiírás
                    $files = explode(';', $dok['dfile']);
                    print('<div class="postedFile">');
                    foreach ($files as $file) {
                        if (!empty($file)) {

                            // /^ = Matches the beginning of the filename (not something in the middle)
                            // dok- = Matches the literal text "dok-"
                            // \d+ = Matches one or more digits (\d means any number, + means one or more)
                            // - = Matches the literal hyphen (-) after the number
                            // / = Regex delimiters (used to enclose the pattern)
                            $noPrefix = preg_replace('/^dok-\d+-/', '', $file);
                            
                            print('<a href="file-download.php?file='. urlencode($file) .'" target="kisablak"><img src="images/download_icon.png">'. htmlspecialchars($noPrefix) .'</a>');
                        }
                    }
                    print('</div><div class="dok-comment">');

                    $query2 = mysqli_query($adb, "SELECT *, DATE_FORMAT(dtime, '%Y-%m-%d %H:%i') AS postedTime FROM dok WHERE dtextid != 0 AND dstatus = 'A' AND dpostid = '$dok[dpostid]'");

                    while ($comment = mysqli_fetch_assoc($query2)) {
                        $pcuser = mysqli_fetch_array(mysqli_query($adb, "SELECT * FROM user WHERE uid='{$comment['duid']}' AND ustatusz!='I' ")); 
                        $pcunick = $pcuser['unick'] ?? "Törölt felhasználó";
                        if ($pcuser['uprofkepnev']=="") $pcupic ="images/alapkep.jfif";
                        else $pcupic = "profilkepek/".$pcuser['uprofkepnev'];

                        print('<div><a href="#" style="margin: 10px;">
                        <img src="'.$pcupic.'"><b> '.$pcunick.'</b></a><i style="font-size: 11px; color: rgb(61, 61, 61);">'.$comment['postedTime'].'</i><p>'.htmlspecialchars($comment['dtext']).'</p>
                        </div>');
                        
                    }
                    print('<p><form action="dok_comment_ir.php" method="POST" style="background: transparent; box-shadow: none;" target="kisablak">
                    <input type="hidden" name="dpostid" value="'.$dok["dpostid"].'">
                    <input type="text" name="commentInput" autocomplete="off"><span onclick="this.closest(\'form\').submit();"><img src="images/comment_send_icon.png"></span>
                    </form></p></div>');
                }
            ?>
        </div>
    </div>
    <div id="dok-event-container">
        
        <div id="dok-event">
            <?php
                $user = mysqli_fetch_array(mysqli_query($adb, "SELECT * FROM user WHERE uid='$_SESSION[uid]'"));  
                $query3 = mysqli_query($adb,"SELECT *, DATE_FORMAT(dtime, '%Y-%m-%d %H:%i') AS postedTime FROM dok WHERE dtextid = 0 AND dstatus = 'A' AND devent = 'I' AND (DATE(deventEnd) = CURDATE() OR deventEnd > NOW()) ORDER BY dpostid DESC");
                while ($event = mysqli_fetch_assoc($query3)) {
                    $puser = mysqli_fetch_array(mysqli_query($adb, "SELECT * FROM user WHERE uid='{$event['duid']}' AND ustatusz!='I' ")); 
                    
                    $punick = $puser['unick'] ?? "Törölt felhasználó";
                    if ($puser['uprofkepnev']=="") $pupic ="images/alapkep.jfif";
                    else $pupic = "profilkepek/".$puser['uprofkepnev'];
                    
                    print('
                        <img src="'.$pupic.'"><b> '.$punick.' </b></a><i style="font-size: 11px; color: rgb(61, 61, 61);">'.$event['postedTime'].'</i>
                    ');
                    print('
                        <div class="posted">
                        <p>
                            '.nl2br(htmlspecialchars($event['dtext'])).'
                        </p></div>'
                    );

                    $files = explode(';', $event['dfile']);
                    print('<div class="postedFile">');
                    if ($event['dvote']!=" "){
                        $votes= explode(';', $event['dvote']);    
                        print('<form action="dok_vote_ir.php" method="post" target="kisablak">');
                        print('<input type="hidden" name="dpostid" value="'.$event['dpostid'].'">');
                        
                        //szavat kiírás
                        $voted= mysqli_fetch_array(mysqli_query($adb, "SELECT * FROM votelog WHERE vuid='{$_SESSION['uid']}' AND vdpostid='{$event['dpostid']}' ORDER BY vid DESC"));
                        for($i=0; $i < count($votes); $i++){
                            $voteCount= mysqli_fetch_array(mysqli_query($adb, "SELECT COUNT(*) AS vote_count
                                                                                FROM votelog v1
                                                                                JOIN (
                                                                                    SELECT vuid, vdpostid, MAX(vid) AS max_vid
                                                                                    FROM votelog
                                                                                    GROUP BY vuid, vdpostid
                                                                                ) v2 
                                                                                ON v1.vuid = v2.vuid 
                                                                                AND v1.vdpostid = v2.vdpostid 
                                                                                AND v1.vid = v2.max_vid
                                                                                WHERE v1.vdpostid = '$event[dpostid]' 
                                                                                AND v1.vchoice = '$votes[$i]'"));
                            $identifier= $event['dpostid'] . $i;

                            if ($voted && $voted['vchoice'] == $votes[$i]){
                                print('
                                    <label for="'.$identifier.'" style="line-height: 16px; background-color: rgba(0, 0, 0, 0.05); border-radius: 16px; padding: 6px;">
                                    <input type="radio" name="'.$event['dpostid'].'" id="'.$identifier.'" value="'.$votes[$i].'" checked>'.htmlspecialchars($votes[$i]).' <i style="float:right; font-size: 12px;">'.$voteCount[0].' szavazó</i></label>
                                ');
                            } 

                            else{ 
                                print('
                                    <label for="'.$identifier.'" style="line-height: 16px; background-color: rgba(0, 0, 0, 0.05); border-radius: 16px; padding: 6px;">
                                    <input type="radio" name="'.$event['dpostid'].'" id="'.$identifier.'" value="'.$votes[$i].'" onchange="this.form.submit()">'.htmlspecialchars($votes[$i]).'<i style="float:right; font-size: 12px;">'.$voteCount[0].' szavazó</i></label>
                                ');
                            }
                        }
                        print('</form>');
                    }
                    foreach ($files as $file) {
                        if (!empty($file)) {

                            // /^ = Matches the beginning of the filename (not something in the middle)
                            // dok- = Matches the literal text "dok-"
                            // \d+ = Matches one or more digits (\d means any number, + means one or more)
                            // - = Matches the literal hyphen (-) after the number
                            // / = Regex delimiters (used to enclose the pattern)
                            $noPrefix = preg_replace('/^dok-\d+-/', '', $file);
                            
                            print('<a href="file-download.php?file='. urlencode($file) .'" target="kisablak"><img src="images/download_icon.png">'. htmlspecialchars($noPrefix) .'</a>');
                        }
                    }
                    
                    print('</div>');
                }
            ?>
        </div>
    </div>
</body>
</html>
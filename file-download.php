<?php
    $file = basename($_GET['file']);
    $filePath = 'dok-files/' . $file;

    if (file_exists($filePath)) {
        
        // /^ = Matches the beginning of the filename (not something in the middle)
        // dok- = Matches the literal text "dok-"
        // \d+ = Matches one or more digits (\d means any number, + means one or more)
        // - = Matches the literal hyphen (-) after the number
        // / = Regex delimiters (used to enclose the pattern)
        $noPrefix = preg_replace('/^dok-\d+-/', '', $file);

        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"$noPrefix\"");
        readfile($filePath);
        exit;
    } else {
        die("<script> alert('Hiba: A fájl nem található.')</script>");
    }
?>
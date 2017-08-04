<?php

function file_mime($filePath)
{
    if(!file_exists($filePath)) {
        throw new \Exception("File does not exist ({$filePath})");
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $filePath);
    finfo_close($finfo);

    return $mimeType;
}

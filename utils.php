<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

function verifyUploadedPhoto($photo, &$filePath)
{

    if ($photo->getError() !== UPLOAD_ERR_OK) {
        return "Upload failed." . $photo->getError();
    }

    $info = getimagesize($photo->file);
    if (!$info) {
        return "File is not an image";
    }
    if ($info[0] < 200 || $info[0] > 1000 || $info[1] < 200 || $info[1] > 1000) {
        return "Width and height must be within 200-1000 pixels range";
    }

    $ext = "";
    switch ($info['mime']) {
        case 'image/jpeg':
            $ext = "jpg";
            break;
        case 'image/gif':
            $ext = "gif";
            break;
        case 'image/png':
            $ext = "png";
            break;
        case 'image/bmp':
            $ext = "bmp";
            break;
        default:
            return "Only JPG, GIF, PNG and BMP file types are accepted";
    }

    $filePath = "uploads/" . mb_ereg_replace("[^\w\s\d\)]", '_', pathinfo($photo->getClientFilename(), PATHINFO_FILENAME)) . "." . $ext;
    return TRUE;
}
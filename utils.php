<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

function verifyUploadedPhoto($photo, &$filePath) {

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

function verfiyEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Please enter a valid email";
    } else {
        $checkEmail = DB::query("SELECT email FROM users WHERE email = '$email'");
        if ($checkEmail) {
            return "This email is already registered.";
        }
    }
    return TRUE;
}

function verfiyEmailUpdate($email, $id) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Please enter a valid email";
    } else {
        $checkEmail = DB::query("SELECT email FROM users WHERE email = '$email' AND id != $id");
        if ($checkEmail) {
            return "This email is already registered.";
        }
    }
    return TRUE;
}

function verifyPasswords($password1, $password2) {
    if (strlen($password1) < 8 || strlen($password1) > 25
        || (preg_match("/[A-Z]/", $password1) !== 1)
        || (preg_match("/[a-z]/", $password1) !== 1)
        || (preg_match("/[0-9]/", $password1) !== 1)
        || (preg_match("/[#?!@$%^&*-]/", $password1) !== 1)
    ) {
        return "Minimum 8 characters with 1 uppercase letter, 1 lowercase letter, 1 number and 1 special character.";
    }

    if ($password1 !== $password2) {
        return 'The passwords you have entered do not match.';
    }
    return TRUE;
}

function verifyLicenseNo($licenseNo) {
    if (!preg_match('/^[A-Z]{3}[0-9]{6}[A-Z]{2}/', $licenseNo)) {
        return "Please enter a valid license number";
    } else {
        $checkLicenseNo = DB::query("SELECT licenseNo FROM users WHERE licenseNo = '$licenseNo'");
        if ($checkLicenseNo) {
            return "This license number is already registered.";
        }
    }
    return TRUE;
}

function verifyLicenseNoUpdate($licenseNo, $id) {
    if (!preg_match('/^[A-Z]{3}[0-9]{6}[A-Z]{2}/', $licenseNo)) {
        return "Please enter a valid license number";
    } else {
        $checkLicenseNo = DB::query("SELECT licenseNo FROM users WHERE licenseNo = '$licenseNo' AND id != $id");
        if ($checkLicenseNo) {
            return "This license number is already registered.";
        }
    }
    return TRUE;
}

function verifyFirstName($firstName) { //TEST REGEX
    if (!preg_match('/^[a-zA-Z\.\'\-]+$/', $firstName) || strlen($firstName) < 1 || strlen($firstName) > 100) {
        return "First name must be between 1 - 100 characters long and can only contain letters, periods, apostrophes and hyphens.";
    }
    return TRUE;
}

function verifyLastName($lastName) { //TEST REGEX
    if (!preg_match('/^[a-zA-Z\.\'\-]+$/', $lastName) || strlen($lastName) < 1 || strlen($lastName) > 100) {
        return "Last name must be between 1 - 100 characters long and can only contain letters, periods, apostrophes and hyphens.";
    }
    return TRUE;
}

function verifyPhone($phone) { //TEST REGEX
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        return "Phone number must be 10 digits.";
    }
    return TRUE;
}

function verifyCompany($company) { //TEST REGEX
    if (!preg_match('/^[a-zA-Z\.\'\-\s]+$/', $company) || strlen($company) < 1 || strlen($company) > 100) {
        return "Company name must be between 1 - 100 characters long and can only contain letters, periods, apostrophes and hyphens.";
    }
    return TRUE;
}

function verifyJobTitle($jobTitle) { //TEST REGEX
    if (!preg_match('/^[a-zA-Z\.\'\-\s]+$/', $jobTitle) || strlen($jobTitle) < 1 || strlen($jobTitle) > 100) {
        return "Job title must be between 1 - 100 characters long and can only contain letters, periods, apostrophes and hyphens.";
    }
    return TRUE;
}

function verifyAppartmentNo($appartmentNo) { //TEST REGEX
    if (!preg_match('/^[0-9]+$/', $appartmentNo)) {
        return "Appartment number can only be made up of numbers.";
    }
    return TRUE;
}

function verifyStreetNo($streetNo) { //TEST REGEX
    if (!preg_match('/^[0-9]+$/', $streetNo)) {
        return "Street number can only be made up of numbers.";
    }
    return TRUE;
}

function verifyStreetName($streetName) { //TEST REGEX
    if (!preg_match('/^[a-zA-Z\.\'\-\s]+$/', $streetName) || strlen($streetName) < 1 || strlen($streetName) > 100) {
        return "Street name must be between 1 - 100 characters long and can only contain letters, periods, apostrophes and hyphens.";
    }
    return TRUE;
}

function verifyCityName($city) { //TEST REGEX
    if (!preg_match('/^[a-zA-Z\.\'\-\s]+$/', $city) || strlen($city) < 1 || strlen($city) > 100) {
        return "City name must be between 1 - 100 characters long and can only contain letters, periods, apostrophes and hyphens.";
    }
    return TRUE;
}

function verifyProvince($province) { //TEST REGEX
    if (!preg_match('/^[A-Z]{2}$/', $province)) {
        return "Province must be in the following format: QC.";
    }
    return TRUE;
}

function verifyPostalCode($postalCode) { //TEST REGEX
    if (!preg_match('/^(?!.*[DFIOQU])[A-VXY][0-9][A-Z] ?[0-9][A-Z][0-9]$/', $postalCode)) {
        return "Postal code must be in the following format: H9X3L9.";
    }
    return TRUE;
}
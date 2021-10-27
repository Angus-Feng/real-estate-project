<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

function verifyUploadedBuyerProfilePhoto($photo, &$filePath) {
    $num = str_pad(mt_rand(1,99999999),8,'0',STR_PAD_LEFT);
    
    // if ($photo->getError() !== UPLOAD_ERR_OK) {
    //     return 'There was an error uploading the photo.';
    // }

    $info = getimagesize($photo->file);

    if ($info[0] < 127 || $info[0] > 127 || $info[1] < 150 || $info[1] > 150) {
        return "Width must be 127px and height must be 150px.";
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
        default:
            return "Only JPG, GIF, PNG file types are accepted";
    }

    $filePath = "uploads/" . $num . "." . $ext;
    return TRUE;
}

function verifyUploadedBrokerProfilePhoto($photo, &$filePath, $licenseNo) {
    // if ($photo->getError() !== UPLOAD_ERR_OK) {
    //     return 'There was an error uploading the photo.';
    // }

    $info = getimagesize($photo->file);

    if ($info[0] < 127 || $info[0] > 127 || $info[1] < 150 || $info[1] > 150) {
        return "Width must be 127px and height must be 150px.";
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
        default:
            return "Only JPG, GIF, PNG file types are accepted";
    }

    $filePath = "uploads/" . $licenseNo . "." . $ext;
    return TRUE;
}

function verifyUploadedHousePhoto($photo, &$filePath, $postalCode, $photoNo) {
    $info = getimagesize($photo->file);

    if ($info[0] < 640 || $info[0] > 640 || $info[1] < 480 || $info[1] > 480) {
        return "Width must be 640px and height must be 480px.";
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
        default:
            return "Only JPG, GIF, PNG file types are accepted";
    }

    $filePath = "uploads/" . $postalCode . "_" . $photoNo . "." . $ext;
    return TRUE;
}

function verfiyEmail($email) {
    if (!$email) {
        return "An email is required";
    }
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

function verifyPasswords($password) {
    if (!$password) {
        $errors['password'] = 'Password can not be empty';
    }
    if (strlen($password) < 8 || strlen($password) > 25
        || (preg_match("/[A-Z]/", $password) !== 1)
        || (preg_match("/[a-z]/", $password) !== 1)
        || (preg_match("/[0-9]/", $password) !== 1)
        || (preg_match("/[#?!@$%^&*-]/", $password) !== 1)
    ) {
        return "Minimum 8 characters including minimum 1 uppercase letter, 1 lowercase letter, 1 number and 1 special character.";
    }
    return TRUE;
}

function verifyLicenseNo($licenseNo) {
    if (!preg_match('/[A-Z]{3}[0-9]{6}[A-Z]{3}/', $licenseNo)) {
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
    if (!preg_match('/[A-Z]{3}[0-9]{6}[A-Z]{2}/', $licenseNo)) {
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
    if (strlen($firstName) < 1 || strlen($firstName) > 100) {
        return "First name must be between 1 - 100 characters long.";
    } else if (!preg_match('/[a-zA-Z\.\'\-]+/', $firstName)) {
        return "First name can only contain letters, periods, apostrophes and hyphens.";
    }
    return TRUE;
}

function verifyLastName($lastName) { //TEST REGEX
    if (strlen($lastName) < 1 || strlen($lastName) > 100) {
        return "Last name must be between 1 - 100 characters long.";
    } else if (!preg_match('/[a-zA-Z\.\'\-]+/', $lastName)) {
        return "Last name can only contain letters, periods, apostrophes and hyphens.";
    }
    return TRUE;
}

function verifyPhone($phone) { //TEST REGEX
    if ($phone == "") {
        return TRUE;
    }
    if (!preg_match('/[0-9]{10}/', $phone)) {
        return "Please enter a valid phone number.";
    }
    return TRUE;
}

function verifyCompany($company) { //TEST REGEX
    if (strlen($company) < 1 || strlen($company) > 100) {
        return "Company name must be between 1 - 100 characters long.";
    } else if (!preg_match('/[a-zA-Z\.\'\-\s]/', $company)) {
        return "Company name can only contain letters, periods, apostrophes and hyphens.";
    }
    return TRUE;
}

function verifyJobTitle($jobTitle) { //TEST REGEX
    if ($jobTitle == "") {
        return TRUE;
    }
    if (!preg_match('/^[a-zA-Z\.\'\-\s]+/', $jobTitle) || strlen($jobTitle) < 1 || strlen($jobTitle) > 100) {
        return "Job title must be between 1 - 100 characters long and can only contain letters, periods, apostrophes and hyphens.";
    }
    return TRUE;
}

function verifyAppartmentNo($appartmentNo) { //TEST REGEX
    if ($appartmentNo == "") {
        return TRUE;
    }
    if (!preg_match('/^[0-9]+/', $appartmentNo)) {
        return "Appartment number can only be made up of numbers.";
    }
    return TRUE;
}


function verifyUserStreetAddress($streetAddress) { //TEST REGEX
    if ($streetAddress == "") {
        return TRUE;
    }
    if (!preg_match('/[a-zA-Z0-9\.\'\-\s]+/', $streetAddress) || strlen($streetAddress) < 1 || strlen($streetAddress) > 100) {
        return "Street name must be between 1 - 100 characters long and can only contain letters, periods, apostrophes and hyphens.";
    }
    return TRUE;
}

function verifyCityName($city) { //TEST REGEX
    if ($city == "") {
        return TRUE;
    }
    if (!preg_match('/[a-zA-Z\.\'\-\s]+/', $city) || strlen($city) < 1 || strlen($city) > 100) {
        return "City name must be between 1 - 100 characters long and can only contain letters, periods, apostrophes and hyphens.";
    }
    return TRUE;
}

// function verifyProvince($province) { //TEST REGEX
//     if ($province == "") {
//         return TRUE;
//     }
//     if (!preg_match('/^[A-Z]{2}$/', $province)) {
//         return "Province must be in the following format: QC.";
//     }
//     return TRUE;
// }

function verifyPostalCode($postalCode) { //TEST REGEX
    if ($postalCode == "") {
        return TRUE;
    }
    if (!preg_match('/[a-zA-Z][0-9][a-zA-Z][\ ]{0,1}[0-9][a-zA-z][0-9]/', $postalCode)) {
        return "Postal code must be in the following format: H9X3L9.";
    }
    return TRUE;
}

function verifyPrice($price) {
    if ($price < 1 && $price > 1000000000) {
        return "The price of the home must range from 1 - 1,000,000,000 CAD.";
    }
    return TRUE;
}

function verifyTitle($title) { //TEST REGEX
    if (!preg_match('/^[a-zA-Z\.\'\-\s]+$/', $title) || strlen($title) < 1 || strlen($title) > 100) {
        return "Title must be between 1 - 100 characters long and can only contain letters, periods, apostrophes and hyphens.";
    }
    return TRUE;
}

function verifyBedrooms($bedrooms) {
    if ($bedrooms < 0 || $bedrooms > 2000) {
        return "Bedrooms should be between 0 - 2000.";
    }
    return TRUE;
}

function verifyBathrooms($bathrooms) {
    if ($bathrooms < 0 || $bathrooms > 1000) {
        return "Bathrooms should be between 0 - 1000.";
    }
    return TRUE;
}

function verifyBuildingYear($buildingYear) {
    if ($buildingYear < 1900 || $buildingYear > 2021) {
        return "Building year must be between 1900 - 2021.";
    }
    return TRUE;
}

function verifyLotArea($lotArea) {
    if ($lotArea < 50 || $lotArea > 500) {
        return "Lot area must be between 50 - 500.";
    }
    return TRUE;
}

function verifyStreetAddress($streetAddress) { //TEST REGEX
    if (!preg_match('/^[a-zA-Z0-9\.\'\-\s]+$/', $streetAddress) || strlen($streetAddress) < 1 || strlen($streetAddress) > 320) {
        return "Street address must be between 1 - 320 characters long and can only contain letters, numbers, periods, apostrophes, spaces and hyphens.";
    }
    return TRUE;
}

function verifyDescription($description) { //TEST REGEX
    if (strlen($description) < 1 || strlen($description) > 2000) {
        return "Title must be between 1 - 2000 characters long.";
    }
    return TRUE;
}
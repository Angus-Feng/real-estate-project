<?php

use Slim\Http\Response;

require_once 'vendor/autoload.php';

require_once 'init.php';

// Default admin page
$app->get('/admin', function ($request, $response, $args) {
    if (@$_SESSION['user']['role'] !== 'admin') {
        return $this->view->render($response, 'error_internal.html.twig');
    }
    return $this->view->render($response, 'admin/index_admin.html.twig');
})->setName('admin');

// Users (Buyers/Brokers)

// View a list of all buyers and their information
$itemsPerPage = 7;
$app->get('/admin/buyer/list[/{pageNo:[0-9]+}]', function ($request, $response, $args) {
    if (@$_SESSION['user']['role'] !== 'admin') {
        return $this->view->render($response, 'error_internal.html.twig');
    }
    global $itemsPerPage;
    $pageNo = $args['pageNo'] ?? 1;
    $pendingCount = DB::queryFirstField("SELECT COUNT(*) AS COUNT FROM users WHERE role=%s", "buyer");
    $maxPages = ceil($pendingCount / $itemsPerPage);
    return $this->view->render($response, 'admin/buyer_list_interface.html.twig', [
        'maxPages' => $maxPages,
        'pageNo' => $pageNo,
    ]);
});

$app->get('/buyerdata/{pageNo:[0-9]+}', function ($request, $response, $args) {
    if (@$_SESSION['user']['role'] !== 'admin') {
        return $this->view->render($response, 'error_internal.html.twig');
    }
    global $itemsPerPage;
    $pageNo = $args['pageNo'] ?? 1;
    $buyerList = DB::query("SELECT * FROM users WHERE role=%s ORDER BY id ASC LIMIT %d OFFSET %d",
            "buyer", $itemsPerPage, ($pageNo - 1) * $itemsPerPage);
    return $this->view->render($response, 'admin/load_pagination_data.html.twig', [
            'buyerList' => $buyerList
        ]);
});

// View a list of all brokers and their information
$app->get('/admin/broker/list[/{pageNo:[0-9]+}]', function ($request, $response, $args) {
    if (@$_SESSION['user']['role'] !== 'admin') {
        return $this->view->render($response, 'error_internal.html.twig');
    }
    global $itemsPerPage;
    $pageNo = $args['pageNo'] ?? 1;
    $pendingCount = DB::queryFirstField("SELECT COUNT(*) AS COUNT FROM users WHERE role=%s", "broker");
    $maxPages = ceil($pendingCount / $itemsPerPage);
    return $this->view->render($response, 'admin/broker_list_interface.html.twig', [
        'maxPages' => $maxPages,
        'pageNo' => $pageNo,
    ]);
});

$app->get('/brokerdata/{pageNo:[0-9]+}', function ($request, $response, $args) {
    if (@$_SESSION['user']['role'] !== 'admin') {
        return $this->view->render($response, 'error_internal.html.twig');
    }
    global $itemsPerPage;
    $pageNo = $args['pageNo'] ?? 1;
    $brokerList = DB::query("SELECT * FROM users WHERE role=%s ORDER BY id ASC LIMIT %d OFFSET %d",
            "broker", $itemsPerPage, ($pageNo - 1) * $itemsPerPage);
    return $this->view->render($response, 'admin/load_pagination_data.html.twig', [
            'brokerList' => $brokerList
        ]);
});

// Add user: GET
$app->get('/admin/users/add/{userType:buyer|broker}', function ($request, $response, $args) {
    if (@$_SESSION['user']['role'] !== 'admin') {
        return $this->view->render($response, 'error_internal.html.twig');
    }
    $userType = $args['userType'];
    return $this->view->render($response, 'admin/users_add.html.twig', ['userType' => $userType]);
});

// Add user: POST
$app->post('/admin/users/add/{userType:buyer|broker}', function ($request, $response, $args) {
    $userType = $args['userType'];

    $email = $request->getParam('email');
    $password1 = $request->getParam('password1');
    $password2 = $request->getParam('password2');
    $firstName = $request->getParam('firstName');
    $lastName = $request->getParam('lastName');
    $phone = $request->getParam('phone');
    $uploadedPhoto = NULL;
    if (isset($request->getUploadedFiles()['photoFilePath'])) {
        $uploadedPhoto = $request->getUploadedFiles()['photoFilePath'];
    }
    $appartmentNo = $request->getParam('appartmentNo');
    $streetAddress = $request->getParam('streetAddress');
    $city = $request->getParam('city');
    $province = $request->getParam('province');
    $postalCode = $request->getParam('postalCode');

    if ($userType === 'broker') {
        $licenseNo = $request->getParam('licenseNo');
        $company = $request->getParam('company');
        $jobTitle = $request->getParam('jobTitle');
    }

    $errorList = [];
    $errors = array(
        'email' => "", 'password1' => "", 'password2' => "",
        'firstName' => "", 'lastName' => "", 'phone' => "", 'uploadedPhoto' => "",
        'appartmentNo' => "", 'streetAddress' => "", 'city' => "", 'province' => "",
        'postalCode' => "", 'licenseNo' => "", 'company' => "", 'jobTitle' => "",
    );

    $emailVerification = verfiyEmail($email);
    if ($emailVerification !== TRUE) {
        $errorList[] = $emailVerification;
        $errors['email'] = $emailVerification;
    }
    $verifyPasswords = verifyPasswords($password1);
    if ($verifyPasswords !== TRUE) {
        $errorList[] = $verifyPasswords;
        $errors['password1'] = $verifyPasswords;
    }
    if ($password1 !== $password2) {
        $errorList[] = 'The passwords you have entered do not match.';
        $errors['password2'] = 'The passwords you have entered do not match.';
    }

    if ($appartmentNo !== "") {
        $verifyAppartmentNo = verifyAppartmentNo($appartmentNo);
        if ($verifyAppartmentNo !== TRUE) {
            $errorList[] = $verifyAppartmentNo;
            $errors['appartmentNo'] = $verifyAppartmentNo;
        }
    } else {
        $appartmentNo = NULL;
    }

    if ($streetAddress !== "") {
        $verifyStreetAddress = verifyUserStreetAddress($streetAddress);
        if ($verifyStreetAddress !== TRUE) {
            $errorList[] = $verifyStreetAddress;
            $errors['streetAddress'] = $verifyStreetAddress;
        }
    } else {
        $streetAddress = NULL;
    }

    if ($city !== "") {
        $verifyCityName = verifyCityName($city);
        if ($verifyCityName !== TRUE) {
            $errorList[] = $verifyCityName;
            $errors['city'] = $verifyCityName;
        }
    } else {
        $city = NULL;
    }

    if ($phone !== "") {
        $verifyPhone = verifyPhone($phone);
        if ($verifyPhone !== TRUE) {
            $errorList[] = $verifyPhone;
            $errors['phone'] = $verifyPhone;
        }
    } else {
        $phone = NULL;
    }

    if ($province === "") {
        $province = NULL;
    }

    if ($postalCode !== "") {
        $verifyPostalCode = verifyPostalCode($postalCode);
        if ($verifyPostalCode !== TRUE) {
            $errorList[] = $verifyPostalCode;
            $errors['postalCode'] = $verifyPostalCode;
        }
    } else {
        $postalCode = NULL;
    }

    if ($userType === 'broker') {
        $verifyLicenseNo = verifyLicenseNo($licenseNo);
        if ($verifyLicenseNo !== TRUE) {
            $errorList[] = $verifyLicenseNo;
            $errors['licenseNo'] = $verifyLicenseNo;
        }

        $verifyFirstName = verifyFirstName($firstName);
        if ($verifyFirstName !== TRUE) {
            $errorList[] = $verifyFirstName;
            $errors['firstName'] = $verifyFirstName;
        }

        $verifyLastName = verifyLastName($lastName);
        if ($verifyLastName !== TRUE) {
            $errorList[] = $verifyLastName;
            $errors['lastName'] = $verifyLastName;
        }

        $verifyCompany = verifyCompany($company);
        if ($verifyCompany !== TRUE) {
            $errorList[] = $verifyCompany;
            $errors['company'] = $verifyCompany;
        }

        if ($jobTitle !== "") {
            $verifyJobTitle = verifyJobTitle($jobTitle);
            if ($verifyJobTitle !== TRUE) {
                $errorList[] = $verifyJobTitle;
                $errors['jobTitle'] = $verifyJobTitle;
            }
        } else {
            $jobTitle = NULL;
        }
    } else { // If user is a buyer
        if ($firstName !== "") {
            $verifyFirstName = verifyFirstName($firstName);
            if ($verifyFirstName !== TRUE) {
                $errorList[] = $verifyFirstName;
                $errors['firstName'] = $verifyFirstName;
            }
        } else {
            $firstName = NULL;
        }

        if ($lastName !== "") {
            $verifyLastName = verifyLastName($lastName);
            if ($verifyLastName !== TRUE) {
                $errorList[] = $verifyLastName;
                $errors['lastName'] = $verifyLastName;
            }
        } else {
            $lastName = NULL;
        }
    }

    $photoFilePath = null;
    if ($uploadedPhoto !== NULL) {
        if ($uploadedPhoto->getError() === UPLOAD_ERR_OK) {
            if ($userType === 'broker') {
                $result = verifyUploadedBrokerProfilePhoto($uploadedPhoto, $photoFilePath, $licenseNo);
                if ($result !== TRUE) {
                    $errorList[] = $result;
                    $errors['uploadedPhoto'] = $result;
                }
            } else if ($userType === 'buyer') {
                $result = verifyUploadedBuyerProfilePhoto($uploadedPhoto, $photoFilePath);
                if ($result !== TRUE) {
                    $errorList[] = $result;
                    $errors['uploadedPhoto'] = $result;
                }
            }
        }
    }


    if ($errorList) {
        return $this->view->render($response, 'admin/users_add.html.twig', ['errorList' => $errorList, 'userType' => $userType, 'errors' => $errors]);
    }

    if ($userType === 'buyer') {
        DB::insert('users', [
            'email' => $email,
            'password' => password_hash($password1, PASSWORD_DEFAULT),
            'role' => 'buyer',
            'firstName' => $firstName,
            'lastName' => $lastName,
            'phone' => $phone,
            'photoFilePath' => $photoFilePath,
            'appartmentNo' => $appartmentNo,
            'streetAddress' => $streetAddress,
            'city' => $city,
            'province' => $province,
            'postalCode' => $postalCode
        ]);
    } else {
        DB::insert('users', [
            'email' => $email,
            'password' => password_hash($password1, PASSWORD_DEFAULT, ['cost' => 12]),
            'role' => 'broker',
            'licenseNo' => $licenseNo,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'phone' => $phone,
            'company' => $company,
            'jobTitle' => $jobTitle,
            'photoFilePath' => $photoFilePath,
            'appartmentNo' => $appartmentNo,
            'streetAddress' => $streetAddress,
            'city' => $city,
            'province' => $province,
            'postalCode' => $postalCode
        ]);
    }

    return $this->view->render($response, 'admin/modification_success.html.twig');
});

// Edit user: GET
$app->get('/admin/users/edit/{id:[0-9]+}', function ($request, $response, $args) {
    if (@$_SESSION['user']['role'] !== 'admin') {
        return $this->view->render($response, 'error_internal.html.twig');
    }
    $user = DB::queryFirstRow("SELECT * FROM users WHERE id=%d", $args['id']);
    if (!$user) {
        return $this->view->render($response, '404_error.html.twig');
    }
    return $this->view->render($response, 'admin/users_edit.html.twig', ['user' => $user]);
});

// Edit user: POST
$app->post('/admin/users/edit/{id:[0-9]+}', function ($request, $response, $args) {
    $user = DB::queryFirstRow("SELECT * FROM users WHERE id=%d", $args['id']);

    // Check if id is an admin and stop it from being editable 
    if ($user['role'] === 'admin') { // TODO
        return $this->view->render($response, 'admin/users_edit.html.twig', ['user' => $user]);
    }

    $id = $args['id'];
    $email = $request->getParam('email');
    $password1 = $request->getParam('password1');
    $password2 = $request->getParam('password2');
    $firstName = $request->getParam('firstName');
    $lastName = $request->getParam('lastName');
    $phone = $request->getParam('phone');
    $uploadedPhoto = NULL;
    if (isset($request->getUploadedFiles()['photoFilePath'])) {
        $uploadedPhoto = $request->getUploadedFiles()['photoFilePath'];
    }
    $appartmentNo = $request->getParam('appartmentNo');
    $streetAddress = $request->getParam('streetAddress');
    $city = $request->getParam('city');
    $province = $request->getParam('province');
    $postalCode = $request->getParam('postalCode');

    if ($user['role'] === 'broker') {
        $licenseNo = $request->getParam('licenseNo');
        $company = $request->getParam('company');
        $jobTitle = $request->getParam('jobTitle');
    }

    $errorList = [];
    $errors = array(
        'email' => "", 'password1' => "", 'password2' => "",
        'firstName' => "", 'lastName' => "", 'phone' => "", 'uploadedPhoto' => "",
        'appartmentNo' => "", 'streetAddress' => "", 'city' => "", 'province' => "",
        'postalCode' => "", 'licenseNo' => "", 'company' => "", 'jobTitle' => "",
    );

    $emailVerification = verfiyEmailUpdate($email, $id);
    if ($emailVerification !== TRUE) {
        $errorList[] = $emailVerification;
        $errors['email'] = $emailVerification;
    }
    
    if ($password1 === "" && $password2 === "") {
        $password1 = $user['password'];
    } else if ($password1 !== $password2) {
        $errorList[] = verifyPasswords($password1);
        $errorList[] = 'The passwords you have entered do not match.';
        $errors['password2'] = 'The passwords you have entered do not match.';
    } else {
        if ($user['role'] === 'buyer') {
            $password1 = password_hash($password1, PASSWORD_DEFAULT);
        } else {
            $password1 = password_hash($password1, PASSWORD_DEFAULT, ['cost' => 12]);
        }
    }

    if ($appartmentNo !== "") {
        $verifyAppartmentNo = verifyAppartmentNo($appartmentNo);
        if ($verifyAppartmentNo !== TRUE) {
            $errorList[] = $verifyAppartmentNo;
            $errors['appartmentNo'] = $verifyAppartmentNo;
        }
    } else {
        $appartmentNo = NULL;
    }

    if ($streetAddress !== "") {
        $verifyStreetAddress = verifyUserStreetAddress($streetAddress);
        if ($verifyStreetAddress !== TRUE) {
            $errorList[] = $verifyStreetAddress;
            $errors['streetAddress'] = $verifyStreetAddress;
        }
    } else {
        $streetAddress = NULL;
    }

    if ($city !== "") {
        $verifyCityName = verifyCityName($city);
        if ($verifyCityName !== TRUE) {
            $errorList[] = $verifyCityName;
            $errors['city'] = $verifyCityName;
        }
    } else {
        $city = NULL;
    }

    if ($phone !== "") {
        $verifyPhone = verifyPhone($phone);
        if ($verifyPhone !== TRUE) {
            $errorList[] = $verifyPhone;
            $errors['phone'] = $verifyPhone;
        }
    } else {
        $phone = NULL;
    }

    if ($province === "") {
        $province = NULL;
    }

    if ($postalCode !== "") {
        $verifyPostalCode = verifyPostalCode($postalCode);
        if ($verifyPostalCode !== TRUE) {
            $errorList[] = $verifyPostalCode;
            $errors['postalCode'] = $verifyPostalCode;
        }
    } else {
        $postalCode = NULL;
    }

    if ($user['role'] === 'broker') {
        $verifyLicenseNo = verifyLicenseNoUpdate($licenseNo, $id);
        if ($verifyLicenseNo !== TRUE) {
            $errorList[] = $verifyLicenseNo;
            $errors['licenseNo'] = $verifyLicenseNo;
        }

        $verifyFirstName = verifyFirstName($firstName);
        if ($verifyFirstName !== TRUE) {
            $errorList[] = $verifyFirstName;
            $errors['firstName'] = $verifyFirstName;
        }

        $verifyLastName = verifyLastName($lastName);
        if ($verifyLastName !== TRUE) {
            $errorList[] = $verifyLastName;
            $errors['lastName'] = $verifyLastName;
        }

        $verifyCompany = verifyCompany($company);
        if ($verifyCompany !== TRUE) {
            $errorList[] = $verifyCompany;
            $errors['company'] = $verifyCompany;
        }
        if ($jobTitle !== "") {
            $verifyJobTitle = verifyJobTitle($jobTitle);
            if ($verifyJobTitle !== TRUE) {
                $errorList[] = $verifyJobTitle;
                $errors['jobTitle'] = $verifyJobTitle;
            }
        } else {
            $jobTitle = NULL;
        }
    } else {
        if ($firstName !== "") {
            $verifyFirstName = verifyFirstName($firstName);
            if ($verifyFirstName !== TRUE) {
                $errorList[] = $verifyFirstName;
                $errors['firstName'] = $verifyFirstName;
            }
        } else {
            $firstName = NULL;
        }

        if ($lastName !== "") {
            $verifyLastName = verifyLastName($lastName);
            if ($verifyLastName !== TRUE) {
                $errorList[] = $verifyLastName;
                $errors['lastName'] = $verifyLastName;
            }
        } else {
            $lastName = NULL;
        }
    }

    $photoFilePath = $user['photoFilePath'];
    if ($uploadedPhoto !== NULL) {
        if ($uploadedPhoto->getError() === UPLOAD_ERR_OK) {
            if ($user['role'] === 'broker') {
                $result = verifyUploadedBrokerProfilePhoto($uploadedPhoto, $photoFilePath, $licenseNo);
                if ($result !== TRUE) {
                    $errorList[] = $result;
                    $errors['uploadedPhoto'] = $result;
                }
            } else if ($user['role'] === 'buyer') {
                $result = verifyUploadedBuyerProfilePhoto($uploadedPhoto, $photoFilePath);
                if ($result !== TRUE) {
                    $errorList[] = $result;
                    $errors['uploadedPhoto'] = $result;
                }
            }
        }
    }

    if ($errorList) {
        if (!$user) {
            $response = $response->withStatus(404);
            return $this->view->render($response, '404_error.html.twig');
        }
        return $this->view->render($response, 'admin/users_edit.html.twig', ['errorList' => $errorList, 'user' => $user, 'errors' => $errors]);
    }

    if ($user['role'] === 'buyer') {
        DB::update('users', [
            'email' => $email,
            'password' => $password1,
            'role' => 'buyer',
            'firstName' => $firstName,
            'lastName' => $lastName,
            'phone' => $phone,
            'photoFilePath' => $photoFilePath,
            'appartmentNo' => $appartmentNo,
            'streetAddress' => $streetAddress,
            'city' => $city,
            'province' => $province,
            'postalCode' => $postalCode
        ], "id=%d", $id);
    } else {
        DB::update('users', [
            'email' => $email,
            'password' => $password1,
            'licenseNo' => $licenseNo, 
            'firstName' => $firstName, 
            'lastName' => $lastName, 
            'phone' => $phone,
            'company' => $company,
            'jobTitle' => $jobTitle,
            'appartmentNo' => $appartmentNo,
            'streetAddress' => $streetAddress,
            'city' => $city,
            'province' => $province,
            'postalCode' => $postalCode
        ], "id=%d", $id);
    }

    return $this->view->render($response, 'admin/modification_success.html.twig');
});

// Delete user: GET
$app->get('/admin/users/delete/{id:[0-9]+}', function ($request, $response, $args) {
    if (@$_SESSION['user']['role'] !== 'admin') {
        return $this->view->render($response, 'error_internal.html.twig');
    }
    $id = $args['id'];
    $user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $id);
    if (!$user) {
        return $this->view->render($response, '404_error.html.twig');
    }
    return $this->view->render($response, 'admin/users_delete.html.twig', ['user' => $user]);
});

// Delete user: POST
$app->post('/admin/users/delete/{id:[0-9]+}', function ($request, $response, $args) {
    $user = DB::queryFirstRow("SELECT * FROM users WHERE id=%d", $args['id']);

    if ($user['role'] === 'admin') {
        return $this->view->render($response, '404_error.html.twig');
    }

    DB::delete('users', 'id=%d', $args['id']);

    return $this->view->render($response, 'admin/modification_success.html.twig');
});

//-----------------------------------------------------------------------------------------------------------------


// Properties

// View a list of all properties and their information
$app->get('/admin/property/list[/{pageNo:[0-9]+}]', function ($request, $response, $args) {
    if (@$_SESSION['user']['role'] !== 'admin') {
        return $this->view->render($response, 'error_internal.html.twig');
    }
    global $itemsPerPage;
    $pageNo = $args['pageNo'] ?? 1;
    $propertiesCount = DB::queryFirstField("SELECT COUNT(*) AS COUNT FROM properties");
    $maxPages = ceil($propertiesCount / $itemsPerPage);
    return $this->view->render($response, 'admin/property_list_interface.html.twig', [
        'maxPages' => $maxPages,
        'pageNo' => $pageNo,
    ]);
});

$app->get('/propertydata/{pageNo:[0-9]+}', function ($request, $response, $args) {
    if (@$_SESSION['user']['role'] !== 'admin') {
        return $this->view->render($response, 'error_internal.html.twig');
    }
    global $itemsPerPage;
    $pageNo = $args['pageNo'] ?? 1;
    $propertyList = DB::query("SELECT * FROM properties ORDER BY id ASC LIMIT %d OFFSET %d",
             $itemsPerPage, ($pageNo - 1) * $itemsPerPage);
    return $this->view->render($response, 'admin/load_pagination_data.html.twig', [
            'propertyList' => $propertyList
        ]);
});

// Add property: GET
$app->get('/admin/property/add', function ($request, $response, $args) {
    if (@$_SESSION['user']['role'] !== 'admin') {
        return $this->view->render($response, 'error_internal.html.twig');
    }
    return $this->view->render($response, 'admin/property_add.html.twig');
});

// Add property: POST
$app->post('/admin/property/add', function ($request, $response, $args) {

    $licenseNo = $request->getParam('licenseNo');
    $price = $request->getParam('price');
    $title = $request->getParam('title');
    $bedrooms = $request->getParam('bedrooms');
    $bathrooms = $request->getParam('bathrooms');
    $buildingYear = $request->getParam('buildingYear');
    $lotArea = $request->getParam('lotArea');
    $description = $request->getParam('description');
    $appartmentNo = $request->getParam('appartmentNo');
    $streetAddress = $request->getParam('streetAddress');
    $city = $request->getParam('city');
    $province = $request->getParam('province');
    $postalCode = $request->getParam('postalCode');
    $uploadedPhotos = $request->getUploadedFiles()['photos'];

    $errorList = [];
    $errors = array(
        'licenseNo' => "", 'price' => "", 'title' => "",
        'bedrooms' => "", 'bathrooms' => "", 'buildingYear' => "", 'lotArea' => "",
        'description' => "", 'appartmentNo' => "", 'streetAddress' => "", 'city' => "",
        'province' => "", 'postalCode' => "", 'uploadedPhotos' => ""
    );

    $errorPhotoCount = 1;
    foreach ($uploadedPhotos as $photo) {
        if ($photo->getError() !== UPLOAD_ERR_OK) {
            $errorList[] = 'There was an error uploading photo ' . $errorPhotoCount . ".";
            $errors['uploadedPhotos'] = 'There was an error uploading photo ' . $errorPhotoCount . ".";
            $errorPhotoCount++;
        }
        $result = verifyFileExt($photo);
        if (!$result) {
            $errorList[] = $result;
            $errors['uploadedPhotos'] = $result;
        }
    }
    $licenseCheck = DB::queryFirstRow("SELECT * FROM users WHERE licenseNo=%s", $licenseNo);
    if (!$licenseCheck['licenseNo']) {
        $errors['licenseNo'] = "You did not enter a valid broker license.";
    }
    $verifyPrice = verifyPrice($price);
    if ($verifyPrice !== TRUE) {
        $errorList[] = $verifyPrice;
        $errors['price'] = $verifyPrice;
    }
    $verifyTitle = verifyTitle($title);
    if ($verifyTitle !== TRUE) {
        $errorList[] = $verifyTitle;
        $errors['title'] = $verifyTitle;
    }
    $verifyBedrooms = verifyBedrooms($bedrooms);
    if ($verifyBedrooms !== TRUE) {
        $errorList[] = $verifyBedrooms;
        $errors['bedrooms'] = $verifyBedrooms;
    }
    $verifyBathrooms = verifyBathrooms($bathrooms);
    if ($verifyBathrooms !== TRUE) {
        $errorList[] = $verifyBathrooms;
        $errors['bathrooms'] = $verifyBathrooms;
    }
    $verifyBuildingYear = verifyBuildingYear($buildingYear);
    if ($verifyBuildingYear !== TRUE) {
        $errorList[] = $verifyBuildingYear;
        $errors['buildingYear'] = $verifyBuildingYear;
    }
    $verifyLotArea = verifyLotArea($lotArea);
    if ($verifyLotArea !== TRUE) {
        $errorList[] = $verifyLotArea;
        $errors['lotArea'] = $verifyLotArea;
    }
    $verifyDescription = verifyDescription($description);
    if ($verifyDescription !== TRUE) {
        $errorList[] = $verifyDescription;
        $errors['description'] = $verifyDescription;
    }
    if ($appartmentNo !== "") {
        $verifyAppartmentNo = verifyAppartmentNo($appartmentNo);
        if ($verifyAppartmentNo !== TRUE) {
            $errorList[] = $verifyAppartmentNo;
            $errors['appartmentNo'] = $verifyAppartmentNo;
        }
    } else {
        $appartmentNo = NULL;
    }
    $verifyStreetAddress = verifyStreetAddress($streetAddress);
    if ($verifyStreetAddress !== TRUE) {
        $errorList[] = $verifyStreetAddress;
        $errors['streetAddress'] = $verifyStreetAddress;
    }
    $verifyCityName = verifyCityNameManditory($city);
    if ($verifyCityName !== TRUE) {
        $errorList[] = $verifyCityName;
        $errors['city'] = $verifyCityName;
    }
    // $verifyProvince = verifyProvince($province);
    // if ($verifyProvince !== TRUE) {
    //     $errorList[] = $verifyProvince;
    // }
    $verifyPostalCode = verifyPostalCodeManditory($postalCode);
    if ($verifyPostalCode !== TRUE) {
        $errorList[] = $verifyPostalCode;
        $errors['postalCode'] = $verifyPostalCode;
    }

    if ($errorList) {
        return $this->view->render($response, 'admin/property_add.html.twig', ['errorList' => $errorList, 'errors' => $errors]);
    }

    $brokerId = DB::queryFirstRow("SELECT id FROM users WHERE licenseNo=%s", $licenseNo);

    DB::insert('properties', [
        'brokerId' => $brokerId['id'],
        'price' => $price,
        'title' => $title,
        'bedrooms' => $bedrooms,
        'bathrooms' => $bathrooms,
        'buildingYear' => $buildingYear,
        'lotArea' => $lotArea,
        'description' => $description,
        'appartmentNo' => $appartmentNo,
        'streetAddress' => $streetAddress,
        'city' => $city,
        'province' => $province,
        'postalCode' => $postalCode
    ]);

    $propertyId = DB::insertId();

    $photoPathArray = [];
    $firstPhoto = TRUE;
    $photoFilePath = null;

    foreach ($uploadedPhotos as $photo) {
        $result = verifyUploadedHousePhoto($photo, $photoFilePath, $propertyId, $firstPhoto);
        if ($result === TRUE) {
            $photoPathArray[] = $photoFilePath;
            $firstPhoto = FALSE;
        }
    }

    $photoNo = 0;
    foreach ($photoPathArray as $photoFilePath) {
        DB::insert('propertyphotos', ['propertyId' => $propertyId, 'ordinalINT' => $photoNo, 'photoFilePath' => $photoFilePath]);
        $photoNo++;
    }

    return $this->view->render($response, 'admin/modification_success.html.twig');
});

// Edit property: GET
$app->get('/admin/property/edit/{id:[0-9]+}', function ($request, $response, $args) {
    if (@$_SESSION['user']['role'] !== 'admin') {
        return $this->view->render($response, 'error_internal.html.twig');
    }
    $property = DB::queryFirstRow("SELECT * FROM properties WHERE id=%d", $args['id']);
    $brokerId = DB::queryFirstRow("SELECT brokerId FROM properties WHERE id=%d", $args['id']);
    $broker = DB::queryFirstRow("SELECT licenseNo, firstName, lastName FROM users WHERE id=%d", $brokerId['brokerId']);
    $propertyPhotos = DB::query("SELECT * FROM propertyphotos WHERE propertyId=%d ORDER BY ordinalINT ASC", $property['id']);
    if (!$property) {
        // $response = $response->withStatus(404);
        return $this->view->render($response, '404_error.html.twig');
    }
    return $this->view->render($response, 'admin/property_edit.html.twig', ['property' => $property, 'broker' => $broker, 'propertyPhotos' => $propertyPhotos, 'propertyId' => $property['id']]);
});

// Edit property: POST
$app->post('/admin/property/edit/{id:[0-9]+}', function ($request, $response, $args) {
    $licenseNo = $request->getParam('licenseNo');
    $price = $request->getParam('price');
    $title = $request->getParam('title');
    $bedrooms = $request->getParam('bedrooms');
    $bathrooms = $request->getParam('bathrooms');
    $buildingYear = $request->getParam('buildingYear');
    $lotArea = $request->getParam('lotArea');
    $description = $request->getParam('description');
    $appartmentNo = $request->getParam('appartmentNo');
    $streetAddress = $request->getParam('streetAddress');
    $city = $request->getParam('city');
    $province = $request->getParam('province');
    $postalCode = $request->getParam('postalCode');
    $uploadedPhotos = $request->getUploadedFiles()['photos'];

    $errorList = [];
    $errors = array(
        'licenseNo' => "", 'price' => "", 'title' => "",
        'bedrooms' => "", 'bathrooms' => "", 'buildingYear' => "", 'lotArea' => "",
        'description' => "", 'appartmentNo' => "", 'streetAddress' => "", 'city' => "",
        'province' => "", 'postalCode' => "", 'uploadedPhotos' => ""
    );

    if (is_object($uploadedPhotos)) {
        $errorPhotoCount = 1;
        foreach ($uploadedPhotos as $photo) {
            if ($photo->getError() !== UPLOAD_ERR_OK) {
                $errorList[] = 'There was an error uploading photo ' . $errorPhotoCount . ".";
                $errorPhotoCount++;
            }
            $result = verifyFileExt($photo);
            if (!$result) {
                $errorList[] = $result;
                $errors['uploadedPhotos'] = $result;
            }
        }
    }

    $licenseCheck = DB::queryFirstRow("SELECT * FROM users WHERE licenseNo=%s", $licenseNo);
    if (!$licenseCheck['licenseNo']) {
        $errors['licenseNo'] = "You did not enter a valid broker license.";
    }
    $verifyPrice = verifyPrice($price);
    if ($verifyPrice !== TRUE) {
        $errorList[] = $verifyPrice;
        $errors['price'] = $verifyPrice;
    }
    $verifyTitle = verifyTitle($title);
    if ($verifyTitle !== TRUE) {
        $errorList[] = $verifyTitle;
        $errors['title'] = $verifyTitle;
    }
    $verifyBedrooms = verifyBedrooms($bedrooms);
    if ($verifyBedrooms !== TRUE) {
        $errorList[] = $verifyBedrooms;
        $errors['bedrooms'] = $verifyBedrooms;
    }
    $verifyBathrooms = verifyBathrooms($bathrooms);
    if ($verifyBathrooms !== TRUE) {
        $errorList[] = $verifyBathrooms;
        $errors['bathrooms'] = $verifyBathrooms;
    }
    $verifyBuildingYear = verifyBuildingYear($buildingYear);
    if ($verifyBuildingYear !== TRUE) {
        $errorList[] = $verifyBuildingYear;
        $errors['buildingYear'] = $verifyBuildingYear;
    }
    $verifyLotArea = verifyLotArea($lotArea);
    if ($verifyLotArea !== TRUE) {
        $errorList[] = $verifyLotArea;
        $errors['lotArea'] = $verifyLotArea;
    }
    $verifyDescription = verifyDescription($description);
    if ($verifyDescription !== TRUE) {
        $errorList[] = $verifyDescription;
        $errors['description'] = $verifyDescription;
    }
    if ($appartmentNo !== "") {
        $verifyAppartmentNo = verifyAppartmentNo($appartmentNo);
        if ($verifyAppartmentNo !== TRUE) {
            $errorList[] = $verifyAppartmentNo;
            $errors['appartmentNo'] = $verifyAppartmentNo;
        }
    } else {
        $appartmentNo = NULL;
    }
    $verifyStreetAddress = verifyStreetAddress($streetAddress);
    if ($verifyStreetAddress !== TRUE) {
        $errorList[] = $verifyStreetAddress;
        $errors['streetAddress'] = $verifyStreetAddress;
    }
    $verifyCityName = verifyCityName($city);
    if ($verifyCityName !== TRUE) {
        $errorList[] = $verifyCityName;
        $errors['city'] = $verifyCityName;
    }
    // $verifyProvince = verifyProvince($province);
    // if ($verifyProvince !== TRUE) {
    //     $errorList[] = $verifyProvince;
    // }
    $verifyPostalCode = verifyPostalCode($postalCode);
    if ($verifyPostalCode !== TRUE) {
        $errorList[] = $verifyPostalCode;
        $errors['postalCode'] = $verifyPostalCode;
    }

    if ($errorList) {
        $property = DB::queryFirstRow("SELECT * FROM properties WHERE id=%d", $args['id']);
        $brokerId = DB::queryFirstRow("SELECT brokerId FROM properties WHERE id=%d", $args['id']);
        $broker = DB::queryFirstRow("SELECT licenseNo, firstName, lastName FROM users WHERE id=%d", $brokerId['brokerId']);
        $propertyPhotos = DB::query("SELECT * FROM propertyphotos WHERE propertyId=%d ORDER BY ordinalINT ASC", $property['id']);
        return $this->view->render($response, 'admin/property_edit.html.twig', ['property' => $property, 'broker' => $broker, 'propertyPhotos' => $propertyPhotos, 'propertyId' => $property['id'], 'errorList' => $errorList, 'errors' => $errors]);
    }

    DB::update('properties', [
        'price' => $price,
        'title' => $title,
        'bedrooms' => $bedrooms,
        'bathrooms' => $bathrooms,
        'buildingYear' => $buildingYear,
        'lotArea' => $lotArea,
        'description' => $description,
        'appartmentNo' => $appartmentNo,
        'streetAddress' => $streetAddress,
        'city' => $city,
        'province' => $province,
        'postalCode' => $postalCode
    ], "id=%d", $args['id']);


    $previousOrdinal = DB::queryFirstRow("SELECT ordinalINT FROM propertyphotos WHERE propertyId=%d ORDER BY ordinalINT DESC", $args['id']);
    $photoNo = 0;
    $firstPhoto = TRUE;
    if ($previousOrdinal) {
        $photoNo = $previousOrdinal['ordinalINT'] + 1;
        $firstPhoto = FALSE;
    }
    $photoPathArray = [];
    $photoFilePath = null;

    foreach ($uploadedPhotos as $photo) {
        $result = verifyUploadedHousePhoto($photo, $photoFilePath, $args['id'], $firstPhoto);
        if ($result === TRUE) {
            $photoPathArray[] = $photoFilePath;
        }
    }

    foreach ($photoPathArray as $photoFilePath) {
        DB::insert('propertyphotos', ['propertyId' => $args['id'], 'ordinalINT' => $photoNo, 'photoFilePath' => $photoFilePath]);
        $photoNo++;
    }
  

    return $this->view->render($response, 'admin/modification_success.html.twig');
});

// Edit Property Image: POST
$app->post('/admin/property/edit/reorder', function ($request, $response, $args) {

    $values = json_decode($request->getBody(), true);
    $propertyId = $values['propertyId'];
    $ids = $values['ids'];

    $propertyPhotos = DB::query("SELECT * FROM propertyphotos WHERE propertyId=%d ORDER BY ordinalINT ASC", $propertyId);

    $counter = 0;
    foreach($propertyPhotos as $photo) {
        print_r($photo['id']);
        echo "<br>";
        DB::update('propertyphotos', [
            'ordinalINT' => $ids[$counter]
        ], "id=%d", $photo['id']);
        $counter++;
    }

    $newPhotoFilePath = DB::queryFirstRow("SELECT * FROM propertyphotos WHERE propertyId=%d ORDER BY ordinalINT ASC", $propertyId);
    $newFileName = '/thmb-' . $newPhotoFilePath['photoFilePath'];

    $files = glob('uploads/' . $propertyId . '/*');
    foreach($files as $file){
        if(strpos($file, 'thmb-') !== false) {
            unlink($file);
        }
    }

    copy('uploads/' . $propertyId . '/640p-' . $newPhotoFilePath['photoFilePath'], 'uploads/' . $propertyId . $newFileName);
});

// Delete property: GET
$app->get('/admin/property/delete/{id:[0-9]+}', function ($request, $response, $args) {
    if (@$_SESSION['user']['role'] !== 'admin') {
        return $this->view->render($response, 'error_internal.html.twig');
    }
    $id = $args['id'];
    $property = DB::queryFirstRow("SELECT p.id, u.firstName, u.lastName, u.licenseNo, p.price, p.title, p.streetAddress, p.postalCode 
        FROM properties AS p, users AS u WHERE u.id=p.brokerId AND p.id=%d", $id);

    if (!$property) {
        // $response = $response->withStatus(404);
        return $this->view->render($response, '404_error.html.twig');
    }

    return $this->view->render($response, 'admin/property_delete.html.twig', ['property' => $property]);
});

// Delete property: POST
$app->post('/admin/property/delete/{id:[0-9]+}', function ($request, $response, $args) {
    DB::delete('propertyphotos', 'propertyId=%d', $args['id']);

    deleteAllPropertyPhotosAndFolder('uploads/' . $args['id'], $args['id']);

    DB::delete('properties', 'id=%d', $args['id']);

    return $this->view->render($response, 'admin/modification_success.html.twig');
});
//-----------------------------------------------------------------------------------------------------------------

// Pending Brokers

// View a list of all pending brokers and their information
$app->get('/admin/pending/list[/{pageNo:[0-9]+}]', function ($request, $response, $args) {
    if (@$_SESSION['user']['role'] !== 'admin') {
        return $this->view->render($response, 'error_internal.html.twig');
    }
    global $itemsPerPage;
    $pageNo = $args['pageNo'] ?? 1;
    $pendingCount = DB::queryFirstField("SELECT COUNT(*) AS COUNT FROM brokerpendinglist");
    $maxPages = ceil($pendingCount / $itemsPerPage);
    return $this->view->render($response, 'admin/pending_list_interface.html.twig', [
        'maxPages' => $maxPages,
        'pageNo' => $pageNo,
    ]);
});

$app->get('/pendingdata/{pageNo:[0-9]+}', function ($request, $response, $args) {
    if (@$_SESSION['user']['role'] !== 'admin') {
        return $this->view->render($response, 'error_internal.html.twig');
    }
    global $itemsPerPage;
    $pageNo = $args['pageNo'] ?? 1;
    $pendingList = DB::query("SELECT * FROM brokerpendinglist ORDER BY id ASC LIMIT %d OFFSET %d",
             $itemsPerPage, ($pageNo - 1) * $itemsPerPage);
    return $this->view->render($response, 'admin/load_pagination_data.html.twig', [
            'pendingList' => $pendingList
        ]);
});

$app->get('/admin/pending/{choice:accept|decline}/{id:[0-9]+}', function ($request, $response, $args) {
    if (@$_SESSION['user']['role'] !== 'admin') {
        return $this->view->render($response, 'error_internal.html.twig');
    }
    $choice = $args['choice'];
    $userId = $args['id'];
    $pendingUser = DB::queryFirstRow("SELECT * FROM brokerpendinglist WHERE userId=%d", $userId);
    return $this->view->render($response, 'admin/pending_confirm.html.twig', ['choice' => $choice, 'pendingUser' => $pendingUser]);
});


// Pending Broker choice handler: POST
$app->post('/admin/pending/{choice:accept|decline}/{id:[0-9]+}', function ($request, $response, $args) {
    $choice = $args['choice'];
    $userId = $args['id'];

    if ($choice === "accept") {
        $pendingUser = DB::queryFirstRow("SELECT * FROM brokerpendinglist WHERE userId=%d", $userId);

        DB::update('users', [
            'role' => 'broker',
            'licenseNo' => $pendingUser['licenseNo'],
            'firstName' => $pendingUser['firstName'],
            'lastName' => $pendingUser['lastName'],
            'company' => $pendingUser['company']
        ], "id=%d", $userId);

        DB::delete('brokerpendinglist', 'userId=%d', $userId);
    } else {
        DB::delete('brokerpendinglist', 'userId=%d', $userId);
    }

    return $this->view->render($response, 'admin/modification_success.html.twig');
});
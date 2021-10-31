<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

// Default admin page
$app->get('/admin', function ($request, $response, $args) {
    return $this->view->render($response, 'admin/index_admin.html.twig'); // COMMENTED OUT FOR TESTING.
})->setName('admin');

// Test interface
$app->get('/admin/interface', function ($request, $response, $args) {
    return $this->view->render($response, 'admin/interface_admin.html.twig');
})->setName('admin');


// Users (Buyers/Brokers)

// View a list of all users and their information
$app->get('/admin/users/list', function ($request, $response, $args) {
    $userList = DB::query("SELECT * FROM users");
    return $this->view->render($response, 'admin/users_list.html.twig', ['usersList' => $userList]);
});

// Add user: GET
$app->get('/admin/users/add/{userType:buyer|broker}', function ($request, $response, $args) {
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
    $uploadedPhoto= $request->getUploadedFiles()['photoFilePath'];
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

    $emailVerification = verfiyEmail($email);
    if ($emailVerification !== TRUE) {
        $errorList[] = $emailVerification;
    }
    $verifyPasswords = verifyPasswords($password1);
    if ($verifyPasswords !== TRUE) {
        $errorList[] = $verifyPasswords;
    }
    if ($password1 !== $password2) {
        $errorList[] = 'The passwords you have entered do not match.';
    }

    if ($appartmentNo !== "") {
        $verifyAppartmentNo = verifyAppartmentNo($appartmentNo);
        if ($verifyAppartmentNo !== TRUE) {
            $errorList[] = $verifyAppartmentNo;
        }
    } else {
        $appartmentNo = NULL;
    }

    if ($streetAddress !== "") {
        $verifyStreetAddress = verifyUserStreetAddress($streetAddress);
        if ($verifyStreetAddress !== TRUE) {
            $errorList[] = $verifyStreetAddress;
        }
    } else {
        $streetAddress = NULL;
    }

    if ($city !== "") {
        $verifyCityName = verifyCityName($city);
        if ($verifyCityName !== TRUE) {
            $errorList[] = $verifyCityName;
        }
    } else {
        $city = NULL;
    }

    if ($phone !== "") {
        $verifyPhone = verifyPhone($phone);
        if ($verifyPhone !== TRUE) {
            $errorList[] = $verifyPhone;
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
        }
    } else {
        $postalCode = NULL;
    }

    if ($userType === 'broker') {
        $verifyLicenseNo = verifyLicenseNo($licenseNo);
        if ($verifyLicenseNo !== TRUE) {
            $errorList[] = $verifyLicenseNo;
        }

        $verifyFirstName = verifyFirstName($firstName);
        if ($verifyFirstName !== TRUE) {
            $errorList[] = $verifyFirstName;
        }

        $verifyLastName = verifyLastName($lastName);
        if ($verifyLastName !== TRUE) {
            $errorList[] = $verifyLastName;
        }

        $verifyCompany = verifyCompany($company);
        if ($verifyCompany !== TRUE) {
            $errorList[] = $verifyCompany;
        }

        if ($jobTitle !== "") {
            $verifyJobTitle = verifyJobTitle($jobTitle);
            if ($verifyJobTitle !== TRUE) {
                $errorList[] = $verifyJobTitle;
            }
        } else {
            $jobTitle = NULL;
        }
    } else { // If user is a buyer
        if ($firstName !== "") {
            $verifyFirstName = verifyFirstName($firstName);
            if ($verifyFirstName !== TRUE) {
                $errorList[] = $verifyFirstName;
            }
        } else {
            $firstName = NULL;
        }

        if ($lastName !== "") {
            $verifyLastName = verifyLastName($lastName);
            if ($verifyLastName !== TRUE) {
                $errorList[] = $verifyLastName;
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
                }
            } else if ($userType === 'buyer') {
                $result = verifyUploadedBuyerProfilePhoto($uploadedPhoto, $photoFilePath);
                if ($result !== TRUE) {
                    $errorList[] = $result;
                }
            }
        }
    }


    if ($errorList) {
        return $this->view->render($response, 'admin/users_add.html.twig', ['errorList' => $errorList, 'userType' => $userType]);
    }

    if ($userType === 'buyer') {
        DB::insert('users', [
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
        ]);
    } else {
        DB::insert('users', [
            'email' => $email,
            'password' => $password1,
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
    $user = DB::queryFirstRow("SELECT * FROM users WHERE id=%d", $args['id']);
    if (!$user) {
        // $response = $response->withStatus(404);
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
    $uploadedPhoto= $request->getUploadedFiles()['photoFilePath'];
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

    $emailVerification = verfiyEmailUpdate($email, $id);
    if ($emailVerification !== TRUE) {
        $errorList[] = $emailVerification;
    }
    if ($password1 === "" && $password2 === "") {
        $password1 = $user['password'];
    } else {
        $errorList[] = verifyPasswords($password1);
        if ($password1 !== $password2) {
            $errorList[] = 'The passwords you have entered do not match.';
        }
    }

    if ($appartmentNo !== "") {
        $verifyAppartmentNo = verifyAppartmentNo($appartmentNo);
        if ($verifyAppartmentNo !== TRUE) {
            $errorList[] = $verifyAppartmentNo;
        }
    } else {
        $appartmentNo = NULL;
    }

    if ($streetAddress !== "") {
        $verifyStreetAddress = verifyUserStreetAddress($streetAddress);
        if ($verifyStreetAddress !== TRUE) {
            $errorList[] = $verifyStreetAddress;
        }
    } else {
        $streetAddress = NULL;
    }

    if ($city !== "") {
        $verifyCityName = verifyCityName($city);
        if ($verifyCityName !== TRUE) {
            $errorList[] = $verifyCityName;
        }
    } else {
        $city = NULL;
    }

    if ($phone !== "") {
        $verifyPhone = verifyPhone($phone);
        if ($verifyPhone !== TRUE) {
            $errorList[] = $verifyPhone;
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
        }
    } else {
        $postalCode = NULL;
    }

    if ($user['role'] === 'broker') {
        $verifyLicenseNo = verifyLicenseNoUpdate($licenseNo, $id);
        if ($verifyLicenseNo !== TRUE) {
            $errorList[] = $verifyLicenseNo;
        }

        $verifyFirstName = verifyFirstName($firstName);
        if ($verifyFirstName !== TRUE) {
            $errorList[] = $verifyFirstName;
        }

        $verifyLastName = verifyLastName($lastName);
        if ($verifyLastName !== TRUE) {
            $errorList[] = $verifyLastName;
        }

        $verifyCompany = verifyCompany($company);
        if ($verifyCompany !== TRUE) {
            $errorList[] = $verifyCompany;
        }
        if ($jobTitle !== "") {
            $verifyJobTitle = verifyJobTitle($jobTitle);
            if ($verifyJobTitle !== TRUE) {
                $errorList[] = $verifyJobTitle;
            }
        } else {
            $jobTitle = NULL;
        }
    } else {
        if ($firstName !== "") {
            $verifyFirstName = verifyFirstName($firstName);
            if ($verifyFirstName !== TRUE) {
                $errorList[] = $verifyFirstName;
            }
        } else {
            $firstName = NULL;
        }

        if ($lastName !== "") {
            $verifyLastName = verifyLastName($lastName);
            if ($verifyLastName !== TRUE) {
                $errorList[] = $verifyLastName;
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
                }
            } else if ($user['role'] === 'buyer') {
                $result = verifyUploadedBuyerProfilePhoto($uploadedPhoto, $photoFilePath);
                if ($result !== TRUE) {
                    $errorList[] = $result;
                }
            }
        }
    }

    if ($errorList) {
        if (!$user) {
            $response = $response->withStatus(404);
            return $this->view->render($response, '404_error.html.twig');
        }
        return $this->view->render($response, 'admin/users_edit.html.twig', ['errorList' => $errorList, 'user' => $user]);
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
    $id = $args['id'];
    // if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    //     $app->redirect('/forbidden');
    //     return;
    // }

    $user = DB::queryFirstRow("SELECT * FROM users WHERE id=%i", $id);
    if (!$user) {
        // $response = $response->withStatus(404);
        return $this->view->render($response, '404_error.html.twig');
    }
    return $this->view->render($response, 'admin/users_delete.html.twig', ['user' => $user]);
});

// Delete user: POST
$app->post('/admin/users/delete/{id:[0-9]+}', function ($request, $response, $args) {
    $user = DB::queryFirstRow("SELECT * FROM users WHERE id=%d", $args['id']);

    // Check if id is an admin and stop it from being editable 
    if ($user['role'] === 'admin') { // TODO
        return $this->view->render($response, '404_error.html.twig');
    }

    DB::delete('users', 'id=%d', $args['id']);

    return $this->view->render($response, 'admin/modification_success.html.twig');
});

//-----------------------------------------------------------------------------------------------------------------


// Properties

// View a list of all properties and their information
$app->get('/admin/property/list', function ($request, $response, $args) {
    $propertyList = DB::query("SELECT * FROM properties");
    return $this->view->render($response, 'admin/property_list_interface.html.twig', ['propertyList' => $propertyList]);
});

// Add property: GET
$app->get('/admin/property/add', function ($request, $response, $args) {
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

    $errorPhotoCount = 1;
    foreach ($uploadedPhotos as $photo) {
        if ($photo->getError() !== UPLOAD_ERR_OK) {
            $errorList[] = 'There was an error uploading photo ' . $errorPhotoCount . ".";
            $errorPhotoCount++;
        }
        $result = verifyFileExt($photo);
        if (!$result) {
            $errorList[] = $result;
        }
    }
    $verifyPrice = verifyPrice($price);
    if ($verifyPrice !== TRUE) {
        $errorList[] = $verifyPrice;
    }
    $verifyTitle = verifyTitle($title);
    if ($verifyTitle !== TRUE) {
        $errorList[] = $verifyTitle;
    }
    $verifyBedrooms = verifyBedrooms($bedrooms);
    if ($verifyBedrooms !== TRUE) {
        $errorList[] = $verifyBedrooms;
    }
    $verifyBathrooms = verifyBathrooms($bathrooms);
    if ($verifyBathrooms !== TRUE) {
        $errorList[] = $verifyBathrooms;
    }
    $verifyBuildingYear = verifyBuildingYear($buildingYear);
    if ($verifyBuildingYear !== TRUE) {
        $errorList[] = $verifyBuildingYear;
    }
    $verifyLotArea = verifyLotArea($lotArea);
    if ($verifyLotArea !== TRUE) {
        $errorList[] = $verifyLotArea;
    }
    $verifyDescription = verifyDescription($description);
    if ($verifyDescription !== TRUE) {
        $errorList[] = $verifyDescription;
    }
    if ($appartmentNo !== "") {
        $verifyAppartmentNo = verifyAppartmentNo($appartmentNo);
        if ($verifyAppartmentNo !== TRUE) {
            $errorList[] = $verifyAppartmentNo;
        }
    } else {
        $appartmentNo = NULL;
    }
    $verifyStreetAddress = verifyStreetAddress($streetAddress);
    if ($verifyStreetAddress !== TRUE) {
        $errorList[] = $verifyStreetAddress;
    }
    $verifyCityName = verifyCityName($city);
    if ($verifyCityName !== TRUE) {
        $errorList[] = $verifyCityName;
    }
    // $verifyProvince = verifyProvince($province);
    // if ($verifyProvince !== TRUE) {
    //     $errorList[] = $verifyProvince;
    // }
    $verifyPostalCode = verifyPostalCode($postalCode);
    if ($verifyPostalCode !== TRUE) {
        $errorList[] = $verifyPostalCode;
    }

    if ($errorList) {
        return $this->view->render($response, 'admin/property_add.html.twig', ['errorList' => $errorList]);
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
$app->get('/admin/property/edit/{id:[0-9]+}', function ($request, $response, $args) { // TODO
    $property = DB::queryFirstRow("SELECT * FROM properties WHERE id=%d", $args['id']);
    $brokerId = DB::queryFirstRow("SELECT brokerId FROM properties WHERE id=%d", $args['id']);
    $broker = DB::queryFirstRow("SELECT licenseNo, firstName, lastName FROM users WHERE id=%d", $brokerId['brokerId']);
    $propertyPhotos = DB::query("SELECT * FROM propertyphotos WHERE propertyId=%d", $property['id']);
    if (!$property) {
        // $response = $response->withStatus(404);
        return $this->view->render($response, '404_error.html.twig');
    }
    $fileNameList = array();
    foreach($propertyPhotos as $propertyPhoto) {
        $fileName = explode("/", $propertyPhoto['photoFilePath'], 3);
        $fileNameList[$fileName[2]] = $propertyPhoto['ordinalINT'];
    }
    return $this->view->render($response, 'admin/property_edit.html.twig', ['property' => $property, 'broker' => $broker, 'propertyPhotos' => $propertyPhotos, 'fileNameList' => $fileNameList, 'propertyId' => $property['id']]);
});

// Edit property: POST
$app->post('/admin/property/edit/{id:[0-9]+}', function ($request, $response, $args) { // TODO
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

    $errorPhotoCount = 1;
    foreach ($uploadedPhotos as $photo) {
        if ($photo->getError() !== UPLOAD_ERR_OK) {
            $errorList[] = 'There was an error uploading photo ' . $errorPhotoCount . ".";
            $errorPhotoCount++;
        }
        $result = verifyFileExt($photo);
        if (!$result) {
            $errorList[] = $result;
        }
    }
    $verifyPrice = verifyPrice($price);
    if ($verifyPrice !== TRUE) {
        $errorList[] = $verifyPrice;
    }
    $verifyTitle = verifyTitle($title);
    if ($verifyTitle !== TRUE) {
        $errorList[] = $verifyTitle;
    }
    $verifyBedrooms = verifyBedrooms($bedrooms);
    if ($verifyBedrooms !== TRUE) {
        $errorList[] = $verifyBedrooms;
    }
    $verifyBathrooms = verifyBathrooms($bathrooms);
    if ($verifyBathrooms !== TRUE) {
        $errorList[] = $verifyBathrooms;
    }
    $verifyBuildingYear = verifyBuildingYear($buildingYear);
    if ($verifyBuildingYear !== TRUE) {
        $errorList[] = $verifyBuildingYear;
    }
    $verifyLotArea = verifyLotArea($lotArea);
    if ($verifyLotArea !== TRUE) {
        $errorList[] = $verifyLotArea;
    }
    $verifyDescription = verifyDescription($description);
    if ($verifyDescription !== TRUE) {
        $errorList[] = $verifyDescription;
    }
    if ($appartmentNo !== "") {
        $verifyAppartmentNo = verifyAppartmentNo($appartmentNo);
        if ($verifyAppartmentNo !== TRUE) {
            $errorList[] = $verifyAppartmentNo;
        }
    } else {
        $appartmentNo = NULL;
    }
    $verifyStreetAddress = verifyStreetAddress($streetAddress);
    if ($verifyStreetAddress !== TRUE) {
        $errorList[] = $verifyStreetAddress;
    }
    $verifyCityName = verifyCityName($city);
    if ($verifyCityName !== TRUE) {
        $errorList[] = $verifyCityName;
    }
    // $verifyProvince = verifyProvince($province);
    // if ($verifyProvince !== TRUE) {
    //     $errorList[] = $verifyProvince;
    // }
    $verifyPostalCode = verifyPostalCode($postalCode);
    if ($verifyPostalCode !== TRUE) {
        $errorList[] = $verifyPostalCode;
    }

    if ($errorList) {
        return $this->view->render($response, 'admin/property_edit.html.twig', ['errorList' => $errorList]);
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
    $photoNo = $previousOrdinal['ordinalINT'] + 1;
    $photoPathArray = [];
    $firstPhoto = FALSE;
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

$app->post('/admin/property/edit/reorder', function ($request, $response, $args) { // TODO

    print_r($args['data']);

    return $this->view->render($response, 'admin/modification_success.html.twig');
});

// Delete property: GET
$app->get('/admin/property/delete/{id:[0-9]+}', function ($request, $response, $args) {
    $id = $args['id'];
    // if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    //     $app->redirect('/forbidden');
    //     return;
    // }

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
    DB::delete('properties', 'id=%d', $args['id']);

    return $this->view->render($response, 'admin/modification_success.html.twig');
});
//-----------------------------------------------------------------------------------------------------------------

// Pending Brokers

// View a list of all pending brokers and their information
$app->get('/admin/pending/list', function ($request, $response, $args) {
    $pendingUserList = DB::query("SELECT * FROM brokerpendinglist");
    return $this->view->render($response, 'admin/pending_list.html.twig', ['list' => $pendingUserList]);
});

$app->get('/admin/pending/{choice:accept|decline}/{id:[0-9]+}', function ($request, $response, $args) {
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
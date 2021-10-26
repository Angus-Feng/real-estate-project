<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

// Default admin page
$app->get('/admin', function ($request, $response, $args) {
    return $this->view->render($response, 'admin/index_admin.html.twig');
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
    // Check if user is a broker 
    if ($userType === 'broker') {
        $licenseNo = $request->getParam('licenseNo');
        $firstName = $request->getParam('firstName');
        $lastName = $request->getParam('lastName');
        $phone = $request->getParam('phone');
        $company = $request->getParam('company');
        $jobTitle = $request->getParam('jobTitle');
        $uploadedPhoto = $request->getUploadedFiles()['photoFilePath'];
        $appartmentNo = $request->getParam('appartmentNo');
        $streetAddress = $request->getParam('streetAddress');
        $city = $request->getParam('city');
        $province = $request->getParam('province');
        $postalCode = $request->getParam('postalCode');
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
    // Check if user is a broker 
    if ($userType === 'broker') {
        $photoFilePath = null;
        $result = verifyUploadedProfilePhoto($uploadedPhoto, $photoFilePath, $licenseNo);
        if ($result !== TRUE) {
            $errorList []= $result;
        }
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
        $verifyPhone = verifyPhone($phone);
        if ($verifyLicenseNo !== TRUE) {
            $errorList[] = $verifyPhone;
        }
        if ($company !== "") {
            $verifyCompany = verifyCompany($company);
            if ($verifyCompany !== TRUE) {
                $errorList[] = $verifyCompany;
            }
        } else {
            $company = NULL;
        }
        if ($jobTitle !== "") {
            $verifyJobTitle = verifyJobTitle($jobTitle);
            if ($verifyJobTitle !== TRUE) {
                $errorList[] = $verifyJobTitle;
            }
        } else {
            $jobTitle = NULL;
        }
        if ($appartmentNo !== "") {
        $verifyAppartmentNo = verifyAppartmentNo($appartmentNo);
            if ($verifyAppartmentNo !== TRUE) {
                $errorList[] = $verifyAppartmentNo;
            }
        } else {
            $appartmentNo = NULL;
        }
        $verifyStreetAddress = verifyUserStreetAddress($streetAddress);
        if ($verifyStreetAddress !== TRUE) {
            $errorList[] = $verifyStreetAddress;
        }
        if ($city !== "") {
            $verifyCityName = verifyCityName($city);
            if ($verifyCityName !== TRUE) {
                $errorList[] = $verifyCityName;
            }
        } else {
            $city = NULL;
        }
        // if ($province !== "") {
        //     $verifyProvince = verifyProvince($province);
        //     if ($verifyProvince !== TRUE) {
        //         $errorList[] = $verifyProvince;
        //     }
        // } else {
        //     $province = NULL;
        // }
        if ($postalCode !== "") {
            $verifyPostalCode = verifyPostalCode($postalCode);
            if ($verifyPostalCode !== TRUE) {
                $errorList[] = $verifyPostalCode;
            }
        } else {
            $postalCode = NULL;
        }
    }

    if ($errorList) {
        return $this->view->render($response, 'admin/users_add.html.twig', ['errorList' => $errorList, 'userType' => $userType]);
    }

    if ($userType === 'buyer') {
        DB::insert('users', ['email' => $email, 'password' => $password1]);
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
    // $email = $request->getParam('email');
    $password1 = $request->getParam('password1');
    $password2 = $request->getParam('password2');
    // Check if user is a broker 
    if ($user['role'] === 'broker') {
        // $licenseNo = $request->getParam('licenseNo');
        // $firstName = $request->getParam('firstName');
        // $lastName = $request->getParam('lastName');
        $phone = $request->getParam('phone');
        $company = $request->getParam('company');
        $jobTitle = $request->getParam('jobTitle');
        $appartmentNo = $request->getParam('appartmentNo');
        $streetAddress = $request->getParam('streetAddress');
        $city = $request->getParam('city');
        $province = $request->getParam('province');
        $postalCode = $request->getParam('postalCode');
    }

    $errorList = [];

    // $emailVerification = verfiyEmailUpdate($email, $id);
    // if ($emailVerification !== TRUE) {
    //     $errorList[] = $emailVerification;
    // }
    if ($password1 === "" && $password2 === "" && $user['role'] === 'broker') {
        $password1 = $user['password'];
    } else {
        $errorList[] = verifyPasswords($password1);
        if ($password1 !== $password2) {
            $errorList[] = "The passwords don't match";
        }
    }
    if ($password1 !== $password2) {
        $errorList[] = 'The passwords you have entered do not match.';
    }
    // Check if user is a broker 
    if ($user['role'] === 'broker') {
        // $verifyLicenseNo = verifyLicenseNo($licenseNo, $id);
        // if ($verifyLicenseNo !== TRUE) {
        //     $errorList[] = $verifyLicenseNo;
        // }
        // $verifyFirstName = verifyFirstName($firstName);
        // if ($verifyFirstName !== TRUE) {
        //     $errorList[] = $verifyFirstName;
        // }
        // $verifyLastName = verifyLastName($lastName);
        // if ($verifyLastName !== TRUE) {
        //     $errorList[] = $verifyLastName;
        // }
        $verifyPhone = verifyPhone($phone);
        if ($verifyPhone !== TRUE) {
            $errorList[] = $verifyPhone;
        }
        $verifyCompany = verifyCompany($company);
        if ($verifyCompany !== TRUE) {
            $errorList[] = $verifyCompany;
        }
        $verifyJobTitle = verifyJobTitle($jobTitle);
        if ($verifyJobTitle !== TRUE) {
            $errorList[] = $verifyJobTitle;
        }
        $verifyAppartmentNo = verifyAppartmentNo($appartmentNo);
        if ($verifyAppartmentNo !== TRUE) {
            $errorList[] = $verifyAppartmentNo;
        }
        $verifyStreetAddress = verifyUserStreetAddress($streetAddress);
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
            // 'email' => $email, 
            'password' => $password1], "id=%d", $id);
    } else {
        DB::update('users', [
            // 'email' => $email, 
            'password' => $password1, 
            // 'licenseNo' => $licenseNo, 
            // 'firstName' => $firstName, 
            // 'lastName' => $lastName, 
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
    return $this->view->render($response, 'admin/property_list.html.twig', ['propertyList' => $propertyList]);
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

    $photoPathArray = [];
    $errorPhotoCount = 1;
    $photoNo = 0;
    foreach ($uploadedPhotos as $photo) {
        if ($photo->getError() !== UPLOAD_ERR_OK) {
            $errorList []= 'There was an error uploading photo ' . $errorPhotoCount . ".";
            $errorPhotoCount++;
        }
        $photoFilePath = null;
        $result = verifyUploadedHousePhoto($photo, $photoFilePath, $postalCode, $photoNo);
        if ($result !== TRUE) {
            $errorList []= $result;
        } else {
            $photoPathArray []= $photoFilePath;
            $photoNo++;
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

    $photoNo = 0;
    $propertyId = DB::insertId();
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
    if (!$property) {
        // $response = $response->withStatus(404);
        return $this->view->render($response, '404_error.html.twig');
    }
    return $this->view->render($response, 'admin/property_edit.html.twig', ['property' => $property, 'broker' => $broker]);
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

    $photoPathArray = [];
    $errorPhotoCount = 1;
    $photoNo = 0;
    foreach ($uploadedPhotos as $photo) {
        if ($photo->getError() !== UPLOAD_ERR_OK) {
            $errorList []= 'There was an error uploading photo ' . $errorPhotoCount . ".";
            $errorPhotoCount++;
        }
        $photoFilePath = null;
        $result = verifyUploadedHousePhoto($photo, $photoFilePath, $postalCode, $photoNo);
        if ($result !== TRUE) {
            $errorList []= $result;
        } else {
            $photoPathArray []= $photoFilePath;
            $photoNo++;
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

    $photoNo = 0;
    $propertyId = DB::insertId();
    foreach ($photoPathArray as $photoFilePath) {
        DB::insert('propertyphotos', ['propertyId' => $propertyId, 'ordinalINT' => $photoNo, 'photoFilePath' => $photoFilePath]);
        $photoNo++;
    }

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
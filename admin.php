<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

// Default admin page
$app->get('/admin', function ($request, $response, $args) {
    return $this->view->render($response, 'admin/index_admin.html.twig');
});


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
        $appartmentNo = $request->getParam('appartmentNo');
        $streetNo = $request->getParam('streetNo');
        $streetName = $request->getParam('streetName');
        $city = $request->getParam('city');
        $province = $request->getParam('province');
        $postalCode = $request->getParam('postalCode');
    }

    $errorList = [];

    $emailVerification = verfiyEmail($email);
    if ($emailVerification !== TRUE) {
        $errorList[] = $emailVerification;
    }
    $verifyPasswords = verifyPasswords($password1, $password2);
    if ($verifyPasswords !== TRUE) {
        $errorList[] = $verifyPasswords;
    }
    // Check if user is a broker 
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
        $verifyPhone = verifyPhone($phone);
        if ($verifyLicenseNo !== TRUE) {
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
        $verifyStreetNo = verifyStreetNo($streetNo);
        if ($verifyStreetNo !== TRUE) {
            $errorList[] = $verifyStreetNo;
        }
        $verifyStreetName = verifyStreetName($streetName);
        if ($verifyStreetName !== TRUE) {
            $errorList[] = $verifyStreetName;
        }
        $verifyCityName = verifyCityName($city);
        if ($verifyCityName !== TRUE) {
            $errorList[] = $verifyCityName;
        }
        $verifyProvince = verifyProvince($province);
        if ($verifyProvince !== TRUE) {
            $errorList[] = $verifyProvince;
        }
        $verifyPostalCode = verifyPostalCode($postalCode);
        if ($verifyPostalCode !== TRUE) {
            $errorList[] = $verifyPostalCode;
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
            'appartmentNo' => $appartmentNo,
            'streetNo' => $streetNo,
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
    // Check if user is a broker 
    if ($user['role'] === 'broker') {
        $licenseNo = $request->getParam('licenseNo');
        $firstName = $request->getParam('firstName');
        $lastName = $request->getParam('lastName');
        $phone = $request->getParam('phone');
        $company = $request->getParam('company');
        $jobTitle = $request->getParam('jobTitle');
        $appartmentNo = $request->getParam('appartmentNo');
        $streetNo = $request->getParam('streetNo');
        $streetName = $request->getParam('streetName');
        $city = $request->getParam('city');
        $province = $request->getParam('province');
        $postalCode = $request->getParam('postalCode');
    }

    $errorList = [];

    $emailVerification = verfiyEmailUpdate($email, $id);
    if ($emailVerification !== TRUE) {
        $errorList[] = $emailVerification;
    }
    $verifyPasswords = verifyPasswords($password1, $password2);
    if ($verifyPasswords !== TRUE) {
        $errorList[] = $verifyPasswords;
    }
    // Check if user is a broker 
    if ($user['role'] === 'broker') {
        $verifyLicenseNo = verifyLicenseNo($licenseNo, $id);
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
        $verifyStreetNo = verifyStreetNo($streetNo);
        if ($verifyStreetNo !== TRUE) {
            $errorList[] = $verifyStreetNo;
        }
        $verifyStreetName = verifyStreetName($streetName);
        if ($verifyStreetName !== TRUE) {
            $errorList[] = $verifyStreetName;
        }
        $verifyCityName = verifyCityName($city);
        if ($verifyCityName !== TRUE) {
            $errorList[] = $verifyCityName;
        }
        $verifyProvince = verifyProvince($province);
        if ($verifyProvince !== TRUE) {
            $errorList[] = $verifyProvince;
        }
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
        DB::update('users', ['email' => $email, 'password' => $password1], "id=%d", $id);
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
            'streetNo' => $streetNo,
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
    return $this->view->render($response, 'admin/property_add.html.twig'); // TODO: Add multiple image upload
});

//-----------------------------------------------------------------------------------------------------------------
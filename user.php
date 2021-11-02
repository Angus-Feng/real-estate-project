<?php

require_once 'vendor/autoload.php';

require_once 'init.php';


$app->get('/register', function ($request, $response, $args) {
    return $this->view->render($response, 'register.html.twig');
});

$app->post('/register', function ($request, $response, $args) use ($log) {

    $email = $request->getParam('email');
    $password = $request->getParam('password');
    $pwRepeat = $request->getParam('pwRepeat');
    $isBroker = $request->getParam('isBroker');
    $brokerFirstName = $request->getParam('brokerFirstName');
    $brokerLastName = $request->getParam('brokerLastName');
    $licenseNo = $request->getParam('licenseNo');
    $company = $request->getParam('company');
    $errors = array(
        'email' => "", 'password' => "", 'pwRepeat' => "",
        'brokerFirstName' => "", 'brokerLastName' => "", 'licenseNo' => "", 'company' => ""
    );

    // verification

    if (verfiyEmail($email) !== TRUE) {
        $errors['email'] = verfiyEmail($email);
    }

    if (verifyPasswords($password) !== TRUE) {
        $errors['password'] = verifyPasswords($password);
    }
    if ($pwRepeat !== $password) {
        $errors['pwRepeat'] = 'Please enter the same password in repeat.';
    }


    if ($isBroker) {
        if (verifyLicenseNo($licenseNo) !== TRUE) {
            $errors['licenseNo'] = verifyLicenseNo($licenseNo);
        }
        if (verifyFirstName($brokerFirstName) !== TRUE) {
            $errors['brokerFirstName'] = verifyFirstName($brokerFirstName);
        }
        if (verifyLastName($brokerLastName) !== TRUE) {
            $errors['brokerLastName'] = verifyLastName($brokerLastName);
        }
        if (verifyCompany($company) !== TRUE) {
            $errors['company'] = verifyCompany($company);
        }
    }

    $valueList = [
        'email' => $email, 'password' => "", 'role' => "", 'licenseNo' => $licenseNo,
        'firstName' => $brokerFirstName, 'lastName' => $brokerLastName, 'company' => $company
    ];
    // return with error msgs || write to db
    if (array_filter($errors)) {
        return $this->view->render($response, 'register.html.twig', ['er' => $errors, 'data' => $valueList]);
    } else {
        if (!$isBroker) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $valueList['password'] = $password;
            $valueList['role'] = "buyer";
            DB::insert('users', $valueList);
            $log->debug(sprintf("new user created with id=%s", DB::insertID()));
        } else {
            $password = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
            $valueList['password'] = $password;
            $valueList['role'] = "broker-check"; //FIXIT add check page to mannually approve broker registration request.
            DB::insert('users', $valueList);
            $id = DB::insertID();
            $log->debug(sprintf("new user created with id=%s", DB::insertID()));
            DB::insert('brokerpendinglist', [
                'userId' => DB::insertID(), 'licenseNo' => $licenseNo,
                'firstName' => $brokerFirstName, 'lastName' => $brokerLastName, 'company' => $company
            ]);
        }
        unset($valueList['password']);
        unset($valueList['licenseNo']);
        session_unset();
        $_SESSION['user'] = $valueList;
        $_SESSION['user']['id'] = $id;
        return $response->withRedirect($this->router->pathFor('index'));
    }
});

$app->get('/login', function ($request, $response, $args) {
    $email = @$_COOKIE['email'];
    return $this->view->render($response, 'login.html.twig', ['email' => $email]);
})->setName('login');

$app->post('/login', function ($request, $response, $args) use ($log) {

    $email = $request->getParam('email');
    $password = $request->getParam('password');
    $rememberMe = $request->getParam('rememberMe');
    $error = "";
    $result = DB::queryFirstRow("SELECT id, email, `password`, `role`, firstName, lastName FROM users WHERE email = '$email'");
    $loginCheck = ($result != NULL) && (password_verify($password, $result['password']));
    if ($loginCheck) {
        unset($result['password']);
        session_unset();
        $_SESSION['user'] = $result;
        $log->debug(sprintf("user login with id=%s", $_SESSION['user']['id']));
        if ($rememberMe) {
            setcookie('email', $email, time() + 604800);
        }
        if ($result['role'] == 'admin') {
            return $response->withRedirect($this->router->pathFor('admin'));
        }
        return $response->withRedirect($this->router->pathFor('index'));
    } else {
        $error = "Invalid email address or password";
        return $this->view->render($response, 'login.html.twig', ['er' => $error, 'data' => $email]);
    }
});

$app->get('/profile', function ($request, $response, $args) {
    $id = @$_SESSION['user']['id'];
    if (!$id) {
        return $response->withRedirect($this->router->pathFor('login'));
    } else {
        $result = DB::queryFirstRow("SELECT * FROM users WHERE id = $id");
        unset($result['password']);
        return $this->view->render($response, 'profile.html.twig', ['user' => $result]);
    }
})->setName('profile');

$app->post('/profile', function ($request, $response, $args) {

    $id = $_SESSION['user']['id'];
    $email = $request->getParam('email');
    $licenseNo = $request->getParam('licenseNo');
    $firstName = $request->getParam('firstName');
    $lastName = $request->getParam('lastName');
    $phone = $request->getParam('phone');
    $company = $request->getParam('company');
    $jobTitle = $request->getParam('jobTitle');
    $appartmentNo = $request->getParam('appartmentNo');
    $streetAddress = $request->getParam('streetAddress');
    $city = $request->getParam('city');
    $province = $request->getParam('province');
    $postalCode = $request->getParam('postalCode');
    // error msg
    $errors = array(
        'licenseNo' => "", 'firstName' => "", 'lastName' => "", 'phone' => "", 'company' => "",
        'jobTitle' => "", 'appartmentNo' => "", 'streetAddress' => "", 'city' => "", 'postalCode' => ""
    );
    //success msg
    $success = "";
    // Verify
    if (verifyPhone($phone) !== TRUE) {
        $errors['phone'] = verifyPhone($phone);
    }
    if (verifyCityName($city) !== TRUE) {
        $errors['city'] = verifyCityName($city);
    }
    if (verifyPostalCode($postalCode) !== TRUE) {
        $errors['postalCode'] = verifyPostalCode($postalCode);
    }
    if (verifyJobTitle($jobTitle) !== TRUE) {
        $errors['jobTitle'] = verifyJobTitle($jobTitle);
    }
    if (verifyAppartmentNo($appartmentNo) !== TRUE) {
        $errors['appartmentNo'] = verifyAppartmentNo($appartmentNo);
    }
    if (verifyUserStreetAddress($streetAddress) !== TRUE) {
        $errors['streetAddress'] = verifyUserStreetAddress($streetAddress);
    }
    // strip the space in postal code.
    $postalCode = str_replace(' ', '', $postalCode);
    // put all values in value list
    $valueList = [
        'email' => $email, 'firstName' => $firstName, 'lastName' => $lastName,
        'phone' => $phone, 'company' => $company, 'jobTitle' => $jobTitle, 'appartmentNo' => $appartmentNo,
        'streetAddress' => $streetAddress, 'city' => $city, 'province' => $province, 'postalCode' => $postalCode
    ];

    if ($_SESSION['user']['role'] == 'buyer' || $_SESSION['user']['role'] == 'broker-check') {
        echo $_SESSION['user']['role'];
        $becomeBroker = $request->getParam('becomeBroker');
        if ($becomeBroker) {
            if (verifyLicenseNo($licenseNo) !== TRUE) {
                $errors['licenseNo'] = verifyLicenseNo($licenseNo);
            }
            if (verifyFirstName($firstName) !== TRUE) {
                $errors['firstName'] = verifyFirstName($firstName);
            }
            if (verifyLastName($lastName) !== TRUE) {
                $errors['lastName'] = verifyLastName($lastName);
            }
            if (verifyCompany($company) !== TRUE) {
                $errors['company'] = verifyCompany($company);
            }
            if (!array_filter($errors)) {
                DB::insert('brokerpendinglist', [
                    'userId' => $id, 'licenseNo' => $licenseNo,
                    'firstName' => $firstName, 'lastName' => $lastName, 'company' => $company
                ]);
                DB::update('users', ['role' => 'broker-check'], "id=%s", $id);
                DB::update('users', $valueList, "id=%s", $id);
                $success = "Request submitted.";
                $valueList['licenseNo'] = $licenseNo;
                return $this->view->render($response, 'profile.html.twig', ['user' => $valueList, 'success' => $success]);
            }
        }
        if (!array_filter($errors)) {
            DB::update('users', $valueList, "id=%s", $id);
            $success = "Profile updated.";
            $valueList['licenseNo'] = $licenseNo;
            return $this->view->render($response, 'profile.html.twig', ['user' => $valueList, 'success' => $success]);
        }
        $valueList['licenseNo'] = $licenseNo;
        return $this->view->render($response, 'profile.html.twig', ['user' => $valueList, 'er' => $errors]);
    } else if ($_SESSION['user']['role'] == 'broker') {
        if (!array_filter($errors)) {
            DB::update('users', $valueList, "id=%s", $id);
            $success = "Profile updated.";
            $valueList['licenseNo'] = $licenseNo;
            $valueList['role'] = 'broker';
            return $this->view->render($response, 'profile.html.twig', ['user' => $valueList, 'success' => $success]);
        } else {
            $valueList['licenseNo'] = $licenseNo;
            $valueList['role'] = 'broker';
            return $this->view->render($response, 'profile.html.twig', ['user' => $valueList, 'er' => $errors]);
        }
    }
});

$app->post('/profile/uploadPhoto', function ($request, $response, $args) {

    if (!($id = @$_SESSION['user']['id'])) {
        return $response->write("Please login first.");
    }
    $broker = DB::queryFirstRow("SELECT licenseNo FROM users WHERE id = %i", $id);
    $photo = $request->getUploadedFiles()['image'];
    $photoFilePath = NULL;
    $retVal = 1;
    if (verifyUploadedBrokerProfilePhoto($photo, $photoFilePath, $broker['licenseNo']) !== TRUE) {
        $retVal = verifyUploadedBrokerProfilePhoto($photo, $photoFilePath, $broker['licenseNo']);
    } else {
        $photo->moveTo($photoFilePath);
        DB::update('users', ['photoFilePath' => $photoFilePath], 'id=%i', $id);
    }
    $response->write($retVal);
    return $response;
});

$app->post('/profile/changepass', function ($request, $response, $args) use($log) {

    if (!($id = @$_SESSION['user']['id'])) {
        return $response->getBody()->write(json_encode("Please login first."));
    }
    $retVal = 1;
    $json = $request->getBody();
    $passList = json_decode($json, TRUE);
    $origPw = $passList['origPw'];
    $newPw = $passList['newPw'];
    $newPwRepeat = $passList['newPwRepeat'];
    $result = DB::queryFirstRow("SELECT `password` FROM users WHERE id =%i", $id);
    $loginCheck = ($result != NULL) && (password_verify($origPw, $result['password'])); 
    if (!$loginCheck) {
        $retVal = "The original password is incorrect.";
        $response->getBody()->write(json_encode($retVal));
        return $response;
    }
    if(verifyPasswords($newPw) !== TRUE) {
        $retVal = verifyPasswords($newPw);
    }
    if($newPw !== $newPwRepeat) {
        $retVal = 'Please enter the same password in repeat.';
    }
    if (!$retVal) {
        if ($_SESSION['user']['role'] == 'broker') {
            $password = password_hash($newPw, PASSWORD_DEFAULT, ['cost' => 12]);
        } else {
            $password = password_hash($newPw, PASSWORD_DEFAULT);
        }
        DB::update('users', ['password' => $password], "id=%i", $id);
        $log->debug(sprintf("user password changed id=%s", $id));
    }
    $response->getBody()->write(json_encode($retVal));
    return $response;
});

<?php

require_once 'vendor/autoload.php';

require_once 'init.php';

// $app->get('/register', function .....);

// $app->get('/login', function .....);

// $app->get('/logout', function .....);

// $app->get('/profile', function .....);


$app->get('/register', function ($request, $response, $args) {
    return $this->view->render($response, 'register.html.twig');
});

$app->post('/register', function ($request, $response, $args) use($log) {

    $email = $request->getParam('email');
    $password = $request->getParam('password');
    $pwRepeat = $request->getParam('pwRepeat');
    $isBroker = $request->getParam('isBroker');
    $brokerFirstName = $request->getParam('brokerFirstName');
    $brokerLastName = $request->getParam('brokerLastName');
    $licenseNo = $request->getParam('licenseNo');
    $errors = array('email' => "", 'password'=> "", 'pwRepeat' => "", 
                    'brokerFirstName' => "", 'brokerLastName' => "", 'LicenseNo' => "");
    
    // verification
    
    if (!$email) {
        $errors['email'] = "An email is required";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email";
    } else {
        $checkEmail = DB::query("SELECT email FROM users WHERE email = '$email'");
        if ($checkEmail) {
            $errors['email'] = "This email is already registered.";
        }
    }

    if (!$password) {
        $errors['password'] = 'Password can not be empty';
    } else if (!$isBroker){
        if (
            strlen($password) < 6 || strlen($password) > 20
            || (preg_match("/[A-Z]/", $password) !== 1)
            || (preg_match("/[a-z]/", $password) !== 1)
            || (preg_match("/[0-9]/", $password) !== 1)
        ) {
            $errors['password'] = "Minimum 6 characters with at least 1 uppercase letter, 1 lowercase letter and 1 number.";
        }
    } else {
        if (
            strlen($password) < 8 || strlen($password) > 25
            || (preg_match("/[A-Z]/", $password) !== 1)
            || (preg_match("/[a-z]/", $password) !== 1)
            || (preg_match("/[0-9]/", $password) !== 1)
            || (preg_match("/[#?!@$%^&*-]/", $password) !== 1)
        ) {
            $errors['password'] = "Minimum 8 characters with 1 uppercase letter, 1 lowercase letter, 1 number and 1 special character.";
        }
    }
    if ($pwRepeat !== $password) {
        $errors['pwRepeat'] = 'Please enter the same password in repeat.';
    }

    
    if ($isBroker) {
        if (!$licenseNo || !preg_match('/[A-Z]{3}[0-9]{6}[A-Z]{3}/', $licenseNo)) {
            $errors['licenseNo'] = "Please enter a valid license number";
        }
        $checkLicense = DB::query("SELECT licenseNo FROM users WHERE licenseNo = '$licenseNo'");
        if ($checkLicense) {
            $errors['licenseNo'] = "This license is already registered in our website.";
        }
        if (!$brokerFirstName) {
            $errors['brokerFirstName'] = "Please enter your first name that matches your license.";
        }
        if (!$brokerLastName) {
            $errors['brokerLastName'] = "Please enter your last name that matches your license.";
        }
    }

    $valueList = ['email' => $email, 'password' => "", 'role' => "", 
                'licenseNo' => $licenseNo, 'firstName' => $brokerFirstName, 'lastName' => $brokerLastName];
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
            $log->debug(sprintf("new user created with id=%s", DB::insertID()));
            DB::insert('brokerpendinglist', ['userId' => DB::insertID(), 'licenseNo' => $licenseNo, 
                    'firstName' => $brokerFirstName, 'lastName' => $brokerLastName]);
        }
        unset($valueList['password']);
        unset($valueList['licenseNo']);
        $_SESSION['user'] = $valueList;
        return $response->withRedirect($this->router->pathFor('index')); //FIXIT add path name to index router
    }

});

$app->get('/login', function ($request, $response, $args) {
    return $this->view->render($response, 'login.html.twig');
})->setName('login');

$app->post('/login', function ($request, $response, $args) use($log) {
    
    $email = $request->getParam('email');
    $password = $request->getParam('password');
    $error = "";
    $result = DB::queryFirstRow("SELECT id, email, `password`, `role`, firstName, lastName FROM users WHERE email = '$email'");
    $loginCheck = ($result != NULL) && (password_verify($password, $result['password']));
    if ($loginCheck) {
        unset($result['password']);
        $_SESSION['user'] = $result;
        $log->debug(sprintf("user login with id=%s", $_SESSION['user']['id']));
        return $response->withRedirect($this->router->pathFor('index'));
    } else {
        $error = "Invalid email address or password";
        return $this->view->render($response, 'login.html.twig', ['er' => $error]);
    }
});

$app->get('/profile', function ($request, $response, $args) {
    $id = @$_SESSION['user']['id'];
    if (!$id) {
        return $response->withRedirect($this->router->pathFor('login'));
    } else {
        $result = DB::queryFirstRow("SELECT * FROM users WHERE id = $id");
        return $this->view->render($response, 'profile.html.twig', ['profile' => $result]);
    }
});

$app->post('/profile', function ($request, $response, $args) {
    
});
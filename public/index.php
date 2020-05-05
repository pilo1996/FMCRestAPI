<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';

require '../includes/DBOperations.php';

$app = new \Slim\App([
    'settings' =>[
        'displayErrorDetails' => true
    ]
]);

//************************************** USERS ******************************************

$app->post('/createuser', function (Request $request, Response $response){

    $message = array();

    if(!hasEmptyParameters(array('email', 'nome', 'password'), $request, $response)){
        $request_data = $request->getParsedBody();

        $email = $request_data['email'];
        $nome = $request_data['nome'];
        $password = $request_data['password'];

        $dbo = new DBOperations();
        $result = $dbo->createUser($email, $nome, $password);

        $status_code = 0;
        switch ($result){
            case USER_CREATED:
                $message['error'] = false;
                $message['message'] = 'Utente creato correttamente.';
                $status_code = 201;
                break;
            case USER_EXISTS:
                $message['error'] = true;
                $message['message'] = 'Utente già registrato.';
                $status_code = 422;
                break;
            case USER_FAILURE:
                $message['error'] = true;
                $message['message'] = 'Utente NON creato.';
                $status_code = 423;
                break;
        }

        $response->write(json_encode($message));
        return $response->withHeader('Content-type', 'application/json')->withStatus($status_code);
    }
    else
        return $response->withHeader('Content-type', 'application/json')->withStatus(422);
});

$app->post('/userlogin', function (Request $request, Response $response){
    if(!hasEmptyParameters(array('email', 'password'), $request, $response)){
        $request_data = $request->getParsedBody();

        $email = $request_data['email'];
        $password = $request_data['password'];

        $dbo = new DBOperations();

        $result = $dbo->userLogin($email, $password);
        $response_data = array();

        $status_code = 422;
        switch ($result){
            case USER_AUTHENTICATED:
                $user = $dbo->getUserByEmail($email);
                $response_data['error'] = false;
                $response_data['message'] = 'Utente autenticato correttamente.';
                $response_data['user'] = $user;
                $status_code = 200;
                break;
            case USER_NOT_FOUND:
                $response_data['error'] = true;
                $response_data['message'] = 'Utente NON trovato.';
                break;
            case USER_PASSWORD_MISMATCH:
                $response_data['error'] = true;
                $response_data['message'] = 'Password non corretta.';
                break;
        }

        $response->write(json_encode($response_data));
        return $response->withHeader('Content-type', 'application/json')->withStatus($status_code);
    }
    else
        return $response->withHeader('Content-type', 'application/json')->withStatus(422);
});

$app->put('/updateUser/{id}', function (Request $request, Response $response, array $args){

    $id = $args['id'];

    if(!hasEmptyParameters(array('nome', 'profile_pic'), $request, $response)){
        $request_data = $request->getParsedBody();
        $nome = $request_data['nome'];
        $profile_pic = $request_data['profile_pic'];


        $dbo = new DBOperations();
        $response_data = array();
        if($dbo->updateUser($id, $nome, $profile_pic)){
            $response_data['error'] = false;
            $response_data['message'] = "Utente aggiornato.";
            $user = $dbo->getUserByID($id);
            $response_data['user'] = $user;
            $response->write(json_encode($response_data));
            return $response->withHeader('Content-type', 'application/json')->withStatus(200);
        }else{
            $response_data['error'] = true;
            $response_data['message'] = "Impossibile aggiornare l'utente.";
            return $response->withHeader('Content-type', 'application/json')->withStatus(422);
        }

    }else
        return $response->withHeader('Content-type', 'application/json')->withStatus(422);
});

//TODO da usarlo per il form di cambio password nel sito web
$app->put('/updatePassword', function (Request $request, Response $response){

    if(!hasEmptyParameters(array('id', 'currentPassword', 'newPassword'), $request, $response)){
        $request_data = $request->getParsedBody();
        $currentPassword = $request_data['currentPassword'];
        $newPassword = $request_data['newPassword'];
        $id = $request_data['id'];

        $dbo = new DBOperations();
        $response_data = array();

        switch ($dbo->updateUserPassword($id, $newPassword, $currentPassword)){
            case PASSWORD_CHANGED:
                $response_data['error'] = false;
                $response_data['message'] = "Password aggiornata.";
                $user = $dbo->getUserByID($id);
                $response_data['user'] = $user;
                $response->write(json_encode($response_data));
                return $response->withHeader('Content-type', 'application/json')->withStatus(200);
                break;
            case PASSWORD_MISMATCH:
                $response_data['error'] = true;
                $response_data['message'] = "Password attuale invalida.";
                $response->write(json_encode($response_data));
                return $response->withHeader('Content-type', 'application/json')->withStatus(422);
                break;
            case PASSWORD_NOT_CHANGED:
                $response_data['error'] = true;
                $response_data['message'] = "Impossibile cambiare la password.";
                $response->write(json_encode($response_data));
                return $response->withHeader('Content-type', 'application/json')->withStatus(422);
                break;
        }
    }else
        return $response->withHeader('Content-type', 'application/json')->withStatus(422);
});

$app->put('/updateSelectedDevice', function (Request $request, Response $response){

    if(!hasEmptyParameters(array('user_id', 'device_id'), $request, $response)) {
        $request_data = $request->getParsedBody();

        $deviceID = $request_data['device_id'];
        $userID = $request_data['user_id'];

        $dbo = new DBOperations();
        $response_data = array();

        if ($dbo->updateSelectedDevice($userID, $deviceID)) {
            $response_data['error'] = false;
            $response_data['message'] = "Dispositivo selezionato aggiornato.";
            $device = $dbo->getDeviceByID($deviceID);
            $response_data['device'] = $device;
            $response->write(json_encode($response_data));
            return $response->withHeader('Content-type', 'application/json')->withStatus(200);
        } else {
            $response_data['error'] = true;
            $response_data['message'] = "Impossibile selezionare il dispositivo.";
            return $response->withHeader('Content-type', 'application/json')->withStatus(422);
        }
    }else
        return $response->withHeader('Content-type', 'application/json')->withStatus(422);
});

//TODO api per richiedere reset password

//************************************** LOCATIONS **************************************

$app->post('/getAllPositions', function(Request $request, Response $response){
    if(!hasEmptyParameters(array('id'), $request, $response)) {
        $request_data = $request->getParsedBody();

        $id = $request_data['id'];

        $dbo = new DBOperations();

        $locations = $dbo->getAllPositionsByUserID($id);
        $response_data = array();

        $response_data['error'] = false;
        $response_data['locations'] = $locations;

        $response->write(json_encode($response_data));
        return $response->withHeader('Content-type', 'application/json')->withStatus(200);
    }else
        return $response->withHeader('Content-type', 'application/json')->withStatus(422);
});

$app->post('/getAllPositionsFromDeviceID', function(Request $request, Response $response){
    if(!hasEmptyParameters(array('userid', 'deviceid'), $request, $response)) {
        $request_data = $request->getParsedBody();

        $userID = $request_data['userid'];
        $deviceID = $request_data['deviceid'];

        $dbo = new DBOperations();

        $locations = $dbo->getPositionsByDeviceUserID($userID, $deviceID);
        $response_data = array();

        $response_data['error'] = false;
        $response_data['locations'] = $locations;

        $response->write(json_encode($response_data));
        return $response->withHeader('Content-type', 'application/json')->withStatus(200);
    }else
        return $response->withHeader('Content-type', 'application/json')->withStatus(422);
});

$app->put('/addPosition', function (Request $request, Response $response){

    if(!hasEmptyParameters(array('device_fk', 'user_fk', 'via', 'latitudine', 'longitudine', 'dayTime', 'dateTime'), $request, $response)) {
        $request_data = $request->getParsedBody();

        $device_fk = $request_data['device_fk'];
        $user_fk = $request_data['user_fk'];
        $traduzione = $request_data['via'];
        $lat = $request_data['latitudine'];
        $lon = $request_data['longitudine'];
        $dayTime = $request_data['dayTime'];
        $dateTime = $request_data['dateTime'];

        $dbo = new DBOperations();
        $response_data = array();

        $position = $dbo->addPosition($user_fk, $device_fk, $traduzione, $lat, $lon, $dayTime, $dateTime);
        if($position != null) {
            $response_data['error'] = false;
            $response_data['message'] = "Posizione aggiunta.";
            $response_data['position'] = $position;
            $response->write(json_encode($response_data));
            return $response->withHeader('Content-type', 'application/json')->withStatus(200);
        } else {
            $response_data['error'] = true;
            $response_data['message'] = "Impossibile aggiungere la posizione.";
            return $response->withHeader('Content-type', 'application/json')->withStatus(422);
        }
    }else
        return $response->withHeader('Content-type', 'application/json')->withStatus(422);
});

$app->delete('/deleteAllPositionsByDevice/{deviceID, userID}', function (Request $request, Response $response, array $args){
    $deviceID = $args['deviceID'];
    $userID = $args['userID'];
    $dbo = new DBOperations();
    $response_data = array();
    if($dbo->deletePositionsByDevice($userID, $deviceID)){
        $response_data['error'] = false;
        $response_data['message'] = 'Posizioni eliminate.';
    }else{
        $response_data['error'] = true;
        $response_data['message'] = 'Posizioni non eliminati.';
    }
    $response->write(json_encode($response_data));
    return $response->withHeader('Content-type', 'application/json')->withStatus(200);
});

$app->delete('/deleteSinglePositionByID/{locationID}', function (Request $request, Response $response, array $args){
    $id = $args['locationID'];
    $dbo = new DBOperations();
    $response_data = array();
    if($dbo->deletePositionByID($id)){
        $response_data['error'] = false;
        $response_data['message'] = 'Posizione eliminata.';
    }else{
        $response_data['error'] = true;
        $response_data['message'] = 'Posizione non eliminata.';
    }
    $response->write(json_encode($response_data));
    return $response->withHeader('Content-type', 'application/json')->withStatus(200);
});

//*************************************** DEVICES ***************************************

$app->post('/registerDevice', function (Request $request, Response $response){

    $response_data = array();

    if(!hasEmptyParameters(array('nome_device', 'uuid_device', 'ownerid'), $request, $response)){
        $request_data = $request->getParsedBody();

        $ownerid = $request_data['ownerid'];
        $nome_device = $request_data['nome_device'];
        $uuid_device = $request_data['uuid_device'];

        $dbo = new DBOperations();
        $result = $dbo->addDevice($nome_device, $uuid_device, $ownerid);

        $status_code = 0;
        switch ($result){
            case DEVICE_ADDED:
                $response_data['error'] = false;
                $response_data['message'] = 'Dispositivo registrato.';
                $device = $dbo->getDeviceByUuidAndOwnerID($uuid_device, $ownerid);
                $response_data['device'] = $device;
                $status_code = 201;
                break;
            case DEVICE_FAILURE:
                $response_data['error'] = true;
                $response_data['message'] = 'Dispositivo NON registrato.';
                $status_code = 422;
                break;
            case DEVICE_ALREADY_REGISTERED:
                $response_data['error'] = true;
                $response_data['message'] = 'Dispositivo già registrato.';
                $status_code = 422;
                break;
            case USER_NOT_FOUND:
                $response_data['error'] = true;
                $response_data['message'] = 'Utente non valido.';
                $status_code = 422;
                break;
        }

        $response->write(json_encode($response_data));
        return $response->withHeader('Content-type', 'application/json')->withStatus($status_code);
    }
    else
        return $response->withHeader('Content-type', 'application/json')->withStatus(422);
});

$app->put('/updateDevice', function (Request $request, Response $response){

    if(!hasEmptyParameters(array('newName', 'uuid', 'ownerID'), $request, $response)){
        $request_data = $request->getParsedBody();
        $nome = $request_data['newName'];
        $uuid = $request_data['uuid'];
        $id = $request_data['ownerID'];

        $dbo = new DBOperations();
        $response_data = array();
        if($dbo->updateDevice($nome, $uuid, $id)){
            $response_data['error'] = false;
            $response_data['message'] = "Dispositivo aggiornato.";
            $device = $dbo->getDeviceByUuidAndOwnerID($uuid, $id);
            $response_data['device'] = $device;
            $response->write(json_encode($response_data));
            return $response->withHeader('Content-type', 'application/json')->withStatus(200);
        }else{
            $response_data['error'] = true;
            $response_data['message'] = "Impossibile aggiornare il dispositivo.";
            return $response->withHeader('Content-type', 'application/json')->withStatus(422);
        }

    }else
        return $response->withHeader('Content-type', 'application/json')->withStatus(422);
}); //non utilizzato

$app->delete('/removeDeviceRegistered/{deviceID}', function (Request $request, Response $response, array $args){
    $deviceID = $args['deviceID'];
    $dbo = new DBOperations();
    $response_data = array();
    if($dbo->deleteDeviceByID($deviceID)){
        $response_data['error'] = false;
        $response_data['message'] = 'Dispositivo proprio eliminato.';
    }else{
        $response_data['error'] = true;
        $response_data['message'] = 'Dispositivo proprio non eliminato.';
    }
    $response->write(json_encode($response_data));
    return $response->withHeader('Content-type', 'application/json')->withStatus(200);
});

$app->post('/bookmarkDevice', function (Request $request, Response $response){

    $message = array();

    if(!hasEmptyParameters(array('userID', 'deviceID'), $request, $response)){
        $request_data = $request->getParsedBody();

        $deviceID = $request_data['deviceID'];
        $userID = $request_data['userID'];

        $dbo = new DBOperations();
        $result = $dbo->saveDeviceToFavorites($userID, $deviceID);

        $status_code = 0;
        switch ($result){
            case DEVICE_ADDED_TO_FAVORITES:
                $message['error'] = false;
                $message['message'] = 'Dispositivo registrato.';
                $status_code = 201;
                break;
            case DEVICE_FAILURE:
                $message['error'] = true;
                $message['message'] = 'Dispositivo NON salvato tra i preferiti.';
                $status_code = 422;
                break;
            case DEVICE_ALREADY_REGISTERED:
                $message['error'] = true;
                $message['message'] = 'Dispositivo già registrato.';
                $status_code = 422;
                break;
            case USER_NOT_FOUND:
                $message['error'] = true;
                $message['message'] = 'Utente non valido.';
                $status_code = 422;
                break;
        }

        $response->write(json_encode($message));
        return $response->withHeader('Content-type', 'application/json')->withStatus($status_code);
    }
    else
        return $response->withHeader('Content-type', 'application/json')->withStatus(422);
});

$app->delete('/removeBookmarkedDevice/{deviceID, userID}', function (Request $request, Response $response, array $args){
    $deviceID = $args['deviceID'];
    $userID = $args['userID'];
    $dbo = new DBOperations();
    $response_data = array();
    if($dbo->deleteBookmarkedDevice($userID, $deviceID)){
        $response_data['error'] = false;
        $response_data['message'] = 'Dispositivo rimosso tra i preferiti.';
    }else{
        $response_data['error'] = true;
        $response_data['message'] = 'Dispositivo non rimosso tra i preferiti.';
    }
    $response->write(json_encode($response_data));
    return $response->withHeader('Content-type', 'application/json')->withStatus(200);
});

$app->post('/getAllDevicesRegistered/{userID}', function(Request $request, Response $response, array $args){
    if(!hasEmptyParameters(array($args['userID']), $request, $response)) {
        $userID = $args['userID'];

        $dbo = new DBOperations();

        $devices = $dbo->getAllDevicesRegistered($userID);
        $response_data = array();

        $response_data['error'] = false;
        $response_data['devices'] = $devices;

        $response->write(json_encode($response_data));
        return $response->withHeader('Content-type', 'application/json')->withStatus(200);
    }else
        return $response->withHeader('Content-type', 'application/json')->withStatus(422);
});

$app->post('/getAllDevicesBookmarked/{userID}', function(Request $request, Response $response, array $args){
    if(!hasEmptyParameters(array($args['userID']), $request, $response)) {
        $userID = $args['userID'];

        $dbo = new DBOperations();

        $devices = $dbo->getAllDevicesBookmarked($userID);
        $response_data = array();

        $response_data['error'] = false;
        $response_data['devices'] = $devices;

        $response->write(json_encode($response_data));
        return $response->withHeader('Content-type', 'application/json')->withStatus(200);
    }else
        return $response->withHeader('Content-type', 'application/json')->withStatus(422);
});

$app->post('/getAllSavedDevices', function(Request $request, Response $response){
    if(!hasEmptyParameters(array('userID'), $request, $response)) {
        $request_data = $request->getParsedBody();

        $userID = $request_data['userID'];

        $dbo = new DBOperations();

        $devices = $dbo->getAllSavedDevices($userID);
        $response_data = array();

        $response_data['error'] = false;
        $response_data['devices'] = $devices;

        $response->write(json_encode($response_data));
        return $response->withHeader('Content-type', 'application/json')->withStatus(200);
    }else
        return $response->withHeader('Content-type', 'application/json')->withStatus(422);
});

//TODO get primo dispositivo mio che trovo in lista

function hasEmptyParameters($required_params, $request, $response){
    $error = false;
    $error_params = '';
    $request_params = $request->getParsedBody();

    foreach ($required_params as $param){
        if(!isset($request_params[$param]) || strlen($request_params[$param])<=0){
            $error = true;
            $error_params .= $param.', ';
        }
    }

    if($error){
        $error_detail = array();
        $error_detail['error'] = true;
        $error_detail['message'] = 'I parametri richiesti ('.substr($error_params, 0, -2). ') sono vuoti.';
        $response->write(json_encode($error_detail));
    }
    return $error;
}

$app->run();

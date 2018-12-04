<?php

require __DIR__ . "/../lib/Counter.php";

$counterObj = new Counter(__DIR__ . '/../.counter');
$action = $_GET['action'];
$action = 'get-name';

switch ($action) {
    case 'get-count':
        $data = $counterObj->getCount();
        break;
    case 'increment':
        $data = $counterObj->increment();
        break;
    case 'decrement':
        $data = $counterObj->decrement();
        break;
    case 'clear':
        $data = $counterObj->clear();
        break;
    case 'get-name':
        $data = ['name' => $counterObj->getCurrentCounterName()];
        break;
    case 'set-default':
        $counterName = $_GET['counter_name'];
        if (preg_match('/^[a-zA-Z0-9_ \-]+$/', $counterName)) {
            $counterObj->setDefaultCounterName($counterName);
            $data = ["status" => "Success"];
        } else {
            $data = ["status" => "Failed"];
        }
        break;
    default:
        $data = [
            'status' => 'Failed',
            'message' => 'Invalid action provided',
        ];
}

if (empty($data['status'])) {
    $data['status'] = "Success";
}

header('Content-Type: application/json');
echo json_encode($data);

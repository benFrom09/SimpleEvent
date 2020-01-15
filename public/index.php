<?php

use SimpleEvent\Event;
use SimpleEvent\Events;
use SimpleEvent\Calendar;
use SimpleEvent\EventPostValidator;

require "../src/bootstrap.php";

if(isset($_GET['p'])) {
    $p = $_GET['p'];
} else {
    $p = "index";
}

ob_start();
if($p === 'index') {
    $title = "Calendar";
    $calendar = new Calendar(getPdo(),$_GET['month']  ?? null ,$_GET['year'] ?? null);     
    render('index.php',['calendar'=>$calendar]);
}
elseif($p === 'event') {
    
    $events = new Events(getPdo());

    try {
        $event = $events->get($_GET['id']);
    } catch (Exception $e) {
        $e->getMessage("event does not exist");
    }
    $title = "Evènement " . $event->getId();
    render('edit.php',["data"=>[
        "name"=>$event->getName(),
        "description"=>$event->getDescription(),
        "date"=>$event->getStartTime()->format('Y-m-d'),
        "start_time"=>$event->getStartTime()->format('H:i'),
        "end_time"=>$event->getEndTime()->format('H:i')
    ]]);      
}
elseif ($p === "add") {
    $data=[];
    if($_SERVER['REQUEST_METHOD'] === "POST") {
        $data = $_POST;
        $validator = new EventPostValidator();
         $errors = $validator->validate($data);
         if(empty($errors)) {
            $event->setName($data['name']);
            $event->setDescription($data['description']);
            $event->setStartTime(DateTime::createFromFormat('Y-m-d H:i',$data['date'] . " " . $data['start_time'])->format('Y-m-d H:i:s'));
            $event->setEndTime(DateTime::createFromFormat('Y-m-d H:i',$data['date'] . " " . $data['end_time'])->format('Y-m-d H:i:s'));
            $events = new Events(getPdo());
            $events->create($event);
            header('location:/index.php?sucess=1');
        }
    } 
    render('add.php',['data'=>$data]);
} 
else {
    http_response_code(404);
    echo "<h1>page not found!</h1>";
    exit();
}
$title = $title ?? '';
$title = 'EventApp ' . $title ;
$content = ob_get_clean();
require '../views/layout.php';

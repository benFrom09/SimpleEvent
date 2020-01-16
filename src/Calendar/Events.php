<?php
namespace SimpleEvent;

use PDO;
use DateTime;
use SimpleEvent\Event;

class Events 
{
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    public function getEvents(?\DateTime $start = null,?\DateTime $end = null):array{
        if(!is_null($start) && !is_null($end)) {
        $sql = "SELECT * FROM events WHERE start_time BETWEEN '{$start->format('Y-m-d 00:00:00')}' AND '{$end->format('Y-m-d 23:59:59')}'";
        
        } else {
           $sql =  "SELECT * FROM events";
        }
        $statement = $this->pdo->query($sql);
        $result = $statement->fetchAll();
        return $result;
    }

    public function getEventsByDay(?\DateTime $start = null,?\DateTime $end = null) {
        $events = $this->getEvents($start,$end);
        $eventsByDay = [];
        foreach($events as $event) {
            $date = explode(' ',$event->start_time)[0];
            if(!isset($eventsByDay[$date])) {
                $eventsByDay[$date] = [$event];
            } else {
                $eventsByDay[$date][] = $event;
            }
        }
        return $eventsByDay;
    }

    public function get(int $id):Event {
        require 'Event.php';
        $query = $this->pdo->prepare("SELECT * FROM events WHERE id = ? LIMIT 1");
        $query->setFetchMode(PDO::FETCH_CLASS,Event::class);
        $query->execute([$id]);
        $event =  $query->fetch();
        return $event;
    }

    public function create(Event $event):bool {
        $query = $this->pdo->prepare('INSERT INTO events (name, description, start_time, end_time) VALUES (?,?,?,?)');
        return $query->execute([$event->getName(),
                        $event->getDescription(),
                        $event->getStartTime()->format('Y-m-d H:i:s'),
                        $event->getEndTime()->format('Y-m-d H:i:s')
                        ]);
    }

    public function update(Event $event):bool{
        $query = $this->pdo->prepare('UPDATE events SET name = ? , description = ?, start_time = ?, end_time = ? WHERE id = ?');
        return $query->execute([$event->getName(),
                        $event->getDescription(),
                        $event->getStartTime()->format('Y-m-d H:i:s'),
                        $event->getEndTime()->format('Y-m-d H:i:s'),
                        $event->getId()
                        ]);
    }

    public function hydrate(Event $event,array $data = []) {
            $event->setName($data['name']);
            $event->setDescription($data['description']);
            $event->setStartTime(DateTime::createFromFormat('Y-m-d H:i',$data['date'] . " " . $data['start_time'])->format('Y-m-d H:i:s'));
            $event->setEndTime(DateTime::createFromFormat('Y-m-d H:i',$data['date'] . " " . $data['end_time'])->format('Y-m-d H:i:s'));
            return $event;
    }

}
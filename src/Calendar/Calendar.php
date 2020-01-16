<?php
namespace SimpleEvent;

use PDO;
use DateTime;
use Exception;

class Calendar
{
    private $months = ["Janvier","Fevrier","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","Decembre"];

    private $days = ["Lun.","Mar.","Mer.","Jeu.","Ven.","Sam.","Dim."];

    private $month;

    private $year;

    protected $events;

    protected $pdo;


    const _BASE_URI = "/index.php";

    /**
     * Month constructor
     *
     * @param integer $month month between 1 and 12
     * @param integer $year
     */
    public function __construct(PDO $pdo,?int $month = null,?int $year = null)
    {
        if($month === null || $month < 1 || $month > 12) {
            $month = intval(date('m'));
        }
        if($year === null) {
            $year = intval(date('Y'));
        }
        if($year < 1970) {
            $year = intval(date('Y'));
        }
        $this->month = $month;
        $this->year = $year;
        $this->pdo = $pdo;
    }

    /**
     * return the month to string
     *
     * @return string
     */
    public function toString():string {
        return $this->months[$this->month - 1] . " " . $this->year;
    }

    public function getStartingDay(?int $month = null,?int $year = null) {
        $month = $month ?? $this->month;
        $year =$year ?? $this->year;
        return new DateTime("{$year}-{$month}-01");
    }

    public function getWeeks(?int $month = null,?int $year = null):int {

        $month = $month ?? $this->month;

        $year =$year ?? $this->year;
        
        $days_in_month =  $this->getTotalDays();

        $number_of_weeks = ($days_in_month % 7 == 0 ? 0 : 1) + intval($days_in_month / 7) ;

        $end = date('N',strtotime($year . '-' . $month . '-' . $days_in_month));

        $start = date('N',strtotime($year . '-' . $month . '-' . '01'));

        if($end < $start) {

            $number_of_weeks++;
        }
        return $number_of_weeks;
    }
    
    public function getTotalDays(?int $month = null,?int $year = null) {
        $month = $month ?? $this->month;
        $year =$year ?? $this->year;
       return  date('t',strtotime($year . '-' . $month . '-' . '01'));
    }

    public function getDays():array {
       return $this->days;
    }

    public function getMonth() {
        return $this->month;
    }

    public function getyear() {
        return $this->year;
    }

    /**
     * display calendar
     *
     * @return void
     */
    public function show():void {
        $content = "<div id=\"Calendar\">
        <div class=\"calendar-flex\">
            {$this->showNavigation()}
            <h2>{$this->toString()}</h2>
        </div>
            <table class=\"calendar w-100\"";

        for($i = 0; $i < $this->getWeeks();$i++) {
            $content .= "<tr>";
            foreach($this->getDays() as $k => $day) {
                $content .= $this->showCells($day,$i,$k);
            }
            $content .= "</tr>";
        }
        $content .= "</table></div>";
        echo $content;

    }

    /**
     * show days cells
     *
     * @param [type] $name
     * @param [type] $cellNumber
     * @return void
     */
    private function showCells($name,$weekNumber,$dayIndex):string {
        $cellNumber = $weekNumber * 7 + $dayIndex;
        $name = $weekNumber === 0 ? $name : '';
        $firstday = $this->getStartingDay();
        //fixes bugs if the first day is monday 
        $start = $firstday->format('N') === "1" ? $firstday : (clone $firstday)->modify('last monday');
        $numdate = (clone $start)->modify("+{$cellNumber} days");
        $classname = $this->isInMonth($numdate) ? '' : 'calendar-overmonth' ;
        $events = $this->getEvents()->getEventsByDay()[$numdate->format('Y-m-d')] ?? [];
        $content = "<td class=\"$classname\">
                    <div>
                        <div>$name <strong>{$numdate->format('d')}</strong></div>
                    </div>
                    <div class=\"calendar-events-container\">";
                    foreach($events as $event) {
                        $content.="<div class=\"calendar-event\">" . (new DateTime($event->start_time))->format('H-i') .": <a href=\"index.php?p=event&id={$event->id}\">$event->name</a></div>";                                   
                    }
                        
        $content .="</div></td>";
        return $content;
    }


    public function getEvents():Events {
       return new Events($this->pdo);
    }


    /**
     * set the start day of screen to be displayed
     *
     * @return void
     */
    public function setStartDayOfScreen():DateTime{
        $firstday = $this->getStartingDay();
        return  $firstday->format('N') === "1" ? $firstday : (clone $firstday)->modify('last monday');
    }

    /**
     * set the last day of screen to be displayed
     *
     * @return void
     */
    public function setEndDayOfScreen():DateTime{
        return (clone $this->setStartDayOfScreen())->modify('+' . (6 + 7 * ($this->getWeeks() - 1)) . 'days');
    }

    /**
     * show navigation bar
     *
     * @return void
     */
    public function showNavigation():string {
        $previous = self::_BASE_URI ."?month=" . $this->previous()->getMonth() . "&year=" . $this->previous()->getyear() ;
        $next = self::_BASE_URI ."?month=" . $this->next()->getMonth() . "&year=" . $this->next()->getyear() ;
        $content = "<div class=\"calendar-navigation\">
                    <a class=\"calendar-btn round\" href=\"$previous\">&lt</a>
                    <a class=\"calendar-btn round\" href=\"$next\">&gt</a>
                    </div>";
        return $content;
    }

    /**
     * check if day is in month
     *
     * @param \DateTime $date
     * @return boolean
     */
    public function isInMonth(\DateTime $date):bool {
        return $this->getStartingDay()->format('Y-m') === $date->format('Y-m');
    }
    /**
     * get next month
     *
     * @return Month
     */
    public function next():Calendar {
        $year = $this->year;
        $month = $this->month + 1;
        if($month > 12) {
            $month = 1;
            $year += 1;
        }
        return new Calendar($this->pdo,$month,$year);
    }

    /**
     * get previuos month
     *
     * @return Month
     */
    public function previous():Calendar {
        $year = $this->year;
        $month = $this->month - 1;
        if($month < 1) {
            $month = 12;
            $year -= 1;
        }
        return new Calendar($this->pdo,$month,$year);
    }
}
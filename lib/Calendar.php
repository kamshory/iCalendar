<?php

class Calendar{
    private $version = "2.0";
    private $calendarScale = "GREGORIAN";
    private $method = "PUBLISH";
    private $data = array();
    private $dtstart = null;
    private $dtend = null;
    private $due = null;
    private $useUTC = false;
    private $isAllDay = false;
    
    public function __construct()
    {
        // Constructor
        $this->dtstart = null;
        $this->dtend = null;
        $this->due = null;
        $this->useUTC = false;
    }
    
    private function chunkSplit($string, $length = 76, $e = "\r\n") {
        $result = "";
        if(strlen($string) <= $length)
        {
            $result = $string;
        }
        else if($length > 1)
        {
            $tmp1 = substr($string, 0, $length);
            $tmp2 = substr($string, $length);
            $tmp3 = array_chunk(preg_split("//u", $tmp2, -1, PREG_SPLIT_NO_EMPTY), $length-1);
            $result .= $tmp1.$e;
            foreach ($tmp3 as $val) {
                $result .= " ".join("", $val) . $e;
            }
            $arr = explode($e, $result);
            if($arr[count($arr)-1] == "")
            {
                unset($arr[count($arr)-1]);
            }
            $result = implode($e, $arr);
        }
        else 
        {
            $tmp1 = substr($string, 0, $length);
            $tmp2 = substr($string, $length);
            $tmp3 = str_split($tmp2, 1);
            $result .= $tmp1.$e;
            foreach ($tmp3 as $val) {
                $result .= " ".$val . $e;
            }
            $arr = explode($e, $result);
            if($arr[count($arr)-1] == "")
            {
                unset($arr[count($arr)-1]);
            }
            $result = implode($e, $arr);
        }
        
        return $result;
    }

    public function addNode($node, $value)
    {
        $value = str_replace(array("\r", "\n", "\t"), array("\\r", "\\n", "\\t"), $value);
        $line = $node.":".$value;
        $this->data[] = $this->chunkSplit($line);
        return $this;
    }
    
    public function setNode($node, $value)
    {
        $value = str_replace(array("\r", "\n", "\t"), array("\\r", "\\n", "\\t"), $value);
        $line = $node.":".$value;
        $index = 0;
        $found = false;
        foreach($this->data as $idx=>$data)
        {
            if(stripos($data, $node.":") === 0)
            {
                $found = true;
                $index = $idx;
            break;
            }
        }

        if($found)
        {
            $this->data[$index] = $this->chunkSplit($line);
        }
        else
        {
            $this->data[] = $this->chunkSplit($line);
        }
        return $this;
    }
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }
    public function setDateTimeStart($timestamp)
    {
        $this->dtstart = $timestamp;
        return $this;
    }
    public function setDTStart($timestamp)
    {
        return $this->setDateTimeStart($timestamp);
    }
    public function setDateTimeEnd($timestamp)
    {
        $this->dtend = $timestamp;
        return $this;
    }
    public function setDTEnd($timestamp)
    {
        return $this->setDateTimeEnd($timestamp);
    }
    public function setDateTimeDue($timestamp)
    {
        $this->due = $timestamp;
        return $this;
    }
    public function setDue($timestamp)
    {
        return $this->setDateTimeDue($timestamp);
    }
    public function setCalScale($calScale)
    {
        $this->calendarScale = $calScale;
        return $this;
    }
    public function setUseUTC($useUTC)
    {
        $this->useUTC = $useUTC;
        return $this;
    }
    public function setIsAllDay($isAllDay)
    {
        $this->isAllDay = $isAllDay;
        return $this;
    }
    public function setDescription($descroption)
    {
        return $this->setNode('DESCRIPTION', $descroption);
    }
    public function setSummary($summary)
    {
        return $this->setNode('SUMMARY', $summary);
    }
    public function setTransp($transp)
    {
        return $this->setNode('TRANSP', $transp);
    }
    public function setStatus($status)
    {
        return $this->setNode('STATUS', $status);
    }
    public function setRRule($rule)
    {
        return $this->setNode('RRULE', $rule);
    }
    public function setLocation($rule)
    {
        return $this->setNode('LOCATION', $rule);
    }
    public function setGeo($geo)
    {
        return $this->setNode('GEO', $geo);
    }
    public function setPriority($priority)
    {
        return $this->setNode('PRIORITY', $priority);
    }
    public function setSequence($sequence)
    {
        return $this->setNode('SEQUENCE', $sequence);
    }
    public function setUID($uid)
    {
        $this->setNode('UID', $uid);
        return $this;
    }
    public function setCreated($timestamp)
    {
        return $this->setNode('CREATED', date("Ymd\\THis\\Z", $timestamp));
    }
    public function setDTSamp($timestamp)
    {
        return $this->setNode('DTSTAMP', date("Ymd\\THis\\Z", $timestamp));
    }
    public function setLastModified($timestamp)
    {
        return $this->setNode('LAST-MODIFIED', date("Ymd\\THis\\Z", $timestamp));
    }
    public function setOrganizer($organizer)
    {
        return $this->setNode('ORGANIZER', $organizer);
    }
    public function setName($name)
    {
        return $this->setNode('NAME', $name);
    }
    private function timeFrom($timestamp)
    {
        if($this->isAllDay)
        {
            return date("Ymd", $timestamp);
        }
        else
        {
            if($this->useUTC)
            {
                return date("Ymd\\THis\\Z", $timestamp);
            }
            else
            {
                return date("Ymd\\THis", $timestamp);
            }
        }
    }
    public function render()
    {
        $buff = array();
        $buff[] = "BEGIN:VCALENDAR";
        $buff[] = "VERSION:".$this->version;
        $buff[] = "PRODID:-//Planetbiru//NONSGML v1.0//EN";
        $buff[] = "CALSCALE:".$this->calendarScale;
        $buff[] = "METHOD:".$this->method;
        $buff[] = "BEGIN:VEVENT";

        foreach($this->data as $data)
        {
            $buff[] = $data;
        }

        if($this->dtstart !== null)
        {
            $buff[] = 'DTSTART:'.$this->timeFrom($this->dtstart);
        }
        if($this->dtend !== null)
        {
            $buff[] = 'DTEND:'.$this->timeFrom($this->dtend);
        }
        if($this->due !== null)
        {
            $buff[] = 'DUE:'.$this->timeFrom($this->due);
        }


        $buff[] = "END:VEVENT";
        $buff[] = "END:VCALENDAR";
        return implode("\r\n", $buff);
    }

}
?>

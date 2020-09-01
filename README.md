# iCalendar
iCalender Creator

## Example

```php
<?php
require_once "lib/Calendar.php";

$cal = new Calendar();
$cal->setCalScale('GREGORIAN')
->setName("This is the event name")
->setDescription("This is the description of the event")
->setSummary("This is the summary of the event")
->setDTStart(strtotime("2020-09-20 21:22:23"))
->setDTEnd(strtotime("2020-09-20 23:22:23"))
->setDue(strtotime("2020-09-20 20:22:23"))
->setCreated(time(0))
->setDTSamp(strtotime('2020-09-01 00:00:00'))
->setStatus('CONFIRMED')
->setTransp('TRANSPARENT')
->setUseUTC(true);

echo $cal->render();
?>
```

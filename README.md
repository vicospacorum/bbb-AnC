# Big Blue Button Automation and Control
The aim of this project is to create a series of PHP scripts that will run on Cron and automate the proccess of controlling BigBlueButton's sessions.

## Technologies
- PHP 7
- MySQL

---

## Scripts

#### 1-getId.php
Needs to be executed during sessions. Adds Id, InternalId and Start Time (in UNIXTIME) to table `new`.

###### Runs 4x per hour

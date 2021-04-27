# Big Blue Button Automation and Control
The aim of this project is to create a series of PHP scripts that will run on Cron and automate the proccess of controlling BigBlueButton's sessions.

## Technologies
- PHP 7
- MySQL
- XML

---

## Scripts

#### 1-getId.php
Adds Id, InternalId and Start Time (in UNIXTIME) to table `new`. Needs to be executed during the session.

###### Runs 4x per hour

#### 2-getInfo.php
Makes an API request for the Session Report of every entry on table `new`. If the request is successfull transfers the data in `new` to the table `tutorings`.

###### Runs every hour


## PHP Pages

#### 3 - callback.php
Callback page for 2-getInfo.php request. Inserts the link for downloading the report in the `Reports` table.
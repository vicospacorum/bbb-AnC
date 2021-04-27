# Big Blue Button Automation and Control
The aim of this project is to create a series of PHP scripts that will run on Cron and automate the proccess of controlling BigBlueButton's sessions.

## Technologies
- PHP 7
- MySQL
- XML

---

## Scripts

#### I. 1-getId.php
Adds Id, InternalId and Start Time (in UNIXTIME) to table `new`. Needs to be executed during the session.
###### Runs 4x per hour

#### II. 2-getInfo.php
Makes an API request for the Session Report of every entry on table `new`. If the request is successfull transfers the data in `new` to the table `tutorings`.

###### Runs every hour
#### IV. reports/4-downloadReports.php
Downloads all reports requested by 2-getInfo.php, and updates the corresponding entry in the `reports` table.
`Downloaded` column:
- 0: New session
- 1: Downloaded

## PHP Pages

#### III. callback.php
Callback page for 2-getInfo.php request. Inserts the link for downloading the report in the `Reports` table.
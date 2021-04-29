# Big Blue Button Automation and Control
The aim of this project is to create a series of PHP scripts that will run on Cron and automate the proccess of controlling BigBlueButton's sessions.

## Requirements
- Google Drive API
`npm i googleapis`

## Technologies
- PHP
- JavaScript
- Shell
- MySQL
- XML
- JSON
- GCE   

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
###### Runs every hour

#### V. 5-processReport.php
Get the *Id* of unprocessed reports from `Reports` and inserts the data received in the JSON files in the `tutoring` table. Add new users to `People` table. Delete the row created by 2-getInfo.php in the `tutoring` table.
###### Runs every hour

#### VI. 6-requestRecording.php
Get the *Id* of unrequested recordings from the `tutorings` table and makes and API call for the recordings. If the request is successfull updates the corresponding entry in the `tutorings` table's `Recording` column:
- 0: No recording
- 1: Requested
- 2: Downloaded
- 3: Processed and Stored
###### Runs twice every hour

#### VIII. 8-downloadRecording.php
Get the *Id* of undownloaded recordings from the `Videos` table and downloads it into the Videos directory. It then updates the database accordingly (see item #VI)
###### Runs twice every hour

#### IX.  9-processRecording.sh
Uses *ffmpeg* to reduce video size using H.265. It them sends the video file to Google Drive and updates the database.
###### Runs every 10 minutes

#### X. 10-send2cloud.js
# Uses *ffmpeg* to reduce video size using H.265. 
###### Runs every 10 minutes


## PHP Pages

#### III. callback.php
Callback page for 2-getInfo.php request. Inserts the link for downloading the report in the `Reports` table.

#### VII. mp4callback.php
Callback page for requestRecording.php request. Inserts the link for downloading the recording in the `Video` table.

---
###### Note: some names in the codes might be written in portuguese but are referenced here by its english translation
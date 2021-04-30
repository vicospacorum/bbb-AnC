/* 
Google Drive API
required npm package: googleapis
*/
const { google } = require('googleapis');
const path = require('path');
const fs = require('fs');

// Credentials
const CLIENT_ID = '681703308523-7us65oqi5383dcob134t7jj218rv8nr3.apps.googleusercontent.com';
const CLIENT_SECRET = 'T73cGj9tQ3BoR6EqWppeCB1k';
const REDIRECT_URI = 'https://developers.google.com/oauthplayground';

const REFRESH_TOKEN = '1//04nPLN9IBVdMyCgYIARAAGAQSNwF-L9IrcPfpUpXSHyoSMOFO_yfJaShsEsNqdxW-yy0knh5vt4p_-zcL_VPMyJJbqpTIr8OlRh4';

const oauth2Client = new google.auth.OAuth2(
    CLIENT_ID,
    CLIENT_SECRET,
    REDIRECT_URI
);

oauth2Client.setCredentials({ refresh_token: REFRESH_TOKEN });

const drive = google.drive({
    version: 'v3',
    auth: oauth2Client,
});

async function uploadFile() {
    try 
    {
      const response = await drive.files.create({
        requestBody: {
          name: arquivo, //This can be name of your choice
          mimeType: 'video/mp4',
          parents: ['1B9soAMgtdoD3DJlgF8mevoY7ZU8tkM-R'] // folderId 
        },
        media: {
            mimeType: 'video/mp4',
            body: fs.createReadStream(filePath),
        },
      });
      console.log(response.data);
    } catch (error) {
      console.log(error.message);
    }
}

async function generatePublicUrl() {
  try {
    await drive.permissions.create({
      fileId: fileId,
      requestBody: {
        role: 'reader',
        type: 'anyone',
      },
    });

    const result = await drive.files.get({
      fileId: fileId,
      fields: 'webViewLink',
    });
    console.log(result.data);
  } catch (error) {
    console.log(error.message);
  }
}

const filePath = process.argv[3];
const arquivo = filePath.substr(30);

if (process.argv[2] == "send")
{
  
  console.log("Uploading File");
  uploadFile();
}
else if (process.argv[2] == "getLink")
{
  console.log("Sharing File");
  generatePublicUrl();
}
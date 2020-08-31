const AWS = require('aws-sdk');
const s3 = new AWS.S3();
// const sharp = require('sharp');

exports.handler = async (event) => {


  const params = {
    Bucket: 'bar-uh'
    , Key: 'dev/image/2020/09/17/50a5733a-9acf-49e9-8112-0c1f3776304b.jpeg'
  };
  var origimage = await s3.getObject(params).promise();
  // var buffer = await sharp(origimage.Body).resize(500).toBuffer();

  await s3.putObject({
    Bucket: 'bar-uh',
    Key: 'dev/copy/2020/09/17/50a5733a-9acf-49e9-8112-0c1f3776304b.jpeg',
    ContentType: "image",
    Body: origimage.Body,
  }).promise();

  return {
    statusCode: 200,
    body: 'йок',
    // body: 'hi'
  };

};

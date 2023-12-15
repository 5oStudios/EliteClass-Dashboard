<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Upload Video | BunnyCDN</title>
</head>

<body>
    <center>
        <div id="loading" style="display: none">
            <div>
                <h3 id="loadingText">Processing your request, Please wait...</h3>
            </div>
        </div>
        <div id="overAllFormPlace" style="">
            <div style="">
                Video Title:<input type="text" id="txtVideoTitle" />
                <form id="uploadForm" onsubmit="uploadStarted(this)" enctype="multipart/form-data"><br>
                  <input required accept="video/mp4,video/x-m4v,video/*" onchange="fileSelected(this)" type="file" name="file_data" id="file"><br><br><br>
                  <input style="" id="btnUpload" type="submit" name="submit" value="Upload Now">
                </form>
            </div>
            <div id="placeholderForForm"></div>
        </div>
    </center>


    <script src="{{ url('admin_assets/assets/js/jquery.min.js') }}"></script>
    <script>
      var videoLibraryId = 87346;
      var acc_key = 'd79b9af8-d9fa-4574-865796317f2b-871b-4331';

      function fileSelected(element)
      {
        let fileNameOnUsersPC = document.getElementById("file").files[0].name;
        let txtInputTitle = document.getElementById("txtVideoTitle").value;
        if (txtInputTitle === null || txtInputTitle === undefined || txtInputTitle === '') {
          document.getElementById("txtVideoTitle").value = fileNameOnUsersPC;
        }

      }


      document.getElementById('uploadForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const fileToUpload = document.getElementById('file');
        const file = fileToUpload.files[0];

        const createResponse = await createVideo();
        let videoId = createResponse.guid;
        const uploadResponse = await uploadVideo(videoId, file);
        console.log(uploadResponse);

        
      });



      async function createVideo(params) {
        let title = document.getElementById('txtVideoTitle').value;
        const options = {
          method: 'POST',
          headers: {
            accept: 'application/json',
            'content-type': 'application/*+json',
            AccessKey: `${acc_key}`
          },
          body: `{"title":"${title}"}`
        };

        try {
          let response = await fetch(`https://video.bunnycdn.com/library/${videoLibraryId}/videos`, options);
          response = await response.json();
          return response;
        } 
        catch (error) {
          console.error(error);
          return null;
        }
          return null;
      }


      async function uploadVideo(videoId, file) {


        const input = document.getElementById('file-input');
        
        
        const endpoint = `https://video.bunnycdn.com/library/${videoLibraryId}/videos/${videoId}`;
        const xhr = new XMLHttpRequest();
        xhr.open("PUT", endpoint);
        xhr.setRequestHeader("Accept", "application/octet-stream");
        xhr.setRequestHeader("AccessKey", acc_key);
        xhr.upload.onprogress = (event) => {
          console.log(event);
            if (event.lengthComputable) {
                let progress = (event.loaded / event.total) * 100;
                console.log(`Upload progress: ${progress}%`);
            }
        };
        xhr.onload = () => {
            if (xhr.status === 200) {
                console.log("File uploaded successfully!");
                alert("File uploaded successfully!");
            } else {
                console.log("Error uploading file");
                alert("Error uploading file");
            }
        };
        xhr.send(file);



        // const options = {
        //   method: 'PUT',
        //   headers: {
        //     accept: `application/octet-stream`,
        //     AccessKey: `${acc_key}`
        //   },
        //   body: file,
        // };

        // try {
        //   let response = await fetch(`https://video.bunnycdn.com/library/${videoLibraryId}/videos/${videoId}`, options);
        //   let res = await response.json();

        //   if (response.ok) {
        //       console.log('File uploaded successfully!');
        //       alert('File uploaded successfully!');
        //       return res;
        //   } 
        //   else {
        //       console.log('Error uploading file');
        //       alert('Error uploading file');
        //       return null;
        //   }
        // }
        //  catch (err) {
        //   console.error(err);
        //   return null;
        // }
        

      }


        if (window.location.host === undefined || window.location.host === null) {
            alert("You browser is not supported of this action. It wont work.");
        }

      
  
    </script>
</body>

</html>

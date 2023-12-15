<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Upload Video | Vimeo</title>
  </head>
  <body>
    <center>
      <div id="loading">
        <div>
          <h3 id="loadingText">Processing your request, Please wait...</h3>
        </div>
      </div>
      <div id="overAllFormPlace" style="visibility: hidden">
        <div style="display: none">
          Title:<input type="text" id="videoTitle" />
        </div>
        <div id="placeholderForForm"></div>
      </div>
    </center>

    <script>
      if (window.location.host === undefined || window.location.host === null) {
        alert("You browser is not supported of this action. It wont work.");
      }

      function getParameterByName(name, url = window.location.href) {
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
          results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return "";
        return decodeURIComponent(results[2].replace(/\+/g, " "));
      }

      let baseURL = window.location.host;
      let redirectPath = "";

      redirectPath = getParameterByName("p");
      //console.log("URL:: ", redirectPath);

      if (redirectPath === null) {
        window.location.href = baseURL; //TODO: Fix this please
      }

      let fullURL = window.location.protocol + "//" + baseURL + redirectPath;
      //fullURL = 'https://panel.lms.elite-class.com/courseclass/create/81/187';
      // console.log("DDDD:: ", baseURL + redirectPath);

      let videoTitle = document.getElementById("videoTitle").textContent;
      videoTitle = "Untitled | LMS Panel | ";
      //TODO: Fix the token thing please.
      var myHeaders = new Headers();
      myHeaders.append(
        "Authorization",
        "bearer 7de62e0b17088001ef6fa77c9a2cfe2f"
      ); //TODO: Fix this please.
      myHeaders.append("Content-Type", "application/json");
      myHeaders.append("Accept", "application/vnd.vimeo.*+json;version=3.4");
      // myHeaders.append("Cookie", "__cf_bm=6eqHOt3wtjZu9k1_a_jdTO2EPWi7yetNCHaCxGRzjiM-1661945264-0-Ad+G0cvv1mC50LWbdBiYhceQ7IezZkqK7DdINBKDJG0YC6bVtjUqSfurMNZh9+HWFCnRrl2aBVniTF/EK8aspQ0=");

      var raw = JSON.stringify({
        upload: {
          approach: "post",
          redirect_url: fullURL,
        },
        name: videoTitle,
        description: "This video is being uploaded from LMS Panel.",
      });

      var requestOptions = {
        method: "POST",
        headers: myHeaders,
        body: raw,
        redirect: "follow",
      };

      try {
        (async () => {
          const responseJSON = await fetch(
            "https://api.vimeo.com/me/videos",
            requestOptions
          );
          const resposne = await responseJSON.json();

          if (resposne.error) {
            alert("Please Upload video to Vimeo Manuall - ERR:ZK:VM:101");
            window.location.href = baseURL;
          }

          if (resposne.upload.approach === "post") {
            //Vimeo check
            //https://developer.vimeo.com/api/upload/videos#form-based-approach-step-1:~:text=take%20a%20moment%20to%20verify%20that%20upload.approach%20has%20the%20value%20post

            document.getElementById("placeholderForForm").innerHTML =
              '<form id="uploadFormInjected" onsubmit="uploadStarted(this)" method="POST" action="' +
              resposne.upload.upload_link +
              '" enctype="multipart/form-data"><label for="file">File:</label><input accept="video/mp4,video/x-m4v,video/*" onchange="fileSelected(this)" type="file" name="file_data" id="file"><br><input style="display:none;" id="btnUpload" type="submit" name="submit" value="Upload Now"></form>';

            document.getElementById("overAllFormPlace").style.visibility =
              "visible";
            document.getElementById("loadingText").innerText =
              "Select the video to upload";
          }
        })();
      } catch (error) {
        console.error("STEP-1-Request Failed:: ", error);
        alert(
          "There is something wrong with the initial request, Please manually Upload the video to Vimeo."
        ); // TODO: Fix this please. See
        //TODO: Set loader false.
      }

      function uploadStarted(params) {
        document.getElementById("loading").innerHTML =
          "<h3>Video Upload is in progress, please wait...</h3><h4>This might take some time, depending upon your video size</h4><h3 style='color:red'><b>NOTE: Do NOT close or refresh this tab, until upload is done</b></h3>";
        document.getElementById("uploadFormInjected").style.display="none";
      }

      function fileSelected(params) {
        document.getElementById("btnUpload").click();
      }
    </script>
  </body>
</html>

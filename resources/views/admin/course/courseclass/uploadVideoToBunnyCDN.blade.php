<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link href="{{ url('admin_assets/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('admin_assets/assets/css/style.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('admin_assets/assets/plugins/pnotify/css/pnotify.custom.min.css') }}" rel="stylesheet"
        type="text/css">

    <title>Upload Video | BunnyCDN</title>

    <style>
        .redstar {
            color: red;
        }
    </style>

</head>

<body>
    <div class="contentbar">
        <div id="loading">
        </div>
        <form id="uploadForm" enctype="multipart/form-data">
            <div class="row">
                <div class="offset-md-4 col-md-4">
                    <h3>Select the video to upload</h3>
                    <div>
                        <label class="text-dark" for="video_title">{{ __('Video Title') }}:<sup
                                class="redstar">*</sup></label>
                        <input type="text" id="txtVideoTitle" class="form-control" />
                    </div>
                    <div class="pt-2">
                        <label class="text-dark" for="video_upload">{{ __('Upload Video') }}: <sup
                                class="redstar">*</sup></label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{ __('Upload') }}</span>
                            </div>
                            <div class="custom-file">
                                <input accept="video/mp4,video/x-m4v,video/*" onchange="fileSelected(this)"
                                    type="file" name="file_data" id="file" class="custom-file-input"
                                    aria-describedby="inputGroupFileAddon01" required>
                                <label class="custom-file-label" id="fileName" for="inputGroupFile01">{{ __('Choose file') }}</label>
                            </div>
                        </div>
                        <div>
                            <button id="btnUpload" type="submit" name="submit" class="btn btn-primary-rgba"><i
                                    class="fa fa-check-circle"></i>
                                {{ __('Upload Now') }}
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>


    <script src="{{ url('admin_assets/assets/js/jquery.min.js') }}"></script>
    <script src="{{ url('admin_assets/assets/js/bootstrap.min.js') }}"></script>

    <script src="{{ url('admin_assets/assets/plugins/pnotify/js/pnotify.custom.min.js') }}"></script>

    <script>
        var bunnycdnkey = @json(env('BUNNYCDN_API_KEY'));
    </script>
    <script>
        var bunnycdn_libraryid = @json(env('BUNNYCDN_LIBRARY_ID'));
    </script>

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

        let fullURL = window.location.protocol + "//" + baseURL + redirectPath;

        if (redirectPath === null) {
            fullURL = window.location.protocol + "//" + baseURL + '/admins';
        }


        function fileSelected(element) {
            let fileNameOnUsersPC = document.getElementById("file").files[0].name;
            let txtInputTitle = document.getElementById("txtVideoTitle").value;
            document.getElementById("fileName").innerHTML = fileNameOnUsersPC;

            if (txtInputTitle === null || txtInputTitle === undefined || txtInputTitle === '') {
                document.getElementById("txtVideoTitle").value = fileNameOnUsersPC;
            }

        }


        document.getElementById('uploadForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const fileToUpload = document.getElementById('file');
            const file = fileToUpload.files[0];

            document.getElementById("loading").innerHTML =
                "<h3>Video Upload is in progress, please wait...</h3><h4>This might take some time, depending upon your video size</h4><h3 style='color:red'><b>NOTE: Do NOT close or refresh this tab, until upload is done</b></h3>";
            document.getElementById("uploadForm").style.display = "none";

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
                    AccessKey: `${bunnycdnkey}`
                },
                body: `{"title":"${title}"}`
            };

            try {
                let response = await fetch(`https://video.bunnycdn.com/library/${bunnycdn_libraryid}/videos`, options);
                response = await response.json();
                return response;
            } catch (error) {
                console.error(error);

                document.getElementById("loading").innerHTML =
                    "<h3>Video Upload is in progress, please wait...</h3><h4>This might take some time, depending upon your video size</h4><h3 style='color:red'><b>There is something wrong with the initial request, Please manually Upload the video to BunnyCDN</b></h3>";
                return null;
            }
            return null;
        }


        async function uploadVideo(videoId, file) {

            const input = document.getElementById('file-input');

            const endpoint = `https://video.bunnycdn.com/library/${bunnycdn_libraryid}/videos/${videoId}`;
            const xhr = new XMLHttpRequest();

            xhr.open("PUT", endpoint);
            xhr.setRequestHeader("Accept", "application/octet-stream");
            xhr.setRequestHeader("AccessKey", bunnycdnkey);
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
                    window.location.href = fullURL;

                    // Display PNotify notification
                    new PNotify({
                        title: 'Uploaded Successfully',
                        text: 'You are being redirected to course class page',
                        type: 'success'
                    });

                } else {
                    console.log("Error uploading file");
                    document.getElementById("loading").innerHTML =
                        "<h3 style='color:red'><b>Oops...</b></h3><h3>Error occur while uploading video</h3>";
                    return null;
                }
            };
            xhr.send(file);

        }
    </script>
</body>

</html>

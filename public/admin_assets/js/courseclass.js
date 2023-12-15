$('#filetype').on('change', function () {
    if ($(this).val() == 'video') {
        $('#myModalab').modal("hide"); //close Modal
        // $('#myvimeoModal').modal("show"); //Open Modal
        $('#change-video').show();
        $('#bunnycdn-videos').show();
        $('#title').show();
        $('#iframeURLBox').show();
        $('#duration').show();
        $('#pdfUpload').hide();
        $('#videotype').hide();
        $('#imageChoose').hide();
        $('#zipChoose').hide();
        $('#pdfChoose').hide();
        $('#imageUpload').hide();
        $('#pdfUpload').hide();
        $('#imageURL').hide();
        $('#size').hide();
        $('#pdfURL').hide();
        $('#zipURL').hide();
        $('#zipUpload').hide();
        $('#subtitle').hide();
        $('#previewUrl').hide();
        $('#audioChoose').hide();
        $('#audioURL').hide();
        $('#audioUpload').hide();
        $('#long_text').hide();
        // $('#meeting').hide();
        // $('#offline_session').hide();
        $('#quiz').hide();
        $('#downloadable').hide();
        $('#printable').hide();
        $("#ch1").prop("checked", false);
        $("#ch2").prop("checked", false);
        $("#ch9").prop("checked", false);
        $("#ch10").prop("checked", false);
    } else if ($(this).val() == 'pdf' || $(this).val() == 'zip' || $(this).val() == 'rar' || $(this).val() == 'word' || $(this).val() == 'excel' || $(this).val() == 'powerpoint') {
        $('#title').show();
        $('#pdfUpload').show();
        $('#fileUpload').show();
        $('#duration').show();
        $('#iframeURLBox').hide();
        $('#videotype').hide();
        $('#videoUpload').hide();
        $('#change-video').hide();
        $('#bunnycdn-videos').hide();
        $('#videoURL').hide();
        $('#imageChoose').hide();
        $('#zipChoose').hide();
        $('#imageUpload').hide();
        $('#size').hide();
        $('#imageURL').hide();
        $('#zipUpload').hide();
        $('#zipURL').hide();
        $('#subtitle').hide();
        $('#previewUrl').hide();
        $('#audioChoose').hide();
        $('#audioURL').hide();
        $('#audioUpload').hide();
        $('#long_text').hide();
        $('#quiz').hide();
        // $('#meeting').hide();
        // $('#offline_session').hide();
        $('#downloadable').show();
        $('#printable').show();
        $("#ch7").prop("checked", false);
        $("#ch8").prop("checked", false);
    } else if ($(this).val() == 'text') {
        $('#title').show();
        $('#long_text').show();
        $('#duration').show();
        $('#iframeURLBox').hide();
        $('#pdfUpload').hide();
        $('#fileUpload').hide();
        $('#change-video').hide();
        $('#bunnycdn-videos').hide();
        $('#pdfChoose').hide();
        $('#videotype').hide();
        $('#videoUpload').hide();
        $('#videoURL').hide();
        $('#imageChoose').hide();
        $('#zipChoose').hide();
        $('#imageUpload').hide();
        $('#size').hide();
        $('#imageURL').hide();
        $('#zipUpload').hide();
        $('#zipURL').hide();
        $('#subtitle').hide();
        $('#previewUrl').hide();
        $('#audioChoose').hide();
        $('#audioURL').hide();
        $('#audioUpload').hide();
        $('#quiz').hide();
        $('#downloadable').hide();
        $('#printable').hide();
        // $('#meeting').hide();
        // $('#offline_session').hide();
    } else if ($(this).val() == 'quiz') {
        $('#quiz').show();
        $('#change-video').hide();
        $('#bunnycdn-videos').hide();
        $('#fileUpload').hide();
        $('#pdfUpload').hide();
        $('#pdfChoose').hide();
        $('#videotype').hide();
        $('#videoUpload').hide();
        $('#videoURL').hide();
        $('#imageChoose').hide();
        $('#duration').hide();
        $('#zipChoose').hide();
        $('#imageUpload').hide();
        $('#size').hide();
        $('#imageURL').hide();
        $('#zipUpload').hide();
        $('#zipURL').hide();
        $('#subtitle').hide();
        $('#previewUrl').hide();
        $('#audioChoose').hide();
        $('#audioURL').hide();
        $('#audioUpload').hide();
        $('#long_text').hide();
        $('#title').hide();
        $('#description').hide();
        // $('#meeting').hide();
        // $('#offline_session').hide();
        $('#iframeURLBox').hide();
        $('#downloadable').hide();
        $('#printable').hide();
    }
    // else if ($(this).val() == 'zip')
    // {
    //     $('#zipChoose').show();
    //     $('#size').show();
    //     $('#iframeURLBox').hide();
    //     $('#videotype').hide();
    //     $('#videoUpload').hide();
    //     $('#change-video').hide();
    //     $('#bunnycdn-videos').hide();
    //     $('#videoURL').hide();
    //     $('#imageChoose').hide();
    //     $('#pdfChoose').hide();
    //     $('#pdfUpload').hide();
    //     $('#duration').hide();
    //     $('#imageUpload').hide();
    //     $('#imageURL').hide();
    //     $('#pdfUpload').hide();
    //     $('#pdfURL').hide();
    //     $('#duration').hide();
    //     $('#subtitle').hide();
    //     $('#previewUrl').hide();
    //     $('#audioChoose').hide();
    //     $('#audioURL').hide();
    //     $('#audioUpload').hide();
    //     $('#long_text').hide();
    //     // $('#meeting').hide();
    //     // $('#offline_session').hide();
    //     $('#quiz').hide();
    //     $("#ch5").prop("checked", false);
    //     $("#ch6").prop("checked", false);
    // } 
    // else if ($(this).val() == 'meeting')
    // {
    //     $('#meeting').show();
    //     $('#change-video').hide();
    //     $('#pdfUpload').hide();
    //     $('#pdfChoose').hide();
    //     $('#videotype').hide();
    //     $('#videoUpload').hide();
    //     $('#videoURL').hide();
    //     $('#imageChoose').hide();
    //     $('#duration').hide();
    //     $('#zipChoose').hide();
    //     $('#imageUpload').hide();
    //     $('#iframeURLBox').hide();
    //     $('#size').hide();
    //     $('#imageURL').hide();
    //     $('#zipUpload').hide();
    //     $('#zipURL').hide();
    //     $('#subtitle').hide();
    //     $('#previewUrl').hide();
    //     $('#audioChoose').hide();
    //     $('#audioURL').hide();
    //     $('#audioUpload').hide();
    //     $('#long_text').hide();
    //     $('#title').hide();
    //     $('#description').hide();
    //     $('#quiz').hide();
    //     $('#offline_session').hide();
    // } else if ($(this).val() == 'offline_session')
    // {
    //     $('#offline_session').show();
    //     $('#meeting').hide();
    //     $('#change-video').hide();
    //     $('#pdfUpload').hide();
    //     $('#pdfChoose').hide();
    //     $('#videotype').hide();
    //     $('#videoUpload').hide();
    //     $('#videoURL').hide();
    //     $('#imageChoose').hide();
    //     $('#duration').hide();
    //     $('#zipChoose').hide();
    //     $('#imageUpload').hide();
    //     $('#iframeURLBox').hide();
    //     $('#size').hide();
    //     $('#imageURL').hide();
    //     $('#zipUpload').hide();
    //     $('#zipURL').hide();
    //     $('#subtitle').hide();
    //     $('#previewUrl').hide();
    //     $('#audioChoose').hide();
    //     $('#audioURL').hide();
    //     $('#audioUpload').hide();
    //     $('#long_text').hide();
    //     $('#title').hide();
    //     $('#description').hide();
    //     $('#quiz').hide();
    // } else if ($(this).val() == 'image')
    // {
    //     $('#iframeURLBox').hide();
    //     $('#imageChoose').show();
    //     $('#videotype').hide();
    //     $('#zipChoose').hide();
    //     $('#pdfChoose').hide();
    //     $('#pdfUpload').hide();
    //     $('#videoUpload').hide();
    //     $('#zipUpload').hide();
    //     $('#videoURL').hide();
    //     $('#size').show();
    //     $('#duration').hide();
    //     $('#pdfURL').hide();
    //     $('#zipURL').hide();
    //     $('#subtitle').hide();
    //     $('#previewUrl').hide();
    //     $('#audioChoose').hide();
    //     $('#audioURL').hide();
    //     $('#audioUpload').hide();
    //     $('#long_text').hide();
    //     // $('#meeting').hide();
    //     // $('#offline_session').hide();
    //     $('#quiz').hide();
    //     $("#ch3").prop("checked", false);
    //     $("#ch4").prop("checked", false);
    // } else if ($(this).val() == 'audio')
    // {
    //     $('#iframeURLBox').hide();
    //     $('#audioChoose').show();
    //     $('#videotype').hide();
    //     $('#imageChoose').hide();
    //     $('#zipChoose').hide();
    //     $('#pdfChoose').hide();
    //     $('#imageUpload').hide();
    //     $('#pdfUpload').hide();
    //     $('#imageURL').hide();
    //     $('#size').hide();
    //     $('#pdfURL').hide();
    //     $('#duration').show();
    //     $('#zipURL').hide();
    //     $('#zipUpload').hide();
    //     $('#subtitle').hide();
    //     $('#previewUrl').hide();
    //     $('#long_text').hide();
    //     // $('#meeting').hide();
    //     // $('#offline_session').hide();
    //     $('#quiz').hide();
    //     $("#ch11").prop("checked", false);
    //     $("#ch12").prop("checked", false);
    // }
    else {
        $('#iframeURLBox').hide();
        $('#pdfUpload').hide();
        $('#videotype').hide();
        $('#change-video').hide();
        $('#bunnycdn-videos').hide();
        $('#videoUpload').hide();
        $('#zipUpload').hide();
        $('#videoURL').hide();
        $('#zipURL').hide();
        $('#pdfUpload').hide();
        $('#pdfChoose').hide();
        $('#pdfURL').hide();
        $('#imageChoose').hide();
        $('#zipChoose').hide();
        $('#subtitle').hide();
        $('#audioChoose').hide();
        $('#audioURL').hide();
        $('#audioUpload').hide();
        $('#long_text').hide();
        $('#quiz').hide();
        // $('#meeting').hide();
        // $('#offline_session').hide();
        $('#title').hide();
        $('#downloadable').hide();
        $('#printable').hide();
    }
});

$('#ch1').on('click', function () {
    $('#videoURL').show();
    $('#videoUpload').hide();
    $('#iframeURLBox').hide();
    $('#duration').show();
    $('#liveclassBox').hide();
    $('#awsBox').hide();
});

$('#ch2').on('click', function () {
    $('#videoURL').hide();
    $('#videoUpload').show();
    $('#iframeURLBox').hide();
    $('#duration').show();
    $('#awsBox').hide();
});

$('#ch9').on('click', function () {
    $('#iframeURLBox').show();
    $('#videoURL').hide();
    $('#videoUpload').hide();
    $('#liveclassBox').hide();
    $('#duration').show();
    $('#awsBox').hide();
});

$('#ch10').on('click', function () {
    $('#videoURL').show();
    $('#liveclassBox').show();
    $('#iframeURLBox').hide();
    $('#videoUpload').hide();
    $('#duration').show();
    $('#awsBox').hide();
});


$('#ch13').on('click', function () {
    $('#videoURL').hide();
    $('#awsBox').show();
    $('#iframeURLBox').hide();
    $('#iframeURLBox').hide();
    $('#videoUpload').hide();
    $('#duration').show();
});


$('#ch22').on('click', function () {
    $('#videoURL').hide();
    $('#videoUpload').hide();
    $('#duration').show();
});

//audio url
$('#ch11').on('click', function () {
    $('#audioURL').show();
    $('#audioUpload').hide();
    $('#duration').show();
});

//audio upload
$('#ch12').on('click', function () {
    $('#audioURL').hide();
    $('#audioUpload').show();
    $('#duration').show();
});

$('#ch3').on('click', function () {
    $('#imageURL').show();
    $('#imageUpload').hide();
});

$('#ch4').on('click', function () {
    $('#imageURL').hide();
    $('#imageUpload').show();
});

$('#ch5').on('click', function () {
    $('#zipURL').show();
    $('#zipUpload').hide();
});

$('#ch6').on('click', function () {
    $('#zipURL').hide();
    $('#zipUpload').show();
});

$('#ch7').on('click', function () {
    $('#pdfURL').show();
    $('#pdfUpload').hide();
});

$('#ch8').on('click', function () {
    $('#pdfURL').hide();
    $('#pdfUpload').show();
});


// dynamic subtitle add js 
$(function () {
    var i = 1;
    $('#add').on('click', function () {
        i++;
        $('#dynamic_field').append('<tr id="row' + i + '" class="dynamic-added"><td><input type="file" name="sub_t[]"/></td><td><input type="text" name="sub_lang[]" placeholder="Subtitle Language" class="form-control name_list" /></td><td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn-sm btn_remove">X</button></td></tr>');
    });

    $(document).on('click', '.btn_remove', function () {
        var button_id = $(this).attr("id");
        $('#row' + button_id + '').remove();
    });

    $('form').on('submit', function (event) {
        $('.loading-block').addClass('active');
    });
    $('#custom_url').hide();

    $('#TheCheckBox').on('switchChange.bootstrapSwitch', function (event, state) {

        if (state == true) {

            $('#ready_url').show();
            $('#custom_url').hide();

        } else if (state == false) {

            $('#ready_url').hide();
            $('#custom_url').show();

        }
    });

    $('.upload-image-main-block').hide();
    $('.subtitle_list').hide();
    $('#subtitle-file').hide();
    $('.movie_id').hide();

    $('input[name="subtitle"]').on('click', function () {
        if ($(this).prop("checked") == true) {
            $('.subtitle_list').fadeIn();
            $('#subtitle-file').fadeIn();
        } else if ($(this).prop("checked") == false) {
            $('.subtitle_list').fadeOut();
            $('#subtitle-file').fadeOut();
        }
    });
    $('.for-custom-image input').on('click', function () {
        if ($(this).prop("checked") == true) {
            $('.upload-image-main-block').fadeIn();
        } else if ($(this).prop("checked") == false) {
            $('.upload-image-main-block').fadeOut();
        }
    });
    $('input[name="series"]').on('click', function () {
        if ($(this).prop("checked") == true) {
            $('.movie_id').fadeIn();
        } else if ($(this).prop("checked") == false) {
            $('.movie_id').fadeOut();
        }
    });
});


$('#youtubeurl').on('click', function () {

    $('#myyoutubeModal').modal("show"); //Open Modal
    $('#videoURL').show();
    $('#videoUpload').hide();
    $('#iframeURLBox').hide();
    $('#duration').show();
    $('#liveclassBox').hide();
    $('#awsBox').hide();
});


$('#vimeourl').on('click', function () {

    $('#myvimeoModal').modal("show"); //Open Modal
    $('#videoURL').show();
    $('#videoUpload').hide();
    $('#iframeURLBox').hide();
    $('#duration').show();
    $('#liveclassBox').hide();
    $('#awsBox').hide();
});


function setVideoURl(videourls) {
    console.log(videourls);
    $('#apiUrl').val(videourls);
    $('#myyoutubeModal').modal("hide"); //add youtube URL
}

tinymce.init({
    selector: 'textarea#vemio_detail',
    rtl_ui: rtl,
    directionality: rtl ? 'rtl' : 'ltr',
    height: 250,
    menubar: 'edit view insert format tools table tc',
    autosave_ask_before_unload: true,
    autosave_interval: "30s",
    autosave_prefix: "{path}{query}-{id}-",
    autosave_restore_when_empty: false,
    autosave_retention: "2m",
    image_advtab: true,
    image_dimensions: false,
    image_class_list: [{
        title: 'Responsive',
        value: 'img-fluid'
    }],
    plugins: [
        'advlist autolink lists link image charmap print preview anchor',
        'searchreplace visualblocks fullscreen',
        'insertdatetime media table paste wordcount',
        'textcolor',
    ],
    toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media  template link anchor codesample | a11ycheck ltr rtl | showcomments addcomment',
    content_css: '//www.tiny.cloud/css/codepen.min.css'
});


function setVideovimeoURl(link, name) {
    $('#myvimeoModal').modal("hide"); // add vimeo URL
    // console.log(link, 'ok');

    var ifrm = '<iframe src="' + link + '&amp;badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479"\
                        frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen style="position:absolute;top:0;left:0;width:100%;height:100%;" \
                        title="' + name + '">\
                </iframe>';
    tinymce.activeEditor.setContent(ifrm); //, {format: 'raw'}
    $(".iframe_url").val(ifrm);
    $(".video_url").val(link);
    // $('#myModalab').modal("show"); //close Modal
}


function setBunnyCDNiframeURl(libraryid, videoid) {
    $('#bunnycdnModal').modal("hide"); // add vimeo URL
    console.log('SetBunnyCDN iframeURL: ', 'https://iframe.mediadelivery.net/embed/' + libraryid + '/' + videoid);


    var ifrm = '<iframe src="' + 'https://iframe.mediadelivery.net/embed/' + libraryid + '/' + videoid + '?autoplay=false" loading="lazy" style="position:absolute;top:0;left:0;width:100%;height:100%;" allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture;" allowfullscreen="true">\n\
                </iframe>';

    var directplayURL = 'https://iframe.mediadelivery.net/embed/' + libraryid + '/' + videoid + '?autoplay=false';

    tinymce.activeEditor.setContent(ifrm); //, {format: 'raw'}

    $(".iframe_url").val(ifrm); // iframe_url class name of CKEditor
    $(".video_url").val(directplayURL);
}


$('#drip_type2').on('change', function () {

    if ($(this).val() == 'date') {
        $('#dripdate2').show();
        $("input[name='drip_date']").attr('required', 'required');
    } else {
        $('#dripdate2').hide();
    }

    if ($(this).val() == 'days') {
        $('#dripdays2').show();
        $("input[name='drip_days']").attr('required', 'required');
    } else {
        $('#dripdays2').hide();
    }
});


$("#date_specific4").datepicker({
    minDate: 0
});


function videoUploadVimeoViaZakiCode(params) {
    console.log("PathName_OfThisURL:: ", params.pathname);
    const vimeoUploadURLPath = '/uploadvideotovimeo';
    const redirectRealtivePath = vimeoUploadURLPath + '?p=' + params.pathname;
    console.log("Redirect URL:: ", redirectRealtivePath);
    window.location.href = redirectRealtivePath;
}


function uploadFile(input) {
    var accesstoken = vimeokey;
    var file = $(input).prop("files")[0];

    var formData = new FormData();
    formData.append("file_data", file);
    formData.append("name", file.name);
    jQuery.ajax({
        url: "https://api.vimeo.com/me/videos/",
        type: "POST",
        data: formData,
        headers: {
            "Authorization": "Bearer " + accesstoken,
        },
        contentType: false,
        crossDomain: true,
        processData: false,
        cache: false,
        beforeSend: function () {
            videoList =
                '<div class="spinner-border" role="status">\
                        <span class="sr-only">Loading...</span>\
                     </div>';

            $("#vimeo-watch-related").html(videoList);
            console.log('before');
        },
        success: function (e) {
            setVideovimeoURl(e.player_embed_url, e.name);
            videoList =
                '<li class="hyv-video-list-item" >\n\
                <div class="hyv-thumb-wrapper"><p class="hyv-thumb-link">\n\
                    <span class="hyv-simple-thumb-wrap"><img alt="' + e.name + '" src="' + e.pictures.sizes[3].link + '" height="90"></span></p>\n\
                </div>\n\
                <div class="hyv-content-wrapper">\n\
                    <p  class="hyv-content-link">' + e.name + '<span class="stat attribution">by <span>' + e.user.name + '</span></span></p>\n\
                    <button class="bn btn-info btn-sm inline" onclick="setVideovimeoURl(\'' + e.player_embed_url + '\',\'' + e.name + '\')">Select</button>\n\
                </div>\n\
            </li>';

            $("#vimeo-watch-related").html(videoList);
        },
        error: function (e) {
            alert("File not uploaded kinldy upload again.");
        }
    });
    $(document).ajaxSend(function (event, request, settings) {
        console.log("hook");
    });
}


// coursechapters
$('.session_type').on('change', function () {
    if ($(this).val() == 'live-streaming') {
        $('#meeting_div').removeClass('d-none');
        $('#selected_meeting').prop("disabled", false);
        $('#session_div').addClass('d-none');

    } else if ($(this).val() == 'in-person-session') {
        $('#session_div').removeClass('d-none');
        $('#selected_session').prop("disabled", false);
        $('#meeting_div').addClass('d-none');
    }
});

$('#selected_meeting').on('change', function () {
    let obj = JSON.parse($(this).val());

    $('#chapter_name').val(obj.meetingname);
    $('#price').val(obj.discount_price);
});

$('#selected_session').on('change', function () {
    let obj = JSON.parse($(this).val());

    $('#chapter_name').val(obj.title);
    $('#price').val(obj.discount_price);
});

$('#is_purchasable1').on('change', function () {
    if ($(this).val() == '1') {
        $('#priceBox1').show();
        $('#price1').attr('required', true);

    } else {
        $('#priceBox1').hide();
        $('#price1').val('0');
        $('#price1').attr('required', false);
    }
});

$('#is_purchasable2').on('change', function () {
    if ($(this).val() == '1') {
        $('#priceBox2').show();
        $('#price2').attr('required', true);

    } else {
        $('#priceBox2').hide();
        $('#price2').val('0');
        $('#price2').attr('required', false);
    }
});

$('#is_purchasable').on('change', function () {
    if ($(this).val() == '1') {
        $('#pricebox').show();
        $('#price').attr('required', true);

    } else {
        $('#pricebox').hide();
        $('#price').val('0');
        $('#price').attr('required', false);
    }
});
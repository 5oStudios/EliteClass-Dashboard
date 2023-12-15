$(document).ready(function () {
    var videourl;
    var myvimeourl = 'https://api.vimeo.com/me/videos?query=&per_page=8';

    vimeoApiCall(myvimeourl);

    $("#vpageTokenNext").on("click", function (event) {
        $("#vpageToken").val($("#vpageTokenNext").val());
        var next = $("#vpageTokenNext").val();
        var text = $("#vimeo-search").val();
        if (next != null && next != '') {
            myvimeourl = 'https://api.vimeo.com' + next;
        } else {
            myvimeourl = 'https://api.vimeo.com/me/videos?query=' + text + '&per_page=8';
        }
        vimeoApiCall(myvimeourl);
    });
    
    $("#vpageTokenPrev").on("click", function (event) {
        $("#vpageToken").val($("#vpageTokenPrev").val());
        var text = $("#vimeo-search").val();
        var prev = $("#vpageTokenPrev").val();
        if (prev != null && prev != '') {
            myvimeourl = 'https://api.vimeo.com' + prev;
        } else {
            myvimeourl = 'https://api.vimeo.com/me/videos?query=' + text + '&per_page=8';
        }
        vimeoApiCall(myvimeourl);
    });

    $("#vimeo-searchBtn").on("click", function (event) {
        var text = $("#vimeo-search").val();
        myvimeourl = 'https://api.vimeo.com/me/videos?query=' + text + '&per_page=8';
        vimeoApiCall(myvimeourl);
        return false;
    });

    jQuery("#vimeo-search").autocomplete({
        source: function (request, response) {
            //console.log(request.term);
            var sqValue = [];
            var accesstoken = vimeokey;
            var myvimeourl = 'https://api.vimeo.com/videos?query=videos' + '&access_token=' + accesstoken + '&per_page=1';
            // console.log(myvimeourl);

            jQuery.ajax({
                type: "GET",
                url: myvimeourl,
                dataType: 'jsonp',

                success: function (data) {
                    // console.log(data[1]);

                    obj = data[1];
                    jQuery.each(obj, function (key, value) {
                        sqValue.push(value[0]);
                    });
                    response(sqValue);
                }
            });
        },
        select: function (event, ui) {
            setTimeout(function () {
                vimeoApiCall();
            }, 300);
        }
    });
});


function vimeoApiCall(myvimeourl) {

    videoList = '<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>';
    $("#vimeo-watch-related").html(videoList);

    $.ajax({
        cache: false,
        dataType: 'json',
        type: 'GET',
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            "Authorization": "Bearer " + vimeokey
        },
        url: myvimeourl,

    })
    .done(function (data) {

        console.log('VIMEO RESP:', data);

        if (data.paging.previous === null) {
            $("#vpageTokenPrev").hide();
        } else {
            $("#vpageTokenPrev").show();
        }
        if (data.paging.next === null) {
            $("#vpageTokenNext").hide();
        } else {
            $("#vpageTokenNext").show();
        }
        var items = data.data, videoList = "";

        $("#vpageTokenNext").val(data.paging.next);
        $("#vpageTokenPrev").val(data.paging.previous);

        $.each(items, function (index, e) {

            videourl = e.link;
            // console.log(e);

            videoList +=
                    '<div class="col col-md-3 mb-3" >\n\
                    <div class="hyv-thumb-wrapper"><p class="hyv-thumb-link">\n\
                        <span class="hyv-simple-thumb-wrap"><img alt="' + e.name + '" src="' + e.pictures.sizes[3].link + '" height="90"></span></p>\n\
                    </div>\n\
                    <div class="hyv-content-wrapper">\n\
                        <p  class="hyv-content-link">' + e.name + '<span class="stat attribution">by <span>' + e.user.name + '</span></span></p>\n\
                        <button class="bn btn-info btn-sm inline" onclick="setBunnyCDNiframeURl(\'' + e.player_embed_url + '\',\'' + e.name + '\')">Select</button>\n\
                    </div>\n\
                    </div>';
                // + '<li class="hyv-video-list-item" ><div class="hyv-thumb-wrapper"><p class="hyv-thumb-link"><span class="hyv-simple-thumb-wrap"><img alt="'+e.name+'" src="'+e.pictures.sizes[3].link+'" height="90"></span></p></div><div class="hyv-content-wrapper"><p  class="hyv-content-link">'+e.name+'<span class="title">'+e.description.substr(0, 105)+'</span><span class="stat attribution">by <span>'+e.user.name+'</span></span></p><button class="bn btn-info btn-sm inline" onclick=setVideovimeoURl("'+videourl+'")>Add</button></div></li>';
        });

        $("#vimeo-watch-related").html(videoList);

    });
}
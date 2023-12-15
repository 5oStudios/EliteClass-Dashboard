$(function () {
    var bunnycdnurl = 'https://video.bunnycdn.com/library/' + bunnycdn_libraryid + '/videos';

    bunnyCDNvideoList(bunnycdnurl);

    $("#bunnyCDN_next_page").on("click", function (event) {
        let next = $(this).val();
        let searchText = $("#bunnycdn_search").val();

        if (next !== null && next !== '') {
            bunnyCDNvideoList(bunnycdnurl, next);

        } else {
            searchVideos(bunnycdnurl, searchText);

        }
    });


    $("#bunnyCDN_previous_page").on("click", function (event) {
        let prev = $(this).val();
        let searchText = $("#bunnycdn_search").val();

        if (prev !== null && prev !== '') {

            bunnyCDNvideoList(bunnycdnurl, prev);

        } else {
            searchVideos(bunnycdnurl, searchText);

        }
    });


    var fld = $('#bunnycdn_search');
    if (fld.length) {

        document.getElementById("bunnycdn_search").addEventListener("search", function (event) {
            bunnyCDNvideoList(bunnycdnurl);

        });
    }


    $("#bunnycdn_search_btn").on("click", function (event) {

        const searchInput = document.getElementById('bunnycdn_search');
        let searchText = $("#bunnycdn_search").val();

        if (searchInput.value.trim() === '') {
            searchInput.focus();

        } else if (searchText !== null && searchText !== '') {

            $("#bunnycdn-video-list").html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');
            searchVideos(bunnycdnurl, searchText);
        }

        return false;
    });

});


function searchVideos(bunnycdnurl, searchText, currentPage = 1) {

    const pageSize = 12;

    $.ajax({
        cache: false,
        dataType: 'json',
        type: 'GET',
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            "AccessKey": bunnycdnkey,
        },
        url: `${bunnycdnurl}?itemsPerPage=${pageSize}&search=${searchText}`,

    }).done(function (response) {

        console.log('SEARCH RESP: ', response);

        let items = response.items,
            videoList = "";

        const totalCount = response.totalItems;

        videoList = generateVideoList(items);

        $("#bunnycdn-video-list").html(videoList);

        // Call the pagination function and pass the data and current page
        paginate(totalCount, pageSize, currentPage);
    });

}


function videoUploadtoBunnyCDN(params) {
    console.log("PathName_OfThisURL:: ", params.pathname);
    const bunnycdnUploadURLPath = '/uploadvideotobunnycdn';
    const redirectRelativePath = bunnycdnUploadURLPath + '?p=' + params.pathname;
    console.log("Redirect URL:: ", redirectRelativePath);
    window.location.href = redirectRelativePath;
}


function bunnyCDNvideoList(bunnycdnurl, currentPage = 1) {

    let videoList = '<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>';

    $("#bunnycdn-video-list").html(videoList);
    const pageSize = 12;

    $.ajax({
        cache: false,
        dataType: 'json',
        type: 'GET',
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            "AccessKey": bunnycdnkey,
        },
        url: `${bunnycdnurl}?itemsPerPage=${pageSize}&page=${currentPage}`,

    }).done(function (response) {

        console.log('BunnyCDN RESP: ', response);

        let items = response.items,
            videoList = "";

        const totalCount = response.totalItems;

        videoList = generateVideoList(items);

        $("#bunnycdn-video-list").html(videoList);

        // Call the pagination function and pass the data and current page
        paginate(totalCount, pageSize, currentPage);
    });
}


function generateVideoList(items) {
    let videoList = "";
    $.each(items, function (index, e) {
        libraryid = e.videoLibraryId;
        videoid = e.guid;

        videoList +=
            '<div class="col col-md-3 mb-3" >\n\
                    <div class="hyv-thumb-wrapper"><p class="hyv-thumb-link">\n\
                        <span class="hyv-simple-thumb-wrap"><img alt="' + e.title + '" src="https://' + bunnycdn_hostname + '/' + videoid + '/' + e.thumbnailFileName + '"' + '" height="90"></span></p>\n\
                    </div>\n\
                    <div class="hyv-content-wrapper">\n\
                        <p  class="hyv-content-link">' + e.title + '</span></p>\n\
                        <button class="bn btn-info btn-sm inline" onclick="setBunnyCDNiframeURl(\'' + libraryid + '\',\'' + videoid + '\')">Select</button>\n\
                    </div>\n\
                    </div>';
    });
    return videoList;
}


function paginate(totalCount, pageSize, currentPage) {
    var prevPage = document.getElementById("bunnyCDN_previous_page");
    var nextPage = document.getElementById("bunnyCDN_next_page");
    const numPages = Math.ceil(totalCount / pageSize);

    function validatePage(page) {
        if (page < 1) page = 1;
        if (page > numPages) page = numPages;
        return page;
    }

    changePage(currentPage);

    function changePage(page) {
        currentPage = validatePage(page);

        if (currentPage == 1) {
            prevPage.style.visibility = "hidden";
        } else {
            prevPage.style.visibility = "visible";
        }

        if (currentPage == numPages) {
            nextPage.style.visibility = "hidden";
        } else {
            nextPage.style.visibility = "visible";
        }

        $("#bunnyCDN_previous_page").val(parseInt(currentPage) - 1);
        $("#bunnyCDN_next_page").val(parseInt(currentPage) + 1);
    }
}

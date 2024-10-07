$(document).ready(function() {
    let videoDetails = [];

    // Set CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    

    $('#video-list').on('change', '#select-all-checkbox', function() {
        let isChecked = $(this).is(':checked');
        $('.video-checkbox').prop('checked', isChecked);
    });

    $('#downloadSelectedButton').on('click', function() {
        let selectedVideos = [];
        $('.video-checkbox:checked').each(function() {
            let index = $(this).data('video-index');
            selectedVideos.push(videoDetails[index].id);
        });

        if (selectedVideos.length > 0) {
            $('#progressText').text('Converting...');
            downloadVideos(selectedVideos);
        } else {
            $('#progressText').text('No videos selected');
        }
    });

        $('#pasteButton').click(function() {
            if (navigator.clipboard && navigator.clipboard.readText) {
                navigator.clipboard.readText().then(function(text) {
                    $('#playlist_url').val(text);
                    // Hide the paste button after successful paste
                    $('#pasteButton').hide();
                }).catch(function(err) {
                    console.error('Failed to read clipboard contents: ', err);
                    alert('Failed to read clipboard contents. Please allow clipboard access.');
                });
            } else {
                alert('Clipboard API not supported or permission denied.');
            }
        });

    
    function downloadVideos(videoIds) {
        if (videoIds.length > 0) {
            let videoId = videoIds.shift();
            let index = videoDetails.findIndex(video => video.id === videoId);

            updateButtonStatus(index, true);

            $.ajax({
                url: '/downloadVideo',
                method: 'POST',
                data: {
                    video_id: videoId
                },
                success: function(response) {
                    var link = document.createElement('a');
                    link.href = response.file_url;
                    link.download = response.file_url.split('/').pop();
                    link.click();
                    updateButtonStatus(index, false, true);
                    downloadVideos(videoIds);
                },
                error: function(response) {
                    $('#progressText').text(response.responseJSON.error || 'Download Failed');
                    $('#inputSection').show();
                    $('.progress').hide();
                    $('#cancelButton').hide();
                }
            });
        } else {
            $('#progressText').text('All downloads completed');
            $('#convertNextButton').show();
            $('#selectAllButton').hide();
            $('#downloadSelectedButton').hide();
        }
    }

    function updateButtonStatus(index, isDownloading, isFinished = false) {
        let button = $(`#video-${index} .download-button`);
        if (isDownloading) {
            button.text('Converting...').addClass('btn-dark').removeClass('btn-primary').prop('disabled', true);
        } else if (isFinished) {
            button.text('Finished').addClass('btn-dark').removeClass('btn-primary').prop('disabled', true);
        }
    


  
    };

    $('#video-list').on('click', '.download-button', function() {
        let index = $(this).data('video-index');
        let videoId = videoDetails[index].id;
        updateButtonStatus(index, true);

        $.ajax({
            url: '/downloadVideo',
            method: 'POST',
            data: {
                video_id: videoId
            },
            success: function(response) {
                var link = document.createElement('a');
                link.href = response.file_url;
                link.download = response.file_url.split('/').pop();
                link.click();
                updateButtonStatus(index, false, true);
            },
            error: function(response) {
                $('#progressText').text(response.responseJSON.error || 'Download Failed');
                $('#inputSection').show();
                $('.progress').hide();
                $('#cancelButton').hide();
            }
        });
    });
});

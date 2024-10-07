// ui.js
// Function to display video details in the table
function displayVideoDetails(videoDetails) {
    let videoList = $('#video-table-body');  // Get the table body element
    videoList.empty();  // Clear existing rows
    $('#table-headers').show();  // Show table headers

    // Iterate through the video details array and add each video to the table
    videoDetails.forEach((video, index) => {
        let videoRow = `
            <tr id="video-${index}">
                <td><input type="checkbox" class="video-checkbox" data-video-index="${index}"></td>
                <td><img src="${video.thumbnail}" alt="${video.title}" width="100"></td>
                <td id="video-${index}">${video.title} <br>  ${video.duration}</td>
                <td>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="format-${index}">Format:</label>
                            <select class="form-control format-select" id="format-${index}" data-video-index="${index}">
                                <option value="mp3">MP3</option>
                                <option value="mp4">MP4</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="quality-${index}">Quality:</label>
                            <select class="form-control quality-select" id="quality-${index}" data-video-index="${index}">
                                <option value="144">144p</option>
                                <option value="240">240p</option>
                                <option value="360">360p</option>
                                <option value="480">480p</option>
                                <option value="720">720p</option>
                                <option value="1080">1080p</option>
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-primary download-button" data-video-index="${index}">Download</button>
                </td>
            </tr>`;
        videoList.append(videoRow);  // Append each video row to the table
    });

    toggleButtons(true);  // Show buttons after displaying the video list
}



function toggleInputSection(show) {
    if (show) {
        $('#inputSection').show();
    } else {
        $('#inputSection').hide();
    }
}

function showProgress(message, animate = false) {
    $('.progress').show();
    $('#progressText').text(message);
    animateProgressText(animate);
}

function hideProgress(message = '') {
    $('.progress').hide();
    $('#progressText').text(message).removeClass('animated-text').css({
        'color': '',
        'font-weight': '',
        'font-size': ''
    });
    $('.progress-bar').css('width', '0%');
}

function updateButtonStatus(index, isDownloading, isFinished = false) {
    let button = $(`#video-${index} .download-button`);
    let checkbox = $(`#video-${index} .video-checkbox`);
   console.log(button.text());
    if (isDownloading) {
        button.text('Converting...').addClass('btn-dark').removeClass('btn-primary').prop('disabled', true);
    } else if (isFinished) {
        button.text('Finished').addClass('btn-dark').removeClass('btn-primary').prop('disabled', true);
        checkbox.prop('disabled', true);
        checkbox.prop('checked', false); 
         // Disable the checkbox
        $(`#video-${index}`).addClass('downloaded'); // Add a class to indicate the video is downloaded
    }
}
function toggleButtons(show) {
    if (show) {
        $('#selectAllButton').show();
        $('#downloadSelectedButton').show();
        $('#cancelButton').hide();
        $('#convertNextButton').show();
    } else {
        $('#selectAllButton').hide();
      
        $('#convertNextButton').show();
    }
}

function animateProgressText(animate) {
    if (animate) {
        $('#progressText').addClass('animated-text').css({
            'color': '#2a9d8f',
            'font-weight': 'bold',
            'font-size': '1.5rem',
            'animation': 'none'
        });
        startDotsAnimation();
    } else {
        $('#progressText').css({
            'color': '#2a9d8f',
            'font-weight': 'bold',
            'font-size': '1.5rem',
            'animation': 'none'
        });
        stopDotsAnimation();
    }
}

function startDotsAnimation() {
    let dots = 0;
    let interval = setInterval(() => {
        if ($('#progressText').hasClass('animated-text')) {
            dots = (dots + 1) % 4;
            let dotText = '.'.repeat(dots);
            $('#progressText').text($('#progressText').text().replace(/\.*$/, dotText));
        } else {
            clearInterval(interval);
        }
    }, 500);
}

function stopDotsAnimation() {
    $('#progressText').removeClass('animated-text');
    $('#progressText').text($('#progressText').text().replace(/\.*$/, ''));
}

$(document).ready(function() {
    $('head').append('<style>@keyframes blink { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }</style>');
});

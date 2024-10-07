// api.js

// Function to fetch playlist data from the server
// formData - the data to send in the POST request (contains playlist URL and other params)
// successCallback - function to execute when the request succeeds
// errorCallback - function to execute when the request fails
function fetchPlaylist(formData, successCallback, errorCallback) {
    $.ajax({
        url: '/download',    // Endpoint where the playlist data is fetched
        method: 'POST',      // Use POST method to send form data
        data: formData,      // The form data serialized from the form inputs
        success: successCallback, // Call on successful request
        error: errorCallback     // Call if there’s an error
    });
}

// Helper function to get the IDs of selected videos
function getSelectedVideos() {
    let selectedVideos = [];
    $('.video-checkbox:checked').each(function() {
        let index = $(this).data('video-index');  // Get index of checked video
        selectedVideos.push(videoDetails[index].id);  // Add the video ID to the array
    });
    return selectedVideos;  // Return the array of selected video IDs
}

// Download the video based on its ID, format, and quality
function downloadVideo(videoId, videoTitle, format, quality, successCallback, errorCallback) {
    $.ajax({
        url: '/downloadVideo',  // Endpoint for video download
        method: 'POST',          // Use POST method to send video details
        data: {
            video_id: videoId,      // Video ID to download
            title: videoTitle,      // Video Title
            format: format,         // Selected format
            quality: quality        // Selected quality
        },
        timeout: 3600000, // 1 hour timeout
        success: function(response) {
            successCallback(response);  // Execute success callback
        },
        error: errorCallback  // Execute error callback on failure
    });
}

// Process the download of multiple videos
function processVideoDownloads(videoIds) {
    if (videoIds.length > 0) {
        let videoId = videoIds.shift();  // Get the first video ID
        let index = videoDetails.findIndex(video => video.id === videoId);  // Find video index
        let format = $(`#format-${index}`).val();  // Get selected format
        let quality = $(`#quality-${index}`).val();  // Get selected quality
        
        updateButtonStatus(index, true);  // Mark the video as being downloaded
        showProgress('converting...', true);  // Show progress message
        
        downloadVideo(videoId, format, quality, function(response) {
            var link = document.createElement('a');
            link.href = response.file_url;  // Get video download URL
            link.download = response.file_url.split('/').pop();  // Set the download filename
            link.click();  // Trigger the download
            updateButtonStatus(index, false, true);  // Mark the download as completed
            showProgress('Download Complete', false);  // Update progress
            processVideoDownloads(videoIds);  // Process the next video
        }, function(response) {
            showProgress(response.responseJSON.error || 'Download Failed', false);  // Handle download failure
            toggleInputSection(true);  // Re-enable input section
            hideProgress();
        });
    } else {
        showProgress('All downloads completed', false);  // Show completion message
        toggleButtons(false);  // Hide buttons after download completion
    }
}

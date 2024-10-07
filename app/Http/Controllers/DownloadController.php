<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Models\Visit;
use Carbon\Carbon; 
use App\Models\Download;
use File;
use ZipArchive;

/**
 * Class DownloadController
 *
 * Handles functionalities related to downloading YouTube videos, tracking visits,
 * and managing user authentication for accessing visit data.
 *
 * @package App\Http\Controllers
 */
class DownloadController extends Controller
{
    /**
     * Display the main page and log the visit.
     *
     * This method records the visitor's IP address, user agent, and the timestamp
     * of the visit. It then returns the 'index' view.
     *
     * @param Request $request The incoming HTTP request instance.
     * @return \Illuminate\View\View The main page view.
     */
    public function index(Request $request)
    {
        // Log the visit details to the database
        Visit::create([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'visited_at' => Carbon::now()
        ]);

        // Return the main page view
        return view('index');
    }
    
    /**
     * Handle the download request for a YouTube video or playlist.
     *
     * This method validates the provided YouTube URL, determines if it's a playlist
     * or a single video, fetches the video details using yt-dlp, and returns the
     * details as a JSON response.
     *
     * @param Request $request The incoming HTTP request instance.
     * @return \Illuminate\Http\JsonResponse JSON response containing video details or error messages.
     */
    public function download(Request $request)
    {
        // Remove the execution time limit to handle long downloads
        set_time_limit(1200);

        // Validate the incoming request parameters
        $request->validate([
            'playlist_url' => 'required|url',
        ]);

        // Retrieve the YouTube URL and optional format and quality parameters
        $url = $request->input('playlist_url');
        $format = $request->input('format', 'mp3');
        $quality = $request->input('quality', 'high');

        // Validate if the provided URL is a valid YouTube link
        if (!preg_match('/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.?be)\/.+/', $url)) {
            return response()->json(['error' => 'Please enter a valid YouTube URL.'], 400);
        }

        // Get the current session ID and define a temporary directory for downloads
        $session_id = Session::getId();
        $temp_dir = storage_path("app" . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "temp_$session_id");


        // Create the temporary directory if it doesn't exist
        if (!file_exists($temp_dir)) {
            mkdir($temp_dir, 0777, true);
        }

        // Check if the URL is a playlist by looking for 'list=' parameter
        if (strpos($url, 'list=') !== false) {
            // It's a playlist; fetch playlist details using yt-dlp
            $fetch_command = sprintf('yt-dlp --flat-playlist --dump-single-json %s', escapeshellarg($url));
            exec($fetch_command . ' 2>&1', $output, $return_var);

            // Filter lines that contain JSON data
            $json_lines = array_filter($output, function ($line) {
                return preg_match('/^{.*}$/', $line);
            });

            // Combine JSON lines into a single string
            $output_string = implode("\n", $json_lines);
            Log::info('Output String', ['output_string' => $output_string]);

            // Decode the JSON string into an associative array
            $playlist_data = json_decode($output_string, true);
            Log::info('Parsed Playlist Data', ['playlist_data' => $playlist_data]);

            // Check if the command was successful and if entries exist
            if ($return_var !== 0 || empty($playlist_data['entries'])) {
                Log::error('Failed to fetch video details', ['output' => $output]);
                return response()->json(['error' => 'Failed to fetch video details.'], 500);
            }

            // Map the playlist entries to extract necessary details
            $video_details = array_map(function ($entry) {
                return [
                    'id' => $entry['id'],
                    'title' => $entry['title'],
                    'duration' => isset($entry['duration']) ? gmdate("H:i:s", $entry['duration']) : 'Unknown',
                    'thumbnail' => $entry['thumbnails'][0]['url'] ?? null
                ];
            }, $playlist_data['entries']);

            // Return the video details and session ID as JSON
            return response()->json(['video_details' => $video_details, 'session_id' => $session_id]);
        } else {
            // It's a single video; fetch video details using yt-dlp
            $fetch_command = sprintf('yt-dlp --dump-single-json %s', escapeshellarg($url));
            exec($fetch_command . ' 2>&1', $output, $return_var);

            // Filter lines that contain JSON data
            $json_lines = array_filter($output, function ($line) {
                return preg_match('/^{.*}$/', $line);
            });

            // Combine JSON lines into a single string
            $output_string = implode("\n", $json_lines);
            Log::info('Output String', ['output_string' => $output_string]);

            // Decode the JSON string into an associative array
            $video_data = json_decode($output_string, true);
            Log::info('Parsed Video Data', ['video_data' => $video_data]);

            // Check if the command was successful and if video data exists
            if ($return_var !== 0 || empty($video_data)) {
                Log::error('Failed to fetch video details', ['output' => $output]);
                return response()->json(['error' => 'Failed to fetch video details.'], 500);
            }

            // Extract necessary details from the video data
            $video_details = [
                [
                    'id' => $video_data['id'],
                    'title' => $video_data['title'],
                    'duration' => isset($video_data['duration']) ? gmdate("H:i:s", $video_data['duration']) : 'Unknown',
                    'thumbnail' => $video_data['thumbnails'][0]['url'] ?? null
                ]
            ];

            // Return the video details and session ID as JSON
            return response()->json(['video_details' => $video_details, 'session_id' => $session_id]);
        }
    }
/**
 * Log a download action.
 *
 * @param string $url The URL of the video or playlist being downloaded.
 * @param string $ipAddress The IP address of the user initiating the download.
 * @return void
 */
protected function logDownload(string $url, string $ipAddress): void
{
    Download::create([
        'url' => $url,
        'ip_address' => $ipAddress,
        'downloaded_at' => now(),
    ]);
}

    /**
     * Download the specified video in the chosen format and quality.
     *
     * This method constructs and executes a yt-dlp command to download the video
     * based on the provided video ID, format, and quality. Upon successful download,
     * it returns the file URL for the user to access.
     *
     * @param Request $request The incoming HTTP request instance.
     * @return \Illuminate\Http\JsonResponse JSON response containing the file URL or error messages.
     */

    public function downloadVideo(Request $request)
    {
        // Validate the incoming request parameters
        $request->validate([
            'video_id' => 'required|string',
            'format' => 'required|string',
            'quality' => 'required|string',
        ]);
        

        // Retrieve the input parameters
        $video_id = $request->input('video_id');
        $format = $request->input('format');
        $quality = $request->input('quality');
        $title = $request->input('quality');
        // Get the current session ID and define the temporary directory
        $session_id = Session::getId();
        $temp_dir = storage_path("app/public/temp_$session_id");

        // Construct the full YouTube video URL
        $video_url = 'https://www.youtube.com/watch?v=' . $video_id;
        Log::info('Selected Format', ['format' => $format]);

        // Define the output template for the downloaded file
        $output_template = $temp_dir . '/%(title)s.%(ext)s';

        // Determine the format flags based on user input
        $format_flag = $format === 'mp3' ? 'bestaudio' : "bestvideo[height<=$quality]+bestaudio/best[height<=$quality]";
        $audio_quality_flag = $format === 'mp3' ? '--audio-quality ' . escapeshellarg($quality) : '';
        $extract_audio_flag = $format === 'mp3' ? '--extract-audio --audio-format mp3' : '';
        $merge_output_format = $format === 'mp4' ? '--merge-output-format mp4' : '';

        // Construct the yt-dlp command with appropriate flags
        $download_command = sprintf(
            'yt-dlp -f %s %s %s %s --output "%s" %s',
            escapeshellarg($format_flag),
            $audio_quality_flag,
            $extract_audio_flag,
            $merge_output_format,
            $output_template,
            escapeshellarg($video_url)
        );

        // Execute the download command using proc_open to capture output
        $process = proc_open($download_command, [
            1 => ['pipe', 'w'], // STDOUT
            2 => ['pipe', 'w']  // STDERR
        ], $pipes);

        if (is_resource($process)) {
            // Read the standard output of the process
            while (!feof($pipes[1])) {
                $output = fgets($pipes[1], 1024);
                Log::info('Download Output', ['output' => $output]);
            }

            // Close the pipes after reading
            fclose($pipes[1]);
            fclose($pipes[2]);

            // Get the exit status of the process
            $return_var = proc_close($process);

            // Check if the download was successful
            if ($return_var !== 0) {
                return response()->json(['error' => 'Download failed for video: ' . $video_id], 500);
            }

            // Determine the file extension based on the selected format
            $file_extension = $format === 'mp3' ? 'mp3' : 'mp4';

            // Search for the downloaded file in the temporary directory
            $files = glob("$temp_dir/*.$file_extension");

            if (count($files) > 0) {
                $this->logDownload($video_url, $request->ip());
                // If the file is found, prepare the download URL
                $file_path = $files[0];
                $file_name = basename($file_path);
                return response()->json(['file_url' => route('download.file', ['file_name' => $file_name])]);
            }
        }

        // If the process failed or no file was found, return an error
        return response()->json(['error' => 'Download failed.'], 500);
    }

    /**
     * Serve the downloaded file to the user for download.
     *
     * This method retrieves the file from the temporary storage based on the
     * provided file name and initiates a download response. After sending the
     * file, it deletes the file from the server.
     *
     * @param string $file_name The name of the file to be downloaded.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     *         The file download response or a 404 error if the file is not found.
     */
    public function getFile($file_name)
    {
        // Retrieve the current session ID to locate the correct temporary directory
        $session_id = Session::getId();
        $file_path = storage_path("app" . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "temp_$session_id" . DIRECTORY_SEPARATOR . $file_name);

        // Check if the file exists
        if (file_exists($file_path)) {
            // Initiate the file download and delete the file after sending
            return response()->download($file_path)->deleteFileAfterSend(true);
        }

        // If the file does not exist, return a 404 error
        return abort(404, 'File not found.');
    }

    /**
     * Check the provided password and return visit data if authenticated.
     *
     * This method validates the incoming password. If the password matches the
     * predefined value, it retrieves all visit records, counts them, and returns
     * the data as a JSON response. Otherwise, it returns a failure response.
     *
     * @param Request $request The incoming HTTP request instance.
     * @return \Illuminate\Http\JsonResponse JSON response containing visit data or failure status.
     */
    public function checkPasswordAndGetVisits(Request $request)
    {
        // Validate the incoming request parameters
        $request->validate([
            'password' => 'required|string',
        ]);

        // Check if the provided password matches the expected password
        if ($request->input('password') === 'Dmoon@95') {
            // Retrieve all visit records from the database
            $visits = Visit::all();

            // Count the total number of visits
            $count = $visits->count();

            // Return the visits and count as a JSON response
            return response()->json([
                'success' => true,
                'visits' => $visits,
                'count' => $count
            ]);
        }

        // If the password is incorrect, return a failure response
        return response()->json(['success' => false]);
    }
    /**
 * Display a listing of all downloads.
 *
 * @return \Illuminate\View\View
 */
/**
 * Check the provided password and return download data if authenticated.
 *
 * This method validates the incoming password. If the password matches the
 * predefined value, it retrieves all download records, counts them, and returns
 * the data as a JSON response. Otherwise, it returns a failure response.
 *
 * @param Request $request The incoming HTTP request instance.
 * @return \Illuminate\Http\JsonResponse JSON response containing download data or failure status.
 */
public function showDownloads(Request $request)
{
    // Validate the incoming request parameters
    $request->validate([
        'password' => 'required|string',
    ]);

    // Check if the provided password matches the expected password
    if ($request->input('password') === 'Dmoon@95') {
        // Retrieve all download records, ordered by most recent
        $downloads = Download::orderBy('downloaded_at', 'desc')->get();

        // Count the total number of downloads
        $count = $downloads->count();

        // Return the downloads and count as a JSON response
        return response()->json([
            'success' => true,
            'downloads' => $downloads,
            'count' => $count
        ]);
    }

    // If the password is incorrect, return a failure response
    return response()->json(['success' => false]);
}


    /**
     * Run a shell command.
     *
     * **Note:** This method is currently not implemented. You can use this method
     * to execute shell commands securely within your application.
     *
     * @param string $command The shell command to execute.
     * @throws ProcessFailedException If the process fails.
     */
    // public function runShellCommand(string $command)
    // {
    //     // Implementation goes here
    // }

    /**
     * Recursively delete a directory and its contents.
     *
     * **Note:** This method is currently not implemented. You can use this method
     * to delete directories and their contents safely.
     *
     * @param string $dir The directory path to delete.
     * @return bool Returns true on success, false on failure.
     */
    // public function deleteDirectory(string $dir): bool
    // {
    //     // Implementation goes here
    // }
}

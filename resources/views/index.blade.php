<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags for character encoding and viewport settings -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSRF token for security in forms (used in Laravel applications) -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Page Title -->
    <title>Video Vibes</title>
    <!-- Bootstrap CSS for styling -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
    <!-- Custom CSS -->
     <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <!-- Additional Styles -->
    <!-- Primary Meta Tags -->
    <meta name="title" content="Video Vibe - Effortlessly Convert YouTube Playlists & Single Videos to MP3/MP4">
    <meta name="description" content="Video Vibe lets you seamlessly convert and download your favorite YouTube playlists and single videos to MP3 or MP4 formats. Enjoy high-quality audio and video downloads with just a few clicks!">
    
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4749076613405516"
     crossorigin="anonymous"></script>
    <!-- Keywords Meta Tag -->
    <meta name="keywords" content="YouTube to MP3, YouTube to MP4, Playlist Converter, Video Downloader, Free YouTube Converter, Download YouTube Videos, Convert YouTube Playlists, Single Video Downloader">
    <link rel="icon" href="{{ asset('icons/favicon.ico') }}" type="image/x-icon">
    
    <!-- PNG Favicon for Modern Browsers -->
    <link rel="icon" href="{{ asset('icons/favicon-32x32.png') }}" type="image/png" sizes="32x32">
    <link rel="icon" href="{{ asset('icons/favicon-16x16.png') }}" type="image/png" sizes="16x16">
    
    <!-- Apple Touch Icon for iOS Devices -->
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
    
    <!-- Manifest for Progressive Web Apps (Optional) -->
    <link rel="manifest" href="{{ asset('icons/manifest.json') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <!-- Open Graph / Facebook Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.videovibes.cc/">
    <meta property="og:title" content="Video Vibe - Effortlessly Convert YouTube Playlists & Single Videos to MP3/MP4">
    <meta property="og:description" content="Seamlessly convert and download YouTube playlists and single videos to MP3 or MP4 formats with Video Vibe. Enjoy your favorite content offline anytime, anywhere!">
    <meta property="og:image" content="https://www.videovibes.cc/assets/img/og-image.jpg">
    
    <!-- Twitter Meta Tags -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://www.videovibes.cc/">
    <meta property="twitter:title" content="Video Vibe - Effortlessly Convert YouTube Playlists & Single Videos to MP3/MP4">
    <meta property="twitter:description" content="Convert and download your favorite YouTube playlists and single videos to MP3 or MP4 formats easily with Video Vibe. Enjoy offline access to your music and videos with our user-friendly tool.">
    <meta property="twitter:image" content="https://www.videovibes.cc/assets/img/twitter-image.jpg">
    
    <!-- Canonical URL to Prevent Duplicate Content Issues -->
    <link rel="canonical" href="https://www.videovibes.cc/" />
    
    <!-- Structured Data (JSON-LD) for Enhanced Search Engine Understanding -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "name": "Video Vibe",
      "url": "https://www.videovibes.cc/",
      "description": "Video Vibe lets you seamlessly convert and download your favorite YouTube playlists and single videos to MP3 or MP4 formats. Enjoy high-quality audio and video downloads with just a few clicks!",
      "potentialAction": {
        "@type": "SearchAction",
        "target": "https://www.videovibes.cc/search?q={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    }
    </script>
    
</head>
<body>
    <!-- Header Section -->
    <header class="app-header">
        <div class="container-fluid">
            <!-- Navigation Links -->
            <div class="header-links text-center">
                  <a href="/">Home</a>
                <a href="/faq">FAQ</a>
                <a href="/t&c">T&C</a>
            </div>
            <!-- Logo and Title -->
            <div class="logo-title-section text-center mt-4">
               <a href="/"> <img src="{{ asset('assets/img/icon.png') }}" alt="Logo" class="logo"></a>
                <h1 class="app-title mt-2">Video Vibes</h1>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <div class="main-container">
        <!-- Orange Div (Left Advertisement) -->
        <div class="side-div orange-div">
        <script src="//servedby.eleavers.com/ads/ads.php?t=MzA5MjU7MjEwMTQ7c3F1YXJlLm1lZGl1bV9yZWN0YW5nbGU=&index=1"></script>
            <!-- Insert your advertisement content here -->
            <!-- Example: <img src="path_to_left_ad.jpg" alt="Left Ad" class="img-fluid"> -->
        </div>

        <!-- Card Component -->
        <div class="card emphasized-card  mx-auto">
            <!-- Card Header -->
            <div class="card-header text-center">
                <h3>Download Your Favorite Playlist to MP3 or MP4</h3>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <!-- Input Section -->
                <div id="inputSection">
                    <!-- Download Form -->
                    <form id="downloadForm" class="d-flex">
                        <!-- CSRF Token for security -->
                        @csrf
                        <!-- Input Group -->
                        <div class="position-relative flex-grow-1 align-items-center">
                            <!-- URL Input Field -->
                            <input type="url" class="form-control rounded-input pr-5" id="playlist_url" name="playlist_url" placeholder="https://youtube.com/watch?v=hWoz_svvMkA" required>
                            <!-- Paste Button inside Input Field -->
                            <button type="button" class="btn btn-primary paste-button" id="pasteButton">
                                <i class="fas fa-paste"></i> Paste
                            </button>
                        </div>
                        <!-- Convert Button -->
                        <button type="submit" class="btn themed-button ml-2" id="convertButton">
                            Convert
                        </button>
                        <div id="urlError" class="alert alert-danger mt-2" style="display: none;" role="alert">
        Please enter a valid YouTube URL.
    </div>
                    </form>
                </div>
                <!-- Progress Bar (Hidden Initially) -->
                <div class="progress mt-3" style="display:none; background-color: white;">
                    <div class="progress-bar progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    <div id="progressText" class="animated-text mt-2"></div>
                </div>
                <!-- Convert Next Button (Hidden Initially) -->
                <button id="convertNextButton" class="btn btn-primary btn-block mt-3" style="display:none;">Convert Next</button>
                <!-- Action Buttons (Hidden Initially) -->
                <div class="action-buttons mt-3">
                    <button id="selectAllButton" class="btn btn-primary" style="display:none;">Select All</button>
                    <button id="downloadSelectedButton" class="btn btn-primary" style="display:none;">Download Selected</button>
                </div>
            </div>
            <!-- End of Card Body -->
        </div>

        <!-- Brown Div (Right Advertisement) -->
        <div class="side-div brown-div">
        <script type="text/javascript">
	atOptions = {
		'key' : '75461091d2d1cd590498a9a5a6d359f8',
		'format' : 'iframe',
		'height' : 600,
		'width' : 160,
		'params' : {}
	};
</script>
<script type="text/javascript" src="//www.topcreativeformat.com/75461091d2d1cd590498a9a5a6d359f8/invoke.js"></script>
        </div>
    </div>
    <!-- End of Main Content Container -->

    <!-- Centered Purple Div -->
    <!-- <div class="centered-purple-div">
        <p>Centered Purple Div</p>
    </div> -->

    <!-- Three Column Layout -->
    <div class="three-column-layout">
        <!-- Left Column -->
        <div class="left-column">
            <!-- Insert your content or advertisements here -->
        </div>
        <!-- Middle Column -->
        <div class="middle-column">
            <!-- Video List Section -->
            <div id="video-list" class="mt-4">
                <!-- Video List Table -->
                <table class="table table-bordered table-striped">
                    <!-- Table Header (Hidden Initially) -->
                    <thead style="display: none;" id="table-headers">
                        <tr>
                            <th scope="col">Select</th>
                            <th scope="col">Thumbnail</th>
                            <th scope="col" style="padding:0px;">Title</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <!-- Table Body for Video Items -->
                    <tbody id="video-table-body">
                    </tbody>
                </table>
            </div>
            <!-- End of Video List Section -->
            <div class="info-section">
                <h4>About Us</h4>
                <p>Welcome to our YouTube Playlist to MP3 downloader. Our tool allows you to easily convert and download YouTube playlists to MP3 format. We are dedicated to providing a simple and efficient service to help you enjoy your favorite music offline.</p>
                <p>If you have any questions or feedback, please feel free to contact us. We are always here to help and improve our service.</p>
            </div>
            <!-- Information Sections -->
            <div class="info-section">
                <h4>How to Download</h4>
                <p>1. Copy the URL of the YouTube playlist you want to download.</p>
                <p>2. Paste the URL into the input box above.</p>
                <p>3. Click on the "Convert" button to fetch the video details.</p>
                <p>4. Select the videos you want to download and click on the "Download Selected" button to start the conversion process.</p>
            </div>
          
        </div>
        <!-- Right Column -->
        <div class="right-column">
            <!-- Insert your content or advertisements here -->
        </div>
    </div>
    <!-- End of Three Column Layout -->

    <!-- Floating Yellow Footer -->
    <footer class="floating-footer">
    <script type="text/javascript">
	atOptions = {
		'key' : '711606b17b97994069c24795c934118a',
		'format' : 'iframe',
		'height' : 50,
		'width' : 320,
		'params' : {}
	};
</script>
<script type="text/javascript" src="//www.topcreativeformat.com/711606b17b97994069c24795c934118a/invoke.js"></script>
    </footer>

    <!-- JavaScript Files -->
    <!-- jQuery Library -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Popper.js and Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Custom JS Files -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('assets/js/api.js') }}"></script>
    <script src="{{ asset('assets/js/ui.js') }}"></script>
    <script src="{{ asset('assets/js/events.js') }}"></script>
</body>
</html>
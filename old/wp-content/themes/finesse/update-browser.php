<!DOCTYPE HTML>
<html> 
<head>
	<!-- begin meta -->
	<meta charset="utf-8">
	<meta name="description" content="Finesse is a responsive business and portfolio theme carefully handcrafted using the latest technologies. It features a clean design, as well as extended functionality that will come in very handy.">
	<meta name="keywords" content="Finesse, responsive business portfolio theme, HTML5, CSS3, clean design">
	<meta name="author" content="Ixtendo">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<!-- end meta -->
	
	<!-- begin CSS -->
	<link href="<?php echo get_template_directory_uri(); ?>/style.css" type="text/css" rel="stylesheet" id="main-style">
    <style type="text/css">
		body {
			font-size: 20px;
			line-height: 1.3em; /* 26px */
			text-align: center;
		}
		
		#wrap {
			padding-top: 60px;	
		}
		
		p {
			margin-bottom: 20px;
		}

		ul#web-browsers {
			margin-top: 40px;
		}
		
		ul#web-browsers li, ul#web-browsers img {
			display: inline;
		}
		
		ul#web-browsers li {
			margin: 0 5px;
		}
	</style>	
	<!-- end CSS -->

    <link href="<?php echo get_template_directory_uri(); ?>/images/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <title>Finesse - Responsive Business Theme</title>
</head>

<body>
<!-- begin container -->
<div id="wrap">
	<!-- begin centered container -->
	<div class="container">
    	<p>It looks like you are still using Internet Explorer 7 or earlier, which is not supported by this website.</p>
        <p>Please update to a newer browser:</p>
        <ul id="web-browsers">
            <li><a href="http://www.mozilla.org/en-US/firefox/new/" title="Firefox"><img src="<?php echo get_template_directory_uri(); ?>/images/web-browsers/firefox.png" alt="Firefox"></a></li>
            <li><a href="https://www.google.com/chrome" title="Chrome"><img src="<?php echo get_template_directory_uri(); ?>/images/web-browsers/chrome.png" alt="Chrome"></a></li>
            <li><a href="http://www.apple.com/safari/download/" title="Safari"><img src="<?php echo get_template_directory_uri(); ?>/images/web-browsers/safari.png" alt="Safari"></a></li>
            <li><a href="http://www.opera.com/download/" title="Opera"><img src="<?php echo get_template_directory_uri(); ?>/images/web-browsers/opera.png" alt="Opera"></a></li>
            <li><a href="http://windows.microsoft.com/en-US/internet-explorer/downloads/ie" title="Internet Explorer"><img src="<?php echo get_template_directory_uri(); ?>/images/web-browsers/internet-explorer.png" alt="Internet Explorer"></a></li>
        </ul>	
    </div>
    <!-- end centered container -->    
</div>
<!-- end container -->
</body>
</html>
<!doctype html>
<html lang="bg-BG">
<head>
    <meta charset="UTF-8">
    <title></title>
    <script type="text/javascript">
        function installApp(){
            var manifestUrl = '//bgzilla-rss.chilyashev.com/manifest.webapp';
            var request = window.navigator.mozApps.install(manifestUrl);
            request.onsuccess = function () {
                // Save the App object that is returned
                var appRecord = this.result;
                alert("Application installed");
            };
            request.onerror = function () {
                // Display the error information from the DOMError object
                alert('Installation error:' + this.error.name);
            };
        }
    </script>
</head>
<body>
<div id="install">
	<a href="javascript:" onclick="installApp();">
	<img src="app-icons/icon-120.png" /><br/>
	Install
	</a>
</div>
</body>
</html>


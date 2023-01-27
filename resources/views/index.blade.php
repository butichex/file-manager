<!DOCTYPE html>
<html lang="{{app()->getLocale()}}">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
		  content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">

	<title>Laravel File Manager</title>
	<script>
	  window.FILE_MANAGER_BASE_PATH = '{{$path}}';
		window.FILE_MANAGER_BASE_URL = '{{$route_prefix}}';
		window.FILE_MANAGER_DS = '{{$ds}}';
		window.CSRF_TOKEN = '{{csrf_token()}}';
	</script>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900">
	<link rel="stylesheet" type="text/css" href="{{asset('vendor/file-manager/css/app.css')}}">
	<script src="{{asset('vendor/file-manager/js/manifest.js')}}"></script>
	<script src="{{asset('vendor/file-manager/js/vendor.js')}}"></script>
	<script src="{{asset('vendor/file-manager/js/main.js')}}" defer></script>
</head>
<body oncontextmenu="return false;">
<div id="app"></div>
</body>
</html>

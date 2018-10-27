<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v2.12&appId=933735970113789&autoLogAppEvents=1';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<div class="fb-page" 
  data-href="{{ $fanpage }}"
  data-hide-cover="false"
  data-show-facepile="false" data-tabs="messages" hide_cover="false" height="317px" width="250px">
</div>
</body>
</html>
(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v3.0&appId=933735970113789&autoLogAppEvents=1';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

$(function() {
	$("#checkboxToggle").change(function() {
		if ($(this).is(":checked")) {
			$(".btSlideUpNav").addClass('fa-rotate-180')
		} else {
			$(".btSlideUpNav").removeClass('fa-rotate-180')
		}
	})
})
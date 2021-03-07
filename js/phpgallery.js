

var url
function changepage(formObject)
{
  url = formObject.options[formObject.options.selectedIndex].value;
  if(url != "empty") {
    window.location = url;
    url = "";
  }
}

function DoSubmission()
{
  document.gallery.submit();
}

// read a cookie by name and return the value
function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0)
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}
// read and update the currBrowserWidth cookie value
// write the previous width and if different to the current width then reload the page
function browserResCookie() {
	var prevBrowserWidth = 0;
	var x = readCookie('currBrowserWidth')
	if (x) { prevBrowserWidth = +x; }
	document.cookie='prevBrowserWidth='+prevBrowserWidth+'; expires=; path=/';
	document.cookie='currBrowserWidth='+window.outerWidth+'; expires=; path=/';
	if (prevBrowserWidth != window.outerWidth) { location.reload(); }
}
browserResCookie(); // called to insturgate the browser width checking

// create an 'onresize' event handler that checks changes in browser width
window.onresize = function(event) {
	browserResCookie();
};


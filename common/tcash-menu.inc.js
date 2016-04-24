
var mainmenutoggle = document.getElementById('main_menu_toggle');
var mainmenu = document.getElementById('main_menu');

mainmenutoggle.addEventListener('click', togglemenu, false);


function togglemenu(e) {
	if (mainmenu.style.display == 'block') {
		mainmenu.style.display = '';
		mainmenutoggle.style.marginBottom = '0.1em';
	} else {
		mainmenu.style.display = 'block';
		mainmenutoggle.style.marginBottom = '0';
	}
}


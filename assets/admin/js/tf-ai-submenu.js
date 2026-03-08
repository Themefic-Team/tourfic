(function(){
	var links = document.querySelectorAll('.tf-ai-submenu-item');
	if (!links.length) return;

	var overlay = document.getElementById('tf-ai-upsell-overlay');

	function openUpsell() {
		overlay.classList.add('active');
		document.body.style.overflow = 'hidden';
	}

	function closeUpsell() {
		overlay.classList.remove('active');
		document.body.style.overflow = '';
	}

	links.forEach(function(span) {
		var a = span.closest('a');
		if (!a) return;
		a.addEventListener('click', function(e) {
			e.preventDefault();
			openUpsell();
		});
	});

	overlay.addEventListener('click', function(e) {
		if (e.target === overlay) closeUpsell();
	});

	var dismissBtns = overlay.querySelectorAll('.tf-ai-upsell-dismiss-action');
	dismissBtns.forEach(function(btn) {
		btn.addEventListener('click', closeUpsell);
	});

	document.addEventListener('keydown', function(e) {
		if (e.key === 'Escape' && overlay.classList.contains('active')) closeUpsell();
	});
})();

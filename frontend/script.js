document.querySelectorAll('.profile-toggle').forEach((toggle) => {
	toggle.addEventListener('click', () => {
		const menu = toggle.closest('.profile-menu');
		const isOpen = menu.classList.toggle('open');
		toggle.setAttribute('aria-expanded', String(isOpen));
	});
});

document.addEventListener('click', (event) => {
	document.querySelectorAll('.profile-menu.open').forEach((menu) => {
		if (!menu.contains(event.target)) {
			menu.classList.remove('open');
			menu.querySelector('.profile-toggle')?.setAttribute('aria-expanded', 'false');
		}
	});
});

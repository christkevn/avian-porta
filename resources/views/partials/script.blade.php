<script>
	document.documentElement.style.setProperty('--' + window.Helpers.prefix + 'primary', '#0D9394');
	const toggleBtn = document.querySelector(".layout-menu-toggle a");
	toggleBtn.addEventListener("click", function (e) {
	e.preventDefault();
	document.documentElement.classList.toggle("layout-menu-collapsed");
	});
	document.addEventListener("scroll", function () {
		const navbar = document.getElementById("layout-navbar");
		if (window.scrollY > 10) {
			navbar.classList.add("navbar-scrolled");
		} else {
			navbar.classList.remove("navbar-scrolled");
		}
	});
</script>
export function toggleMenu(event) {
    document.querySelector('.nav-left').classList.toggle("open");
}

export function isMenuOpen() {
    return document.querySelector('.nav-left').classList.contains("open");
}

window.addEventListener('resize', function () {
    if (isMenuOpen() && window.innerWidth >= 1250)
        toggleMenu()
})

document.querySelectorAll("#nav-responsive_close, #nav-responsive_open").forEach(x=>x.addEventListener("click", toggleMenu))
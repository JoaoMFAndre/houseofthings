const sideMenu = document.querySelector("aside");
const menuBtn = document.querySelector("#menu-btn");
const closeBtn = document.querySelector("#close-btn");

menuBtn.addEventListener('click', () => {
    sideMenu.style.display = 'block';
})

closeBtn.addEventListener('click', () => {
    sideMenu.style.display = 'none';
})
window.addEventListener("resize", function () {
    if (window.matchMedia("(min-width: 769px)").matches) {
        sideMenu.style.display = 'block';
    } else if (window.matchMedia("(max-width: 768px)").matches) {
        sideMenu.style.display = 'none';
    }
})
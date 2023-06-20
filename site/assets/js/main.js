/* ---------- TOGGLE MENU LEFT ---------- */
//  --menu-left-width-collapse

const menuLeft = document.querySelector('.menu-left');
const mainContent = document.querySelector('.main-content');


function toggleMenu() {
    menuLeft.classList.toggle('collapse');
    mainContent.classList.toggle('collapse');

}

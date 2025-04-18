// let hamMenu = document.querySelector('.ham-menu');

// let sideBar = document.querySelector('.sidebar');

// hamMenu.addEventListener('click', () => { 
//    hamMenu.classList.toggle('active');
//    sideBar.classList.toggle('active');
// });

const hamMenu = document.querySelector('.ham-menu');
const sidebar = document.getElementById('sidebar');

// Toggle sidebar and hamburger icon
hamMenu.addEventListener('click', (e) => {
  e.stopPropagation(); // Prevent closing when clicking on the menu
  hamMenu.classList.toggle('active');
  sidebar.classList.toggle('active');
});

// Hide sidebar when clicking outside
document.addEventListener('click', (e) => {
  const isClickInsideSidebar = sidebar.contains(e.target);
  const isClickOnHamburger = hamMenu.contains(e.target);

  if (sidebar.classList.contains('active') && !isClickInsideSidebar && !isClickOnHamburger) {
    sidebar.classList.remove('active');
    hamMenu.classList.remove('active');
  }
});
let hamMenu = document.querySelector('.ham-menu');

let sideBar = document.querySelector('.sidebar');

hamMenu.addEventListener('click', () => { 
   hamMenu.classList.toggle('active');
   sideBar.classList.toggle('active');
});
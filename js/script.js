const wrapper = document.querySelector('.wrapper');
const loginlink = document.querySelector('.login-link');
const registerlink = document.querySelector('.register-link');
const loginButton = document.querySelector('.btn-popup');
const closelog = document.getElementById('logi');
const closereg = document.getElementById('regi');
const loginForm = document.getElementById('login');
const registerForm = document.getElementById('register');
const hText = document.querySelector('.h-text'); // Selecting the .h-text element

// Function to hide .h-text
function hideHText() {
  hText.style.display = 'none';
}

// Function to show .h-text
function showHText() {
  hText.style.display = 'block';
}

// login button sa nav
loginButton.addEventListener('click', () => {
  wrapper.classList.add('active-popup');
  hideHText(); // Hide .h-text when login/register form opens
});

//papuntang signup
registerlink.addEventListener('click', () => {
  wrapper.classList.add('active');
  loginForm.reset();
});

// papuntang login
loginlink.addEventListener('click', () => {
  wrapper.classList.remove('active');
  registerForm.reset(); 
});

//close button sa login
closelog.addEventListener('click', () => {
  wrapper.classList.remove('active-popup');
  loginForm.reset();
  showHText(); // Show .h-text when login/register form closes
});

//close button sa signup
closereg.addEventListener('click', () => {
  wrapper.classList.remove('active-popup');
  registerForm.reset();
  showHText(); // Show .h-text when login/register form closes  
});

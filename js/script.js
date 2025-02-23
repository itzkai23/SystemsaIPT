const wrapper = document.querySelector('.wrapper');
const loginlink = document.querySelector('.login-link');
const registerlink = document.querySelector('.register-link');
const loginButton = document.querySelector('.btn-popup');
const closelog = document.getElementById('logi');
const closereg = document.getElementById('regi');
const loginForm = document.getElementById('login');
const registerForm = document.getElementById('register');

// login button sa nav
loginButton.addEventListener('click', () => {
  wrapper.classList.add('active-popup');
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
});

//close button sa signup
closereg.addEventListener('click', () => {
  wrapper.classList.remove('active-popup');
  registerForm.reset(); 
});

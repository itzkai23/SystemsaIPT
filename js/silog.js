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

document.getElementById("registerForm").addEventListener("submit", function(event) {
    var email = document.getElementById("email").value;
    var allowedDomain = "@cityofmalabonuniversity.edu.ph";

    if (!email.endsWith(allowedDomain)) {
        alert("Invalid email! Use your @cityofmalabonuniversity.edu.ph gmail");
        event.preventDefault(); // Prevents form submission
    }
});

document.getElementById("login").addEventListener("submit", function (event) {
  const urlParams = new URLSearchParams(window.location.search);
  const error = urlParams.get('error');
  const errorDiv = document.getElementById('login-error');

  // Handle error messages
  if (error === 'incorrect_password') {
      errorDiv.textContent = 'Incorrect password. Please try again.';
  } else if (error === 'user_not_found') {
      errorDiv.textContent = 'User not found. Please check your username.';
  }

  // Automatically open login modal if error exists
  if (error) {
      document.querySelector('.wrapper').classList.add('active-popup');
      document.querySelector('.form-box.login').classList.add('active');
  }

  // Clear error message when the user starts typing
  document.getElementById('username').addEventListener('input', () => {
      errorDiv.textContent = ''; // Clear error when typing username
  });

  document.getElementById('password').addEventListener('input', () => {
      errorDiv.textContent = ''; // Clear error when typing password
  });
});
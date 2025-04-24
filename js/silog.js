const wrapper = document.querySelector('.wrapper');
const loginlink = document.querySelector('.login-link');
const registerlink = document.querySelector('.register-link');
const loginButton = document.querySelector('.btn-popup');
const closelog = document.getElementById('logi');
const closereg = document.getElementById('regi');
const loginForm = document.getElementById('login');
const registerForm = document.getElementById('register');
const hText = document.querySelector('.h-text');

// Hide and show hText
function hideHText() {
  hText.style.display = 'none';
}

function showHText() {
  hText.style.display = 'block';
}

// Login popup open
loginButton.addEventListener('click', () => {
  wrapper.classList.add('active-popup');
  hideHText();
});

// Switch to register form
registerlink.addEventListener('click', () => {
  wrapper.classList.add('active');
  loginForm.reset();
  document.getElementById('login-error').textContent = ''; // Clear login errors
});

// Switch to login form
loginlink.addEventListener('click', () => {
  wrapper.classList.remove('active');
  registerForm.reset();
  document.getElementById('register-error').textContent = ''; // Clear register errors
});

// Close login form
closelog.addEventListener('click', () => {
  wrapper.classList.remove('active-popup');
  loginForm.reset();
  document.getElementById('login-error').textContent = ''; // Clear login errors
  showHText();
});

// Close register form
closereg.addEventListener('click', () => {
  wrapper.classList.remove('active-popup');
  registerForm.reset();
  document.getElementById('register-error').textContent = ''; // Clear register errors
  showHText();
});

// Register email validation
document.getElementById("registerForm").addEventListener("submit", function (event) {
  var email = document.getElementById("email").value;
  var allowedDomain = "@cityofmalabonuniversity.edu.ph";
  var errorDiv = document.getElementById("register-error");

  if (!email.endsWith(allowedDomain)) {
    errorDiv.textContent = "Invalid email! Use your @cityofmalabonuniversity.edu.ph email.";
    event.preventDefault();
  } else {
    errorDiv.textContent = ""; // Clear error if valid
  }
});

// Login error handler from PHP via URL param
document.addEventListener("DOMContentLoaded", function () {
  const errorDiv = document.getElementById("login-error");
  const urlParams = new URLSearchParams(window.location.search);
  const error = urlParams.get('error');

  // Check for errors and display messages
  if (error === 'invalid_credentials') {
    errorDiv.textContent = 'Incorrect password. Please try again.';
    wrapper.classList.add('active-popup'); // Keep popup open
  } else if (error === 'user_not_found') {
    errorDiv.textContent = 'User not found. Please check your username.';
    wrapper.classList.add('active-popup'); // Keep popup open
  }

  // Clear error message on input for both username and password fields
  document.getElementById('username').addEventListener('input', () => {
    errorDiv.textContent = '';
  });

  document.getElementById('password').addEventListener('input', () => {
    errorDiv.textContent = '';
  });

  // Handle the form submission using JavaScript to prevent page reload
  const loginForm = document.getElementById('login');
  loginForm.addEventListener('submit', function(event) {
    // Prevent the form from submitting and reloading the page
    event.preventDefault();
    
    // Fetch the username and password values
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    // Send the login data to the server using AJAX (e.g., with Fetch API or XMLHttpRequest)
    const formData = new FormData();
    formData.append('uname', username);
    formData.append('password', password);
    formData.append('sub', 'true'); // Simulating button click

    fetch('log.php', {
      method: 'POST',
      body: formData,
    })
    .then(response => response.text())
    .then(data => {
      // Handle server response here, depending on success or failure
      if (data.includes('invalid_credentials')) {
        errorDiv.textContent = 'Incorrect password. Please try again.';
        wrapper.classList.add('active-popup');
      } else if (data.includes('user_not_found')) {
        errorDiv.textContent = 'User not found. Please check your username.';
        wrapper.classList.add('active-popup');
      } else {
        // Redirect user to their role-based page on success (handled by PHP after login)
        window.location.href = data;
      }
    })
    .catch(error => console.error('Error:', error));
  });
});

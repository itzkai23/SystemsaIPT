function toggleMenu(button) {
    var menu = button.nextElementSibling; // Get the corresponding menu
    menu.style.display = (menu.style.display === "block") ? "none" : "block";

    // Close other open menus
    document.querySelectorAll(".menu-options").forEach(function (otherMenu) {
        if (otherMenu !== menu) {
            otherMenu.style.display = "none";
        }
    });
}

// Close menu when clicking outside
document.addEventListener("click", function (event) {
    if (!event.target.matches(".menu-btn")) {
        document.querySelectorAll(".menu-options").forEach(function (menu) {
            menu.style.display = "none";
        });
    }
});


// const scoreOutOf5 = <?php echo isset($professor_avg_score) ? number_format($professor_avg_score, 2) : 0; ?>;
// const maxScore = 5.0;
// const percentage = (scoreOutOf5 / maxScore) * 100;

// document.addEventListener("DOMContentLoaded", function () {
//   const circle = document.getElementById('progressCircle');
//   const offset = 440 - (440 * percentage) / 100;
//   circle.style.strokeDashoffset = offset;

//   // Dynamic stroke color
//   if (percentage >= 90) {
//     circle.style.stroke = '#4ade80';
//   } else if (percentage >= 75) {
//     circle.style.stroke = '#facc15';
//   } else if (percentage >= 60) {
//     circle.style.stroke = '#f97316';
//   } else {
//     circle.style.stroke = '#ef4444';
//   }

//   // Feedback text
//   const feedbackText = document.getElementById("feedbackText");
//   if (percentage >= 90) {
//     feedbackText.textContent = "Excellent performance – well above expectations.";
//   } else if (percentage >= 75) {
//     feedbackText.textContent = "Good performance – meets expectations.";
//   } else if (percentage >= 60) {
//     feedbackText.textContent = "Average – some improvement needed.";
//   } else {
//     feedbackText.textContent = "Below average – improvement required.";
//   }

//   // Set values
//   document.getElementById("scoreDisplay").textContent = `${scoreOutOf5.toFixed(2)} / ${maxScore.toFixed(2)}`;
//   document.getElementById("percentageDisplay").textContent = `${Math.round(percentage)}%`;
//   document.getElementById("averageDisplay").textContent = scoreOutOf5.toFixed(2);

//   // Modal logic
//   const trigger = document.getElementById("triggerPopup");
//   const modal = document.getElementById("popupModal");
//   const closeBtn = document.getElementById("closeModal");

//   trigger.addEventListener("click", () => {
//     modal.style.display = "flex";
//   });

//   closeBtn.addEventListener("click", () => {
//     modal.style.display = "none";
//   });

//   window.addEventListener("click", (e) => {
//     if (e.target === modal) {
//       modal.style.display = "none";
//     }
//   });
// });
document.addEventListener("DOMContentLoaded", function () {
    const modal = document.querySelector(".modal_usercom");
    const closeModal = document.querySelector(".modal_usercom .close");
    const commentsContainer = document.getElementById("commentsContainer");

    document.querySelectorAll(".rant-post").forEach(item => {
        item.addEventListener("click", function () {
            const profId = this.getAttribute("data-id");
            const name = this.getAttribute("data-name");
            const role = this.getAttribute("data-role");
            const image = this.getAttribute("data-image");
            const evaluationCount = this.getAttribute("data-evaluations");
            const averageScore = this.getAttribute("data-average");

            document.getElementById("profName").textContent = name;
            document.getElementById("profRole").textContent = role;
            document.getElementById("profImg").src = image;
            document.getElementById("evaluationCount").textContent = evaluationCount;
            document.getElementById("averageScore").textContent = averageScore;

            modal.style.display = "block";

            fetch(`profstatus.php?fetch_comments=1&professor_id=${profId}`)
                .then(response => response.text()) 
                .then(data => {
                    commentsContainer.innerHTML = data;
                })
                .catch(error => {
                    console.error("Error fetching comments:", error);
                    commentsContainer.innerHTML = "<p>Failed to load comments.</p>";
                });
        });
    });

    closeModal.addEventListener("click", function () {
        modal.style.display = "none";
    });

    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
});
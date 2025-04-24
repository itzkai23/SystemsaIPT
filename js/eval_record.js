function openModal(data) {
    document.getElementById("modal-professor").textContent = data.professor_name;
    document.getElementById("modal-score").textContent = parseFloat(data.evaluation_avg_score).toFixed(2);
    document.getElementById("modal-avg").textContent = parseFloat(data.professor_avg_score).toFixed(2);

    // Create rows for questions 1-10 and 11-20
    let questionsRow1_10 = "";
    let questionsRow11_20 = "";

    // Populate questions 1-10
    for (let i = 1; i <= 10; i++) {
        questionsRow1_10 += `<td>${data['q' + i] || 'N/A'}</td>`;
    }

    // Populate questions 11-20
    for (let i = 11; i <= 20; i++) {
        questionsRow11_20 += `<td>${data['q' + i] || 'N/A'}</td>`;
    }

    // Insert rows into the modal table
    document.getElementById("modal-questions-1-10").innerHTML = questionsRow1_10;
    document.getElementById("modal-questions-11-20").innerHTML = questionsRow11_20;

    // Show the delete button and set record ID for deletion
    // document.getElementById("delete-record-id").value = data.id;  // Set the record id for deletion
    // document.getElementById("delete-button").style.display = "block";  // Show delete button
    
    // Display the modal
    document.getElementById("modal").style.display = "block";
}

function closeModal() {
    document.getElementById("modal").style.display = "none";
}

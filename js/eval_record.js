function openModal(data) {
    // Set student and professor names, and evaluation average
    document.getElementById("modal-student").textContent = data.student_name;
    document.getElementById("modal-professor").textContent = data.professor_name;
    document.getElementById("modal-score").textContent = parseFloat(data.evaluation_avg_score).toFixed(2);
    document.getElementById("modal-avg").textContent = parseFloat(data.professor_avg_score).toFixed(2);

    // Populate the 20 question responses
    let questionsRow = "";
    for (let i = 1; i <= 20; i++) {
        questionsRow += `<td>${data['q' + i] || 'N/A'}</td>`;
    }

    // Add delete button at the end of the row
    // questionsRow += `
    //     <td>
    //         <form action='delete_record.php' method='POST' onsubmit='return confirm("Are you sure you want to delete this record?");'>
    //             <input type='hidden' name='record_id' value='${data.id}'>
    //             <button type='submit'>Delete</button>
    //         </form>
    //     </td>`;

    document.getElementById("modal-questions").innerHTML = questionsRow;

    // Show the delete button
    document.getElementById("delete-record-id").value = data.id;  // Set the record id for deletion
    document.getElementById("delete-button").style.display = "block";  // Show delete button
    
    // Display the modal
    document.getElementById("modal").style.display = "block";
}

function closeModal() {
    document.getElementById("modal").style.display = "none";
}
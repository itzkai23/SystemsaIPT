// function showReport(section) {
//     document.getElementById('report1').style.display = section === 'prof' ? 'block' : 'none';
//     document.getElementById('report2').style.display = section === 'comm' ? 'block' : 'none';
// }

function showReport(section, clickedBtn) {
    // Toggle report visibility
    document.getElementById('report1').style.display = section === 'prof' ? 'block' : 'none';
    document.getElementById('report2').style.display = section === 'comm' ? 'block' : 'none';

    // Remove active from all buttons
    const buttons = document.querySelectorAll('.repcomcon-btn a');
    buttons.forEach(btn => btn.classList.remove('active'));

    // Add active to clicked button
    clickedBtn.classList.add('active');
}

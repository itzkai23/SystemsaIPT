function showReport(section) {
    document.getElementById('report1').style.display = section === 'prof' ? 'block' : 'none';
    document.getElementById('report2').style.display = section === 'comm' ? 'block' : 'none';
}
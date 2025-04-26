function openModal() {
    document.getElementById('scheduleModal').style.display = 'flex';
  }

  function closeModal() {
    document.getElementById('scheduleModal').style.display = 'none';
    closeAnnouncement();
  }

  function showAnnouncement() {
    document.getElementById('announcementPopup').style.display = 'block';
  }

  function closeAnnouncement() {
    document.getElementById('announcementPopup').style.display = 'none';
  }

  window.onclick = function(e) {
    const modal = document.getElementById('scheduleModal');
    if (e.target === modal) {
      closeModal();
    }
  }
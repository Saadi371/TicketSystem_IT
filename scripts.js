document.getElementById('submitTicketForm').addEventListener('submit', function(event) {
    event.preventDefault();
    // Collect form data
    const formData = new FormData(event.target);

    // Submit form data to backend
    fetch('submit_ticket.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log('Form submitted:', data);
        alert('Ticket submitted successfully!');
        // Clear form fields
        event.target.reset();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('There was an error submitting your ticket.');
    });
});
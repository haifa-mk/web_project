//login
function validateAdminLogin() {
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    const errorDiv = document.getElementById('adminLoginError');

    if (username === '' || password === '') {
        errorDiv.textContent = 'Please enter both username and password.';
        return false;
    }

    errorDiv.textContent = '';
    return true;
}

//add evemt 
function validateAddEvent() {
    const name = document.getElementById('eventName').value.trim();
    const date = document.getElementById('eventDate').value.trim();
    const location = document.getElementById('location').value.trim();
    const price = document.getElementById('ticketPrice').value.trim();
    const maxTickets = document.getElementById('maxTickets').value.trim();
    const image = document.getElementById('eventImage').value.trim();
    const errorDiv = document.getElementById('eventAddError');

    if (!name || !date || !location || !price || !maxTickets || !image) {
        errorDiv.textContent = "All fields are required.";
        return false;
    }

    if (isNaN(price) || price <= 0) {
        errorDiv.textContent = "Ticket price must be a positive number.";
        return false;
    }

    if (!Number.isInteger(Number(maxTickets)) || maxTickets <= 0) {
        errorDiv.textContent = "Maximum tickets must be a positive integer.";
        return false;
    }

    errorDiv.textContent = ""; // Clear error
    return true;
}




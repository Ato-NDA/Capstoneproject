// Handle camera rental
function rentCamera(cameraId) {
    // Check if user is logged in
    const isLoggedIn = checkLoginStatus();
    
    if (!isLoggedIn) {
        window.location.href = 'login.php';
        return;
    }
    
    // Redirect to reservation page with camera ID
    window.location.href = `reservation.php?camera_id=${cameraId}`;
}

// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category');
    const priceSelect = document.getElementById('price');
    const availabilitySelect = document.getElementById('availability');

    const filters = [categorySelect, priceSelect, availabilitySelect];
    
    filters.forEach(filter => {
        filter.addEventListener('change', applyFilters);
    });
});

function applyFilters() {
    const category = document.getElementById('category').value;
    const price = document.getElementById('price').value;
    const availability = document.getElementById('availability').value;

    // Send AJAX request to filter results
    fetch(`api/filter-cameras.php?category=${category}&price=${price}&availability=${availability}`)
        .then(response => response.json())
        .then(data => updateCameraGrid(data))
        .catch(error => console.error('Error:', error));
}

function updateCameraGrid(cameras) {
    const grid = document.querySelector('.camera-grid');
    grid.innerHTML = '';

    cameras.forEach(camera => {
        const card = createCameraCard(camera);
        grid.appendChild(card);
    });
}

function createCameraCard(camera) {
    const card = document.createElement('div');
    card.className = 'camera-card';
    card.innerHTML = `
        <img src="${camera.image}" alt="${camera.title}">
        <div class="camera-info">
            <h3>${camera.title}</h3>
            <p class="price">$${camera.price}/day</p>
            <button class="rent-button" onclick="rentCamera(${camera.id})">Rent Now</button>
        </div>
    `;
    return card;
}

// Check login status
function checkLoginStatus() {
    // This would typically check a session or token
    // For now, we'll return false to always redirect to login
    return false;
}

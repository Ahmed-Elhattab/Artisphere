const toggleBtn = document.getElementById('sidebarToggle');
const sidebar = document.getElementById('sidebar');

toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('open');
});


//  Live search filter
const searchInput = document.getElementById('productSearch');
const productCards = document.querySelectorAll('.product-card');

if (searchInput) {
    searchInput.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase().trim();

        productCards.forEach((card) => {
        const nameElement = card.querySelector('.product-name');
        const productName = nameElement ? nameElement.textContent.toLowerCase() : '';

        // Show card if query is empty OR name includes query
        const match = query === '' || productName.includes(query);

        card.style.display = match ? '' : 'none';
        });
    });
}
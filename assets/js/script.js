// Open search overlay
document.getElementById('searchBtn').addEventListener('click', function(e) {
    e.preventDefault();
    const overlay = document.getElementById('searchOverlay');
    overlay.style.display = 'flex';
    setTimeout(() => overlay.classList.add('active'), 10);
    document.getElementById('searchInput').focus();
});

// Close overlay
function closeSearch() {
    const overlay = document.getElementById('searchOverlay');
    overlay.classList.remove('active');
    setTimeout(() => { overlay.style.display = 'none'; }, 400);
}

document.getElementById('closeSearch').addEventListener('click', closeSearch);

// Close when clicking outside input
document.getElementById('searchOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeSearch();
});

// Close with ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeSearch();
});

// MAIN: Press Enter → Go to search page
document.getElementById('searchInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        const query = this.value.trim();
        if (query !== '') {
            // Go to search results page
            window.location.href = 'search.php?q=' + encodeURIComponent(query);
        }
    }
});
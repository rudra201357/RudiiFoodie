// --- Setup ---
const hamburger = document.getElementById('hamburger');
const mobileMenu = document.getElementById('mobile-menu');

// --- Functions to Open/Close ---

function openMenu() {
    mobileMenu.classList.add('mobile-menu-active');
    document.body.classList.add('blur-background');
}

function closeMenu() {
    mobileMenu.classList.remove('mobile-menu-active');
    document.body.classList.remove('blur-background');
}

// --- Event Listeners ---

// 1. Toggle the menu on hamburger click
hamburger.addEventListener('click', function (e) {
    e.preventDefault(); 
    e.stopPropagation(); // Stop this click from triggering the 'document' listener

    // Check if menu is already open
    const isOpen = mobileMenu.classList.contains('mobile-menu-active');

    if (isOpen) {
        closeMenu();
    } else {
        openMenu();
    }
});

// 2. Close menu when clicking a link *inside* it
const menuLinks = mobileMenu.querySelectorAll('a');

menuLinks.forEach(function(link) {
    link.addEventListener('click', function() {
        // We add a small delay to let the page navigation start
        // before the menu disappears, especially for anchor links.
        setTimeout(() => {
            closeMenu();
        }, 100); 
    });
});

// 3. Close menu when clicking *outside* of it
document.addEventListener('click', function (e) {
    // Check if the menu is open
    const isMenuOpen = mobileMenu.classList.contains('mobile-menu-active');
    
    // Check if the click was *inside* the menu
    const isClickInsideMenu = mobileMenu.contains(e.target);
    
    // Check if the click was on the hamburger itself
    const isClickOnHamburger = hamburger.contains(e.target);

    if (isMenuOpen && !isClickInsideMenu && !isClickOnHamburger) {
        closeMenu();
    }
});
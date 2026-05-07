
document.addEventListener("DOMContentLoaded", function () {
    let cartCount = 0;
    const cartValue = document.querySelector(".cart-value");
    const addButtons = document.querySelectorAll(".add-btn");

    // Store added items (optional)
    const cartItems = [];

    addButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Find the parent .menu-item div
            const itemDiv = button.closest(".menu-item");

            // Get item details
            const itemName = itemDiv.querySelector("h4").textContent.trim();
            const itemPrice = itemDiv.querySelector(".price").textContent.trim();
            const itemImage = itemDiv.querySelector("img").getAttribute("src");

            // Store the item (you can later display this in a cart page or modal)
            cartItems.push({
                name: itemName,
                price: itemPrice,
                image: itemImage
            });

            // Increment count
            cartCount++;
            cartValue.textContent = cartCount;

            // Optional: visual feedback
            button.textContent = "Added ✓";
            button.disabled = true;
            // Fly image animation toward cart
            const cartIcon = document.querySelector('.cart-icon');
            const itemImageElement = itemDiv.querySelector('img');
            if (cartIcon && itemImageElement) {
                const imageRect = itemImageElement.getBoundingClientRect();
                const cartRect = cartIcon.getBoundingClientRect();
                const flyingImage = itemImageElement.cloneNode(true);

                flyingImage.classList.add('fly-to-cart');
                flyingImage.style.top = `${imageRect.top}px`;
                flyingImage.style.left = `${imageRect.left}px`;
                flyingImage.style.width = `${imageRect.width}px`;
                flyingImage.style.height = `${imageRect.height}px`;

                document.body.appendChild(flyingImage);

                const destinationX = cartRect.left + cartRect.width / 2 - imageRect.left - imageRect.width / 2;
                const destinationY = cartRect.top + cartRect.height / 2 - imageRect.top - imageRect.height / 2;

                requestAnimationFrame(() => {
                    flyingImage.style.transform = `translate(${destinationX}px, ${destinationY}px) scale(0.2)`;
                    flyingImage.style.opacity = '0.2';
                });

                flyingImage.addEventListener('transitionend', () => {
                    flyingImage.remove();
                    cartIcon.classList.add('cart-pulse');
                    setTimeout(() => cartIcon.classList.remove('cart-pulse'), 300);
                }, { once: true });
            }
            console.log(cartItems); // for testing
        });
    });
});

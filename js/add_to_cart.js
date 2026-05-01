
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

            console.log(cartItems); // for testing
        });
    });
});

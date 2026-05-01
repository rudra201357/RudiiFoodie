document.addEventListener("DOMContentLoaded", function () {
    const menuContainer = document.getElementById("menu-container");
    const sortSelect = document.getElementById("sort-options");

    const option1 = document.getElementById("option1"); // Delivery
    const option2 = document.getElementById("option2"); // Cafe
    const filterSwitch = document.getElementById("firstFilter");
    const fastDelivery = document.getElementById("service-description");
    const menuOption = document.getElementById("menu-options");
    const REFRESH_INTERVAL = 3000; // 10 seconds

    // Create tooltip element once
    const tooltip = document.createElement("div");
    tooltip.className = "unavailable-tooltip";
    tooltip.textContent = "This item is not deliverable or unavailable right now";
    tooltip.style.position = "fixed";
    tooltip.style.pointerEvents = "none";
    tooltip.style.transition = "opacity .12s ease";
    tooltip.style.opacity = 0;
    document.body.appendChild(tooltip);

    function attachTooltip() {
        
        const items = menuContainer.querySelectorAll(".menu-item.unavailable");

        items.forEach(item => {
           
            item.onmousemove = e => {
                
                const offsetX = 12;
                const offsetY = 18;
                tooltip.style.left = (e.clientX + offsetX) + "px";
                tooltip.style.top = (e.clientY + offsetY) + "px";
                tooltip.style.opacity = 1;
            };
            item.onmouseleave = () => {
                tooltip.style.opacity = 0;
            };
        });
    }

    // Helper: read current order mode from radios
    function getOrderMode() {
        return option2 && option2.checked ? "cafe" : "delivery";
    }

    function loadMenu() {
        const currentSortValue = sortSelect ? sortSelect.value : "";
        const orderMode = getOrderMode();
        if(orderMode == "cafe"){
            fastDelivery.innerText= "Your Order will be ready within minutes";
            menuOption.innerText = "You are in Cafe mode. Order and sit in your preferred table.";
        }
        else{
            fastDelivery.innerText="We will reach you within 15 minutes";
            menuOption.innerText="You are in Delivery mode. Offers and Drinks items are not avaiable here."
        }
        fetch("fetch_menu.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body:
                "sort=" + encodeURIComponent(currentSortValue) +
                "&order_mode=" + encodeURIComponent(orderMode),
        })
        .then(response => response.text())
        .then(html => {
            menuContainer.innerHTML = html;
            // Re-attach tooltip after rendering
            attachTooltip();
        })
        .catch(error => {
            console.error("Error fetching menu:", error);
            menuContainer.innerHTML = "<p>Error loading menu.</p>";
        });
    }

    // Initial load
    loadMenu();

    // Auto-refresh every 10s
    const intervalID = setInterval(() => loadMenu(), REFRESH_INTERVAL);

    // Sort change listener
    if (sortSelect) {
        sortSelect.addEventListener("change", function () {
            loadMenu();
        });
    }

    // Radio change listeners — update menu whenever selection changes
    if (option1 && option2) {
        [option1, option2].forEach(radio => {
            radio.addEventListener("change", function () {
                loadMenu();
            });
        });

        // Also support clicks on the visual switch container (optional)
        if (filterSwitch) {
            filterSwitch.addEventListener("click", function (e) {
                // if label clicked, radio state changes automatically; just reload
                // small timeout ensures radio checked state is updated
                setTimeout(loadMenu, 0);
            });
        }
    } else {
        // Fallback: if radios are missing, log a warning (so you can debug)
        console.warn("Radio inputs #option1 / #option2 not found. Menu toggle will default to 'delivery'.");
    }

    // Clear interval on unload
    window.addEventListener("beforeunload", function () {
        clearInterval(intervalID);
    });
});

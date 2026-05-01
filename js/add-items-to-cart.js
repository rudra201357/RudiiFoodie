
document.addEventListener('DOMContentLoaded', () => {
    const cartItemsContainer = document.getElementById('cartItems');
    const cartValue = document.querySelector('.cart-value');
    const cartTotal = document.getElementById('cartTotal');
    const cartFooter = document.querySelector('.cart-footer');
    const emptyCart = document.getElementById('empty-cart');

    // Mode radio buttons
    const deliveryRadio = document.getElementById('option1'); // Delivery
    const cafeRadio = document.getElementById('option2');     // Cafe

    const MODE_DELIVERY = 'delivery';
    const MODE_CAFE = 'cafe';
    const STORAGE_SELECTED_MODE = 'selectedMode';
    const STORAGE_CART_PREFIX = 'cart_'; // store as cart_delivery, cart_cafe

    // Read saved mode or default to delivery
    let currentMode = localStorage.getItem(STORAGE_SELECTED_MODE) || MODE_DELIVERY;
    applyModeToRadios();

    // Load cart for current mode
    let cart = loadCartForMode(currentMode);

    // Initial UI
    showMenuItemsForMode(currentMode);
    updateCartUI();

    // Mode change listeners (radio inputs)
    if (deliveryRadio) deliveryRadio.addEventListener('change', () => switchMode(MODE_DELIVERY));
    if (cafeRadio)     cafeRadio.addEventListener('change', () => switchMode(MODE_CAFE));

    // Event delegation for add/increase/decrease buttons
    document.body.addEventListener('click', function (e) {

        // ADD BUTTON (menu add)
        if (e.target.classList.contains('add-btn')) {
            const button = e.target;
            if (button.hasAttribute('disabled')) return;

            const menuItem = button.closest('.menu-item');
            if (!menuItem) return;

            const itemIdRaw = button.getAttribute('data-id');
            const nameEl = menuItem.querySelector('h4');
            const priceEl = menuItem.querySelector('.price');
            const imageEl = menuItem.querySelector('img');

            if (!itemIdRaw) {
                console.error("❌ data-id missing on add button");
                return;
            }

            const itemName = nameEl ? nameEl.textContent.trim() : 'Unnamed';
            const priceText = priceEl ? priceEl.textContent.replace(/[^\d.,]/g, '').replace(',', '.') : '0';
            const itemPrice = parseFloat(priceText) || 0;

            // Storage key unique per mode to avoid cross-mode collisions
            const storageKey = storageKeyFor(currentMode, itemIdRaw);

            if (!cart[storageKey]) {
                cart[storageKey] = {
                    id: itemIdRaw,              // raw item id (not prefixed)
                    storageKey,                // internal key
                    name: itemName,
                    price: itemPrice,
                    image: imageEl ? imageEl.src : '',
                    quantity: 1
                };
            } else {
                cart[storageKey].quantity += 1;
            }

            saveCartForMode(currentMode, cart);
            updateCartUI();
        }

        // INCREASE (cart UI)
        if (e.target.classList.contains('increase')) {
            const key = e.target.getAttribute('data-id');
            if (!key || !cart[key]) return;
            cart[key].quantity++;
            saveCartForMode(currentMode, cart);
            updateCartUI();
        }

        // DECREASE (cart UI)
        if (e.target.classList.contains('decrease')) {
            const key = e.target.getAttribute('data-id');
            if (!key || !cart[key]) return;
            cart[key].quantity--;
            if (cart[key].quantity <= 0) delete cart[key];
            saveCartForMode(currentMode, cart);
            updateCartUI();
        }
    });

    // ---- Functions ----

    function switchMode(mode) {
        if (mode !== MODE_DELIVERY && mode !== MODE_CAFE) return;
        if (currentMode === mode) return;
        currentMode = mode;
        localStorage.setItem(STORAGE_SELECTED_MODE, currentMode);
        cart = loadCartForMode(currentMode);
        showMenuItemsForMode(currentMode);
        updateCartUI();
        applyModeToRadios();
    }

    function applyModeToRadios() {
        if (deliveryRadio) deliveryRadio.checked = (currentMode === MODE_DELIVERY);
        if (cafeRadio) cafeRadio.checked = (currentMode === MODE_CAFE);
    }

    // Show/hide menu items. Menu items should have attribute data-mode="delivery"|"cafe"|"both"
    function showMenuItemsForMode(mode) {
        const menuItems = document.querySelectorAll('.menu-item');
        menuItems.forEach(mi => {
            const modes = (mi.getAttribute('data-mode') || 'delivery').split(',').map(s => s.trim().toLowerCase());
            const visible = modes.includes(mode) || modes.includes('both');
            mi.style.display = visible ? '' : 'none';
        });
    }

    function storageKeyFor(mode, itemIdRaw) {
        return `${mode}|${itemIdRaw}`;
    }

    function storageKeyToParts(storageKey) {
        // returns {mode, id} if formatted as mode|id, otherwise {mode: currentMode, id: storageKey}
        if (!storageKey) return { mode: currentMode, id: storageKey };
        const idx = storageKey.indexOf('|');
        if (idx === -1) return { mode: currentMode, id: storageKey };
        return { mode: storageKey.slice(0, idx), id: storageKey.slice(idx + 1) };
    }

    function loadCartForMode(mode) {
        const key = STORAGE_CART_PREFIX + mode;
        try {
            const raw = localStorage.getItem(key);
            return raw ? JSON.parse(raw) : {};
        } catch (err) {
            console.warn('Invalid cart in localStorage for', mode, ' - resetting.', err);
            return {};
        }
    }

    function saveCartForMode(mode, cartObj) {
        const key = STORAGE_CART_PREFIX + mode;
        try {
            localStorage.setItem(key, JSON.stringify(cartObj));
        } catch (err) {
            console.warn('Could not save cart for mode', mode, err);
        }
    }

    // Update Cart UI + Save to LocalStorage (cart is always the in-memory cart for currentMode)
    function updateCartUI() {
        if (cartItemsContainer) cartItemsContainer.innerHTML = '';

        let total = 0;
        let count = 0;

        for (let storageKey in cart) {
            if (!Object.prototype.hasOwnProperty.call(cart, storageKey)) continue;
            const item = cart[storageKey];
            const qty = item.quantity || 0;
            total += (item.price || 0) * qty;
            count += qty;

            const div = document.createElement('div');
            div.classList.add('cart-item');

            // Use storageKey for increase/decrease buttons so the handlers can find the right entry
            div.innerHTML = `
                ${item.image ? `<img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.name)}">` : ''}
                <div class="cart-item-info">
                    <h5>${escapeHtml(item.name)}</h5>
                    <span>₹${(item.price || 0).toFixed(2)}</span>
                </div>
                <div class="quantity" >
                    <button data-id="${storageKey}" class="decrease">-</button>
                    <span>${qty}</span>
                    <button data-id="${storageKey}" class="increase">+</button>
                </div>
            `;

            if (cartItemsContainer) cartItemsContainer.appendChild(div);
        }

        // Update counters & totals
        if (cartValue) cartValue.textContent = count;
        if (cartTotal) cartTotal.textContent = total.toFixed(2);
        const isEmpty = count === 0;

        if (cartFooter) {
            cartFooter.style.display = isEmpty ? 'none' : '';
        }
        if (emptyCart) {
            emptyCart.style.display = isEmpty ? '' : 'none';
        }

        // Persist current cart
        saveCartForMode(currentMode, cart);
    }

    // Escape helper
    function escapeHtml(str) {
        return str ? str.replace(/[&<>"']/g, m => ( {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        }[m])) : '';
    }
});


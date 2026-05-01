const cartIcon = document.getElementById('cartIcon');
const cartPanel = document.getElementById('cartPanel');
const closeCart = document.getElementById('closeCart');

const allQtyDivs = document.querySelectorAll('.quantity');

cartIcon.addEventListener('click', (e) => {
    e.preventDefault();
    cartPanel.classList.add('active');
});


closeCart.addEventListener('click', () => {
    cartPanel.classList.remove('active');
});


document.addEventListener('click', function (e) {
     if (!cartPanel.classList.contains('active')) return;

    if (cartPanel.contains(e.target) || cartIcon.contains(e.target) ) return;

      if (e.target.closest('.quantity'))  return;
    cartPanel.classList.remove('active');
});

document.addEventListener('keydown', (e) => {
    if (e.key === "Escape") {
        cartPanel.classList.remove('active');
    }
});

document.getElementById('checkoutBtn').addEventListener('click', () => {
  const mode = localStorage.getItem('selectedMode') || 'delivery';
  const cart = localStorage.getItem('cart_' + mode) || '{}';
  document.getElementById('form_mode').value = mode;
  document.getElementById('form_cart').value = cart;

  // if you have logged-in user id in JS, set it here:
  // document.getElementById('form_user_id').value = window.LOGGED_IN_USER_ID || '';

  document.getElementById('checkoutForm').submit();
});

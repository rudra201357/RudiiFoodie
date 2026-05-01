

// -------------------- AMOUNT --------------------
function setAmount(val) {
   let num= document.getElementById('rechargeAmount').value = val.toFixed(2);
    updatePayButton(num);
}

const amountInput = document.getElementById('rechargeAmount');
amountInput.addEventListener('input', function () {

    let value = this.value.replace(/[^0-9.]/g, '');

    let parts = value.split('.');
    if (parts.length > 2) {
        value = parts[0] + '.' + parts[1];
    }

    if (parts[1]) {
        parts[1] = parts[1].substring(0, 2);
        value = parts[0] + '.' + parts[1];
    }

    let num = parseFloat(value);

    // ✅ Force limit BEFORE updating anything
    if (!isNaN(num) && num > 5000) {
        num = 5000;
        value = "5000";
    }

    this.value = value;

    // ✅ Pass CLEAN value directly
    updatePayButton(num);
});

amountInput.addEventListener('keypress', function (e) {
    if (this.value.length >= 7) { // e.g. 5000.00
        e.preventDefault();
    }
});

function updatePayButton(amount) {
    let num = amount || 0;
    document.getElementById('cardPayBtn').innerText =`Add ₹${num.toFixed(2)} to Wallet`;
}

// -------------------- FORM SWITCH --------------------
function showForm(el, formId) {
    document.querySelectorAll('.method-item').forEach(m => m.classList.remove('active'));
    document.querySelectorAll('.form-section').forEach(f => f.classList.remove('active'));

    // Reset errors + fields
    document.getElementById("err-msg").style.display = 'none';
    document.getElementById('cardNumber').value = "";

    el.classList.add('active');
    document.getElementById(formId).classList.add('active');
}

// -------------------- CARD FORMAT --------------------
const cardInput = document.getElementById('cardNumber');
let cardType = "UNKNOWN";

cardInput.addEventListener('input', function () {
    let value = this.value.replace(/\D/g, '').substring(0,16);

    let formatted = value.match(/.{1,4}/g);
    this.value = formatted ? formatted.join(' ') : '';

    let badge = document.getElementById('cardBadge');

    if (value.startsWith('4')) {
        badge.innerText = "VISA";
        cardType = "VISA";
    }
    else if (/^5[1-5]/.test(value)) {
        badge.innerText = "MASTERCARD";
        cardType = "MASTERCARD";
    }
    else if (value.startsWith('6')) {
        badge.innerText = "RUPAY";
        cardType = "RUPAY";
    }
    else {
        badge.innerText = "";
        cardType = "UNKNOWN";
    }
});

// -------------------- CVV --------------------
document.getElementById('cvv').addEventListener('input', function () {
    this.value = this.value.replace(/\D/g, '');
});

// -------------------- EXPIRY --------------------
const expiryInput = document.getElementById('expdate');

expiryInput.addEventListener('input', function () {
    let value = this.value.replace(/\D/g, '').substring(0,4);

    let month = value.substring(0,2);
    let year = value.substring(2,4);

    // Month validation
    if (month.length === 1 && !['0','1'].includes(month)) value = '';

    if (month.length === 2) {
        let m1 = month[0], m2 = month[1];

        if (!((m1 === '0' && m2 >= '1' && m2 <= '9') ||
              (m1 === '1' && m2 >= '0' && m2 <= '2'))) {
            value = value.substring(0,1);
        }
    }

    // Year validation
    if (year.length === 2 && parseInt(year) < 26) {
        alert("Year must be 26 or later");
        value = month;
    }

    this.value = value.length > 2 
        ? value.substring(0,2) + '/' + value.substring(2)
        : value;
});

// -------------------- PAYMENT HANDLERS --------------------
function handlePayment(e) {
    const btn = e.target;
    const amount = document.getElementById('rechargeAmount').value;

    sendPayment({
        cardNumber: null,
        expdate: null,
        cvv: null,
        amount,
        cardType: null,
        mode: "UPI"
    }, btn);
}

function handlePaymentCard(e) {

    const btn = e.target;

    let card = document.getElementById('cardNumber').value;
    let exp = document.getElementById('expdate').value;
    let cvv = document.getElementById('cvv').value;
    let amount = document.getElementById('rechargeAmount').value;

    let cardNumber = card.replace(/\s+/g, "");
    let expdate = exp.replace(/\//g, "");

    if (cardNumber.length === 16 && expdate.length === 4 && cvv.length === 3) {

        sendPayment({
            cardNumber,
            expdate,
            cvv,
            amount,
            cardType,
            mode: "CARD"
        }, btn);

    } else {
        document.getElementById('err-msg').style.display = 'block';
    }
}

// -------------------- API CALL --------------------
function sendPayment(data, btn) {

    btn.disabled = true;
    btn.innerText = "Processing...";
    btn.style.background = "#9ca3af";

    fetch('recharge.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(res => {
        console.log(res);

        if (res.success) {
            alert(res.message || "Payment Successful");
            window.location.reload();
            window.location.href = "../index.php";
        } else {
            alert(res.error || "Payment Failed");
            resetBtn(btn);
        }
    })
    .catch(err => {
        console.error(err);
        alert("Server error");
        resetBtn(btn);
    });
}

// -------------------- RESET BUTTON --------------------
function resetBtn(btn) {
    btn.disabled = false;
    btn.innerText = "Pay Now";
    btn.style.background = "#6366f1";
}


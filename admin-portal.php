<?php 
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin'){
   die("Unauthorized Access");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Portal</title>
  <link rel="icon" type="image/png" href="images/logo.png">
  <link rel="stylesheet" href="css/admin-portal.css">
 <style>
    .row-list {
      display: flex;
      flex-direction: column;
      width: 100%;
      gap: 8px;
    }

    .item-row {
      display: grid;
      grid-template-columns: 700px 120px 140px 150px;
      /* name | price | availability */
      align-items: center;
      gap: 10px;
      padding: 12px 15px;
      border-radius: 6px;
      border: 1px solid #ddd;
      font-family: Arial, sans-serif;
    }

    .form-section {
      display: flex;
      justify-content: space-around;
      align-items: center;
    }

    .form-section input {
      padding: 10px;
      border: 1px solid #ccc;
      margin: 5px;
    }

    #save-btn {
      width: 9rem;
      border-radius: 10px;
    }

    #save-btn:hover {
      background: #ff9500;
      border-radius: 15px;
    }

    .item-row h4 {
      margin: 0;
      font-size: 18px;
      text-transform: capitalize;
    }

    .item-row p {
      margin: 0;
      font-size: 16px;
    }

    .item-row button {
      height: 35px;
      border-radius: 10px;
      background: #f2bd12;

    }

    .header-option {
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%;
    }

    .header-option button {
      height: 55px;
      background: #f2bd12;
      border-radius: 10px;
      width: 10%;
    }

    .modal-backdrop {
      position: fixed;
      inset: 0;
      display: none;
      background: rgba(0, 0, 0, 0.45);
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }

    .modal {
      background: #fff;
      width: 100%;
      max-width: 700px;
      border-radius: 10px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      padding: 18px;
      max-height: 90vh;
      overflow: auto;
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 8px;
    }

    .modal-body {
      display: grid;
      gap: 12px;
      grid-template-columns: 1fr 1fr;
    }

    .modal-body .full {
      grid-column: 1 / -1;
    }

    .form-row {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    input[type="text"],
    input[type="number"],
    select,
    textarea {
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }

    textarea {
      min-height: 90px;
      resize: vertical;
    }

    .modal-footer {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 12px;
    }

    /* Responsive: stack fields on small screens */
    @media (max-width:600px) {
      .modal-body {
        grid-template-columns: 1fr;
      }
    }

    .button {
      background: #2d8cff;
      color: #fff;
      border: none;
      padding: 8px 14px;
      border-radius: 8px;
      cursor: pointer;
    }

    .button.secondary {
      background: #f2f2f2;
      color: #111;
    }

    .small-muted {
      font-size: 12px;
      color: #666;
    }



    #add-item:hover {
      background: #ff9500;
      border-radius: 15px;
    }

    .update-btn:hover {
      background: #ff9500;
      border-radius: 15px;
    }

    .sorting {
      display: flex;
      gap: 20px;
      align-items: center;
      padding: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      background-color: #f9f9f9;
      font-family: Arial, sans-serif;
      max-width: fit-content;
    }


    .sorting label {
      font-weight: bold;
      color: #333;
      margin-right: 5px;
    }


    .sorting select {
      padding: 8px 12px;
      border: 1px solid #aaa;
      border-radius: 4px;
      background-color: #fff;
      cursor: pointer;
      font-size: 16px;
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      background-repeat: no-repeat;
      background-position: right 10px center;
      min-width: 120px;
    }


    .sorting select:hover {
      border-color: #555;
    }

    .sorting select:focus {
      border-color: #007bff;
      outline: none;
      box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
    }

    .sorting option {
      padding: 5px;
    }

    #category-select option {
      text-transform: capitalize;
    }

    select {
      text-transform: capitalize;
    }

    .secondary:hover {
      background: #d3c080cb;
    }

    .save-btn:hover {
      background: #ff9500;
    }

    table{
      width: 100%;
      text-align: center;
    }
    table #status{
      text-transform: capitalize;
    }

    .logout-btn {
      padding: 10px 18px;
      background-color: #ff6b6b;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
      transition: background-color 0.3s ease;
      cursor: pointer;
    }

    .logout-btn:hover {
      background-color: #ff5252;
    }
  </style>
</head>

<body>
  <script>

    function checkDeviceSize() {
      if (window.innerWidth <= 768) {
        return "mobile";
      }
      else return "desktop";
    }
    checkDeviceSize();

    console.log("Detected:", checkDeviceSize());
    if (checkDeviceSize() == "mobile") {
      alert("You are using " + checkDeviceSize() + ". Please use desktop.");
      window.location.href = "admin-portal.html";
    }

  </script>
  <header>
    <nav class="navbar flex between wrapper">
      <a href="#" class="logo">
        <img src="images/logo.png" alt="RudiiFoodie logo">
        <span>RudiiFoodie</span>
      </a>

      <div class="switch-toggle switch-3 switch-candy">
        <input id="on" name="state-d" type="radio" value="order" onclick="toggleRoleFields()" />
        <label for="on">Check <br>Orders</label>

        <input id="na" name="state-d" type="radio" value="item" checked onclick="toggleRoleFields()" />
        <label for="na">Items Management</label>

        <input id="off" name="state-d" type="radio" value="history" onclick="toggleRoleFields()" />
        <label for="off">Order <br>History</label>
      </div>
      <a href="logout.php" class="logout-btn">Logout</a>
    </nav>
  </header>
  <hr>
  <section id="check-orders">Rudra</section>
  <section id="items-management">
    <div class="header-option">
      <div class="sorting">
        <label for="sort-options" id="sort-options-label">Sort By: </label>
        <select id="sort-options" name="sort-by">
          <option value="name" selected>Name</option>
          <option value="price">Price</option>
          <option value="quantity">Quantity</option>
        </select>
        <label for="view-options" id="view-lebel">Order: </label>
        <select id="view-options" name="view-by">
          <option value="asc" selected>Ascending</option>
          <option value="desc">Descending</option>
        </select>

      </div>
      <button id="add-item">Add Item</button>
      <div id="modal-backdrop" class="modal-backdrop" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
          <div class="modal-header">
            <h3 id="modal-title">Add New Menu Item</h3>
            <button id="modal-close" class="button secondary">&times;</button>
          </div>

          <form id="add-item-form" enctype="multipart/form-data" method="post" action="add_item.php">
            <div class="modal-body">
              <div class="form-row">
                <label>Item Name</label>
                <input type="text" name="item_name" required maxlength="255" />
              </div>

              <div class="form-row">
                <label>Price (₹)</label>
                <input type="number" name="price" step="1" min="0" required />
              </div>

              <div class="form-row">
                <label>Quantity</label>
                <input type="number" name="quantity" min="0" required />
              </div>

              <div class="form-row">
                <label>Category</label>
                <select name="category_id" id="category-select" required>
                  <option value="">Loading categories...</option>
                </select>
                <div class="small-muted">Categories loaded from database</div>
              </div>

              <div class="form-row full">
                <label>Description</label>
                <textarea name="description" maxlength="2000"></textarea>
              </div>

              <div class="form-row">
                <label>Image</label>
                <input type="file" name="image" accept="image/*" />
                <div class="small-muted">Optional. Max 2MB recommended.</div>
              </div>

              <div class="form-row">
                <label>In Stock</label>
                <select name="in_stock" required>
                  <option value="1" selected>Yes</option>
                  <option value="0">No</option>
                </select>
              </div>

            </div>

            <div class="modal-footer">
              <button type="button" id="cancel-btn" class="button secondary">Cancel</button>
              <button type="submit" class="button save-btn">Save Item</button>
            </div>
          </form>

        </div>
      </div>
    </div>
    <p id="message"></p>
    <hr>
    <div id="items"></div>

  </section>

 

  <section id="order-history">

  </section>

  <script>
    const checkOrders = document.getElementById("check-orders");
    const itemsManagement = document.getElementById("items-management");
    const orderHistory = document.getElementById("order-history");

    const sortby = document.getElementById("sort-options");
    const viewby = document.getElementById("view-options");

    // loadMenu uses the CURRENT .value of selects
    function loadMenu() {
      const payload = "sort=" + encodeURIComponent(sortby.value) +
        "&view_mode=" + encodeURIComponent(viewby.value);

      console.log("Sending payload:", payload);

      fetch("fetch_items_admin.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: payload,
      })
        .then(res => res.text())
        .then(data => {
          document.getElementById("items").innerHTML = data;
        })
        .catch(err => console.error(err));
    }

    function toggleRoleFields() {
      const selectedRole = document.querySelector('input[name="state-d"]:checked')?.value;

      if (selectedRole === 'order') {
        checkOrders.style.display = 'block';
        itemsManagement.style.display = 'none';
        orderHistory.style.display = 'none';
      } else if (selectedRole === 'item') {
        itemsManagement.style.display = 'block';
        orderHistory.style.display = 'none';
        checkOrders.style.display = 'none';

        // Load items once
        loadMenu();

        // Add listeners (only once) — guard to avoid duplicate listeners
        if (!sortby._hasListener) {
          sortby.addEventListener("change", loadMenu);
          sortby._hasListener = true;
        }
        if (!viewby._hasListener) {
          viewby.addEventListener("change", loadMenu);
          viewby._hasListener = true;
        }

      } else if (selectedRole === 'history') {
        orderHistory.style.display = 'block';
        checkOrders.style.display = 'none';
        itemsManagement.style.display = 'none';
      }
    }

    // run on load so default checked radio shows correct section
    window.addEventListener("load", toggleRoleFields);

    // also update layout when radio changes
    document.querySelectorAll('input[name="state-d"]').forEach(r => {
      r.addEventListener("change", toggleRoleFields);
    });




    function showUpdateSection(code) {
      let box = document.getElementById("box_" + code);
      let updateBtn = document.getElementById("btn_" + code);
      if (box.style.display === "none") {
        box.style.display = "block";
        updateBtn.innerText = "Cancel Update";
      } else {
        box.style.display = "none";
        updateBtn.innerText = "Update";
      }
    }




    const params = new URLSearchParams(window.location.search);
    const msg = params.get('msg');
    const messageEl = document.getElementById('message');

    if (msg === 'success') {
      messageEl.innerText = 'Item updated successfully.';
      messageEl.style.color = 'green';
    } else if (msg === 'invalid_price') {
      messageEl.innerText = 'Invalid price!';
      messageEl.style.color = 'red';
    } else if (msg === 'invalid_qty') {
      messageEl.innerText = 'Invalid quantity!';
      messageEl.style.color = 'red';
    } else if (msg === 'invalid_code') {
      messageEl.innerText = 'Invalid item code!';
      messageEl.style.color = 'red';
    } else if (msg === 'no_change') {
      messageEl.innerText = 'No rows changed.';
      messageEl.style.color = 'orange';
    } else if (msg === 'db_error') {
      const err = decodeURIComponent(params.get('err') || '');
      messageEl.innerText = 'DB error: ' + err;
      messageEl.style.color = 'red';
    }

    setTimeout(() => {
      document.getElementById("message").innerText = "";
    }, 5000);



    const addItemBtn = document.getElementById('add-item');
    const backdrop = document.getElementById('modal-backdrop');
    const closeBtn = document.getElementById('modal-close');
    const cancelBtn = document.getElementById('cancel-btn');
    const categorySelect = document.getElementById('category-select');

    function openModal() {
      backdrop.style.display = 'flex';
      backdrop.setAttribute('aria-hidden', 'false');
      loadCategories();
    }
    function closeModal() {
      backdrop.style.display = 'none';
      backdrop.setAttribute('aria-hidden', 'true');
      // reset form if desired
      document.getElementById('add-item-form').reset();
      categorySelect.innerHTML = '<option value="">Loading categories...</option>';
    }

    addItemBtn.addEventListener('click', openModal);
    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    backdrop.addEventListener('click', (e) => { if (e.target === backdrop) closeModal(); });

   

async function loadCategories() {
  try {
    const res = await fetch('get_categories.php');
    const text = await res.text();
    console.log('raw response from get_categories.php:\n', text);

    let json;
    try {
      json = JSON.parse(text);    // try to parse whatever came
    } catch (e) {
      console.error('Invalid JSON from get_categories.php', e);
      categorySelect.innerHTML = '<option value="">Unable to load</option>';
      return;
    }

    categorySelect.innerHTML = '';

    if (Array.isArray(json) && json.length) {
      const placeholder = document.createElement('option');
      placeholder.value = '';
      placeholder.textContent = 'Select category';
      categorySelect.appendChild(placeholder);

      json.forEach(cat => {
        const opt = document.createElement('option');
        // make sure these match your PHP keys, e.g. cat.category_id, cat.category_name
        opt.value = cat.category_id;       
        opt.textContent = cat.name;
        categorySelect.appendChild(opt);
      });
    } else {
      categorySelect.innerHTML = '<option value="">No categories found</option>';
    }
  } catch (err) {
    categorySelect.innerHTML = '<option value="">Unable to load</option>';
    console.error('Failed to load categories', err);
  }
}

    
document.getElementById('add-item-form').addEventListener('submit', async function (e) {
  e.preventDefault();
  const form = e.currentTarget;
  const data = new FormData(form);

  try {
    const res = await fetch(form.action, {
      method: 'POST',
      body: data
    });

    const text = await res.text();   // plain text response
    console.log("SERVER RESPONSE:", text);

    if (text.trim() === "OK") {
      alert('Item added successfully');
      closeModal();
      location.reload();
    } else {
      alert('Error: ' + text);
    }

  } catch (err) {
    alert('Request failed: ' + err.message);
    console.error(err);
  }
});






    // Load Order  History
    function loadHistory(){
        fetch("order_history.php")
        .then(res => res.text())
        .then(data => {
          document.getElementById("order-history").innerHTML = data;
        })
        .catch(err => console.error(err));
       
    }
    loadHistory();
   setInterval( loadHistory,5000);
  </script>
  <script>
    function loadOrder(){
        fetch("check_order.php")
        .then(res => res.text())
        .then(data => {
          document.getElementById("check-orders").innerHTML = data;
        })
        .catch(err => console.error(err));
      
    }
 loadOrder();
    setInterval(loadOrder, 5000);
  </script>
  <script>
window.acceptOrder = function(orderId, btn){
    fetch("accept_order_admin.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "order_id=" + orderId + "&action=accept"
    })
    .then(res => res.text())
    .then(data => {

        if(data.trim() === "success"){
            btn.innerText = "Done";
            btn.classList.add("done");
            btn.disabled = true;

            setTimeout(() => {
                document.getElementById("row_" + orderId).remove();
            }, 800);
        } else {
            alert("Failed to Accept!");
        }

    })
    .catch(() => {
        alert("Server error!");
    });
}
window.cancelOrder = function(orderId, btn){
    fetch("accept_order_admin.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "order_id=" + orderId + "&action=cancel"
    })
    .then(res => res.text())
    .then(data => {

        if(data.trim() === "success"){
            btn.innerText = "Done";
            btn.classList.add("done");
            btn.disabled = true;

            setTimeout(() => {
                document.getElementById("row_" + orderId).remove();
            }, 800);
        } else {
            alert("Failed to Accept!");
        }

    })
    .catch(() => {
        alert("Server error!");
    });
}
</script>
</body>

</html>

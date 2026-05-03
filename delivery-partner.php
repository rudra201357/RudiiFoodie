<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Partner</title>
    <link rel="icon" type="image/png" href="images/logo.png">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css" />
    <link rel="stylesheet" href="style.css">
  <style>
    /* ... your existing styles ... */
    .order-container {
        border: 2px solid #ff9800;
        padding: 15px;
        margin: 20px auto;
        max-width: 600px;
        background: #fff3e0;
        border-radius: 8px;
    }
    .order-card {
        border: 1px solid #ffb74d;
        padding: 10px;
        margin-bottom: 10px;
        background: #fff;
        border-radius: 6px;
    }
    .preparing-status {
        color: #ff9800;
        font-weight: bold;
    }
    
    /* --- NEW MODAL STYLES --- */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1000; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        padding-top: 60px;
    }
    .modal-content {
        background-color: #fefefe;
        margin: 5% auto; /* 5% from the top and centered */
        padding: 20px;
        border: 1px solid #888;
        width: 80%; /* Could be more or less, depending on screen size */
        max-width: 400px;
        border-radius: 8px;
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
    }
    .close-btn {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }
    .close-btn:hover,
    .close-btn:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }
    .modal-content h2 {
        color: #ff9800;
        text-align: center;
        margin-bottom: 20px;
    }
    .modal-content input[type="text"],
    .modal-content input[type="password"] {
        width: 100%;
        padding: 10px;
        margin: 8px 0 16px 0;
        display: inline-block;
        border: 1px solid #ccc;
        box-sizing: border-box;
        border-radius: 4px;
    }
    .modal-content button {
        background-color: #ff9800;
        color: white;
        padding: 14px 20px;
        margin: 8px 0;
        border: none;
        cursor: pointer;
        width: 100%;
        border-radius: 4px;
        font-weight: bold;
    }
    .modal-content button:hover {
        opacity: 0.8;
    }
</style>
</head>
<body>
    <header>
        <nav class="navbar flex between wrapper">
            <a href="#" class="logo">
                <img src="images/logo.png" alt="RudiiFoodie logo">
                <span>RudiiFoodie.Delivery-Partner</span>
            </a>
            
            <a href="#" class="btn" id="openLoginModal">
                Sign in
                <i class="fa-solid fa-arrow-right-to-bracket"></i>
            </a>
            
            </div>
        </nav>
    </header>

    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Delivery Partner Login</h2>
            <form action="/login" method="post"> <label for="username"><b>Email or Phone</b></label>
                <input type="text" placeholder="Enter Email or Phone" name="username" required>

                <label for="password"><b>Password</b></label>
                <input type="password" placeholder="Enter Password" name="password" required>
                    
                <button type="submit">Log In</button>
            </form>
            <p style="text-align: center; font-size: 0.9em; margin-top: 15px;">
                <a href="#" style="color: #132ac2ff; text-decoration: none;">Forgot Password?</a><br>
              
            </p>
            <p style="text-align: center; font-size: 0.9em; margin-top: 15px;">  
                 <a href="#" style="color: #1443ddff; text-decoration: none;">Dont Have Account?</a>
                </p>
        </div>
    </div>
    
   <script>
    // --- Get all necessary elements ---
    var modal = document.getElementById("loginModal");
    var openBtn = document.getElementById("openLoginModal");
    var closeSpan = document.getElementsByClassName("close-btn")[0];
    var loginForm = modal.querySelector('form');
    var usernameField = modal.querySelector('input[name="username"]');
    var passwordField = modal.querySelector('input[name="password"]');
    var forgotPasswordLink = modal.querySelector('a[href="#"]'); 

    // --- Function to clear input fields ---
    function clearFields() {
        if (usernameField) usernameField.value = "";
        if (passwordField) passwordField.value = "";
    }

    
    openBtn.onclick = function() {
        clearFields();
        modal.style.display = "block";
    }
    loginForm.onsubmit = function() {
        
        setTimeout(clearFields, 50); 
       
    }
   
    if (forgotPasswordLink) {
        forgotPasswordLink.onclick = function() {
            n
            setTimeout(clearFields, 100); 
            
        };
    }

    closeSpan.onclick = function() {
        modal.style.display = "none";
        clearFields();
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
            clearFields(); 
        }
    }
</script>
</body>
</html>

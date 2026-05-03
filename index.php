<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RudiiFood</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css" />
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script> -->

    <link rel="stylesheet" href="style.css">
    <script defer src="js/script.js"></script>

</head>

<body>
<?php session_start(); include "balance.php";if (isset($_SESSION['user_id'])) $balance = balance($_SESSION["user_id"]); ?>
    <!-- HEADER  -->
    <header>
        <nav class="navbar flex between wrapper " >
            <a href="#" class="logo">
                <img src="images/logo.png" alt="RudiiFoodie logo">
                <span>RudiiFoodie</span>
            </a>
            <!-- DESKTOP MENU -->
            <ul class="navlist flex gap-3 ">
                <li>
                    <a href="#home">HOME</a>
                </li>
                <li>
                    <a href="#order">MENU</a>
                </li>
                <li>
                    <a href="#services">SERVICES</a>
                </li>
                <li>
                    <a href="#footer">ABOUT US</a>
                </li>
                <li>
                    <!-- <a href="#footer">CONTACTS</a> -->

                </li>

            </ul>
            
<!-- Toggle option for mobile menu -->
<div class="mobile-toggle">
<div id="firstFilter" class="filter-switch">
  <input checked="" id="option1" name="options" type="radio" />
  <label class="option" for="option1">Delivery</label>
  <input id="option2" name="options" type="radio" />
  <label class="option" for="option2">Cafe</label>
  <span class="background"></span>
</div>
</div>
<link rel="stylesheet" href="css/mobile-menu-toggle.css">


            <div class="nav-btn flex gap-2">
               <!-- 🛒 Cart Icon -->
<a href="#" class="cart-icon" id="cartIcon">
    <i class="fa-solid fa-bag-shopping"></i>
    <span class="cart-value">0</span>
</a>

<!-- 🛍️ Slide Cart Panel -->
<div id="cartPanel" class="cart-panel">
    <div class="cart-header">
        <h3>Your Cart</h3>
        <button id="closeCart">&times;</button>
    </div>
    <div id="cartItems" class="cart-items"></div>
    <div class="cart-footer" >
        <h4>Total: ₹<span id="cartTotal">0</span></h4>
  <form id="checkoutForm" action="checkout.php" method="POST" style="display:none;">
  <input type="hidden" name="mode" id="form_mode">
  <input type="hidden" name="cart" id="form_cart">
  <!-- optional: logged in user's id -->
  <input type="hidden" name="user_id" id="form_user_id" value="">
</form>
        <button id="checkoutBtn">Checkout</button>
    </div>
    <div id="empty-cart" >
        <h3 style="color:#ff0800">Your Cart is empty!! Go back and add itmes.</h3>
    </div>
</div>
<link rel="stylesheet" href="css/cart.css">
<script src="js/cart.js"></script>

<div class="profile-dropdown" id="profileDropdownContainer">
    <div class="profile-trigger" id="profileTrigger">
        
        <?php

        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
            
            // --- USER IS LOGGED IN ---
            $userInitial = $_SESSION['user_initial'] ?? 'User';
                echo '<div class="profile-initial"><span>' . htmlspecialchars($userInitial) . '</span></div>';
               echo '<div class="dropdown-content" id="profileMenu">
                      <a href="./recharge/payment.html">
                    <i class="fa-solid fa-indian-rupee-sign"></i></i>Balance: ₹'.  htmlspecialchars($balance) .'
                    
                    </a>
                      <a href="view-orders.php">
                    <i class="fa-solid fa-box"></i> View Orders
                    </a>
                    <a href="addresses.php">
                        <i class="fa-solid fa-location-dot"></i> Addresses
                    </a>
                    <a href="logout.php">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out
                    </a>
                     </div>';

        } else {
            
    
            echo '<a href="register.html" class="profile-signin-btn">Log In &nbsp;
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                  </a>';
        }
        ?>

    </div>
</div>
  <link rel="stylesheet" href="css/profile.css">

                    <!--  Profile options End -->


                <a href="#" class="hamburger" id="hamburger">
                    <i class="fa-solid fa-bars"></i>
                </a>
            </div>
            <!-- MOBILE MENU  -->
            <ul class="mobile-menu" id="mobile-menu">
                <li>
                    <a href="#home">HOME</a>
                </li>
                <li>
                    <a href="#order">MENU</a>
                </li>
                <li>
                    <a href="#services">SERVICES</a>
                </li>
                <li>
                    <a href="#footer">ABOUT US</a>
                </li>
             
            </ul>
            <script src="js/open-mobile-menu.js"></script>
        </nav>
    </header>

    </section>
    <!-- MAIN SECTION  -->
    <main>
        <section id="home">
            <div class="hero-section flex wrapper gap-4">
                <div class="content">
                    <h1>From Craving to Doorstep — <span style="color: var(--golden);">Fast & Fresh</span></h1>
                    <p class="para">
                        We will deliver your <span style="color: rgb(255, 162, 0);">Favorite Food </span> in just
                        minutes . . .
                    </p>
                    <div class="flex gap-2">
                        <a href="#order" class="btn">Order Now</a>
                        <a href="https://www.facebook.com/rudradeb.pal.14" target="_main" class="social-icon">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/its_rudra_004" target="_main" class="social-icon">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="https://www.threads.com/@its_rudra_004" target="_main" class="social-icon">
                            <i class="fa-brands fa-threads"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fa-brands fa-x-twitter"></i>
                        </a>
                    </div>
                </div>
                <div class="image-container">
                    <img src="images/rudra-Photoroom.png">
                </div>
            </div>
        </section>
        <section class="wrapper p-top" id="services">
            <h3 class="text-center">OUR SERVICES</h3>
            <div class="flex text-center gap-4">
                <div class="service-card">
                    <div class="service-img-container">
                        <img src="images/easy-to-order.png">
                    </div>
                    <h5>Easy To Order</h5>
                    <p>Follow only few steps to order</p>
                </div>

                <div class="service-card">
                    <div class="service-img-container">
                        <img src="images/fast-delivery.png" id="service-image">
                    </div>
                    <h5 id="service-title">Fast Delivery</h5>
                    <p id="service-description">We will reach you within 15 minutes</p>  <!--Menu.js  52 line -->
                </div>
                <script>
                </script>
                <div class="service-card">
                    <div class="service-img-container">
                        <img src="images/best-quality.png">
                    </div>
                    <h5>Best Quality</h5>
                    <p>We serve the best quality of foods</p>
                </div>
            </div>
        </section>
        <section id="order">
            <div class="wrapper p-top">
                <div class="text-center">
                    <h3>Our Menu</h3>
                </div>

                <label for="sort-options" id="sort-options-label">Looking For:</label>
                <select id="sort-options" name="sort-by">
                    <option value="all" selected>Our Menu</option>
                    <option value="offer">Today's Offer</option>
                    <option value="veg">Veg</option>
                    <option value="nonveg">Non Veg</option>
                    <option value="meal">Meal</option>
                    <option value="drinks">Drinks</option>
                    <option value="price-asc">Price: Low to High</option>
                    <option value="price-desc">Price: High to Low</option>
                    <option value="name-asc">Name: Ascending</option>
                    <option value="name-desc">Name: Descending</option>
                </select>

                <div id="menu-options" style="color: rgb(255, 132, 0);  font-size: 0.9em;">
                    <p>Cafe/Delivey Mode</p>
                </div>
                
                <div id="menu-container" class="menu-container flex ">
                    <script src="js/menu.js"></script>
                    <script src="js/add-items-to-cart.js"></script>

                    <!-- CART TAB -->

        </section>
        <section>
            <div class="wrapper p-top ">
                <h3 class="text-center">our reviews</h3>
                <div class="swiper mySwiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="star">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star-half-stroke"></i>
                                <p>I am Rudra</p>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="star">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <p>It will  come Soon</p>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="star">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <p>Stay stunned</p>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="star">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star-half-stroke"></i>
                                <p>Stay stunned</p>
                            </div>
                        </div>

                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
            </div>
        </section>
        <section>
            <div class="wrapper p-top">
                <div class="subscribe text-center">
                    <h3>Join With Us</h3>
                    <p class="m-auto">Join with us to get daily updates and rewards. Drop your email here.</p>
                    <br>
                    <form id="subscribe-form">
                        <label for="email" class="input-container m-auto">
                            <input type="email" name="email" id="email" placeholder="Enter your email"
                                autocomplete="off" required>
                            <button type="submit" class="btn">Subscribe</button>
                        </label>
                    </form>

                    <p id="message" style="color:green; text-align:center;"></p>

                    <script src="js/subscribe.js"></script>


                </div>
            </div>
        </section>

    </main>
    <!-- FOOTER  -->

    <footer id="footer">

        <div class="footer-container">
            <div class="flex wrapper gap-2">
                <div class="footer-wrapper">
                    <a href="#" class="logo">
                        <img src="images/logo.png" alt="RudiiFoodie logo">
                        <span>RudiiFoodie</span>
                    </a>
                    <p>We are happy to serve you.</p>
                    <div class="flex gap-1  ">

                        <a href="https://www.facebook.com/rudradeb.pal.14" target="_main"
                            class="footer-icon social-icon">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/its_rudra_004" target="_main"
                            class="social-icon footer-icon">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="https://www.threads.com/@its_rudra_004" target="_main" class="social-icon footer-icon">
                            <i class="fa-brands fa-threads"></i>
                        </a>
                        <a href="#" class="social-icon footer-icon">
                            <i class="fa-brands fa-x-twitter"></i>
                        </a>
                    </div>
                </div>
                <ul class="footer-wrapper">
                    <li>
                        <h4>Our Menu</h4>
                    </li>
                    <li>
                        <a href="" class="footer-link">Special</a>
                    </li>
                    <li>
                        <a href="" class="footer-link">Popular</a>
                    </li>
                    <li>
                        <a href="" class="footer-link">Category</a>
                    </li>
                </ul>
                <ul class="footer-wrapper">
                    <li>
                        <h4>Company</h4>
                    </li>
                    <li>
                        <a href="" class="footer-link">Why RudiiFoodie</a>
                    </li>
                    <li>
                        <a style="color: rgb(255, 0, 0);" href="work-with-us.html" class="footer-link">Work with us</a>
                    </li>
                    <li>
                        <a href="about.html" class="footer-link">About us</a>
                    </li>
                    <li>
                        <a href="" class="footer-link">FAQ's</a>
                    </li>
                </ul>
                <ul class="footer-wrapper">
                    <li>
                        <h4>Support</h4>
                    </li>
                    <li>
                        <a href="" class="footer-link">Account</a>
                    </li>
                    <li>
                        <a href="" class="footer-link">Support Center</a>
                    </li>
                    <li>
                        <a href="" class="footer-link">Feedback</a>
                    </li>
                    <li>
                        <a href="" class="footer-link">Contacts</a>
                    </li>
                </ul>
            </div>
        </div>
    </footer>




    <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const targetId = this.getAttribute('href');
                e.preventDefault();
                document.querySelector(targetId).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>

</html>

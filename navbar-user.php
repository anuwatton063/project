

<nav class="user-navbar navbar navbar-expand-lg navbar-light bg-light">
    <div class="container px-4 px-lg-5">
        <!-- Logo -->
        <a class="navbar-brand" href=""></a>
        <!-- Toggler button for small screens -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#userNavbarSupportedContent" aria-controls="userNavbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <!-- Navigation links -->
        <div class="collapse navbar-collapse" id="userNavbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="">About</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="userNavbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Shop</a>
                    <ul class="dropdown-menu" aria-labelledby="userNavbarDropdown">
                        <li><a class="dropdown-item" href="index.php">All Products</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li><a class="dropdown-item" href="product_phone.php">โทรศัพท์</a></li>
                        <li><a class="dropdown-item" href="product_headphone.php">หูฟัง</a></li>
                        <li><a class="dropdown-item" href="product_case.php">เคส</a></li>
                        <li><a class="dropdown-item" href="product_Charging_cable.php">สายชาร์จ</a></li>
                    </ul>
                </li>
            </ul>
            
            <?php 
                session_start(); // Start Session
                include('condb.php'); // Include database connection
                
                // Check if user is logged in
                if(isset($_SESSION['username'])) {
                    // Retrieve user information from the database
                    $query = "SELECT * FROM user_information WHERE username = '".$_SESSION['username']."'";
                    $result = $conn->query($query);
                    
                    // Check if the query is successful
                    if($result && $result->num_rows > 0) {
                        $user_row = $result->fetch_assoc();                    
                        echo '<span class="navbar-text me-2">Welcome, ' . $_SESSION['username'] . '</span>';                       
                        // Check if user_type_ID is set and equal to 1
                        if($user_row['user_type_ID'] == 1) {
                            // If user_type_ID is 1, display the "admin" button
                            echo '<a class="user-navbar-btn btn btn-outline-dark me-2" href="admin-user.php">admin</a>';
                        }
                        
                        // Display dropdown menu for Edit Profile and Edit Address
                        echo '
                            <div class="dropdown">
                                <button class="user-navbar-btn btn btn-outline-dark me-2 dropdown-toggle" type="button" id="userDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    Edit
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="userDropdownMenuButton">
                                    <li><a class="dropdown-item" href="user_profile.php">Profile</a></li>
                                    <li><a class="dropdown-item" href="user_address.php">Address</a></li>
                                    <li><a class="dropdown-item" href="user_order.php">Order</a></li>
                                </ul>
                            </div>
                        ';
                        
                        // Display Welcome message and Logout button
                        echo '<a class="user-navbar-btn btn btn-outline-dark me-2" href="logout.php">Logout</a>';
                    } else {
                        // If user data cannot be retrieved, display Login button
                        echo '<a class="user-navbar-btn btn btn-outline-dark me-2" href="login.php">Login</a>';
                    }
                } else {
                    // If not logged in, display the Login button
                    echo '<a class="user-navbar-btn btn btn-outline-dark me-2" href="login.php">Login</a>';
                }
                
            ?>

            <a class="user-navbar-btn btn btn-outline-dark me-2" href="cart.php">Cart</a> 
        </div>
    </div>
</nav>
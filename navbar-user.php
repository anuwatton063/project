<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container px-4 px-lg-5">
        <!-- Logo -->
        <a class="navbar-brand" href="">Start Bootstrap</a>
        <!-- Toggler button for small screens -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <!-- Navigation links -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="">About</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Shop</a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="">All Products</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li><a class="dropdown-item" href="">Popular Items</a></li>
                        <li><a class="dropdown-item" href="">New Arrivals</a></li>
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
                            echo '<a class="btn btn-outline-dark me-2" href="admin.php">admin</a>';
                        }
                        
                        // Display welcome message and Logout button
                        
                        echo '<a class="btn btn-outline-dark me-2" href="logout.php">Logout</a>';
                    } else {
                        // If user data cannot be retrieved, display Login button
                        echo '<a class="btn btn-outline-dark me-2" href="login.php">Login</a>';
                    }
                } else {
                    // If not logged in, display the Login button
                    echo '<a class="btn btn-outline-dark me-2" href="login.php">Login</a>';
                }
                $conn->close();
            ?>




            <a class="btn btn-outline-dark me-2" href="cart.php">Cart</a> 
        </div>
    </div>
</nav>

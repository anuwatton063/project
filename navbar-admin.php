<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Shop Homepage - Start Bootstrap Template</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <style>
        /* Custom styles for sidebar */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 200px; /* Set the width of the sidebar */
            z-index: 1000;
            padding-top: 3.5rem;
            background-color: #f8f9fa; /* Background color */
            border-right: 1px solid #dee2e6; /* Sidebar border */
        }
        #sidebar .nav-link {
            color: #495057; /* Link color */
        }
        #sidebar .nav-link.active {
            font-weight: bold; /* Active link font weight */
            color: #495057; /* Active link color */
        }
        #sidebar .dropdown-menu {
            background-color: #f8f9fa; /* Dropdown menu background color */
        }
        #sidebar .dropdown-divider {
            border-top: 1px solid #dee2e6; /* Dropdown divider color */
        }
        #sidebar .dropdown-item {
            color: #495057; /* Dropdown item color */
        }
        #sidebar .dropdown-item:hover {
            background-color: #e9ecef; /* Dropdown item hover background color */
        }

        /* Add separation and prevent overlapping */
        #content {
            margin-left: 200px; /* Width of the sidebar */
            padding: 20px;
        }
        
        #wrapper {
            display: flex;
        }
        
        /* For smaller screens, adjust layout */
        @media (max-width: 768px) {
            #sidebar {
                position: static;
                height: auto;
                width: auto;
            }
            #content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="position-sticky">
                <div class="sidebar-sticky">
                    <!-- Logo -->
                    <div class="d-flex justify-content-center mb-4">
                        <a class="navbar-brand" href="#">Admin</a>
                    </div>
                    <!-- Navigation links -->
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="admin-user.php">ข้อมูลผู้ใช้</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="product.php">ราการสินค้า</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Shop</a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="#">All Products</a></li>
                                <li><hr class="dropdown-divider" /></li>
                                <li><a class="dropdown-item" href="#">Popular Items</a></li>
                                <li><a class="dropdown-item" href="#">New Arrivals</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main content area -->
        <div id="content" class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Content goes here -->
        </div>
    </div>
    
    <!-- Bootstrap bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
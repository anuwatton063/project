<?php include('condb.php'); ?>

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@5.3.0/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <link href="css/styles.css" rel="stylesheet" />
    <style>
        /* Custom styles for sidebar */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
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
            color: #007bff; /* Active link color */
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
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky">
                    <div class="sidebar-sticky">
                        <!-- Logo -->
                        <div class="d-flex justify-content-center mb-4">
                            <a class="navbar-brand" href="#">Start Bootstrap</a>
                        </div>
                        <!-- Navigation links -->
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">About</a>
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
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Content goes here -->
            </main>
        </div>
    </div>

    <!-- Bootstrap bundle (includes Popper) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
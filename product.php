<?php
include 'navbar-user.php';
include('condb.php');
include 'checkuser.php';

$user_type_ID = getUserTypeID();
if ($user_type_ID == 1) {
    include 'navbar-admin.php';
}
if ($user_type_ID != 1){
    header("Location: index.php"); 
    exit(); 
}


if(isset($_POST['delete_id'])) {

    $delete_id = $_POST['delete_id'];
    $sql = "DELETE FROM `products_phone` WHERE product_ID = $delete_id";
    if(mysqli_query($conn, $sql)) {
    } else {
        echo "Error deleting product: " . mysqli_error($conn);
    }
}

$products_per_page = 10;

$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;

$offset = ($current_page - 1) * $products_per_page;

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'product_ID'; 
$order = isset($_GET['order']) && in_array($_GET['order'], ['asc', 'desc']) ? $_GET['order'] : 'asc'; 
$sort_options = array('product_ID', 'type_name', 'brand_name', 'product_color', 'Phone_capacity', 'product_stock', 'product_name', 'product_detail', 'product_price');
if (!in_array($sort, $sort_options)) {
    $sort = 'product_ID'; 
}

$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT p.*, pt.type_name, pb.brand_name 
        FROM `products_phone` p
        INNER JOIN `products_types` pt ON p.product_type_ID = pt.product_type_ID
        INNER JOIN `product_brand` pb ON p.product_brand_ID = pb.product_brand_ID";

if (!empty($search)) {
    $sql .= " WHERE p.product_ID LIKE '%$search%' OR pt.type_name LIKE '%$search%' OR pb.brand_name LIKE '%$search%' OR p.product_color LIKE '%$search%' OR p.Phone_capacity LIKE '%$search%' OR p.product_stock LIKE '%$search%' OR p.product_name LIKE '%$search%' OR p.product_detail LIKE '%$search%' OR p.product_price LIKE '%$search%'";
}

$sql .= " ORDER BY $sort $order";
$sql .= " LIMIT $offset, $products_per_page";
$result = mysqli_query($conn, $sql);

$total_products_query = "SELECT COUNT(*) as total FROM `products_phone`";
$total_result = mysqli_query($conn, $total_products_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_products = $total_row['total'];

$total_pages = ceil($total_products / $products_per_page);

$pagination_links = "";
if ($total_pages > 1) {
    $pagination_links .= "<div class='row'>
                            <div class='col-md-12'>
                                <ul class='pagination justify-content-center'>";
    for ($i = 1; $i <= $total_pages; $i++) {
        $active = $i == $current_page ? "active" : "";
        $pagination_links .= "<li class='page-item $active'><a class='page-link' href='?page=$i&search=" . htmlentities($search) . "'>$i</a></li>";
    }
    $pagination_links .= "</ul></div></div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@5.3.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <style>
        .edit-btn,
        .delete-btn {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
            width: 80px; 
            text-align: center;
        }

        .edit-btn {
            background-color: #007bff;
            color: #fff;
            border: none;
        }

        .delete-btn {
            background-color: #dc3545;
            color: #fff;
            border: none;
        }

        .edit-btn:hover {
            background-color: #0056b3;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        .sort-btn {
            color: #000; 
            text-decoration: none;
        }

        .sort-asc::after,
        .sort-desc::after {
            font-weight: bold;
            margin-left: 5px;
        }

        .sort-asc::after {
            content: '↑';
        }

        .sort-desc::after {
            content: '↓';
        }

        .sort-asc.active,
        .sort-desc.active {
            color: green; 
        }

        .table {
            border: 1px solid #dee2e6;
        }

        th,
        td {
            border: 1px solid #dee2e6; 
            padding: 8px; 
            vertical-align: middle; 
        }

        thead {
            background-color: #f8f9fa; 
        }

        .btn-container {
            white-space: nowrap; 
        }

    </style>
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Product Editor</h1>
        <div class="row mb-3">
            <div class="col-md-6">
                <form action="" method="GET" class="form-inline">
                    <div class="input-group"> 
                        <input type="text" name="search" class="form-control mr-2" placeholder="Search" value="<?= htmlentities($search) ?>">
                        <div class="input-group-append"> 
                            <button type="submit" class="btn btn-primary mr-3">Search</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6 text-right">
                <a class="btn btn-success mr-2" href="product-add.php">Add Product</a>
                <a class="btn btn-success mr-2" href="admin_addProductType.php">Add Product Type</a>
                <a class="btn btn-success mr-2" href="admin_addProductBrand.php">Add Product Brand</a>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th><a href="?sort=product_ID<?= $sort == 'product_ID' ? '&order=' . ($order == 'asc' ? 'desc' : 'asc') : '' ?>" class="sort-btn <?= $sort == 'product_ID' ? 'active ' . ($order == 'asc' ? 'sort-asc' : 'sort-desc') : '' ?>">ID</a></th>
                    <th><a href="?sort=type_name<?= $sort == 'type_name' ? '&order=' . ($order == 'asc' ? 'desc' : 'asc') : '' ?>" class="sort-btn <?= $sort == 'type_name' ? 'active ' . ($order == 'asc' ? 'sort-asc' : 'sort-desc') : '' ?>">ประเภทสินค้า</a></th>
                    <th><a href="?sort=brand_name<?= $sort == 'brand_name' ? '&order=' . ($order == 'asc' ? 'desc' : 'asc') : '' ?>" class="sort-btn <?= $sort == 'brand_name' ? 'active ' . ($order == 'asc' ? 'sort-asc' : 'sort-desc') : '' ?>">ยี่ห้อสินค้า</a></th>
                    <th><a href="?sort=product_color<?= $sort == 'product_color' ? '&order=' . ($order == 'asc' ? 'desc' : 'asc') : '' ?>" class="sort-btn <?= $sort == 'product_color' ? 'active ' . ($order == 'asc' ? 'sort-asc' : 'sort-desc') : '' ?>">สี</a></th>
                    <th><a href="?sort=Phone_capacity<?= $sort == 'Phone_capacity' ? '&order=' . ($order == 'asc' ? 'desc' : 'asc') : '' ?>" class="sort-btn <?= $sort == 'Phone_capacity' ? 'active ' . ($order == 'asc' ? 'sort-asc' : 'sort-desc') : '' ?>">ความจุ</a></th>
                    <th><a href="?sort=product_stock<?= $sort == 'product_stock' ? '&order=' . ($order == 'asc' ? 'desc' : 'asc') : '' ?>" class="sort-btn <?= $sort == 'product_stock' ? 'active ' . ($order == 'asc' ? 'sort-asc' : 'sort-desc') : '' ?>">จำนวนสินค้า</a></th>
                    <th><a href="?sort=product_name<?= $sort == 'product_name' ? '&order=' . ($order == 'asc' ? 'desc' : 'asc') : '' ?>" class="sort-btn <?= $sort == 'product_name' ? 'active ' . ($order == 'asc' ? 'sort-asc' : 'sort-desc') : '' ?>">ชื่อ</a></th>
                    <th><a href="?sort=product_detail<?= $sort == 'product_detail' ? '&order=' . ($order == 'asc' ? 'desc' : 'asc') : '' ?>" class="sort-btn <?= $sort == 'product_detail' ? 'active ' . ($order == 'asc' ? 'sort-asc' : 'sort-desc') : '' ?>">รายละเอียด</a></th>
                    <th>ภาพสินค้า</th>
                    <th><a href="?sort=product_price<?= $sort == 'product_price' ? '&order=' . ($order == 'asc' ? 'desc' : 'asc') : '' ?>" class="sort-btn <?= $sort == 'product_price' ? 'active ' . ($order == 'asc' ? 'sort-asc' : 'sort-desc') : '' ?>">ราคา</a></th>
                    <th>ภาพสินค้า 1</th>
                    <th>ภาพสินค้า 2</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $base_dir = "../project/png/";

                        $cover_image_path = $base_dir . $row["product_cover_image"];
                        $image1_path = $base_dir . $row["product_Image1"];
                        $image2_path = $base_dir . $row["product_Image2"];

                        if (!empty($row["product_cover_image"]) && file_exists($cover_image_path)) {
                            $cover_image_html = "<img src='$cover_image_path' alt='Cover Image' width='100' />";
                        } else {
                            $cover_image_html = "No Image Available";
                        }

                        if (!empty($row["product_Image1"]) && file_exists($image1_path)) {
                            $image1_html = "<img src='$image1_path' alt='Image 1' width='100' />";
                        } else {
                            $image1_html = "No Image Available";
                        }

                        if (!empty($row["product_Image2"]) && file_exists($image2_path)) {
                            $image2_html = "<img src='$image2_path' alt='Image 2' width='100' />";
                        } else {
                            $image2_html = "No Image Available";
                        }

                        echo "<tr>";
                        echo "<td>" . $row["product_ID"] . "</td>";
                        echo "<td>" . $row["type_name"] . "</td>";
                        echo "<td>" . $row["brand_name"] . "</td>";
                        echo "<td>" . $row["product_color"] . "</td>";
                        echo "<td>" . $row["Phone_capacity"] . "</td>";
                        echo "<td>" . $row["product_stock"] . "</td>";
                        echo "<td>" . $row["product_name"] . "</td>";
                        echo "<td>";
                        if (mb_strlen($row["product_detail"]) > 20) {
                            $trimmed_detail = mb_substr($row["product_detail"], 0, 20) . "...";
                        } else {
                            $trimmed_detail = $row["product_detail"];
                        }
                        $trimmed_detail = str_replace('�', '', $trimmed_detail);
                        
                        echo $trimmed_detail;                                             
                        echo "</td>";
                        echo "<td>$cover_image_html</td>";
                        echo "<td>" . $row["product_price"] . "</td>";
                        echo "<td>$image1_html</td>";
                        echo "<td>$image2_html</td>";
                        echo "<td class='btn-container'><a class='edit-btn' href='product-edit.php?id=" . $row["product_ID"] . "'>Edit</a>";
                        echo "<form method='POST' onsubmit='return confirm(\"Are you sure you want to delete this product?\")' style='display:inline;'><input type='hidden' name='delete_id' value='" . $row["product_ID"] . "'><button type='submit' class='delete-btn'>Delete</button></form></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='13'>No products found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <?php
        if ($total_pages > 1) {
            echo "<div class='row'>
                    <div class='col-md-12'>
                        $pagination_links
                    </div>
                  </div>";
        }
        ?>
    </div>
</body>
</html>



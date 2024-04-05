<?php
include('condb.php');

include 'navbar-user.php';

if (!isset($_SESSION['user_ID'])) {
    header("Location: login.php");
    exit();
}

$orderID = $_GET['orderID'];
$userID = $_SESSION['user_ID'];
$price_query = "SELECT net_price FROM orders WHERE order_ID = ?";
$stmt = $conn->prepare($price_query);
$stmt->bind_param("i", $orderID);
$stmt->execute();
$stmt->bind_result($total_price);
$stmt->fetch();
$stmt->close();

$uploadOk = 1; 

$existing_order_query = "SELECT COUNT(*) FROM payment WHERE order_ID = ?";
$stmt = $conn->prepare($existing_order_query);
$stmt->bind_param("i", $orderID);
$stmt->execute();
$stmt->bind_result($order_count);
$stmt->fetch();
$stmt->close();

$button_text = ($order_count > 0) ? "Update" : "Apply";

$target_dir = "../project/slip/";
$transfer_slip_filename = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["transfer_slip"])) {
    $total_price = $_POST['total_price'];
    $transfer_slip_filename = basename($_FILES["transfer_slip"]["name"]); 
    $target_file = $target_dir . $transfer_slip_filename; 
    
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }
    
    if ($_FILES["transfer_slip"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    $allowed_formats = array("jpg", "jpeg", "png", "gif");
    $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_formats)) {
        echo "Sorry, only JPG, JPEG, PNG, and GIF files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["transfer_slip"]["tmp_name"], $target_file)) {
            echo "The file ". htmlspecialchars(basename($_FILES["transfer_slip"]["name"])). " has been uploaded.";
        } else {
        }
    }

    if ($order_count > 0) {
        $update_payment_query = "UPDATE payment SET net_price = ?, transfer_slip = ? WHERE order_ID = ?";
        $stmt = $conn->prepare($update_payment_query);
        $stmt->bind_param("ssi", $total_price, $transfer_slip_filename, $orderID);
    } else {
        $insert_payment_query = "INSERT INTO payment (order_ID, user_ID, net_price, transfer_slip) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_payment_query);
        $stmt->bind_param("iiis", $orderID, $userID, $total_price, $transfer_slip_filename);
    }

    if ($stmt->execute()) {
        $update_order_status_query = "UPDATE orders SET orderstatus_ID = 2 WHERE order_ID = ?";
        $stmt = $conn->prepare($update_order_status_query);
        $stmt->bind_param("i", $orderID);
        if ($stmt->execute()) {
            header("Location: user_order.php");
        } else {

        }
    } else {

    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Your Orders</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="css/styles.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }


        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .mt-5 {
            margin-top: 50px;
        }

        .mt-4 {
            margin-top: 40px;
        }

        .table {
            background-color: #fff;
            border-radius: 8px;
        }

        .qr-code-container {
            position: static;
            top: 25%;
            right: 400px;
            text-align: center;

        }

        .qr-code-img {
            max-width: 300px;
            height: auto;
        }

        .file-input {
            margin-bottom: 20px;
        }

        #transferSlipPreview {
            max-width: 100%;
            height: auto;
            display: none;
            margin-top: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
        }

        .col-md-6 {
            margin-right: 200px; 
        }
    </style>
</head>

<body>
    <div class="qr-code-container">
        <h2>การชำระเงิน</h2>
        <img src="../project/qrcode/qrcode1.PNG" alt="Payment QR Code" class="qr-code-img" />
    </div>
    <div class="container">
        <div class="row mt-4">
            <div class="col-md-6">
                <h2>รายละเอียด</h2>
                <table class="table">
                    <tr>
                        
                        <td>Order ID : <?php echo $_GET['orderID']; ?></td>
                    </tr>
                    <tr>
                        <td>ราคารวม : ฿ <?php echo number_format($total_price, 2); ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <h2><?php echo $button_text; ?> หลักฐานการชำระเงิน</h2>
                <form method="post" enctype="multipart/form-data">
                    <div class="file-input">
                        <label for="transfer_slip" class="form-label">อัปโหลดสลิป:</label>
                        <input type="file" id="transfer_slip" name="transfer_slip" class="form-control" onchange="previewImage(this, 'transferSlipPreview')">
                    </div>
                    <img id="transferSlipPreview" src="../project/slip/" alt="Transfer Slip Preview">
                    <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">
                    <button type="submit" class="btn btn-primary" name="apply"><?php echo $button_text; ?></button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input, imgId) {
            const img = document.getElementById(imgId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    img.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                img.src = '#';
                img.style.display = 'none';
            }
        }
    </script>
</body>

</html>

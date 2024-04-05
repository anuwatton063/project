<?php
include('condb.php');
include 'navbar-user.php';

if (!isset($_SESSION['user_ID'])) {
    header("Location: login.php");
    exit();
}

$user_ID = $_SESSION['user_ID'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $conn->prepare("INSERT INTO address (user_ID, name, phone, Address_information, tumbon, amphoe, province, Zipcode) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssss", $user_ID, $name, $phone, $address, $sub_district, $district, $province, $postcode);

    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $sub_district = $_POST['sub_district'];
    $district = $_POST['district'];
    $province = $_POST['province'];
    $postcode = $_POST['postcode'];

    if ($stmt->execute()) {
        echo "<script>
                var confirmed = confirm('Data inserted successfully. Do you want to go back to the address list?');
                if (confirmed) {
                    window.location.href = 'user_address.php';
                }
              </script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Shop Homepage - Start Bootstrap Template</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <link href="css/styles.css" rel="stylesheet" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thailand Address Form</title>
    <link rel="stylesheet" href="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dist/jquery.Thailand.min.css">
    <style>
        .container1 {
            max-width: 1000px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .container1 h2 {
            margin: 20px 0;
        }

        .form-control {
            width: 50%;
            margin-bottom: 20px;
        }

        .txt {
            width: 100%;
            background: #f2f2f2;
            outline: none;
            border: none;
            padding: 10px;
            border-radius: 10px;
            font-size: 1rem;
        }

        .btn1 {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container container1">
        <h2>เพิ่มข้อมูล</h2>
        <div class="form-control">
            <span>ตำบล/แขวง</span>
            <input id="sub_district" type="text" class="txt" placeholder="ตำบล">
        </div>
        <div class="form-control">
            <span>อำเภอ/เขต</span>
            <input id="district" type="text" class="txt" placeholder="อำเภอ">
        </div>
        <div class="form-control">
            <span>จังหวัด</span>
            <input id="province" type="text" class="txt" placeholder="จังหวัด">
        </div>
        <div class="form-control">
            <span>รหัสไปรษณีย์</span>
            <input id="postcode" type="text" class="txt" placeholder="รหัสไปรษณีย์">
        </div>
        <div class="form-control">
            <span>ข้อมูลที่อยู่</span>
            <input id="address" type="text" class="txt" placeholder="ข้อมูลที่อยู่">
        </div>
        <div class="form-control">
            <span>ชื่อ</span>
            <input id="name" type="text" class="txt" placeholder="ชื่อ">
        </div>
        <div class="form-control">
            <span>เบอร์โทรศัพท์</span>
            <input id="phone" type="text" class="txt" placeholder="เบอร์โทรศัพท์">
        </div>
        <button id="applyBtn" class="btn1">Apply Data</button>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dependencies/JQL.min.js"></script>
    <script src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dependencies/typeahead.bundle.js"></script>
    <script src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dist/jquery.Thailand.min.js"></script>
    <script>
        $(document).ready(function() {
            $.Thailand({
                $district: $("#sub_district"),
                $amphoe: $("#district"),
                $province: $("#province"),
                $zipcode: $("#postcode")
            });

            function restrictInputToNumbers(inputField) {
                inputField.on('input', function() {
                    var sanitized = $(this).val().replace(/\D/g, '');
                    var maxLength = 10;
                    if (sanitized.length > maxLength) {
                        sanitized = sanitized.substr(0, maxLength);
                    }
                    $(this).val(sanitized);
                });
            }

            restrictInputToNumbers($("#phone"));
            restrictInputToNumbers($("#postcode"));

            $("#applyBtn").click(function() {
                var subDistrict = $("#sub_district").val();
                var district = $("#district").val();
                var province = $("#province").val();
                var postcode = $("#postcode").val();
                var address = $("#address").val();
                var name = $("#name").val();
                var phone = $("#phone").val();

                if (subDistrict.trim() === '' || district.trim() === '' || province.trim() === '' || postcode.trim() === '' || address.trim() === '' || name.trim() === '' || phone.trim() === '') {
                    alert("Please fill in all fields.");
                    return;
                }

                var confirmed = confirm('Are you sure you want to apply this data?');
                if (confirmed) {
                    $.ajax({
                        type: "POST",
                        url: window.location.href,
                        data: {
                            user_ID: <?php echo $user_ID; ?>,
                            sub_district: subDistrict,
                            district: district,
                            province: province,
                            postcode: postcode,
                            address: address,
                            name: name,
                            phone: phone
                        },
                        success: function(response) {
                            console.log("Data submitted successfully:", response);
                            alert("Data submitted successfully");
                            window.location.href = "user_address.php";
                        },
                        error: function(xhr, status, error) {
                            console.error("Error submitting data:", error);
                            alert("Error submitting data: " + error);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>

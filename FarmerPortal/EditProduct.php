<?php
include("../Includes/db.php");
session_name('agro_farmer');
session_start();
$sessphonenumber = $_SESSION['phonenumber'] ?? null;
if (!$sessphonenumber) {
    echo "<script>window.open('../auth/FarmerLogin.php','_self')</script>";
    exit;
}
$farmerRes = mysqli_query($con, "SELECT farmer_id FROM farmerregistration WHERE farmer_phone = " . intval($sessphonenumber));
$farmerRow = $farmerRes ? mysqli_fetch_assoc($farmerRes) : null;
$farmerId = $farmerRow ? intval($farmerRow['farmer_id']) : 0;
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product_title = $product_cat = $product_type = $product_stock = $product_price = $product_expiry = $product_desc = $product_keywords = $product_delivery = $product_image = '';
if ($id) {
    $getting_prod = "SELECT * FROM products WHERE product_id = $id AND farmer_fk = $farmerId";
    if ($run = mysqli_query($con, $getting_prod)) {
        if ($details = mysqli_fetch_assoc($run)) {
            $product_title = $details['product_title'];
            $product_cat = $details['product_cat'];
            $product_type = $details['product_type'];
            $product_stock = $details['product_stock'];
            $product_price = $details['product_price'];
            $product_expiry = $details['product_expiry'];
            $product_desc = $details['product_desc'];
            $product_keywords = $details['product_keywords'];
            $product_delivery = $details['product_delivery'];
            $product_image = $details['product_image'];
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_pro']) && $id) {
    $product_title = mysqli_real_escape_string($con, $_POST['product_title']);
    $product_cat = intval($_POST['product_cat']);
    $product_type = mysqli_real_escape_string($con, $_POST['product_type']);
    $product_stock = intval($_POST['product_stock']);
    $product_price = floatval($_POST['product_price']);
    $product_expiry = mysqli_real_escape_string($con, $_POST['product_expiry']);
    $product_desc = mysqli_real_escape_string($con, $_POST['product_desc']);
    $product_keywords = mysqli_real_escape_string($con, $_POST['product_keywords']);
    $product_delivery = mysqli_real_escape_string($con, $_POST['product_delivery']);
    $new_image = isset($_FILES['product_image']['name']) ? trim($_FILES['product_image']['name']) : '';
    if ($new_image !== '') {
        $tmp = $_FILES['product_image']['tmp_name'];
        move_uploaded_file($tmp, "../Admin/product_images/$new_image");
        $product_image = $new_image;
    }
    $update_sql = "UPDATE products SET product_title='$product_title', product_cat=$product_cat, product_type='$product_type', product_stock=$product_stock, product_price=$product_price, product_expiry='$product_expiry', product_desc='$product_desc', product_keywords='$product_keywords', product_delivery='$product_delivery'";
    if ($product_image !== '') { $update_sql .= ", product_image='$product_image'"; }
    $update_sql .= " WHERE product_id = $id AND farmer_fk = $farmerId";
    if (mysqli_query($con, $update_sql)) {
        echo "<script>alert('Product updated');window.open('FarmerProductDetails.php?id=$id','_self');</script>";
        exit;
    } else {
        echo "<script>alert('Update failed');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://kit.fontawesome.com/c587fc1763.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="../portal_files/bootstrap.min.css">


    <title>Edit Product</title>
    <style>
        @import url(https://fonts.googleapis.com/css?family=Raleway:300,400,600);

        body {
            margin: 0;
            font-size: .9rem;
            font-weight: 400;
            line-height: 1.6;
            color: #212529;
            text-align: left;
            background-color: #f5f8fa;
        }

        .my-form,
        .login-form {
            font-family: Raleway, sans-serif;
        }

        .my-form {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }

        .my-form .row {
            margin-left: 0;
            margin-right: 0;
        }

        .login-form {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }

        .login-form .row {
            margin-left: 0;
            margin-right: 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <main class="my-form">
            <div class="cotainer">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <?php /*
                            if (isset($_SESSION['phonenumber'])) {
                                if (isset($_GET['id'])) {
                                    $id = $_GET['id'];
                                    $getting_prod = "select * from products where product_id = $id";
                                    $run = mysqli_query($con, $getting_prod);

                                    while ($details = mysqli_fetch_array($run)) {
                                        $product_title = $details['product_title'];
                                        $product_cat = $details['product_cat'];
                                        $product_type = $details['product_type'];
                                        $product_stock = $details['product_stock'];
                                        $product_price = $details['product_price'];
                                        $product_expiry = $details['product_expiry'];
                                        $product_desc = $details['product_desc'];
                                        $product_keywords = $details['product_keywords'];
                                        $product_delivery = $details['product_delivery'];
                                    }
                                }



                         


                            */ ?>

                            <div class="card-header">
                                <h4 class="text-center font-weight-bold">Edit Product <i class="fas fa-edit"></i></h4>
                            </div>
                            <div class="card-body">

                                <form name="my-form" action="EditProduct.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">

                                    <div class="form-group row">
                                        <label for="full_name" class="col-md-4 col-form-label text-md-right text-center font-weight-bolder">Product Title:</label>
                                        <div class="col-md-6">
                                            <input type="text" id="full_name" class="form-control" name="product_title" value="<?php echo htmlspecialchars($product_title); ?>" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="email_address" class="col-md-4 col-form-label text-md-right text-center font-weight-bolder">Product Stock:(In kg)</label>
                                        <div class="col-md-6">
                                            <input type="text" id="full_name" class="form-control" name="product_stock" value="<?php echo htmlspecialchars($product_stock); ?>" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="user_name" class="col-md-4 col-form-label text-md-right text-center font-weight-bolder">Product Categories:</label>
                                        <div class="col-md-6">
                                            <select name="product_cat" required>
                                                <option>Select a Category</option>
                                                <?php
                                                $get_cats = "select * from categories";
                                                $run_cats =  mysqli_query($con, $get_cats);
                                                while ($row_cats = mysqli_fetch_array($run_cats)) {
                                                    $cat_id = $row_cats['cat_id'];
                                                    $cat_title = $row_cats['cat_title'];
                                                    $sel = ($cat_id == $product_cat) ? "selected" : "";
                                                    echo "<option value='$cat_id' $sel>$cat_title</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="phone_number" class="col-md-4 col-form-label text-md-right text-center font-weight-bolder">Product type :</label>
                                        <div class="col-md-6">
                                            <input type="text" id="phone_number" class="form-control" name="product_type" value="<?php echo htmlspecialchars($product_type); ?>" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="present_address" class="col-md-4 col-form-label text-md-right text-center font-weight-bolder">Product Expiry :</label>
                                        <div class="col-md-6">
                                            <input id="present_address" class="form-control" type="date" name="product_expiry" value="<?php echo htmlspecialchars($product_expiry); ?>" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="permanent_address" class="col-md-4 col-form-label text-md-right text-center font-weight-bolder">Product Image :</label>
                                        <div class="col-md-6">
                                            <input id="permanent_address" type="file" name="product_image">
                                            <?php if ($product_image) { echo "<small class='form-text text-muted'>Current: " . htmlspecialchars($product_image) . "</small>"; } ?>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="nid_number" class="col-md-4 col-form-label text-md-right text-center font-weight-bolder">Product MRP : (Per kg)</label>
                                        <div class="col-md-6">
                                            <input type="text" id="nid_number" class="form-control" name="product_price" value="<?php echo htmlspecialchars($product_price); ?>" required>
                                        </div>
                                    </div>

                                    <!-- <div class="form-group row">
                                        <label for="nid_number1" class="col-md-4 col-form-label text-md-right text-center font-weight-bolder">Product Base Price:(Per kg)</label>
                                        <div class="col-md-6">
                                            <input type="text" id="nid_number1" class="form-control" name="product_baseprice" placeholder="Enter Product base price" required>
                                        </div>
                                    </div> -->

                                    <div class="form-group row">
                                        <label for="nid_number2" class="col-md-4 col-form-label text-md-right text-center font-weight-bolder"> Product Description:</label>
                                        <div class="col-md-6">
                                            <textarea name="product_desc" id="nid_number2" class="form-control" name="product_desc" rows="3" required><?php echo htmlspecialchars($product_desc); ?></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="nid_number3" class="col-md-4 col-form-label text-md-right text-center font-weight-bolder">Product Keywords:</label>
                                        <div class="col-md-6">
                                            <input type="text" id="nid_number3" class="form-control" name="product_keywords" value="<?php echo htmlspecialchars($product_keywords); ?>" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="nid_number4" class="col-md-4 col-form-label text-md-right text-center font-weight-bolder">Delivery :</label>
                                        <div class="col-md-6">
                                            <input type="radio" id="nid_number4" name="product_delivery" value="yes" <?php echo ($product_delivery === 'yes') ? 'checked' : ''; ?>/>Yes
                                            <input type="radio" id="nid_number4" name="product_delivery" value="no" <?php echo ($product_delivery === 'no') ? 'checked' : ''; ?>/>No
                                        </div>
                                    </div>
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary" name="update_pro">
                                            Update
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </main>
    </div>

    <body>

</html>
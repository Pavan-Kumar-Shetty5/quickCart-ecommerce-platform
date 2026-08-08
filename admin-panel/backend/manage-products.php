<?php
// backend/manage-products.php

// 1. Handle Deletion
// backend/manage-products.php

// 1. Handle Product Deletion
// backend/manage-products.php

if (isset($_GET['del'])) {
    $id = (int)$_GET['del'];

    // 1. Check if the product exists in the orders table
    $checkOrder = $conn->query("SELECT id FROM orders WHERE product_id = $id LIMIT 1");

    if ($checkOrder && $checkOrder->num_rows > 0) {
        // 2. If it exists in orders, do not delete. Redirect with a warning.
        echo "<script>
                alert('Product cannot be deleted! It has already been ordered by a customer. Please hide it instead.');
                window.location.href='admin.php?view=products';
              </script>";
        exit();
    } else {
        // 3. If NOT ordered, proceed with deletion (Clean cart first to avoid FK error)
        $conn->query("DELETE FROM cart_items WHERE product_id = $id");
        $conn->query("DELETE FROM products WHERE id = $id");

        header("Location: admin.php?view=products");
        exit();
    }
}

// 2. Handle Add Product (Saves path exactly like your Orders page expects)
if(isset($_POST['save_p'])) {
    $n = mysqli_real_escape_string($conn, $_POST['n']); 
    $p = $_POST['p']; 
    $c_id = $_POST['c']; 
    
    // Get category name to determine the folder
    $cat_q = $conn->query("SELECT category_name FROM categories WHERE id = $c_id");
    $cat_data = $cat_q->fetch_assoc();
    $folder = strtolower(trim($cat_data['category_name']));

    $img_name = $_FILES['i']['name'];
    $tmp_name = $_FILES['i']['tmp_name'];

    // This creates "images/electronics/filename.png"
    $db_save_path = "images/" . $folder . "/" . $img_name;
    $upload_path = "../" . $db_save_path;

    if (move_uploaded_file($tmp_name, $upload_path)) {
        $stmt = $conn->prepare("INSERT INTO products (name, price, category_id, image_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdss", $n, $p, $c_id, $db_save_path);
        $stmt->execute();
    }
}
?>

<div class="card" style="margin-bottom: 20px; background: #222; padding: 20px; border-radius: 8px;">
    <h3 style="color: white; margin-top: 0;">Add Product</h3>
    <form method="POST" enctype="multipart/form-data" style="display: flex; gap: 10px; flex-wrap: wrap;">
        <input type="text" name="n" placeholder="Name" required style="padding: 8px; flex: 1;">
        <input type="number" name="p" placeholder="Price" required style="padding: 8px; width: 100px;">
        <select name="c" required style="padding: 8px;">
            <?php
            $cats = $conn->query("SELECT * FROM categories");
            while($c = $cats->fetch_assoc()) echo "<option value='{$c['id']}'>{$c['category_name']}</option>";
            ?>
        </select>
        <input type="file" name="i" required style="color: white;">
        <button type="submit" name="save_p" style="background: #28a745; color: white; border: none; padding: 8px 15px; cursor: pointer; border-radius: 4px;">Save</button>
    </form>
</div>

<table style="width: 100%; border-collapse: collapse; background: #1a1a1a; color: white;">
    <thead>
        <tr style="background: #333; text-align: left;">
            <th style="padding: 12px;">Image</th>
            <th>Product</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $res = $conn->query("SELECT * FROM products ORDER BY id DESC");
        while($row = $res->fetch_assoc()): 
            
            // This is the "Order Page" secret: 
            // If your DB has "images/electronics/fan.png", we just add "../"
            $raw_path = trim($row['image_path']);
            
            // Check if the path already contains 'images/'. If not, we fix it.
            if (strpos($raw_path, 'images/') === false) {
                // This is for your OLD data that only has the filename
                // We assume it's in a default or placeholder spot
                $display_path = "../images/electronics/" . $raw_path;
            } else {
                // This is for your NEW data that matches the Order Page logic
                $display_path = "../" . $raw_path;
            }

            // Fallback for missing files
            if (!file_exists($display_path) || empty($raw_path)) {
                $display_path = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='50' height='50'><rect width='50' height='50' fill='%23111'/><text x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' fill='%23444' font-size='8px'>No Image</text></svg>";
            }
        ?>
            <tr style="border-bottom: 1px solid #333;">
                <td style="padding: 10px;">
                    <img src="<?= $display_path ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #444;">
                </td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td>₹<?= number_format($row['price'], 2) ?></td>
                <td>
                    <a href="admin.php?view=products&del=<?= $row['id'] ?>" style="color: #ff4d4d; text-decoration: none;">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php
// backend/manage-orders.php

$query = "SELECT 
            o.*, 
            p.image_path, 
            p.name AS product_name 
          FROM orders o
          LEFT JOIN products p ON o.product_id = p.id 
          WHERE o.product_id != 0 
          ORDER BY o.order_date DESC";

$res = $conn->query($query);

if (!$res) {
    die("Query Failed: " . $conn->error);
}
?>

<div class="table-wrapper">
    <table class="styled-table" style="width: 100%; border-collapse: collapse; background: #1a1a1a; color: #fff;">
        <thead>
            <tr style="background: #333; text-align: left;">
                <th style="padding: 12px;">Product</th>
                <th>Qty</th> <th>Payment Method</th> 
                <th>Placed On</th> 
                <th>Contact</th> 
                <th>Total</th>
                <th>Address</th> 
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $res->fetch_assoc()): 
                
                $raw_path = trim($row['image_path'] ?? '');
                $img_src = (!empty($raw_path)) ? "../" . $raw_path : "../images/placeholder.png";
                
                $order_time = date("d M, Y | h:i A", strtotime($row['order_date']));
            ?>
                <tr style="border-bottom: 1px solid #444;">
                    <td style="padding: 10px; display: flex; align-items: center; gap: 10px;">
                        <div style="width: 50px; height: 50px; border-radius: 4px; overflow: hidden; border: 1px solid #555; background: #000;">
                            <img src="<?= $img_src ?>" 
                                 style="width: 100%; height: 100%; object-fit: cover;"
                                 onerror="this.src='../images/placeholder.png';">
                        </div>
                        <span style="font-size: 0.9em;"><?= htmlspecialchars($row['product_name'] ?? 'Unknown Item') ?></span>
                    </td>

                    <td style="font-size: 0.9em; font-weight: bold; color: #fff;">
                        <?= $row['quantity'] ?? '1' ?>
                    </td>

                    <td style="font-size: 0.85em; color: #aaa; text-transform: uppercase;">
                        <?= htmlspecialchars($row['payment_method'] ?? 'COD') ?>
                    </td>

                    <td style="font-size: 0.85em; color: #888;">
                        <?= $order_time ?>
                    </td>

                    <td style="padding: 10px;">
                        <div style="font-size: 0.9em; color: #3498db;">
                            <i class="fa fa-phone" style="font-size: 0.8em; margin-right: 5px;"></i>
                            <?= htmlspecialchars($row['phone'] ?? 'N/A') ?>
                        </div>
                    </td>

                    <td style="color: #4bb543; font-weight: bold;">₹<?= number_format($row['total_amount'], 2) ?></td>

                    <td style="padding: 10px; max-width: 180px; font-size: 0.85em; line-height: 1.4;">
                        <div style="color: #ccc;"><?= htmlspecialchars($row['address'] ?? 'No Address') ?></div>
                        <div style="font-size: 0.8em; color: #777;">PIN: <?= htmlspecialchars($row['pincode'] ?? '000000') ?></div>
                    </td>

                    <td>
                        <span style="padding: 4px 8px; border-radius: 4px; background: #222; font-size: 10px; border: 1px solid #444; color: #ffcc00; font-weight: bold;">
                            <?= strtoupper($row['status'] ?? 'PENDING') ?>
                        </span>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
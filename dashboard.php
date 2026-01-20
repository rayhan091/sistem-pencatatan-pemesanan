<?php
// dashboard.php
session_start();
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'auth-check.php';

$db = new Database();

// Ambil statistik
$stats = [
    'total_orders' => 0,
    'total_customers' => 0,
    'total_products' => 0,
    'pending_orders' => 0,
    'revenue_today' => 0
];

try {
    // Total pesanan
    $stmt = $db->connect()->query("SELECT COUNT(*) as total FROM orders");
    $stats['total_orders'] = $stmt->fetch()['total'];
    
    // Total pelanggan
    $stmt = $db->connect()->query("SELECT COUNT(*) as total FROM customers");
    $stats['total_customers'] = $stmt->fetch()['total'];
    
    // Total produk
    $stmt = $db->connect()->query("SELECT COUNT(*) as total FROM products");
    $stats['total_products'] = $stmt->fetch()['total'];
    
    // Pesanan pending
    $stmt = $db->connect()->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
    $stats['pending_orders'] = $stmt->fetch()['total'];
    
    // Revenue hari ini
    $stmt = $db->connect()->query("SELECT SUM(grand_total) as total FROM orders WHERE DATE(order_date) = CURDATE()");
    $revenue = $stmt->fetch()['total'];
    $stats['revenue_today'] = $revenue ?: 0;
    
} catch (PDOException $e) {
    error_log("Database error in dashboard: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Pemesanan Barang</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'nav.php'; ?>
    
    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1><i class="fas fa-home"></i> Dashboard</h1>
                <p>Selamat datang, <?php echo $_SESSION['full_name']; ?>!</p>
            </div>
            
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['total_orders']; ?></h3>
                        <p>Total Pesanan</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['total_customers']; ?></h3>
                        <p>Total Pelanggan</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['total_products']; ?></h3>
                        <p>Total Produk</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['pending_orders']; ?></h3>
                        <p>Pesanan Pending</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Rp <?php echo number_format($stats['revenue_today'], 0, ',', '.'); ?></h3>
                        <p>Pendapatan Hari Ini</p>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card">
                <h3><i class="fas fa-bolt"></i> Aksi Cepat</h3>
                <div class="quick-actions">
                    <a href="admin/orders/create.php" class="action-btn">
                        <i class="fas fa-plus-circle"></i>
                        <span>Tambah Pesanan Baru</span>
                    </a>
                    <a href="products/" class="action-btn">
                        <i class="fas fa-box"></i>
                        <span>Kelola Produk</span>
                    </a>
                    <a href="customers/" class="action-btn">
                        <i class="fas fa-user-plus"></i>
                        <span>Tambah Pelanggan</span>
                    </a>
                    <a href="admin/orders/" class="action-btn">
                        <i class="fas fa-list"></i>
                        <span>Lihat Semua Pesanan</span>
                    </a>
                </div>
            </div>
            
            <!-- Recent Orders -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-history"></i> Pesanan Terbaru</h3>
                    <a href="admin/orders/" class="btn btn-sm">Lihat Semua →</a>
                </div>
                <div class="card-body">
                    <?php
                    try {
                        $query = "SELECT o.*, c.name as customer_name 
                                  FROM orders o 
                                  LEFT JOIN customers c ON o.customer_id = c.id 
                                  ORDER BY o.created_at DESC 
                                  LIMIT 10";
                        $stmt = $db->connect()->query($query);
                        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (empty($orders)) {
                            echo '<p class="text-muted">Belum ada pesanan</p>';
                        } else {
                            echo '<div class="table-responsive">';
                            echo '<table class="data-table">';
                            echo '<thead>
                                    <tr>
                                        <th>No. Order</th>
                                        <th>Pelanggan</th>
                                        <th>Tanggal</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                  </thead>';
                            echo '<tbody>';
                            
                            foreach ($orders as $order) {
                                $status_class = '';
                                switch ($order['status']) {
                                    case 'pending': $status_class = 'badge-warning'; break;
                                    case 'processing': $status_class = 'badge-info'; break;
                                    case 'completed': $status_class = 'badge-success'; break;
                                    case 'cancelled': $status_class = 'badge-danger'; break;
                                    default: $status_class = 'badge-secondary';
                                }
                                
                                echo '<tr>
                                        <td><a href="admin/orders/view.php?id=' . $order['id'] . '">' . $order['order_number'] . '</a></td>
                                        <td>' . htmlspecialchars($order['customer_name'] ?? '-') . '</td>
                                        <td>' . date('d/m/Y', strtotime($order['order_date'])) . '</td>
                                        <td>Rp ' . number_format($order['grand_total'], 0, ',', '.') . '</td>
                                        <td><span class="badge ' . $status_class . '">' . ucfirst($order['status']) . '</span></td>
                                      </tr>';
                            }
                            
                            echo '</tbody></table></div>';
                        }
                    } catch (PDOException $e) {
                        echo '<p class="text-danger">Error: ' . $e->getMessage() . '</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    <script src="js/app.js"></script>
    <script src="js/notifications.js"></script>
</body>
</html>
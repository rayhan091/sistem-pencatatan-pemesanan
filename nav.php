<?php
// nav.php
// Navigation Menu
if (!isset($_SESSION['user_id'])) return;
?>
<nav class="main-nav">
    <div class="container">
        <div class="nav-content">
            <div class="nav-left">
                <a href="<?php echo BASE_URL; ?>dashboard.php" class="nav-logo">
                    <i class="fas fa-boxes"></i>
                    <span><?php echo APP_NAME; ?></span>
                </a>
            </div>
            
            <div class="nav-center">
                <a href="<?php echo BASE_URL; ?>dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                
                <div class="nav-dropdown">
                    <a href="#" class="nav-link">
                        <i class="fas fa-shopping-cart"></i> Pesanan
                    </a>
                    <div class="dropdown-menu">
                        <a href="<?php echo BASE_URL; ?>admin/orders/">
                            <i class="fas fa-list"></i> Semua Pesanan
                        </a>
                        <a href="<?php echo BASE_URL; ?>admin/orders/create.php">
                            <i class="fas fa-plus"></i> Pesanan Baru
                        </a>
                    </div>
                </div>
                
                <a href="<?php echo BASE_URL; ?>products/" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'products') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-box"></i> Produk
                </a>
                
                <a href="<?php echo BASE_URL; ?>customers/" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'customers') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Pelanggan
                </a>
                
                <?php if (is_admin()): ?>
                <div class="nav-dropdown">
                    <a href="#" class="nav-link">
                        <i class="fas fa-cog"></i> Admin
                    </a>
                    <div class="dropdown-menu">
                        <a href="#">
                            <i class="fas fa-user-cog"></i> Pengguna
                        </a>
                        <a href="#">
                            <i class="fas fa-chart-bar"></i> Laporan
                        </a>
                        <a href="#">
                            <i class="fas fa-cogs"></i> Pengaturan
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="nav-right">
                <div class="user-dropdown">
                    <button class="user-btn">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <span><?php echo $_SESSION['full_name']; ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a href="#">
                            <i class="fas fa-user"></i> Profil Saya
                        </a>
                        <a href="#">
                            <i class="fas fa-cog"></i> Pengaturan
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="<?php echo BASE_URL; ?>logout.php" class="logout-link">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
.main-nav {
    background: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 0 0;
}

.nav-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 60px;
}

.nav-left {
    display: flex;
    align-items: center;
}

.nav-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    color: #333;
    font-size: 20px;
    font-weight: 600;
}

.nav-logo i {
    color: #667eea;
    font-size: 24px;
}

.nav-center {
    display: flex;
    gap: 20px;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 15px;
    text-decoration: none;
    color: #666;
    border-radius: 5px;
    transition: all 0.3s;
}

.nav-link:hover {
    background: #f5f7fa;
    color: #667eea;
}

.nav-link.active {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.nav-dropdown {
    position: relative;
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    border-radius: 5px;
    min-width: 200px;
    display: none;
    z-index: 1000;
}

.nav-dropdown:hover .dropdown-menu {
    display: block;
}

.dropdown-menu a {
    display: block;
    padding: 10px 15px;
    text-decoration: none;
    color: #666;
    transition: background 0.3s;
}

.dropdown-menu a:hover {
    background: #f5f7fa;
    color: #667eea;
}

.dropdown-divider {
    height: 1px;
    background: #eee;
    margin: 5px 0;
}

.logout-link {
    color: #dc3545 !important;
}

.user-dropdown {
    position: relative;
}

.user-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    background: none;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
    color: #666;
    transition: background 0.3s;
}

.user-btn:hover {
    background: #f5f7fa;
}

.user-avatar {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.user-dropdown .dropdown-menu {
    right: 0;
    left: auto;
}

@media (max-width: 768px) {
    .nav-center {
        display: none;
    }
    
    .nav-content {
        justify-content: space-between;
    }
}
</style>

<script>
// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const userBtn = document.querySelector('.user-btn');
    const userDropdown = document.querySelector('.user-dropdown .dropdown-menu');
    
    if (userBtn && userDropdown) {
        userBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.style.display = userDropdown.style.display === 'block' ? 'none' : 'block';
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            userDropdown.style.display = 'none';
        });
    }
});
</script>
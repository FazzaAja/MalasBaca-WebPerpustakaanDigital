<?php
$role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'guest';
// Tentukan link dasar
$home_link = ($role == 'member') ? $path.'user/index.php' : $path.'index';
$login_alert = "login"; // Link untuk guest
?>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo"><i class="fas fa-book-open"></i> BookBase</div>
        <button class="close-btn" onclick="toggleSidebar()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <nav class="menu">
        <a href="<?php echo $home_link; ?>" class="active">
            <i class="fas fa-home"></i> Discover
        </a>
        
        <a href="<?php echo ($role == 'member') ? '#categories' : $path.$login_alert; ?>">
            <i class="fas fa-th-large"></i> Category
        </a>
        
        <a href="<?php echo ($role == 'member') ? '#' : $path.$login_alert; ?>">
            <i class="fas fa-book"></i> My Library <?php if($role=='guest') echo '<i class="fas fa-lock" style="font-size:10px; float:right; margin-top:5px;"></i>'; ?>
        </a>
        
        <a href="<?php echo ($role == 'member') ? '#' : $path.$login_alert; ?>">
            <i class="fas fa-heart"></i> Favourite <?php if($role=='guest') echo '<i class="fas fa-lock" style="font-size:10px; float:right; margin-top:5px;"></i>'; ?>
        </a>
    </nav>

    <nav class="menu bottom-menu">
        <?php if ($role != 'guest'): ?>
            <a href="<?php echo $path; ?>logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <?php else: ?>
            <a href="<?php echo $path; ?>login"><i class="fas fa-sign-in-alt"></i> Login</a>
        <?php endif; ?>
    </nav>
</aside>
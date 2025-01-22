<?php
$isLoggedIn = isset($_SESSION['user_id']);
$currentRole = $isLoggedIn ? $_SESSION['role'] : null;
?>

<nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="home.php" class="text-2xl font-bold text-blue-600">CourseLab</a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-8">
                <?php if($isLoggedIn): ?>
                    <?php if($currentRole === 'student'): ?>
                        <a href="student_dashboard.php" class="text-gray-600 hover:text-blue-600">My Profile</a>
                        <a href="home.php" class="text-gray-600 hover:text-blue-600">Browse</a>
                    <?php elseif($currentRole === 'teacher'): ?>
                        <a href="teacher_dashboard.php" class="text-gray-600 hover:text-blue-600">Dashboard</a>
                        <a href="profile.php" class="block px-4 py-2 text-gray-800 hover:bg-blue-50">Profile</a>
                    <?php elseif($currentRole === 'admin'): ?>
                        <a href="admin_dashboard.php" class="text-gray-600 hover:text-blue-600">Admin Dashboard</a>
                    <?php endif; ?>
                    <a href="auth/logout.php" class="block px-4 py-2 text-gray-800 hover:bg-blue-50">Logout</a>
                    <div class="relative group">
                        <button class="flex items-center space-x-2">
                            <img src="<?= $_SESSION['profile_image'] ?? '../up/defaultPFP.jpg' ?>" 
                                 class="w-8 h-8 rounded-full" 
                                 alt="Profile">
                            <span class="text-gray-600"><?= $_SESSION['first_name'] ?? 'User' ?></span>
                        </button>
                    </div>
                <?php else: ?>
                    <a href="home.php" class="text-gray-600 hover:text-blue-600">Home</a>
                    <a href="login.php" class="text-gray-600 hover:text-blue-600">Login</a>
                    <a href="signup.php" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Sign Up</a>
                <?php endif; ?>
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-gray-600 hover:text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden px-4 pt-2 pb-3 space-y-1">
        <?php if($isLoggedIn): ?>
            <?php if($currentRole === 'student'): ?>
                <a href="student_dashboard.php" class="block text-gray-600 hover:bg-blue-50 px-3 py-2 rounded-md">My Courses</a>
                <a href="home.php" class="block text-gray-600 hover:bg-blue-50 px-3 py-2 rounded-md">Browse</a>
            <?php elseif($currentRole === 'teacher'): ?>
                <a href="teacher_dashboard.php" class="block text-gray-600 hover:bg-blue-50 px-3 py-2 rounded-md">Dashboard</a>
                <a href="write_chapter.php" class="block text-gray-600 hover:bg-blue-50 px-3 py-2 rounded-md">Create Course</a>
            <?php elseif($currentRole === 'admin'): ?>
                <a href="admin_dashboard.php" class="block text-gray-600 hover:bg-blue-50 px-3 py-2 rounded-md">Admin Dashboard</a>
            <?php endif; ?>
           
            <a href="logout.php" class="block text-gray-600 hover:bg-blue-50 px-3 py-2 rounded-md">Logout</a>
        <?php else: ?>
            <a href="home.php" class="block text-gray-600 hover:bg-blue-50 px-3 py-2 rounded-md">Home</a>
            <a href="login.php" class="block text-gray-600 hover:bg-blue-50 px-3 py-2 rounded-md">Login</a>
            <a href="signup.php" class="block text-gray-600 hover:bg-blue-50 px-3 py-2 rounded-md">Sign Up</a>
        <?php endif; ?>
    </div>
</nav>

<script>
    // Mobile menu toggle
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    });
</script>
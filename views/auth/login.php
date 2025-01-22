<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: ../home.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CourseLab</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Log In</h2>
        <?php if(isset($_SESSION['error'])): ?>
            <p class="text-red-600 text-center mb-4"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form action="login_process.php" method="POST">
            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Log In
                </button>
            </div>

            <!-- Signup Link -->
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-600">Don't have an account? <a href="signup.php" class="text-blue-600 hover:underline">Sign Up</a></p>
            </div>
        </form>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-Guard Authentication Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-12">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h1 class="text-3xl font-bold text-center mb-8">🛡️ Multi-Guard Authentication System</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Admin Login -->
                <div class="border-2 border-red-200 rounded-lg p-6 text-center hover:border-red-400 transition-colors">
                    <div class="text-4xl mb-4">👨‍💼</div>
                    <h3 class="text-xl font-bold text-red-600 mb-2">Admin Portal</h3>
                    <p class="text-gray-600 mb-4">System administration and management</p>
                    <a href="/admin/login" class="inline-block bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition-colors">
                        Admin Login
                    </a>
                    <div class="mt-2 text-xs text-gray-500">
                        Route: admin.login<br>
                        Guard: admin
                    </div>
                </div>

                <!-- Merchant Login -->
                <div class="border-2 border-orange-200 rounded-lg p-6 text-center hover:border-orange-400 transition-colors">
                    <div class="text-4xl mb-4">🏪</div>
                    <h3 class="text-xl font-bold text-orange-600 mb-2">Merchant Portal</h3>
                    <p class="text-gray-600 mb-4">Business dashboard and services</p>
                    <a href="/merchant/login" class="inline-block bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700 transition-colors">
                        Merchant Login
                    </a>
                    <div class="mt-2 text-xs text-gray-500">
                        Route: merchant.login<br>
                        Guard: merchant
                    </div>
                </div>

                <!-- Customer Login -->
                <div class="border-2 border-blue-200 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
                    <div class="text-4xl mb-4">👤</div>
                    <h3 class="text-xl font-bold text-blue-600 mb-2">Customer Portal</h3>
                    <p class="text-gray-600 mb-4">Bookings and account management</p>
                    <a href="/customer/login" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                        Customer Login
                    </a>
                    <div class="mt-2 text-xs text-gray-500">
                        Route: customer.login<br>
                        Guard: customer
                    </div>
                </div>
            </div>

            <div class="mt-12 bg-green-50 border border-green-200 rounded-lg p-4">
                <h3 class="text-lg font-bold text-green-800 mb-2">✅ Phase 1.1 Complete: Multi-Guard Authentication</h3>
                <ul class="text-sm text-green-700 space-y-1">
                    <li>✓ 4 Guards configured (web, admin, merchant, customer)</li>
                    <li>✓ Separate authentication controllers created</li>
                    <li>✓ Custom middleware for each guard</li>
                    <li>✓ Dedicated route files for each guard</li>
                    <li>✓ Specialized login views with unique branding</li>
                    <li>✓ Password reset configurations</li>
                </ul>
            </div>

            <div class="mt-6 text-center">
                <a href="/" class="text-gray-600 hover:text-gray-900">← Back to Homepage</a>
            </div>
        </div>
    </div>
</body>
</html>
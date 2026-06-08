<?php

/**
 * Test Script for Activity Log
 * 
 * Run this in VPS after deploy:
 * docker exec -it setorin_app php test-activity-log.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\AktivitasAdmin;
use App\Helpers\AdminActivityLogger;

echo "\n=================================\n";
echo "Activity Log Test Script\n";
echo "=================================\n\n";

// Test 1: Check database connection
echo "Test 1: Database Connection\n";
try {
    $count = AktivitasAdmin::count();
    echo "✅ Database connected. Current aktivitas_admin records: {$count}\n\n";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 2: Check if admin user exists
echo "Test 2: Admin User Check\n";
try {
    $admin = User::where('role', 'admin')->first();
    if ($admin) {
        echo "✅ Admin user found: ID={$admin->id}, Nama={$admin->nama}\n\n";
    } else {
        echo "❌ No admin user found\n\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ Error finding admin: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 3: Direct model insert
echo "Test 3: Direct Model Insert\n";
try {
    $log = AktivitasAdmin::create([
        'id_pengguna' => $admin->id,
        'jenis_aktivitas' => 'test',
        'modul' => 'Test Script',
        'deskripsi' => 'Test dari script PHP',
        'created_at' => now(),
    ]);
    echo "✅ Direct insert successful. Log ID: {$log->id}\n\n";
} catch (Exception $e) {
    echo "❌ Direct insert failed: " . $e->getMessage() . "\n\n";
}

// Test 4: Using AdminActivityLogger helper
echo "Test 4: AdminActivityLogger Helper (Simulated)\n";
echo "⚠️  Note: This test can't authenticate user, so it will fail.\n";
echo "   This is NORMAL - the helper needs Filament auth context.\n";
try {
    // Simulate login
    auth()->login($admin);
    
    AdminActivityLogger::create(
        'Test Module',
        999,
        'Test Item'
    );
    
    echo "✅ AdminActivityLogger executed\n\n";
} catch (Exception $e) {
    echo "⚠️  Expected failure: " . $e->getMessage() . "\n\n";
}

// Test 5: Verify foreign key relationship
echo "Test 5: Foreign Key Relationship\n";
try {
    $latest = AktivitasAdmin::latest()->first();
    if ($latest && $latest->pengguna) {
        echo "✅ Relationship works: Log ID {$latest->id} belongs to {$latest->pengguna->nama}\n\n";
    } else {
        echo "❌ Relationship not working\n\n";
    }
} catch (Exception $e) {
    echo "❌ Relationship error: " . $e->getMessage() . "\n\n";
}

// Summary
echo "=================================\n";
echo "Test Summary\n";
echo "=================================\n";
$finalCount = AktivitasAdmin::count();
echo "Total aktivitas_admin records: {$finalCount}\n";
echo "Latest record:\n";
$latest = AktivitasAdmin::latest()->first();
if ($latest) {
    echo "  - ID: {$latest->id}\n";
    echo "  - User: {$latest->pengguna->nama}\n";
    echo "  - Jenis: {$latest->jenis_aktivitas}\n";
    echo "  - Modul: {$latest->modul}\n";
    echo "  - Created: {$latest->created_at}\n";
}
echo "\n✅ Test script completed!\n";
echo "Now test via Filament UI and watch Laravel logs.\n\n";

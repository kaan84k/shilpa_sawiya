<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../src/Models/Donation.php';

use App\Models\Donation;

class DummyConn {
    public function prepare($query) {
        throw new Exception('DB should not be accessed');
    }
}

function assertThrows(callable $fn) {
    try {
        $fn();
        echo "FAIL: Expected exception not thrown\n";
        return false;
    } catch (Exception $e) {
        echo "PASS: " . $e->getMessage() . "\n";
        return true;
    }
}

function run_tests() {
    $conn = new DummyConn();
    $donation = new Donation($conn);
    $result = assertThrows(function() use ($donation) {
        $donation->createDonation(1, '', '', '', '', '');
    });
    return $result ? 0 : 1;
}

exit(run_tests());


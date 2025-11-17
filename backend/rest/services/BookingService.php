<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/BookingDao.php';
require_once __DIR__ . '/../dao/CarDao.php';
require_once __DIR__ . '/../dao/UsersDao.php';

class BookingService extends BaseService {
    protected $carDao;
    protected $usersDao;

    public function __construct() {
        parent::__construct(new BookingDao());
        $this->carDao = new CarDao();
        $this->usersDao = new UsersDao();
    }

    /**
     * Create a booking with basic business rules:
     * - required fields: user_id, car_id, rented_at, return_time, price
     * - rented_at < return_time
     * - car must exist and be available
     * - no overlapping active bookings for same car
     */
    public function createBooking(array $data) {
        $required = ['user_id','car_id','rented_at','return_time','price'];
        foreach ($required as $f) {
            if (!isset($data[$f]) || $data[$f] === '') {
                throw new Exception("Field '$f' is required for creating a booking");
            }
        }

        $user = $this->usersDao->getById($data['user_id']);
        if (!$user) {
            throw new Exception('user does not exist');
        }

        $car = $this->carDao->getById($data['car_id']);
        if (!$car) {
            throw new Exception('car does not exist');
        }

        if (!$car['availability']) {
            throw new Exception('car is currently not available');
        }

        $start = strtotime($data['rented_at']);
        $end = strtotime($data['return_time']);
        if ($start === false || $end === false) {
            throw new Exception('invalid date format for rented_at or return_time');
        }

        if ($start >= $end) {
            throw new Exception('rented_at must be before return_time');
        }

        // check overlapping bookings for the car
        $existing = $this->dao->getByCarId($data['car_id']);
        foreach ($existing as $b) {
            if (!in_array($b['status'], ['in process','confirmed'])) continue;

            $bStart = strtotime($b['rented_at']);
            $bEnd = strtotime($b['return_time']);
            if ($bStart === false || $bEnd === false) continue;

            // overlap test: newStart < bEnd && newEnd > bStart
            if ($start < $bEnd && $end > $bStart) {
                throw new Exception('requested period overlaps an existing booking');
            }
        }

        $payload = [
            'user_id' => $data['user_id'],
            'car_id' => $data['car_id'],
            'rented_at' => date('Y-m-d H:i:s', $start),
            'return_time' => date('Y-m-d H:i:s', $end),
            'price' => $data['price'],
            'status' => $data['status'] ?? 'in process'
        ];

        $ok = $this->dao->insert($payload);
        if ($ok) {
            // mark car unavailable while booked
            $this->carDao->updateById($data['car_id'], ['availability' => false]);
        }

        return $ok;
    }

    public function completeBooking($id) {
        // set booking status to completed and free car
        $booking = $this->dao->getById($id);
        if (!$booking) throw new Exception('booking not found');

        $this->dao->updateById($id, ['status' => 'completed']);
        // free car
        $this->carDao->updateById($booking['car_id'], ['availability' => true]);
        return true;
    }

    public function getByUser($user_id) {
        return $this->dao->getByUserId($user_id);
    }

    public function getActiveBookings() {
        return $this->dao->getActiveBookings();
    }
}

?>

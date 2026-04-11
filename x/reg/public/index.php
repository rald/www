/* file: index.php */

session_start();

$action = $_GET['action'] ?? 'student-register';

switch ($action) {
    case 'student-register':
        (new StudentController())->showRegister();
        break;
    case 'student-store':
        (new StudentController())->store();
        break;
    case 'schedule-add':
        (new ScheduleController())->showAdd();
        break;
    case 'schedule-check':
        (new ScheduleController())->check();
        break;
}
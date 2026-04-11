/* file: StudentController.php */

class StudentController {
    public function showRegister(): void {
        $msg = $_SESSION['msg'] ?? '';
        unset($_SESSION['msg']);
        require __DIR__ . '/../Views/student/register.php';
    }

    public function store(): void {
        $stu = trim($_POST['stu'] ?? '');
        $nck = trim($_POST['nck'] ?? '');

        if ($stu === '' || $nck === '') {
            $_SESSION['msg'] = 'Please fill in all fields';
            header('Location: /?action=student-register');
            exit;
        }

        if (Student::exists($stu)) {
            $_SESSION['msg'] = 'Student ID already exists';
            header('Location: /?action=student-register');
            exit;
        }

        Student::create($stu, $nck);
        $_SESSION['msg'] = 'Record added';
        header('Location: /?action=student-register');
        exit;
    }
}
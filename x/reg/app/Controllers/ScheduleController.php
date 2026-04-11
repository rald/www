/* file: ScheduleController.php */

class ScheduleController {
    private function toMinutes(string $time): int {
        [$h, $m] = explode(':', $time);
        return ((int)$h * 60) + (int)$m;
    }

    private function conflict(array $input, array $existing): array {
        $out = [];
        foreach ($existing as $row) {
            if ($row['dayOfWeek'] === $input['dow']) {
                $a1 = $this->toMinutes($input['beg']);
                $a2 = $this->toMinutes($input['end']);
                $b1 = $this->toMinutes($row['beginTime']);
                $b2 = $this->toMinutes($row['endTime']);
                if ($a1 < $b2 && $b1 < $a2) $out[] = $row;
            }
        }
        return $out;
    }

    public function showAdd(): void {
        $subjects = Subject::all();
        $msg = $_SESSION['msg'] ?? '';
        $conflicts = $_SESSION['conflicts'] ?? [];
        unset($_SESSION['msg'], $_SESSION['conflicts']);
        require __DIR__ . '/../Views/schedule/add.php';
    }

    public function check(): void {
        $stu = trim($_POST['stu'] ?? '');
        $sbj = trim($_POST['sbj'] ?? '');
        $dow = trim($_POST['dow'] ?? '');
        $beg = trim($_POST['beg'] ?? '');
        $end = trim($_POST['end'] ?? '');

        if (!Student::exists($stu)) {
            $_SESSION['msg'] = 'Invalid Student ID';
        } elseif ($this->toMinutes($beg) >= $this->toMinutes($end)) {
            $_SESSION['msg'] = 'Invalid Time';
        } else {
            $input = compact('stu', 'sbj', 'dow', 'beg', 'end');
            $existing = Schedule::all();
            $conflicts = $this->conflict($input, $existing);

            if ($conflicts) {
                $_SESSION['conflicts'] = $conflicts;
                $_SESSION['msg'] = 'Conflict Schedule';
            } else {
                $_SESSION['msg'] = 'No conflict schedule';
                $_SESSION['can_add'] = true;
                $_SESSION['pending'] = $input;
            }
        }

        header('Location: /?action=schedule-add');
        exit;
    }
}
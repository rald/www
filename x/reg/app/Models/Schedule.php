/* file: Schedule.php */

class Schedule {
    public static function all(): array {
        $db = Database::connection();
        $res = $db->query("
            SELECT id, student_id, subject_id, dayOfWeek, beginTime, endTime
            FROM schedule
        ");
        $rows = [];
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public static function create(string $stu, string $sbj, string $dow, string $beg, string $end): bool {
        $db = Database::connection();
        $stmt = $db->prepare("
            INSERT INTO schedule (student_id, subject_id, dayOfWeek, beginTime, endTime)
            VALUES (:stu, :sbj, :dow, :beg, :end)
        ");
        $stmt->bindValue(':stu', $stu, SQLITE3_TEXT);
        $stmt->bindValue(':sbj', $sbj, SQLITE3_TEXT);
        $stmt->bindValue(':dow', $dow, SQLITE3_TEXT);
        $stmt->bindValue(':beg', $beg, SQLITE3_TEXT);
        $stmt->bindValue(':end', $end, SQLITE3_TEXT);
        return (bool)$stmt->execute();
    }
}
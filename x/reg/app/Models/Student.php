/* file: Student.php */

class Student {
    public static function exists(string $id): bool {
        $db = Database::connection();
        $stmt = $db->prepare("SELECT 1 FROM student WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_TEXT);
        return (bool)$stmt->execute()->fetchArray(SQLITE3_NUM);
    }

    public static function create(string $id, string $nick): bool {
        $db = Database::connection();
        $stmt = $db->prepare("INSERT INTO student (id, nick) VALUES (:id, :nick)");
        $stmt->bindValue(':id', $id, SQLITE3_TEXT);
        $stmt->bindValue(':nick', $nick, SQLITE3_TEXT);
        return (bool)$stmt->execute();
    }
}



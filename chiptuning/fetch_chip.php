<?php
require_once 'config.php';

try {
    $stmt = $conn->query("SELECT * FROM chip ORDER BY id DESC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['car']}</td>";
        echo "<td>{$row['original_hp']}</td>";
        echo "<td>{$row['stage1_hp']}</td>";
        echo "<td>{$row['stage2_hp']}</td>";
        echo "<td>{$row['original_torque']}</td>";
        echo "<td>{$row['stage1_torque']}</td>";
        echo "<td>{$row['stage2_torque']}</td>";
        echo "<td>
                <a href='edit.php?id={$row['id']}' class='btn btn-secondary'>Edit</a>
                <a href='delete.php?id={$row['id']}' class='btn btn-secondary' onclick='return confirm(\"Delete this entry?\")'>Delete</a>
              </td>";
        echo "</tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='9'>Error: " . $e->getMessage() . "</td></tr>";
}
?>

<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
require_once 'conn.php';
try {
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $title = isset($_POST['title']) ? $_POST['title'] : null;
        $description = isset($_POST['description']) ? $_POST['description'] : null;
        if ($title) {
            $sql = "INSERT INTO crud_php (title, description) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ss", $title, $description);
                if ($stmt->execute()) {
                    $_SESSION['message'] = 'Task Saved Successfully';
                    $_SESSION['message_type'] = 'success';
                    header("Location: index.php");
                    exit();
                } else {
                    session_start();
                    $_SESSION['message'] = 'Error Saving Task';
                    $_SESSION['message_type'] = 'danger';
                    throw new Exception("Error executing query: " . $stmt->error);
                }
                $stmt->close();
            } else {
                throw new Exception("Error preparing statement: " . $conn->error);
            }
        } else {
            throw new Exception("Title is required.");
        }
    } else {
        throw new Exception("Invalid request method.");
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
    $conn->close();
}

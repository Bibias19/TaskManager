<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
require_once 'conn.php';
try {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "DELETE FROM crud_php WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                session_start();
                $_SESSION['message'] = 'Task Deleted Successfully';
                $_SESSION['message_type'] = 'success';

                header("Location: index.php");
                exit();
            } else {
                session_start();
                $_SESSION['message'] = 'Error Deleting Task';
                $_SESSION['message_type'] = 'danger';
                throw new Exception("Error executing query: " . $stmt->error);
            }
            $stmt->close();
        } else {
            throw new Exception("Error preparing statement: " . $conn->error);
        }
    } else {
        throw new Exception("No ID provided for deletion."); 
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
    $conn->close();
}
?>
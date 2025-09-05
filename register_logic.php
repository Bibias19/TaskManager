<?php
require_once 'conn.php';
try {
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ??null;
        $confirm_password = $_POST['confirm_password'] ?? null;
        if ($email && $password) {
            if ($password !== $confirm_password) {
                throw new Exception("As senhas não coincidem.");
            }
             if (!$conn || $conn->connect_error) {
                throw new Exception("Conexão com bancos de dados não está ativa: " . $conn->connect_error);
            }
            $hashed_password = password_hash($password, PASSWORD_ARGON2I);
            $sql = "INSERT INTO users (email, password) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            if($stmt){
                $stmt->bind_param("ss", $email, $hashed_password);
                if ($stmt->execute()) {
                    session_start();
                    $_SESSION['message'] = 'Cadastro realizado com sucesso. Por favor, faça login.';
                    $_SESSION['message_type'] = 'primary';
                    header("Location: login.php");
                    exit();
                } else {
                    throw new Exception("Erro ao executar o cadastro " . $stmt->error);
                }
                $stmt->close();
            }else{
                throw new Exception("Erro ao preparar a consulta: " . $conn->error);
            }
        } else {
            throw new Exception("Email,senha e confirmação são obrigatórios " );
        }
    } else {
        throw new Exception("Metodo de requisição inválido.");
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
    if(isset($conn) && $conn){
        $conn->close();
    }
}
?>
             
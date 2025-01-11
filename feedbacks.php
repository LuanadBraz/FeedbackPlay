<?php
$servername = "http://localhost/feedbacks.html"; // Endereço do servidor (localhost para servidores locais)
$username = "root"; // Usuário do banco de dados
$password = "1864"; // Senha do banco de dados (geralmente vazia em localhost)
$dbname = "feedback_db"; // Nome do banco de dados

// Cria conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die(json_encode(['message' => 'Conexão falhou: ' . $conn->connect_error]));
}

// Configuração para retornar dados no formato JSON
header('Content-Type: application/json');

// Função para carregar feedbacks existentes
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $sql = "SELECT * FROM feedbacks ORDER BY date DESC";
    $result = $conn->query($sql);

    $feedbacks = [];
    while ($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }
    echo json_encode($feedbacks);

// Função para adicionar um novo feedback
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recebe os dados enviados no corpo da requisição
    $data = json_decode(file_get_contents('php://input'), true);
    
    $username = $data['username'];
    $level = $data['level'];
    $area = $data['area'];
    $feedback = $data['feedback'];

    // Prepara a consulta para inserir os dados no banco
    $stmt = $conn->prepare("INSERT INTO feedbacks (username, level, area, feedback) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $level, $area, $feedback);

    // Executa a consulta e verifica se foi bem-sucedida
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Feedback adicionado com sucesso']);
    } else {
        echo json_encode(['message' => 'Erro ao adicionar feedback']);
    }

    $stmt->close();
}

$conn->close();
?>

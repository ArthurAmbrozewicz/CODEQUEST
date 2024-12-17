<?php
$host = "localhost:3306";
$usuario = "root";
$senha = "";
$database = "db_codequest";

$conn = new mysqli($host, $usuario, $senha, $database);

$data = json_decode(file_get_contents("php://input"), true);
$question_id = $data['question_id'];
$selected_choice = $data['selected_choice'];

$sql = "SELECT correct_choice, feedback FROM correct_answers WHERE question_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $question_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$is_correct = ($row['correct_choice'] === $selected_choice);
$feedback = $is_correct ? $row['feedback'] : "Resposta incorreta! Tente novamente.";

echo json_encode(["correct" => $is_correct, "feedback" => $feedback]);

$conn->close();
?>

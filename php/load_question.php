<?php
$host = "localhost:3306";
$usuario = "root";
$senha = "";
$database = "db_codequest";

$conn = new mysqli($host, $usuario, $senha, $database);

// Recebe o ID da última pergunta respondida ou 0 para iniciar da primeira
$last_question_id = isset($_GET['last_question_id']) ? intval($_GET['last_question_id']) : 0;

// Consulta para obter a próxima pergunta
$sql_question = "SELECT id AS question_id, question_text 
                 FROM questions 
                 WHERE id > ? AND module_id = 1 
                 ORDER BY id ASC 
                 LIMIT 1";
                 
$stmt_question = $conn->prepare($sql_question);
$stmt_question->bind_param("i", $last_question_id);
$stmt_question->execute();
$result_question = $stmt_question->get_result();

// Verifica se há uma pergunta
if ($question_row = $result_question->fetch_assoc()) {
    $question = [
        "question_id" => $question_row["question_id"],
        "question_text" => $question_row["question_text"]
    ];

    // Consulta para obter todas as alternativas da pergunta atual
    $sql_choices = "SELECT choice_text, choice_label 
                    FROM choices 
                    WHERE question_id = ?";
                    
    $stmt_choices = $conn->prepare($sql_choices);
    $stmt_choices->bind_param("i", $question["question_id"]);
    $stmt_choices->execute();
    $result_choices = $stmt_choices->get_result();

    $choices = [];
    while ($choice_row = $result_choices->fetch_assoc()) {
        $choices[] = [
            "label" => $choice_row["choice_label"],
            "text" => $choice_row["choice_text"]
        ];
    }

    // Retorna a pergunta e todas as alternativas como JSON
    echo json_encode([
        "question" => $question,
        "choices" => $choices
    ]);
} else {
    // Indica que não há mais perguntas
    echo json_encode(["end" => true]);
}

$conn->close();
?>

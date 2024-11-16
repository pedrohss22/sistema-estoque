<?php
include 'conexao.php';
$mensagem = ""; // Variável para armazenar mensagem de feedback

// Processa o formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $quantidade = (int) $_POST['quantidade'];
    $tipo = $_POST['tipo'];

    // Insere a movimentação no banco de dados
    $stmt = $pdo->prepare("INSERT INTO mercadorias (nome, quantidade, tipo) VALUES (:nome, :quantidade, :tipo)");
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':quantidade', $quantidade);
    $stmt->bindParam(':tipo', $tipo);

    if ($stmt->execute()) {
        $mensagem = "Movimentação registrada com sucesso!";
    } else {
        $mensagem = "Erro ao registrar a movimentação.";
    }
}

// Consulta para exibir o saldo de estoque
$stmt = $pdo->query("SELECT nome, SUM(CASE WHEN tipo='Entrada' THEN quantidade ELSE -quantidade END) AS saldo FROM mercadorias GROUP BY nome");
$estoque = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Estoque</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-image: url('estoque.jpg');
            background-size: cover;
            background-attachment: fixed;
            color: #333;
            display: flex;
        }

        /* Menu Lateral */
        .menu {
            background-color: rgba(0, 0, 0, 0.7);
            width: 200px;
            padding: 15px;
            position: fixed;
            height: 100%;
            top: 0;
            left: 0;
        }

        .menu a {
            display: block;
            color: #fff;
            text-decoration: none;
            margin: 10px 0;
            font-size: 18px;
            text-align: center;
            padding: 10px;
            border: 2px solid #ffffff; /* Borda adicionada */
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }

        .menu a:hover {
            background-color: #ffffff;
            color: #333;
            border-color: #333;
        }

        /* Estilização dos Modais */
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            max-width: 500px;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            z-index: 1000;
            text-align: center;
        }

        /* Botão de Fechar Modal */
        .close-btn {
            background-color: #d9534f;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            float: right;
        }

        /* Formulário */
        .form-movimentacao .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        label {
            color: #333;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .btn-registrar {
            background-color: #007bff;
            color: #fff;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        /* Tabela de Estoque */
        .table-estoque {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table-estoque th, .table-estoque td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .table-estoque th {
            background-color: #007bff;
            color: #fff;
        }

        .table-estoque td {
            background-color: #f4f4f9;
        }
    </style>
</head>
<body>
    <!-- Menu Lateral -->
    <div class="menu">
        <a href="#" onclick="openModal('movimentacaoModal')">Registrar Movimentação</a>
        <a href="#" onclick="openModal('saldoModal')">Saldo de Estoque</a>
    </div>

    <!-- Modal de Movimentação -->
    <div class="modal" id="movimentacaoModal">
        <button class="close-btn" onclick="closeModal('movimentacaoModal')">X</button>
        <h2>Registrar Movimentação</h2>

        <?php if ($mensagem): ?>
            <p><?php echo htmlspecialchars($mensagem); ?></p>
        <?php endif; ?>

        <form action="index.php" method="post" class="form-movimentacao">
            <div class="form-group">
                <label for="nome">Nome da Mercadoria:</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            <div class="form-group">
                <label for="quantidade">Quantidade:</label>
                <input type="number" id="quantidade" name="quantidade" required>
            </div>
            <div class="form-group">
                <label for="tipo">Tipo de Movimentação:</label>
                <select id="tipo" name="tipo">
                    <option value="Entrada">Entrada</option>
                    <option value="Saída">Saída</option>
                </select>
            </div>
            <button type="submit" class="btn-registrar">Registrar</button>
        </form>
    </div>

    <!-- Modal de Saldo de Estoque -->
    <div class="modal" id="saldoModal">
        <button class="close-btn" onclick="closeModal('saldoModal')">X</button>
        <h2>Saldo de Estoque Atual</h2>
        <table class="table-estoque">
            <tr>
                <th>Nome da Mercadoria</th>
                <th>Saldo</th>
            </tr>
            <?php foreach ($estoque as $item) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['nome']); ?></td>
                    <td><?php echo (int)$item['saldo']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <script>
        // Função para fechar qualquer modal aberto
        function closeAllModals() {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                modal.style.display = 'none';
            });
        }

        // Função para abrir um modal, fechando o anterior se houver
        function openModal(modalId) {
            closeAllModals(); // Fecha todos os modais abertos
            document.getElementById(modalId).style.display = 'block';
        }

        // Função para fechar um modal específico
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
    </script>
</body>
</html>
<?php
ob_start();

$host = getenv('DB_HOST') ?: 'db';
$db   = getenv('DB_NAME') ?: 'crud';
$user = getenv('DB_USER') ?: 'cruduser';
$pass = getenv('DB_PASSWORD') ?: 'crudpass';

$pdo = null;
$error = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $pdo->exec("CREATE TABLE IF NOT EXISTS pessoas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(120) NOT NULL,
        email VARCHAR(180) NOT NULL,
        criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    $error = 'Não foi possível conectar ao banco. Tente novamente em instantes.';
}

if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'excluir') {
        $stmt = $pdo->prepare('DELETE FROM pessoas WHERE id = ?');
        $stmt->execute([(int)($_POST['id'] ?? 0)]);
    } elseif ($acao === 'salvar') {
        $id = (int)($_POST['id'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($nome !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if ($id > 0) {
                $stmt = $pdo->prepare(
                    'UPDATE pessoas SET nome = ?, email = ? WHERE id = ?'
                );
                $stmt->execute([$nome, $email, $id]);
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO pessoas (nome, email) VALUES (?, ?)'
                );
                $stmt->execute([$nome, $email]);
            }
        }
    }

    header('Location: /', true, 303);
    exit;
}

$editando = null;

if ($pdo && isset($_GET['editar'])) {
    $stmt = $pdo->prepare('SELECT * FROM pessoas WHERE id = ?');
    $stmt->execute([(int)$_GET['editar']]);
    $editando = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

$pessoas = $pdo
    ? $pdo->query('SELECT * FROM pessoas ORDER BY id DESC')
        ->fetchAll(PDO::FETCH_ASSOC)
    : [];

function e($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro de Pessoas | PHP CRUD</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #172033;
        }

        main {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }

        h1 { margin-bottom: 8px; }

        .sub {
            color: #64748b;
            margin-bottom: 24px;
        }

        .card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 24px;
            box-shadow: 0 4px 16px #0f172a0d;
        }

        label {
            display: block;
            margin: 12px 0 6px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 16px;
        }

        button, .link {
            display: inline-block;
            border: 0;
            border-radius: 7px;
            padding: 10px 14px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            margin-top: 14px;
        }

        button {
            background: #2563eb;
            color: white;
        }

        .link {
            background: #e2e8f0;
            color: #172033;
        }

        .danger {
            background: #fee2e2;
            color: #991b1b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            text-align: left;
            padding: 12px 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        th { color: #475569; }

        .error { color: #b91c1c; }

        .actions {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .actions form { margin: 0; }

        @media (max-width: 600px) {
            main {
                margin: 10px auto;
                padding: 12px;
            }

            .card {
                padding: 14px;
                overflow-x: auto;
            }

            th, td { padding: 8px 5px; }
        }
    </style>
</head>
<body>
<main>
    <h1>Cadastro de Pessoas</h1>
    <p class="sub">PHP + MySQL + Docker | Projeto DevOps</p>

    <?php if ($error): ?>
        <div class="card error"><?= e($error) ?></div>
    <?php else: ?>
        <section class="card">
            <h2><?= $editando ? 'Editar pessoa' : 'Nova pessoa' ?></h2>

            <form method="post">
                <input type="hidden" name="acao" value="salvar">
                <input type="hidden" name="id"
                       value="<?= e($editando['id'] ?? 0) ?>">

                <label for="nome">Nome</label>
                <input
                    id="nome"
                    name="nome"
                    maxlength="120"
                    required
                    value="<?= e($editando['nome'] ?? '') ?>"
                >

                <label for="email">E-mail</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    maxlength="180"
                    required
                    value="<?= e($editando['email'] ?? '') ?>"
                >

                <button type="submit">Salvar</button>

                <?php if ($editando): ?>
                    <a class="link" href="/">Cancelar</a>
                <?php endif; ?>
            </form>
        </section>

        <section class="card">
            <h2>Pessoas cadastradas (<?= count($pessoas) ?>)</h2>

            <?php if (!$pessoas): ?>
                <p>Nenhum cadastro ainda. Adicione a primeira pessoa acima.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($pessoas as $pessoa): ?>
                        <tr>
                            <td><?= e($pessoa['nome']) ?></td>
                            <td><?= e($pessoa['email']) ?></td>
                            <td>
                                <div class="actions">
                                    <a
                                        class="link"
                                        href="?editar=<?= (int)$pessoa['id'] ?>"
                                    >Editar</a>

                                    <form
                                        method="post"
                                        onsubmit="return confirm('Excluir este cadastro?')"
                                    >
                                        <input
                                            type="hidden"
                                            name="acao"
                                            value="excluir"
                                        >
                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int)$pessoa['id'] ?>"
                                        >
                                        <button
                                            class="danger"
                                            type="submit"
                                        >Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</main>
</body>
</html>
```

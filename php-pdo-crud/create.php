
<?php
//Arquivo para criar um novo usuário no sistema.
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';

$pdo = getPDO();
$errors = [];
$name = '';
$sobrenome = '';
$email = '';
$endereco = '';
$telefone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Verifica o token CSRF
    verify_csrf_or_die();
    $name  = trim($_POST['name'] ?? '');
    $sobrenome  = trim($_POST['sobrenome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($name === '')  $errors[] = 'Nome é obrigatório.';
    if ($sobrenome === '')  $errors[] = 'Sobrenome é obrigatório.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido.';
    if ($endereco === '')  $errors[] = 'Endereço é obrigatório.';
    if ($telefone === '')  $errors[] = 'Telefone é obrigatório.';
    if (strlen($pass) < 4) $errors[] = 'Senha deve ter pelo menos 4 caracteres.';

    if (!$errors) {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (name, sobrenome, email, endereco, telefone, password_hash) VALUES (:n, :o, :e, :q, :r, :p)");
            $stmt->execute([':n' => $name, ':o' => $sobrenome, ':e' => $email, ':q' => $endereco, ':r' => $telefone, ':p' => $hash]);
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors[] = 'Email já cadastrado.';
            } else {
                $errors[] = 'Erro ao inserir: ' . $e->getMessage();
            }
        }
    }
}

require_once __DIR__ . '/header.php';
$token = csrf_token();
?>
<div class="row">
  <div class="col-lg-6">
    <h2>Novo Usuário</h2>
    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errors as $err): ?>
            <li><?= htmlspecialchars($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" autocomplete="on">
      <input type="hidden" name="csrf_token" value="<?= $token ?>">
      <div class="mb-3">
        <label class="form-label">Nome</label>
        <input type="text" name="name" class="form-control" placeholder="Digite seu nome" value="<?= htmlspecialchars($name) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Sobrenome</label>
        <input type="text" name="sobrenome" class="form-control" placeholder="Digite seu sobrenome" value="<?= htmlspecialchars($sobrenome) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" placeholder="exemplo@email.com" value="<?= htmlspecialchars($email) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Endereço</label>
        <input type="text" name="endereco" class="form-control" value="<?= htmlspecialchars($endereco) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Telefone</label>
        <input type="tel" name="telefone" class="form-control" pattern="\(\d{2})\d{4,5}-\d{4}" placeholder="(xx)xxxxx-xxxx" value="<?= htmlspecialchars($telefone) ?>" required>
      </div>
        <label class="form-label">Senha</label>
        <input type="password" name="password" class="form-control" placeholder="Crie uma senha" required>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-primary">Salvar</button>
        <a class="btn btn-secondary" href="index.php">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>

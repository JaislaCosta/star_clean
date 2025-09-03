
<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';

$pdo = getPDO();
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
$stmt->execute([':id' => $id]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(404);
    exit('Usuário não encontrado.');
}

$errors = [];
$name = $user['name'];
$sobrenome = $user['sobrenome'];
$email = $user['email'];
$endereco = $user['endereco'];
$telefone = $user['telefone'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    if (!$errors) {
        try {
            if ($pass !== '') {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE usuarios SET name=:n, sobrenome=:o, email=:e, endereco=:q, telefone=:r, password_hash=:p WHERE id=:id");
                $stmt->execute([':n'=>$name,':o'=>$sobrenome, ':e'=>$email, ':q'=>$endereco, ':r'=>$telefone, ':p'=>$hash, ':id'=>$id]);
            } else {
                $stmt = $pdo->prepare("UPDATE usuarios SET name=:n, email=:e, sobrenome=:o, endereco=:q, telefone=:r WHERE id=:id");
                $stmt->execute([':n'=>$name, ':o'=>$sobrenome, ':e'=>$email, ':q'=>$endereco, ':r'=>$telefone, ':id'=>$id]);
            }
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors[] = 'Email já cadastrado.';
            } else {
                $errors[] = 'Erro ao atualizar: ' . $e->getMessage();
            }
        }
    }
}

require_once __DIR__ . '/header.php';
$token = csrf_token();
?>
<div class="row">
  <div class="col-lg-6">
    <h2>Editar Usuário #<?= (int)$user['id'] ?></h2>
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
        <label>Cep:
        <input name="cep" type="text" id="cep" value="" size="10" maxlength="9"
               onblur="pesquisacep(this.value);" /></label><br />
        <label>Rua:
        <input name="rua" type="text" id="rua" size="60" /></label><br />
        <label>Bairro:
        <input name="bairro" type="text" id="bairro" size="40" /></label><br />
        <label>Cidade:
        <input name="cidade" type="text" id="cidade" size="40" /></label><br />
        <label>Estado:
        <input name="uf" type="text" id="uf" size="2" /></label><br />
        <label>IBGE:
        <input name="ibge" type="text" id="ibge" size="8" /></label><br />
      </div>
      <div class="mb-3">
        <label class="form-label">Telefone</label>
        <input type="text" name="telefone" class="form-control" pattern="\(\d{2})\d{4,5}-\d{4}" value="<?= htmlspecialchars($telefone) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Senha (deixe em branco para manter)</label>
        <input type="password" name="password" class="form-control" placeholder="Mudar senha">
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-primary">Salvar</button>
        <a class="btn btn-secondary" href="index.php">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>

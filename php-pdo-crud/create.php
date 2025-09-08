<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';

$pdo = getPDO();
$errors = [];
$success = '';
$name = $sobrenome = $email = $telefone = $data_nascimento = '';
$cep = $logradouro = $bairro = $cidade = $uf = $numero = $complemento = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf_or_die();

  // Dados do usuário
  $name            = trim($_POST['name'] ?? '');
  $sobrenome       = trim($_POST['sobrenome'] ?? '');
  $email           = trim($_POST['email'] ?? '');
  $telefone        = trim($_POST['telefone'] ?? '');
  $data_nascimento = trim($_POST['data_nascimento'] ?? '');
  $pass            = $_POST['password'] ?? '';
  $confirmPass     = $_POST['confirm_password'] ?? '';

  // Dados do endereço
  $cep        = trim($_POST['cep'] ?? '');
  $logradouro = trim($_POST['logradouro'] ?? '');
  $bairro     = trim($_POST['bairro'] ?? '');
  $cidade     = trim($_POST['cidade'] ?? '');
  $uf         = trim($_POST['uf'] ?? '');
  $numero     = trim($_POST['numero'] ?? '');
  $complemento = trim($_POST['complemento'] ?? '');

  // Validações
  if ($name === '') $errors[] = 'Nome é obrigatório.';
  if ($sobrenome === '') $errors[] = 'Sobrenome é obrigatório.';
  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido.';
  if ($telefone === '') $errors[] = 'Telefone é obrigatório.';
  if ($data_nascimento === '') $errors[] = 'Data de nascimento é obrigatória.';

  // Validação senha forte
  $senhaForte = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
  if (!preg_match($senhaForte, $pass)) {
    $errors[] = 'A senha deve ter pelo menos 8 caracteres, incluindo letra maiúscula, minúscula, número e caractere especial.';
  }
  if ($pass !== $confirmPass) $errors[] = 'As senhas não conferem.';

  if (!$errors) {
    $hash = password_hash($pass, PASSWORD_DEFAULT);

    try {
      $pdo->beginTransaction();

      // Insere usuário
      $stmtUser = $pdo->prepare("INSERT INTO usuarios (name, sobrenome, email, telefone, data_nascimento, password_hash) 
                                       VALUES (:n, :o, :e, :t, :d, :p)");
      $stmtUser->execute([
        ':n' => $name,
        ':o' => $sobrenome,
        ':e' => $email,
        ':t' => $telefone,
        ':d' => $data_nascimento,
        ':p' => $hash
      ]);

      $userId = $pdo->lastInsertId();

      // Insere endereço
      $stmtEnd = $pdo->prepare("INSERT INTO enderecos (id_usuario, cep, logradouro, bairro, cidade, uf, numero, complemento) 
                                      VALUES (:u, :c, :l, :b, :ci, :uf, :n, :co)");
      $stmtEnd->execute([
        ':u' => $userId,
        ':c' => $cep,
        ':l' => $logradouro,
        ':b' => $bairro,
        ':ci' => $cidade,
        ':uf' => $uf,
        ':n' => $numero,
        ':co' => $complemento
      ]);

      $pdo->commit();

      // Mensagem de sucesso
      $success = 'Usuário cadastrado com sucesso!';
      // Limpar campos do formulário
      $name = $sobrenome = $email = $telefone = $data_nascimento = '';
      $cep = $logradouro = $bairro = $cidade = $uf = $numero = $complemento = '';
    } catch (PDOException $e) {
      $pdo->rollBack();
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

    <!-- Mensagem de sucesso -->
    <?php if ($success): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Mensagens de erro -->
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

      <!-- Nome e Sobrenome -->
      <div class="mb-3">
        <label class="form-label">Nome</label>
        <input type="text" name="name" class="form-control" placeholder="Digite seu nome" value="<?= htmlspecialchars($name) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Sobrenome</label>
        <input type="text" name="sobrenome" class="form-control" placeholder="Digite seu sobrenome" value="<?= htmlspecialchars($sobrenome) ?>" required>
      </div>

      <!-- Email e Data de Nascimento -->
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" placeholder="exemplo@email.com" value="<?= htmlspecialchars($email) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Data de Nascimento</label>
        <input type="date" name="data_nascimento" class="form-control" value="<?= htmlspecialchars($data_nascimento) ?>" required>
      </div>

      <!-- Endereço -->
      <h4>Endereço</h4>
      <div class="mb-3">
        <label class="form-label">CEP</label>
        <input type="text" name="cep" id="cep" class="form-control" placeholder="00000-000" maxlength="9" onblur="pesquisacep(this.value);" value="<?= htmlspecialchars($cep) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Logradouro</label>
        <input type="text" name="logradouro" id="logradouro" class="form-control" placeholder="Rua / Avenida" value="<?= htmlspecialchars($logradouro) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Bairro</label>
        <input type="text" name="bairro" id="bairro" class="form-control" placeholder="Bairro" value="<?= htmlspecialchars($bairro) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Cidade</label>
        <input type="text" name="cidade" id="cidade" class="form-control" placeholder="Cidade" value="<?= htmlspecialchars($cidade) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">UF</label>
        <input type="text" name="uf" id="uf" class="form-control" placeholder="Estado (UF)" maxlength="2" value="<?= htmlspecialchars($uf) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Número</label>
        <input type="text" name="numero" id="numero" class="form-control" placeholder="Número" value="<?= htmlspecialchars($numero) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Complemento (opcional)</label>
        <input type="text" name="complemento" id="complemento" class="form-control" placeholder="Complemento" value="<?= htmlspecialchars($complemento) ?>">
      </div>

      <!-- Telefone e Senha -->
      <div class="mb-3">
        <label class="form-label">Telefone</label>
        <input type="tel" name="telefone" id="telefone" class="form-control" placeholder="(00) 00000-0000" value="<?= htmlspecialchars($telefone) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Senha</label>
        <input type="text" name="password" class="form-control" placeholder="Crie uma senha" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Confirmar Senha</label>
        <input type="password" name="confirm_password" class="form-control" placeholder="Repita a senha" required>
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-primary">Salvar</button>
        <a class="btn btn-secondary" href="index.php">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
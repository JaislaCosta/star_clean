
<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/header.php';

$pdo = getPDO();

$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE name LIKE :q OR email LIKE :q ORDER BY id DESC");
    $stmt->execute([':q' => '%' . $search . '%']);
} else {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
}
$users = $stmt->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="mb-0">Usuários</h2>
  <a href="create.php" class="btn btn-primary">+ Novo Usuário</a>
</div>

<form class="row g-2 mb-3" method="get">
  <div class="col-auto">
    <input type="text" class="form-control" name="q" placeholder="Buscar por nome ou email" value="<?= htmlspecialchars($search) ?>">
  </div>
  <div class="col-auto">
    <button class="btn btn-outline-secondary">Buscar</button>
  </div>
  <?php if ($search !== ''): ?>
  <div class="col-auto">
    <a class="btn btn-link" href="index.php">Limpar</a>
  </div>
  <?php endif; ?>
</form>

<div class="card">
  <div class="table-responsive">
    <table class="table table-striped mb-0">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Nome</th>
          <th>Sobrenome</th>
          <th>Email</th>
          <th>Endereco</th>
          <th>Telefone</th>
          <th>Cadastrado em</th>
          <th style="width:160px">Ações</th>
        </tr>
      </thead>
      <tbody>
      <?php if (!$users): ?>
        <tr><td colspan="5" class="text-center">Nenhum usuário encontrado.</td></tr>
      <?php else: foreach ($users as $u): ?>
        <tr>
          <td><?= (int)$u['id'] ?></td>
          <td><?= htmlspecialchars($u['name']) ?></td>
          <td><?= htmlspecialchars($u['sobrenome']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= htmlspecialchars($u['endereco']) ?></td>
          <td><?= htmlspecialchars($u['telefone']) ?></td>
          <td><?= htmlspecialchars($u['created_at']) ?></td>
          <td>
            <a class="btn btn-sm btn-warning" href="edit.php?id=<?= (int)$u['id'] ?>">Editar</a>
            <form action="delete.php" method="post" class="d-inline">
              <?php
              require_once __DIR__ . '/csrf.php';
              $token = csrf_token();
              ?>
              <input type="hidden" name="csrf_token" value="<?= $token ?>">
              <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
              <button class="btn btn-sm btn-danger" onclick="return confirm('Excluir este usuário?')">Excluir</button>
            </form>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>

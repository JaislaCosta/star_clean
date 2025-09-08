<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/header.php';

$pdo = getPDO();

// Busca por pesquisa
$search = trim($_GET['q'] ?? '');
if ($search !== '') {
  $stmt = $pdo->prepare("
        SELECT u.*, e.cep, e.logradouro, e.bairro, e.cidade, e.uf, e.numero, e.complemento
        FROM usuarios u
        LEFT JOIN enderecos e ON u.id = e.id_usuario
        WHERE u.name LIKE :q1 OR u.email LIKE :q2
        ORDER BY u.id DESC
    ");
  $stmt->execute([
    ':q1' => '%' . $search . '%',
    ':q2' => '%' . $search . '%',
  ]);
} else {
  $stmt = $pdo->query("
        SELECT u.*, e.cep, e.logradouro, e.bairro, e.cidade, e.uf, e.numero, e.complemento
        FROM usuarios u
        LEFT JOIN enderecos e ON u.id = e.id_usuario
        ORDER BY u.id DESC
    ");
}

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <button class="btn btn-primary">Buscar</button>
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
          <th>Data Nascimento</th>
          <th>Endereço</th>
          <th>Telefone</th>
          <th>Cadastrado em</th>
          <th style="width:160px">Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$users): ?>
          <tr>
            <td colspan="9" class="text-center">Nenhum usuário encontrado.</td>
          </tr>
          <?php else: foreach ($users as $u): ?>
            <tr>
              <td><?= (int)$u['id'] ?></td>
              <td><?= htmlspecialchars($u['name']) ?></td>
              <td><?= htmlspecialchars($u['sobrenome']) ?></td>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td><?= htmlspecialchars(date('d/m/Y', strtotime($u['data_nascimento']))) ?></td>
              <td>
                <?= htmlspecialchars($u['cep'] ?? '') ?> -
                <?= htmlspecialchars($u['logradouro'] ?? '') ?>,
                <?= htmlspecialchars($u['bairro'] ?? '') ?>,
                <?= htmlspecialchars($u['cidade'] ?? '') ?> -
                <?= htmlspecialchars($u['uf'] ?? '') ?>,
                <?= htmlspecialchars($u['numero'] ?? '') ?>
                <?= $u['complemento'] ? '- ' . htmlspecialchars($u['complemento']) : '' ?>
              </td>
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
        <?php endforeach;
        endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
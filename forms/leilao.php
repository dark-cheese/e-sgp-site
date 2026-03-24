<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php?erro=2');
    exit;
}
include_once '../conexao.php';

$titulo = "Leilão";
$editando = false;
$id = $edital = $data_leilao = $status = $observacoes = '';
$incluir_item = isset($_GET['incluir']) ? $_GET['incluir'] : null;

if (isset($_GET['editar'])) {
    $editando = true;
    $id = $_GET['editar'];
    $sql = "SELECT * FROM leiloes WHERE id = $id";
    $res = mysqli_query($conexao, $sql);
    $dados = mysqli_fetch_assoc($res);
    $edital = $dados['edital'];
    $data_leilao = $dados['data_leilao'];
    $status = $dados['status'];
    $observacoes = $dados['observacoes'];
}

include_once '../includes/cabecalho.php';
include_once '../includes/menu.php';
?>

<header class="header">
    <div class="page-title">
        <h1><?php echo $editando ? 'Editar' : 'Novo'; ?> Leilão</h1>
        <div class="breadcrumb">Leilões / <?php echo $editando ? 'Editar' : 'Novo'; ?></div>
    </div>
    <a href="../pages/leiloes.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Voltar</a>
</header>

<div class="card-form">
    <form method="POST" action="../process/leilao.php">
        <?php if($editando): ?>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="editar" value="1">
        <?php endif; ?>
        
        <?php if($incluir_item): ?>
            <input type="hidden" name="incluir_item" value="<?php echo $incluir_item; ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label>Edital *</label>
            <input type="text" name="edital" value="<?php echo $edital; ?>" placeholder="Ex: Edital 01/2026" required>
        </div>
        <div class="form-group">
            <label>Data do Leilão</label>
            <input type="date" name="data_leilao" value="<?php echo $data_leilao; ?>">
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status">
                <option value="agendado" <?php if($status == 'agendado') echo 'selected'; ?>>Agendado</option>
                <option value="em_andamento" <?php if($status == 'em_andamento') echo 'selected'; ?>>Em Andamento</option>
                <option value="concluido" <?php if($status == 'concluido') echo 'selected'; ?>>Concluído</option>
                <option value="cancelado" <?php if($status == 'cancelado') echo 'selected'; ?>>Cancelado</option>
            </select>
        </div>
        <div class="form-group">
            <label>Observações</label>
            <textarea name="observacoes" rows="3"><?php echo $observacoes; ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
    </form>
</div>

<?php include_once '../includes/rodape.php'; ?>
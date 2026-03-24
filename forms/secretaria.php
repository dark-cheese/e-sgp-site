<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php?erro=2');
    exit;
}
include_once '../conexao.php';

$titulo = "Secretaria";
$editando = false;
$id = $nome = $responsavel = $descricao = '';

if (isset($_GET['editar'])) {
    $editando = true;
    $id = $_GET['editar'];
    $sql = "SELECT * FROM secretarias WHERE id = $id";
    $res = mysqli_query($conexao, $sql);
    $dados = mysqli_fetch_assoc($res);
    $nome = $dados['nome'];
    $responsavel = $dados['responsavel'];
    $descricao = $dados['descricao'];
}

include_once '../includes/cabecalho.php';
include_once '../includes/menu.php';
?>

<header class="header">
    <div class="page-title">
        <h1><?php echo $editando ? 'Editar' : 'Nova'; ?> Secretaria</h1>
        <div class="breadcrumb">Secretarias / <?php echo $editando ? 'Editar' : 'Nova'; ?></div>
    </div>
    <a href="../pages/secretarias.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Voltar</a>
</header>

<div class="card-form">
    <form method="POST" action="../process/secretaria.php">
        <?php if($editando): ?>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="editar" value="1">
        <?php endif; ?>
        
        <div class="form-group">
            <label>Nome da Secretaria *</label>
            <input type="text" name="nome" value="<?php echo $nome; ?>" required>
        </div>
        <div class="form-group">
            <label>Responsável</label>
            <input type="text" name="responsavel" value="<?php echo $responsavel; ?>">
        </div>
        <div class="form-group">
            <label>Descrição</label>
            <textarea name="descricao" rows="3"><?php echo $descricao; ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
    </form>
</div>

<?php include_once '../includes/rodape.php'; ?>
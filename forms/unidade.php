<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php?erro=2');
    exit;
}
include_once '../conexao.php';

$titulo = "Unidade";
$editando = false;
$id = $nome = $endereco = $responsavel = $secretaria_id = '';

if (isset($_GET['editar'])) {
    $editando = true;
    $id = $_GET['editar'];
    $sql = "SELECT * FROM unidades WHERE id = $id";
    $res = mysqli_query($conexao, $sql);
    $dados = mysqli_fetch_assoc($res);
    $nome = $dados['nome'];
    $endereco = $dados['endereco'];
    $responsavel = $dados['responsavel'];
    $secretaria_id = $dados['secretaria_id'];
}

$sql_secretarias = "SELECT * FROM secretarias ORDER BY nome";
$res_secretarias = mysqli_query($conexao, $sql_secretarias);

include_once '../includes/cabecalho.php';
include_once '../includes/menu.php';
?>

<header class="header">
    <div class="page-title">
        <h1><?php echo $editando ? 'Editar' : 'Nova'; ?> Unidade</h1>
        <div class="breadcrumb">Unidades / <?php echo $editando ? 'Editar' : 'Nova'; ?></div>
    </div>
    <a href="../pages/unidades.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Voltar</a>
</header>

<div class="card-form">
    <form method="POST" action="../process/unidade.php">
        <?php if($editando): ?>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="editar" value="1">
        <?php endif; ?>
        
        <div class="form-group">
            <label>Nome da Unidade *</label>
            <input type="text" name="nome" value="<?php echo $nome; ?>" required>
        </div>
        <div class="form-group">
            <label>Secretaria *</label>
            <select name="secretaria_id" required>
                <option value="">Selecione...</option>
                <?php while($sec = mysqli_fetch_assoc($res_secretarias)): ?>
                <option value="<?php echo $sec['id']; ?>" <?php if($sec['id'] == $secretaria_id) echo 'selected'; ?>>
                    <?php echo $sec['nome']; ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Endereço</label>
            <input type="text" name="endereco" value="<?php echo $endereco; ?>">
        </div>
        <div class="form-group">
            <label>Responsável</label>
            <input type="text" name="responsavel" value="<?php echo $responsavel; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
    </form>
</div>

<?php include_once '../includes/rodape.php'; ?>
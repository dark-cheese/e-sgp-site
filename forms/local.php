<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php?erro=2');
    exit;
}
include_once '../conexao.php';

$titulo = "Local";
$editando = false;
$id = $nome = $descricao = $unidade_id = '';

if (isset($_GET['editar'])) {
    $editando = true;
    $id = $_GET['editar'];
    $sql = "SELECT * FROM locais WHERE id = $id";
    $res = mysqli_query($conexao, $sql);
    $dados = mysqli_fetch_assoc($res);
    $nome = $dados['nome'];
    $descricao = $dados['descricao'];
    $unidade_id = $dados['unidade_id'];
}

$sql_unidades = "SELECT u.*, s.nome as secretaria_nome 
                 FROM unidades u
                 JOIN secretarias s ON u.secretaria_id = s.id
                 ORDER BY s.nome, u.nome";
$res_unidades = mysqli_query($conexao, $sql_unidades);

include_once '../includes/cabecalho.php';
include_once '../includes/menu.php';
?>

<header class="header">
    <div class="page-title">
        <h1><?php echo $editando ? 'Editar' : 'Novo'; ?> Local</h1>
        <div class="breadcrumb">Locais / <?php echo $editando ? 'Editar' : 'Novo'; ?></div>
    </div>
    <a href="../pages/locais.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Voltar</a>
</header>

<div class="card-form">
    <form method="POST" action="../process/local.php">
        <?php if($editando): ?>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="editar" value="1">
        <?php endif; ?>
        
        <div class="form-group">
            <label>Nome do Local *</label>
            <input type="text" name="nome" value="<?php echo $nome; ?>" placeholder="Ex: Sala 1, Biblioteca, Almoxarifado..." required>
        </div>
        <div class="form-group">
            <label>Unidade *</label>
            <select name="unidade_id" required>
                <option value="">Selecione a unidade...</option>
                <?php while($un = mysqli_fetch_assoc($res_unidades)): ?>
                <option value="<?php echo $un['id']; ?>" <?php if($un['id'] == $unidade_id) echo 'selected'; ?>>
                    <?php echo $un['secretaria_nome'] . ' / ' . $un['nome']; ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Descrição</label>
            <textarea name="descricao" rows="3" placeholder="Observações sobre o local..."><?php echo $descricao; ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
    </form>
</div>

<?php include_once '../includes/rodape.php'; ?>
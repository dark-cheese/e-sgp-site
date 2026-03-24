<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php?erro=2');
    exit;
}
include_once '../conexao.php';

$titulo = "Item Patrimonial";
$editando = false;
$id = $numero_patrimonio = $descricao = $marca = $modelo = $numero_serie = '';
$estado = 'bom';
$data_aquisicao = '';
$valor = '';
$local_id = '';
$observacoes = '';

// Gerar próximo número de patrimônio
$sql_ultimo = "SELECT MAX(CAST(SUBSTRING_INDEX(numero_patrimonio, '.', -1) AS UNSIGNED)) as ultimo FROM itens";
$res_ultimo = mysqli_query($conexao, $sql_ultimo);
$row_ultimo = mysqli_fetch_assoc($res_ultimo);
$proximo = ($row_ultimo['ultimo'] ?? 0) + 1;
$numero_patrimonio_auto = date('Y') . '.' . str_pad($proximo, 3, '0', STR_PAD_LEFT);

if (isset($_GET['editar'])) {
    $editando = true;
    $id = $_GET['editar'];
    $sql = "SELECT * FROM itens WHERE id = $id";
    $res = mysqli_query($conexao, $sql);
    $dados = mysqli_fetch_assoc($res);
    $numero_patrimonio = $dados['numero_patrimonio'];
    $descricao = $dados['descricao'];
    $marca = $dados['marca'];
    $modelo = $dados['modelo'];
    $numero_serie = $dados['numero_serie'];
    $estado = $dados['estado'];
    $data_aquisicao = $dados['data_aquisicao'];
    $valor = $dados['valor'];
    $local_id = $dados['local_id'];
    $observacoes = $dados['observacoes'];
}

$sql_locais = "SELECT l.*, u.nome as unidade_nome, s.nome as secretaria_nome 
               FROM locais l
               JOIN unidades u ON l.unidade_id = u.id
               JOIN secretarias s ON u.secretaria_id = s.id
               ORDER BY s.nome, u.nome, l.nome";
$res_locais = mysqli_query($conexao, $sql_locais);

include_once '../includes/cabecalho.php';
include_once '../includes/menu.php';
?>

<header class="header">
    <div class="page-title">
        <h1><?php echo $editando ? 'Editar' : 'Novo'; ?> Item</h1>
        <div class="breadcrumb">Itens / <?php echo $editando ? 'Editar' : 'Novo'; ?></div>
    </div>
    <a href="../pages/itens.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Voltar</a>
</header>

<div class="card-form">
    <form method="POST" action="../process/item.php">
        <?php if($editando): ?>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="editar" value="1">
        <?php endif; ?>
        
        <div class="form-group">
            <label>Número de Patrimônio</label>
            <input type="text" name="numero_patrimonio" value="<?php echo $editando ? $numero_patrimonio : $numero_patrimonio_auto; ?>" <?php echo $editando ? '' : 'readonly'; ?> style="<?php echo $editando ? '' : 'background:#F5F7FA;'; ?>">
            <?php if(!$editando): ?>
                <small style="color: #7F8C8D;">Gerado automaticamente</small>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label>Descrição *</label>
            <input type="text" name="descricao" value="<?php echo $descricao; ?>" required>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Marca</label>
                <input type="text" name="marca" value="<?php echo $marca; ?>">
            </div>
            <div class="form-group">
                <label>Modelo</label>
                <input type="text" name="modelo" value="<?php echo $modelo; ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Nº Série</label>
                <input type="text" name="numero_serie" value="<?php echo $numero_serie; ?>">
            </div>
            <div class="form-group">
                <label>Estado *</label>
                <select name="estado" required>
                    <option value="otimo" <?php if($estado == 'otimo') echo 'selected'; ?>>Ótimo</option>
                    <option value="bom" <?php if($estado == 'bom') echo 'selected'; ?>>Bom</option>
                    <option value="regular" <?php if($estado == 'regular') echo 'selected'; ?>>Regular</option>
                    <option value="ruim" <?php if($estado == 'ruim') echo 'selected'; ?>>Ruim</option>
                    <option value="inservivel" <?php if($estado == 'inservivel') echo 'selected'; ?>>Inservível</option>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Data Aquisição</label>
                <input type="date" name="data_aquisicao" value="<?php echo $data_aquisicao; ?>">
            </div>
            <div class="form-group">
                <label>Valor (R$)</label>
                <input type="number" step="0.01" name="valor" value="<?php echo $valor; ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label>Local *</label>
            <select name="local_id" required>
                <option value="">Selecione o local...</option>
                <?php while($loc = mysqli_fetch_assoc($res_locais)): ?>
                <option value="<?php echo $loc['id']; ?>" <?php if($loc['id'] == $local_id) echo 'selected'; ?>>
                    <?php echo $loc['secretaria_nome'] . ' / ' . $loc['unidade_nome'] . ' / ' . $loc['nome']; ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Observações</label>
            <textarea name="observacoes" rows="3"><?php echo $observacoes; ?></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">Salvar Item</button>
    </form>
</div>

<?php include_once '../includes/rodape.php'; ?>
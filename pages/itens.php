<?php
$titulo = "Itens Patrimoniais";
include_once '../includes/auth.php';
verificarLogin();
include_once '../conexao.php';

$sql = "SELECT i.*, l.nome as local_nome, u.nome as unidade_nome, s.nome as secretaria_nome
        FROM itens i
        JOIN locais l ON i.local_id = l.id
        JOIN unidades u ON l.unidade_id = u.id
        JOIN secretarias s ON u.secretaria_id = s.id
        ORDER BY i.numero_patrimonio";
$resultado = mysqli_query($conexao, $sql);

$sql_total = "SELECT COUNT(*) as total, SUM(valor) as total_valor FROM itens";
$res_total = mysqli_query($conexao, $sql_total);
$totais = mysqli_fetch_assoc($res_total);

include_once '../includes/cabecalho.php';
include_once '../includes/menu.php';
?>

<header class="header">
    <div class="page-title">
        <h1>Itens Patrimoniais</h1>
        <div class="breadcrumb">Início / Itens</div>
    </div>
    <a href="../forms/item.php" class="btn btn-primary"><i class="fas fa-plus"></i> Novo Item</a>
</header>

<div class="stats-grid" style="grid-template-columns: repeat(2, 1fr);">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Total de Itens</h3>
            <div class="stat-number"><?php echo $totais['total']; ?></div>
        </div>
        <div class="stat-icon"><i class="fas fa-boxes"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Valor Total</h3>
            <div class="stat-number">R$ <?php echo number_format($totais['total_valor'], 2, ',', '.'); ?></div>
        </div>
        <div class="stat-icon"><i class="fas fa-coins"></i></div>
    </div>
</div>

<div class="filter-section">
    <div class="filter-row">
        <input type="text" id="busca" placeholder="Buscar por nº patrimônio ou descrição..." onkeyup="filtrarTabela()">
        <select id="filtro_status" onchange="filtrarPorStatus()">
            <option value="">Todos os estados</option>
            <option value="otimo">Ótimo</option>
            <option value="bom">Bom</option>
            <option value="regular">Regular</option>
            <option value="ruim">Ruim</option>
            <option value="inservivel">Inservível</option>
        </select>
        <button class="btn btn-primary" onclick="filtrarTabela()">Filtrar</button>
    </div>
</div>

<table id="tabela">
    <thead>
        <tr>
            <th>Nº Patrimônio</th>
            <th>Descrição</th>
            <th>Localização</th>
            <th>Estado</th>
            <th>Valor</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($resultado)): 
            $estados = ['otimo'=>'Ótimo', 'bom'=>'Bom', 'regular'=>'Regular', 'ruim'=>'Ruim', 'inservivel'=>'Inservível'];
        ?>
        <tr data-status="<?php echo $row['estado']; ?>">
            <td><strong><?php echo $row['numero_patrimonio']; ?></strong></td>
            <td>
                <?php echo $row['descricao']; ?><br>
                <small style="color: #7F8C8D;"><?php echo $row['marca']; ?> <?php echo $row['modelo']; ?></small>
            </td>
            <td><?php echo $row['secretaria_nome']; ?> / <?php echo $row['unidade_nome']; ?> / <?php echo $row['local_nome']; ?></td>
            <td>
                <span class="status status-<?php echo $row['estado']; ?>">
                    <?php echo $estados[$row['estado']]; ?>
                </span>
            </td>
            <td>R$ <?php echo number_format($row['valor'], 2, ',', '.'); ?></td>
            <td>
                <a href="../forms/item.php?editar=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                <a href="../process/item.php?excluir=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir?')"><i class="fas fa-trash"></i></a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<script>
function filtrarTabela() {
    let input = document.getElementById('busca').value.toLowerCase();
    let rows = document.querySelectorAll('#tabela tbody tr');
    let statusFiltro = document.getElementById('filtro_status').value;
    
    rows.forEach(row => {
        let texto = row.cells[0].textContent.toLowerCase() + row.cells[1].textContent.toLowerCase();
        let status = row.getAttribute('data-status');
        let passaStatus = (statusFiltro === '' || status === statusFiltro);
        let passaBusca = texto.includes(input);
        
        row.style.display = (passaBusca && passaStatus) ? '' : 'none';
    });
}

function filtrarPorStatus() {
    filtrarTabela();
}
</script>

<?php include_once '../includes/rodape.php'; ?>
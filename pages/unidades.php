<?php
$titulo = "Unidades";
include_once '../includes/auth.php';
verificarLogin();
include_once '../conexao.php';

// SQL para listar todas as unidades com suas secretarias
$sql = "SELECT u.*, s.nome as secretaria_nome,
        (SELECT COUNT(*) FROM locais WHERE unidade_id = u.id) as total_locais
        FROM unidades u
        JOIN secretarias s ON u.secretaria_id = s.id
        ORDER BY u.nome";
$resultado = mysqli_query($conexao, $sql);

// Estatísticas
$sql_total = "SELECT COUNT(*) as total FROM unidades";
$res_total = mysqli_query($conexao, $sql_total);
$total_unidades = mysqli_fetch_assoc($res_total)['total'];

$sql_escolas = "SELECT COUNT(*) as total FROM unidades u JOIN secretarias s ON u.secretaria_id = s.id WHERE s.nome = 'Educação'";
$res_escolas = mysqli_query($conexao, $sql_escolas);
$total_escolas = mysqli_fetch_assoc($res_escolas)['total'];

$sql_saude = "SELECT COUNT(*) as total FROM unidades u JOIN secretarias s ON u.secretaria_id = s.id WHERE s.nome = 'Saúde'";
$res_saude = mysqli_query($conexao, $sql_saude);
$total_saude = mysqli_fetch_assoc($res_saude)['total'];

include_once '../includes/cabecalho.php';
include_once '../includes/menu.php';
?>

<header class="header">
    <div class="page-title">
        <h1>Unidades Administrativas</h1>
        <div class="breadcrumb">Início / Unidades</div>
    </div>
    <a href="../forms/unidade.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nova Unidade
    </a>
</header>

<div class="filter-section">
    <div class="filter-row">
        <input type="text" id="busca" placeholder="Buscar unidade..." onkeyup="filtrarTabela()">
        <button class="btn btn-primary" onclick="filtrarTabela()">
            <i class="fas fa-search"></i> Buscar
        </button>
    </div>
</div>

<div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 20px;">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Total de Unidades</h3>
            <div class="stat-number"><?php echo $total_unidades; ?></div>
        </div>
        <div class="stat-icon"><i class="fas fa-building"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Escolas</h3>
            <div class="stat-number"><?php echo $total_escolas; ?></div>
        </div>
        <div class="stat-icon"><i class="fas fa-school"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Unidades de Saúde</h3>
            <div class="stat-number"><?php echo $total_saude; ?></div>
        </div>
        <div class="stat-icon"><i class="fas fa-hospital"></i></div>
    </div>
</div>

<table id="tabela">
    <thead>
        <tr>
            <th>Unidade</th>
            <th>Secretaria</th>
            <th>Locais</th>
            <th>Endereço</th>
            <th>Responsável</th>
            <th>Ações</th>
         </tr>
    </thead>
    <tbody>
        <?php if(mysqli_num_rows($resultado) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($resultado)): ?>
            <tr>
                <td>
                    <i class="fas fa-school" style="color: #2A7DE1; margin-right: 8px;"></i>
                    <strong><?php echo htmlspecialchars($row['nome']); ?></strong>
                </td>
                <td>
                    <span style="background: #2A7DE1; color: white; padding: 3px 8px; border-radius: 3px; font-size: 11px;">
                        <?php echo htmlspecialchars($row['secretaria_nome']); ?>
                    </span>
                </td>
                <td><?php echo $row['total_locais']; ?></td>
                <td><?php echo htmlspecialchars($row['endereco']); ?></td>
                <td><?php echo htmlspecialchars($row['responsavel']); ?></td>
                <td>
                    <a href="../forms/unidade.php?editar=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="../process/unidade.php?excluir=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir esta unidade?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align: center; padding: 40px;">
                    <i class="fas fa-school" style="font-size: 48px; color: #E1E8ED; margin-bottom: 10px; display: block;"></i>
                    Nenhuma unidade cadastrada.
                    <br>
                    <a href="../forms/unidade.php" class="btn btn-primary" style="margin-top: 10px;">Cadastrar primeira unidade</a>
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
function filtrarTabela() {
    let input = document.getElementById('busca').value.toLowerCase();
    let rows = document.querySelectorAll('#tabela tbody tr');
    
    rows.forEach(row => {
        let texto = row.cells[0].textContent.toLowerCase() + 
                    row.cells[1].textContent.toLowerCase() +
                    row.cells[3].textContent.toLowerCase();
        row.style.display = texto.includes(input) ? '' : 'none';
    });
}
</script>

<?php include_once '../includes/rodape.php'; ?>
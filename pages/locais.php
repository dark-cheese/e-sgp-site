<?php
$titulo = "Locais";
include_once '../includes/auth.php';
verificarLogin();
include_once '../conexao.php';

$sql = "SELECT l.*, u.nome as unidade_nome, s.nome as secretaria_nome,
        (SELECT COUNT(*) FROM itens WHERE local_id = l.id) as total_itens
        FROM locais l
        JOIN unidades u ON l.unidade_id = u.id
        JOIN secretarias s ON u.secretaria_id = s.id
        ORDER BY l.nome";
$resultado = mysqli_query($conexao, $sql);

include_once '../includes/cabecalho.php';
include_once '../includes/menu.php';
?>

<header class="header">
    <div class="page-title">
        <h1>Locais Físicos</h1>
        <div class="breadcrumb">Início / Locais</div>
    </div>
    <a href="../forms/local.php" class="btn btn-primary"><i class="fas fa-plus"></i> Novo Local</a>
</header>

<div class="filter-section">
    <div class="filter-row">
        <input type="text" id="busca" placeholder="Buscar local..." onkeyup="filtrarTabela()">
        <button class="btn btn-primary" onclick="filtrarTabela()">Buscar</button>
    </div>
</div>

<table id="tabela">
    <thead>
        <tr><th>Local</th><th>Unidade</th><th>Secretaria</th><th>Itens</th><th>Ações</th></tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($resultado)): ?>
        <tr>
            <td><i class="fas fa-door-open" style="color: #2A7DE1;"></i> <?php echo $row['nome']; ?></td>
            <td><?php echo $row['unidade_nome']; ?></td>
            <td><?php echo $row['secretaria_nome']; ?></td>
            <td><?php echo $row['total_itens']; ?></td>
            <td>
                <a href="../forms/local.php?editar=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                <a href="../process/local.php?excluir=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir?')"><i class="fas fa-trash"></i></a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<script>
function filtrarTabela() {
    let input = document.getElementById('busca').value.toLowerCase();
    let rows = document.querySelectorAll('#tabela tbody tr');
    rows.forEach(row => {
        let texto = row.cells[0].textContent.toLowerCase();
        row.style.display = texto.includes(input) ? '' : 'none';
    });
}
</script>

<?php include_once '../includes/rodape.php'; ?>
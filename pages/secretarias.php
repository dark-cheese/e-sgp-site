<?php
$titulo = "Secretarias";
include_once '../includes/auth.php';
verificarLogin();
include_once '../conexao.php';

$sql = "SELECT s.*, 
        (SELECT COUNT(*) FROM unidades WHERE secretaria_id = s.id) as total_unidades
        FROM secretarias s 
        ORDER BY s.nome";
$resultado = mysqli_query($conexao, $sql);

include_once '../includes/cabecalho.php';
include_once '../includes/menu.php';
?>

<header class="header">
    <div class="page-title">
        <h1>Secretarias</h1>
        <div class="breadcrumb">Início / Secretarias</div>
    </div>
    <a href="../forms/secretaria.php" class="btn btn-primary"><i class="fas fa-plus"></i> Nova Secretaria</a>
</header>

<div class="filter-section">
    <div class="filter-row">
        <input type="text" id="busca" placeholder="Buscar secretaria..." onkeyup="filtrarTabela()">
        <button class="btn btn-primary" onclick="filtrarTabela()">Buscar</button>
    </div>
</div>

<table id="tabela">
    <thead>
        <tr><th>ID</th><th>Nome</th><th>Responsável</th><th>Unidades</th><th>Ações</th></tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($resultado)): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['nome']; ?></td>
            <td><?php echo $row['responsavel']; ?></td>
            <td><?php echo $row['total_unidades']; ?></td>
            <td>
                <a href="../forms/secretaria.php?editar=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                <a href="../process/secretaria.php?excluir=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza?')"><i class="fas fa-trash"></i></a>
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
        let texto = row.cells[1].textContent.toLowerCase();
        row.style.display = texto.includes(input) ? '' : 'none';
    });
}
</script>

<?php include_once '../includes/rodape.php'; ?>
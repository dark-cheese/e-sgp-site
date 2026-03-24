<?php
$titulo = "Unidades";
include_once '../includes/auth.php';
verificarLogin();
include_once '../conexao.php';

$sql = "SELECT u.*, s.nome as secretaria_nome,
        (SELECT COUNT(*) FROM locais WHERE unidade_id = u.id) as total_locais
        FROM unidades u
        JOIN secretarias s ON u.secretaria_id = s.id
        ORDER BY u.nome";
$resultado = mysqli_query($conexao, $sql);

include_once '../includes/cabecalho.php';
include_once '../includes/menu.php';
?>

<header class="header">
    <div class="page-title">
        <h1>Unidades Administrativas</h1>
        <div class="breadcrumb">Início / Unidades</div>
    </div>
    <a href="../forms/unidade.php" class="btn btn-primary"><i class="fas fa-plus"></i> Nova Unidade</a>
</header>

<div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
    <?php
    $sql_total = "SELECT COUNT(*) as total FROM unidades";
    $res_total = mysqli_query($conexao, $sql_total);
    $total = mysqli_fetch_assoc($res_total)['total'];
    
    $sql_escolas = "SELECT COUNT(*) as total FROM unidades u JOIN secretarias s ON u.secretaria_id = s.id WHERE s.nome = 'Educação'";
    $res_escolas = mysqli_query($conexao, $sql_escolas);
    $escolas = mysqli_fetch_assoc($res_escolas)['total'];
    ?>
    <div class="stat-card"><div class="stat-info"><h3>Total</h3><div class="stat-number"><?php echo $total; ?></div></div></div>
    <div class="stat-card"><div class="stat-info"><h3>Escolas</h3><div class="stat-number"><?php echo $escolas; ?></div></div></div>
</div>

<table>
    <thead><tr><th>Unidade</th><th>Secretaria</th><th>Locais</th><th>Responsável</th><th>Ações</th></tr></thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($resultado)): ?>
        <tr>
            <td><?php echo $row['nome']; ?></td>
            <td><?php echo $row['secretaria_nome']; ?></td>
            <td><?php echo $row['total_locais']; ?></td>
            <td><?php echo $row['responsavel']; ?></td>
            <td>
                <a href="../forms/unidade.php?editar=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                <a href="../process/unidade.php?excluir=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza?')"><i class="fas fa-trash"></i></a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include_once '../includes/rodape.php'; ?>
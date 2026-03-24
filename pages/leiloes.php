<?php
$titulo = "Leilões";
include_once '../includes/auth.php';
verificarLogin();
include_once '../conexao.php';

$sql_leiloes = "SELECT * FROM leiloes ORDER BY data_leilao DESC";
$res_leiloes = mysqli_query($conexao, $sql_leiloes);

$sql_inserviveis = "SELECT i.id, i.numero_patrimonio, i.descricao, s.nome as secretaria_nome
                    FROM itens i
                    JOIN locais l ON i.local_id = l.id
                    JOIN unidades u ON l.unidade_id = u.id
                    JOIN secretarias s ON u.secretaria_id = s.id
                    WHERE i.estado = 'inservivel'";
$res_inserviveis = mysqli_query($conexao, $sql_inserviveis);

include_once '../includes/cabecalho.php';
include_once '../includes/menu.php';
?>

<header class="header">
    <div class="page-title">
        <h1>Leilões</h1>
        <div class="breadcrumb">Início / Leilões</div>
    </div>
    <a href="../forms/leilao.php" class="btn btn-primary"><i class="fas fa-plus"></i> Novo Leilão</a>
</header>

<div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
    <?php
    $sql_ativos = "SELECT COUNT(*) as total FROM leiloes WHERE status = 'agendado' OR status = 'em_andamento'";
    $res_ativos = mysqli_query($conexao, $sql_ativos);
    $ativos = mysqli_fetch_assoc($res_ativos)['total'];
    
    $sql_inserviveis_total = "SELECT COUNT(*) as total FROM itens WHERE estado = 'inservivel'";
    $res_inserviveis_total = mysqli_query($conexao, $sql_inserviveis_total);
    $inserviveis = mysqli_fetch_assoc($res_inserviveis_total)['total'];
    ?>
    <div class="stat-card">
        <div class="stat-info"><h3>Leilões Ativos</h3><div class="stat-number"><?php echo $ativos; ?></div></div>
    </div>
    <div class="stat-card">
        <div class="stat-info"><h3>Itens Inservíveis</h3><div class="stat-number"><?php echo $inserviveis; ?></div></div>
    </div>
</div>

<h3 style="margin: 20px 0 15px;">Leilões Cadastrados</h3>
<table>
    <thead>
        <tr><th>Edital</th><th>Data</th><th>Status</th><th>Ações</th></tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($res_leiloes)): 
            $status_texto = ['agendado'=>'Agendado', 'em_andamento'=>'Em Andamento', 'concluido'=>'Concluído', 'cancelado'=>'Cancelado'];
            $status_class = ['agendado'=>'status-regular', 'em_andamento'=>'status-bom', 'concluido'=>'status-otimo', 'cancelado'=>'status-ruim'];
        ?>
        <tr>
            <td><?php echo $row['edital']; ?></td>
            <td><?php echo date('d/m/Y', strtotime($row['data_leilao'])); ?></td>
            <td><span class="status <?php echo $status_class[$row['status']]; ?>"><?php echo $status_texto[$row['status']]; ?></span></td>
            <td>
                <a href="../forms/leilao.php?editar=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                <a href="../process/leilao.php?excluir=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza?')"><i class="fas fa-trash"></i></a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<h3 style="margin: 30px 0 15px;">Itens Inservíveis (aptos para leilão)</h3>
<table>
    <thead>
        <tr><th>Nº Patrimônio</th><th>Descrição</th><th>Secretaria</th><th>Ações</th></tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($res_inserviveis)): ?>
        <tr>
            <td><?php echo $row['numero_patrimonio']; ?></td>
            <td><?php echo $row['descricao']; ?></td>
            <td><?php echo $row['secretaria_nome']; ?></td>
            <td>
                <a href="../forms/leilao.php?incluir=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm"><i class="fas fa-gavel"></i> Incluir em Leilão</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include_once '../includes/rodape.php'; ?>
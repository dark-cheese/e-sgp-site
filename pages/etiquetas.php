<?php
$titulo = "Etiquetas";
include_once '../includes/auth.php';
verificarLogin();
include_once '../conexao.php';

$sql_itens = "SELECT i.id, i.numero_patrimonio, i.descricao, l.nome as local_nome
              FROM itens i
              JOIN locais l ON i.local_id = l.id
              ORDER BY i.numero_patrimonio
              LIMIT 10";
$res_itens = mysqli_query($conexao, $sql_itens);

include_once '../includes/cabecalho.php';
include_once '../includes/menu.php';
?>

<header class="header">
    <div class="page-title">
        <h1>Etiquetas Patrimoniais</h1>
        <div class="breadcrumb">Início / Etiquetas</div>
    </div>
</header>

<div class="card-form" style="margin-bottom: 20px;">
    <h3 style="margin-bottom: 15px;">Filtrar Itens</h3>
    <div class="filter-row">
        <select>
            <option>Todas Secretarias</option>
            <option>Educação</option>
            <option>Saúde</option>
        </select>
        <select>
            <option>Todas Unidades</option>
            <option>E.M. Pedro</option>
        </select>
        <select>
            <option>Todos Locais</option>
            <option>Sala 1</option>
        </select>
        <button class="btn btn-primary" onclick="alert('Funcionalidade: Filtrar')">Buscar</button>
    </div>
</div>

<div class="card-form" style="margin-bottom: 20px;">
    <h3 style="margin-bottom: 15px;">Itens Selecionados</h3>
    <table>
        <thead>
            <tr><th><input type="checkbox" id="selecionar_todos"></th><th>Nº Patrimônio</th><th>Descrição</th><th>Local</th></tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($res_itens)): ?>
            <tr>
                <td><input type="checkbox" class="item-checkbox" value="<?php echo $row['id']; ?>"></td>
                <td><strong><?php echo $row['numero_patrimonio']; ?></strong></td>
                <td><?php echo $row['descricao']; ?></td>
                <td><?php echo $row['local_nome']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <div class="selecao-info" style="margin-top: 15px;">
        <strong id="contador">0</strong> itens selecionados
    </div>
</div>

<div class="card-form" style="margin-bottom: 20px;">
    <h3 style="margin-bottom: 15px;">Configuração da Etiqueta</h3>
    
    <div class="form-row">
        <div class="form-group">
            <label>Tamanho</label>
            <select>
                <option>50mm x 25mm (pequena)</option>
                <option selected>80mm x 50mm (padrão)</option>
                <option>100mm x 70mm (grande)</option>
            </select>
        </div>
        <div class="form-group">
            <label>Incluir</label>
            <div>
                <label><input type="checkbox" checked> Nº Patrimônio</label><br>
                <label><input type="checkbox" checked> Descrição</label><br>
                <label><input type="checkbox" checked> QR Code</label><br>
                <label><input type="checkbox"> Localização</label>
            </div>
        </div>
    </div>
</div>

<div class="card-form">
    <h3 style="margin-bottom: 15px;">Pré-visualização</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px;">
        <div style="border: 1px solid #E1E8ED; padding: 10px; text-align: center; border-radius: 4px;">
            <div style="width: 60px; height: 60px; background: #F5F7FA; margin: 0 auto 8px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-qrcode" style="font-size: 30px; color: #2C3E50;"></i>
            </div>
            <strong>2026.001</strong>
            <p style="font-size: 11px; color: #7F8C8D;">Computador</p>
        </div>
        <div style="border: 1px solid #E1E8ED; padding: 10px; text-align: center; border-radius: 4px;">
            <div style="width: 60px; height: 60px; background: #F5F7FA; margin: 0 auto 8px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-qrcode" style="font-size: 30px; color: #2C3E50;"></i>
            </div>
            <strong>2026.002</strong>
            <p style="font-size: 11px; color: #7F8C8D;">Mesa</p>
        </div>
    </div>
    
    <div style="display: flex; gap: 15px; justify-content: center; margin-top: 20px;">
        <button class="btn btn-primary" onclick="alert('Gerando PDF com as etiquetas selecionadas...')"><i class="fas fa-file-pdf"></i> Gerar PDF</button>
        <button class="btn btn-outline" onclick="alert('Enviando para impressão...')"><i class="fas fa-print"></i> Imprimir</button>
    </div>
</div>

<script>
document.getElementById('selecionar_todos')?.addEventListener('change', function(e) {
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = e.target.checked);
    atualizarContador();
});

document.querySelectorAll('.item-checkbox').forEach(cb => {
    cb.addEventListener('change', atualizarContador);
});

function atualizarContador() {
    let total = document.querySelectorAll('.item-checkbox:checked').length;
    document.getElementById('contador').textContent = total;
}
</script>

<?php include_once '../includes/rodape.php'; ?>
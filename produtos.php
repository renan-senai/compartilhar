<h2><?php echo $produtoEditar ? 'Editar' : 'Novo'; ?> Produto</h2>
<form method="POST">
    <input type="hidden" name="id" value="<?php echo $produtoEditar['id'] ?? 0; ?>">

    <table border="1">
        <tr>
            <td><label>Código: *</label></td>
            <td><input type="text" name="codigo" value="<?php echo $produtoEditar['codigo'] ?? ''; ?>" required></td>
        </tr>
        <tr>
            <td><label>Nome: *</label></td>
            <td><input type="text" name="nome" size="40" value="<?php echo $produtoEditar['nome'] ?? ''; ?>" required></td>
        </tr>
        <tr>
            <td><label>Categoria: *</label></td>
            <td>
                <select name="categoria_id" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"
                            <?php echo ($produtoEditar['categoria_id'] ?? 0) == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo $cat['nome']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><label>Preço de Venda: *</label></td>
            <td><input type="number" name="preco" value="<?php echo $produtoEditar['preco_venda'] ?? ''; ?>" required></td>
        </tr>
        <tr>
            <td><label>Estoque Atual:</label></td>
            <td><input type="number" name="estoque_atual" min="0" value="<?php echo $produtoEditar['estoque_atual'] ?? 0; ?>"></td>
        </tr>
        <tr>
            <td><label>Estoque Mínimo:</label></td>
            <td><input type="number" name="estoque_minimo" min="0" value="<?php echo $produtoEditar['estoque_minimo'] ?? 5; ?>"></td>
        </tr>
    </table>

    <p>
        <button type="submit"><?php echo $produtoEditar ? 'Atualizar' : 'Cadastrar'; ?></button>
        <?php if ($produtoEditar): ?>
            <a href="produtos.php"><button type="button">Cancelar</button></a>
        <?php endif; ?>
    </p>
</form>

<hr>
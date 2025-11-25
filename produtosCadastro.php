// Processar cadastro/edição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $codigo = sanitizar($_POST['codigo'] ?? '');
    $nome = sanitizar($_POST['nome'] ?? '');
    $categoria_id = (int)($_POST['categoria_id'] ?? 0);
    $preco = floatval($_POST['preco'] ?? 0);
    $estoque_atual = (int)($_POST['estoque_atual'] ?? 0);
    $estoque_minimo = (int)($_POST['estoque_minimo'] ?? 0);
    
    if (empty($codigo) || empty($nome) || $categoria_id == 0 || $preco <= 0) {
        $mensagem = 'Preencha todos os campos obrigatórios!';
        $tipoMensagem = 'erro';
    } else {
        try {
            if ($id > 0) {
                // Editar
                $sql = "UPDATE produtos SET codigo = :codigo, nome = :nome, categoria_id = :cat, 
                        preco_venda = :preco, estoque_atual = :estoque, estoque_minimo = :minimo 
                        WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    'codigo' => $codigo,
                    'nome' => $nome,
                    'cat' => $categoria_id,
                    'preco' => $preco,
                    'estoque' => $estoque_atual,
                    'minimo' => $estoque_minimo,
                    'id' => $id
                ]);
                $mensagem = 'Produto atualizado!';
            } else {
                // Cadastrar
                $sql = "INSERT INTO produtos (codigo, nome, categoria_id, preco_venda, estoque_atual, estoque_minimo) 
                        VALUES (:codigo, :nome, :cat, :preco, :estoque, :minimo)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    'codigo' => $codigo,
                    'nome' => $nome,
                    'cat' => $categoria_id,
                    'preco' => $preco,
                    'estoque' => $estoque_atual,
                    'minimo' => $estoque_minimo
                ]);
                $mensagem = 'Produto cadastrado!';
            }
            $tipoMensagem = 'sucesso';
        } catch (PDOException $e) {
            $mensagem = 'Erro: ' . $e->getMessage();
            $tipoMensagem = 'erro';
        }
    }
}
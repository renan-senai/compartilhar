// Processar movimentação
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produto_id = (int)($_POST['produto_id'] ?? 0);
    $tipo = sanitizar($_POST['tipo'] ?? '');
    $quantidade = (int)($_POST['quantidade'] ?? 0);
    $data = sanitizar($_POST['data'] ?? '');
    
    if ($produto_id == 0 || empty($tipo) || $quantidade <= 0 || empty($data)) {
        $mensagem = 'Preencha todos os campos!';
        $tipoMensagem = 'erro';
    } else {
        try {
            // Buscar estoque atual
            $sql = "SELECT estoque_atual, nome FROM produtos WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['id' => $produto_id]);
            $produto = $stmt->fetch();
            
            if (!$produto) {
                $mensagem = 'Produto não encontrado!';
                $tipoMensagem = 'erro';
            } else {
                $estoque_anterior = $produto['estoque_atual'];
                
                // Calcular novo estoque
                if ($tipo === 'ENTRADA') {
                    $estoque_novo = $estoque_anterior + $quantidade;
                } else {
                    $estoque_novo = $estoque_anterior - $quantidade;
                    if ($estoque_novo < 0) {
                        $mensagem = 'Estoque insuficiente!';
                        $tipoMensagem = 'erro';
                    }
                }
                
                if (empty($mensagem)) {
                    $conn->beginTransaction();
                    
                    // Registrar movimentação
                    $sql = "INSERT INTO movimentacoes 
                           (produto_id, usuario_id, tipo_movimentacao, quantidade, data_movimentacao, 
                            estoque_anterior, estoque_posterior)
                           VALUES (:produto_id, :usuario_id, :tipo, :quantidade, :data, :ant, :post)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([
                        'produto_id' => $produto_id,
                        'usuario_id' => $_SESSION['usuario_id'],
                        'tipo' => $tipo,
                        'quantidade' => $quantidade,
                        'data' => $data,
                        'ant' => $estoque_anterior,
                        'post' => $estoque_novo
                    ]);
                    
                    // Atualizar estoque
                    $sql = "UPDATE produtos SET estoque_atual = :estoque WHERE id = :id";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute(['estoque' => $estoque_novo, 'id' => $produto_id]);
                    
                    $conn->commit();
                    $mensagem = 'Movimentação registrada com sucesso!';
                    $tipoMensagem = 'sucesso';
                }
            }
        } catch (PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $mensagem = 'Erro: ' . $e->getMessage();
            $tipoMensagem = 'erro';
        }
    }
}
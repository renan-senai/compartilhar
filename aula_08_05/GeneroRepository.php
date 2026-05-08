<?php

class GeneroRepository
{
    private $pdo;
    private $tabela = 'generos';

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function listarTodos()
    {
        $sql = 'SELECT id, nome FROM ' . $this->tabela . ' ORDER BY nome ASC';
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function buscarPorId($id)
    {
        $sql = 'SELECT id, nome FROM ' . $this->tabela . ' WHERE id = :id LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $linha = $stmt->fetch();

        if ($linha === false) {
            return null;
        }

        return Genero::fromArray($linha);
    }

    public function criar($genero)
    {
        if ($this->existeNome($genero->getNome())) {
            return false;
        }

        $sql = 'INSERT INTO ' . $this->tabela . ' (nome) VALUES (:nome)';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'nome' => $genero->getNome(),
        ]);
    }

    public function atualizar($id, $genero)
    {
        if ($this->existeNome($genero->getNome(), $id)) {
            return false;
        }

        $sql = 'UPDATE ' . $this->tabela . ' SET nome = :nome, atualizado_em = CURRENT_TIMESTAMP WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'nome' => $genero->getNome(),
        ]);
    }

    public function excluir($id)
    {
        $sql = 'DELETE FROM ' . $this->tabela . ' WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function contarLivrosPorGenero($id)
    {
        $sql = 'SELECT COUNT(*) AS total FROM livros WHERE genero_id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $linha = $stmt->fetch();

        return (int) ($linha['total'] ?? 0);
    }

    private function existeNome($nome, $ignorarId = null)
    {
        $sql = 'SELECT id FROM ' . $this->tabela . ' WHERE LOWER(nome) = LOWER(:nome)';
        $params = ['nome' => $nome];

        if ($ignorarId !== null) {
            $sql .= ' AND id <> :id';
            $params['id'] = $ignorarId;
        }

        $sql .= ' LIMIT 1';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch() !== false;
    }
}

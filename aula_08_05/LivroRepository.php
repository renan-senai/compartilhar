<?php

class LivroRepository
{
    private $pdo;
    private $tabela = 'livros';

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function listar($titulo = '', $status = 'ativo')
    {
        $sql = 'SELECT l.id, l.titulo, l.ano, l.genero_id, g.nome AS genero_nome, l.quantidade, l.status_livro FROM ' . $this->tabela . ' l LEFT JOIN generos g ON g.id = l.genero_id WHERE 1=1';
        $params = [];

        if ($titulo !== '') {
            $sql .= ' AND l.titulo LIKE :titulo';
            $params['titulo'] = '%' . $titulo . '%';
        }

        if ($status === 'ativo' || $status === 'inativo') {
            $sql .= ' AND l.status_livro = :status';
            $params['status'] = $status;
        }

        $sql .= ' ORDER BY l.titulo ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $linhas = $stmt->fetchAll();
        $livros = [];

        foreach ($linhas as $linha) {
            $livros[] = LivroModel::fromArray($linha);
        }

        return $livros;
    }

    public function buscarPorId($id)
    {
        $stmt = $this->pdo->prepare('SELECT l.id, l.titulo, l.ano, l.genero_id, g.nome AS genero_nome, l.quantidade, l.status_livro FROM ' . $this->tabela . ' l LEFT JOIN generos g ON g.id = l.genero_id WHERE l.id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $linha = $stmt->fetch();

        if ($linha === false) {
            return null;
        }

        return LivroModel::fromArray($linha);
    }

    public function criar($livro)
    {
        $sql = 'INSERT INTO ' . $this->tabela . ' (titulo, ano, genero_id, quantidade, status_livro) VALUES (:titulo, :ano, :genero_id, :quantidade, :status_livro)';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'titulo' => $livro->getTitulo(),
            'ano' => $livro->getAno(),
            'genero_id' => (int) $livro->getGeneroId(),
            'quantidade' => $livro->getQuantidade(),
            'status_livro' => $livro->getStatusLivro(),
        ]);
    }

    public function atualizar($id, $livro)
    {
        $sql = 'UPDATE ' . $this->tabela . ' SET titulo = :titulo, ano = :ano, genero_id = :genero_id, quantidade = :quantidade, status_livro = :status_livro, atualizado_em = CURRENT_TIMESTAMP WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'titulo' => $livro->getTitulo(),
            'ano' => $livro->getAno(),
            'genero_id' => (int) $livro->getGeneroId(),
            'quantidade' => $livro->getQuantidade(),
            'status_livro' => $livro->getStatusLivro(),
        ]);
    }

    public function inativar($id)
    {
        $stmt = $this->pdo->prepare("UPDATE " . $this->tabela . " SET status_livro = 'inativo', atualizado_em = CURRENT_TIMESTAMP WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}

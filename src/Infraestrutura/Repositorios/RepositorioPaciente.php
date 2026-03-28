<?php

namespace Luizlins\Projeto01\Infraestrutura\Repositorios;

use Luizlins\Projeto01\Dominio\Modulos\Paciente;
use Luizlins\Projeto01\Dominio\Repositorio\RepositorioPacienteInterface;
use Luizlins\Projeto01\Infraestrutura\Persistencia\FabricaConexao;
use PDO;
use PDOStatement;
use DateTimeImmutable;

class RepositorioPaciente implements RepositorioPacienteInterface
{
    private PDO $conexao;

    public function __construct()
    {
        $this->conexao = FabricaConexao::criarConexao();
    }

    public function listar(): array
    {
        $sqlQuery = "SELECT * FROM pacientes;";
        $stmt = $this->conexao->query($sqlQuery);

        return $this->hidratacao($stmt);
    }

    public function inserir(Paciente $paciente): bool
    {
        $inserirQuery = "INSERT INTO pacientes (cpf, nome, data_nascimento) VALUES (:cpf, :nome, :data_nascimento);";
        $stmt = $this->conexao->prepare($inserirQuery);

        return $stmt->execute([
            ':cpf' => $paciente->recuperarCpf(),
            ':nome' => $paciente->recuperarNome(),
            ':data_nascimento' => $paciente->recuperarDataNascimento()->format('Y-m-d')
        ]);
    }

    public function deletar(Paciente $paciente): bool
    {
        $stmt = $this->conexao->prepare("DELETE FROM pacientes WHERE cpf = ?;");
        $stmt->bindValue(1, $paciente->recuperarCpf(), PDO::PARAM_STR);
        return $stmt->execute();
    }
    
    public function atualizar(Paciente $paciente): bool
    {
        $atualizarQuery = "UPDATE pacientes SET nome = :nome, data_nascimento = :data_nascimento WHERE cpf = :cpf;";
        $stmt = $this->conexao->prepare($atualizarQuery);
        
        return $stmt->execute([
            ':nome' => $paciente->recuperarNome(),
            ':data_nascimento' => $paciente->recuperarDataNascimento()->format('Y-m-d'),
            ':cpf' => $paciente->recuperarCpf()
        ]);
    }

    public function recuperar(Paciente $paciente): Paciente|null
    {
        $sqlQuery = "SELECT * FROM pacientes WHERE cpf = :cpf;";
        $stmt = $this->conexao->prepare($sqlQuery);
        $stmt->bindValue(':cpf', $paciente->recuperarCpf());
        $stmt->execute();

        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dados === false) return null;

        return new Paciente($dados['cpf'], $dados['nome'], [], new DateTimeImmutable($dados['data_nascimento']));
    }

    private function hidratacao(PDOStatement $stmt): array
    {
        $listaDados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $listaPacientes = [];

        foreach($listaDados as $dados) {
            $listaPacientes[] = new Paciente($dados['cpf'], $dados['nome'], [], new DateTimeImmutable($dados['data_nascimento']));
        }
        return $listaPacientes;
    }
}
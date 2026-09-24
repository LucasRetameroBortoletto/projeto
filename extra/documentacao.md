# Levando de requisitos 

## Mini sistema gerenciamento de alunos 

### CRUD utilizando PHP + HTML + POSTGRESQL

//////////////////////////////////////

### Objetivos : 
1. Cadastrar aluno: 
- Receber Nome, Turma, Nascimento, Ativo 

2. Excluir : 
- Excluir um Aluno a partir de um ID

3. Relatório 
- Listar todos os alunos cadastrados no sistema 

4. Consultar aluno 
- Onde será feito a consulta de um aluno específico a partir de o ID

5. Atualizar um aluno 
- Atualizar aluno a partir do ID

### Sistema de login 

RF descrição 

1. Tabela usuários  

```mermaid
erDiagram
Usuario {
    id INT PK,
    email VARCHAR(60)
    senha VARCHAR(12)
}
```

2. Cadastro de novos usuários

3. Criar login 

4. Verificar se os usuários são válidos em todas as páginas
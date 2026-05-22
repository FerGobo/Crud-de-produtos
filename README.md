# Crud-de-produtos
# Mini Sistema de Gestão de Produtos
Trabalho acadêmico Sistemas de Informações - UNIPAR
Maria Fernanda Bandeira Gobo
RA:60300664


## Visão Geral

uma aplicação web desenvolvida com PHP (backend) e MySQL (banco de dados), utilizando os princípios de **Orientação a Objetos** para organização do código. O sistema permite o gerenciamento completo de produtos, fornecedores e cestas de compras, com controle de acesso por autenticação de usuários.

## Funcionalidades Principais

| Funcionalidade | Descrição |

| Cadastro de Usuários | Registro com senha armazenada em hash SHA-254 |
| Gestão de Fornecedores | CRUD completo com edição via AJAX |
| Gestão de Produtos | CRUD completo com vínculo ao fornecedor |
| Loja com Checkboxes | Seleção de produtos com validação JavaScript |
| Cesta de Compras | Visualização do carrinho com resumo e valor total |

---

## Tecnologias Utilizadas

- **PHP 8.0+** — Backend e lógica de negócio
- **MySQL 5.7+** — Banco de dados relacional
- **PDO** — Camada de abstração para acesso ao banco de dados
- **HTML5 / CSS3** — Interface do usuário
- **JavaScript (Vanilla)** — Interatividade e requisições AJAX via Fetch API
- **SHA-254** — Hash para armazenamento seguro de senhas


## Estrutura de Pastas

```
sistema/
├── config/
│   └── database.php          # Constantes de conexão com o banco
│
├── classes/                  # Camada de modelo (OOP)
│   ├── Database.php          # Singleton de conexão PDO
│   ├── Usuario.php           # Lógica de autenticação e cadastro
│   ├── Produto.php           # CRUD de produtos
│   ├── Fornecedor.php        # CRUD de fornecedores
│   └── Cesta.php             # Lógica do carrinho de compras
│
├── auth/                     # Páginas públicas de autenticação
│   ├── login.php             # Formulário e processamento de login
│   ├── logout.php            # Encerramento de sessão
│   └── register.php          # Cadastro de novo usuário
│
├── pages/                    # Páginas protegidas (requer login)
│   ├── dashboard.php         # Página inicial após login
│   ├── produtos.php          # Cadastro e listagem de produtos
│   ├── fornecedores.php      # Cadastro e listagem de fornecedores
│   ├── loja.php              # Seleção de produtos via checkbox
│   └── cesta.php             # Carrinho de compras do usuário
│
├── ajax/                     # Endpoints exclusivos para requisições AJAX
│   ├── ajax_produtos.php     # Atualizar / deletar produto via JSON
│   ├── ajax_fornecedores.php # Atualizar / deletar fornecedor via JSON
│   └── ajax_cesta.php        # Operações da cesta via JSON
│
├── includes/                 # Fragmentos de layout reutilizáveis
│   ├── header.php            # Navbar + verificação de sessão
│   └── footer.php            # Fechamento de tags + inclusão do JS
│
├── assets/
│   ├── css/
│   │   └── style.css         # Estilos globais do sistema
│   └── js/
│       └── main.js           # Funções AJAX e interatividade
│
├── banco.sql                 # Script SQL para criação do banco
└── index.php                 # Ponto de entrada (redireciona conforme sessão)
```

---

## Banco de Dados

### Diagrama de Relacionamento

```
usuarios (1) ──────────── (N) cestas
                                  │
fornecedores (1) ─── (N) produtos │
                                  │
                    cesta_itens ──┘
                    (N:N entre cestas e produtos)
```

### Tabelas e Relacionamentos

#### `usuarios`
Armazena os dados de acesso ao sistema.

| Campo | Tipo | Descrição |
|---|---|---|
| id | INT PK AI | Identificador único |
| nome | VARCHAR(100) | Nome completo |
| email | VARCHAR(150) UNIQUE | Login do usuário |
| senha | VARCHAR(64) | Hash SHA-256 da senha |
| criado_em | TIMESTAMP | Data de cadastro |

#### `fornecedores`
Cadastro de empresas fornecedoras de produtos.

| Campo | Tipo | Descrição |
|---|---|---|
| id | INT PK AI | Identificador único |
| nome | VARCHAR(150) | Razão social |
| cnpj | VARCHAR(18) | CNPJ formatado |
| telefone | VARCHAR(20) | Telefone de contato |
| email | VARCHAR(150) | E-mail de contato |
| criado_em | TIMESTAMP | Data de cadastro |

#### produtos
Catálogo de produtos vinculados a fornecedores.

| Campo | Tipo | Descrição |
|---|---|---|
| id | INT PK AI | Identificador único |
| nome | VARCHAR(150) | Nome do produto |
| descricao | TEXT | Descrição detalhada |
| preco | DECIMAL(10,2) | Preço unitário |
| estoque | INT | Quantidade em estoque |
| fornecedor_id | INT FK | Referência ao fornecedor |
| criado_em | TIMESTAMP | Data de cadastro |

#### cestas
Representa o carrinho de cada usuário.

| Campo | Tipo | Descrição |
|---|---|---|
| id | INT PK AI | Identificador único |
| usuario_id | INT FK | Referência ao usuário dono |
| criado_em | TIMESTAMP | Data de criação |

#### cesta_itens
Tabela intermediária que relaciona cestas a produtos (N:N).

| Campo | Tipo | Descrição |
|---|---|---|
| id | INT PK AI | Identificador único |
| cesta_id | INT FK | Referência à cesta |
| produto_id | INT FK | Referência ao produto |

### Integridade Referencial

- `produtos.fornecedor_id` → `fornecedores.id` com `ON DELETE SET NULL`
- `cestas.usuario_id` → `usuarios.id` com `ON DELETE CASCADE`
- `cesta_itens.cesta_id` → `cestas.id` com `ON DELETE CASCADE`
- `cesta_itens.produto_id` → `produtos.id` com `ON DELETE CASCADE`

---


# Nome do Projeto
<!-- GoTicket -->

## Descrição

 O GoTicket é um sistema de venda de ingressos digitais criado para tornar o processo de compra e venda mais rápido, seguro e acessível. Sua finalidade é eliminar problemas comuns como lentidão, fraudes, taxas ocultas e dificuldades de pagamento, oferecendo uma experiência transparente e confiável tanto para usuários quanto para organizadores de eventos. Entre suas principais funcionalidades, destacam-se a autenticação segura dos ingressos, fila virtual inteligente para evitar travamentos, opções de parcelamento, painel intuitivo para organizadores e suporte técnico eficiente. 

## Integrantes

-   Enzo Paolucci: 12303224
-   Samuel Elias: 22301470 
-   Silas Deslandes: 12300730
-   Lucas Servulo: 1302180
-   Francisco Bricio: 12300888
-   Davi Passos: 12300896

## Estrutura de Diretórios

    GoTicket-main/
    ├── src/               # Código-fonte principal (classes PHP com PSR-4)
    ├── config/            # Arquivos de configuração (banco de dados, email, etc.)
    ├── eventos/           # Gerenciamento de eventos e ingressos
    ├── admin/             # Painel administrativo
    ├── organizador/       # Painel de vendas do organizador
    ├── usuarios/          # Gerenciamento de usuários
    ├── public/            # Arquivos públicos e assets
    ├── docs/              # Documentação
    ├── lib/               # Bibliotecas externas (PHPMailer)
    ├── css/               # Arquivos de estilo
    ├── js/                # Scripts JavaScript
    ├── sql/               # Scripts SQL
    ├── README.md          # Arquivo de descrição do projeto
    └── composer.json      # Dependências do projeto PHP

## Como Executar o Projeto

### 1. Pré-requisitos

- **PHP >= 8.1** (com extensões PDO e PDO_PGSQL habilitadas)
- **Composer** (para autoload PSR-4)
- **Servidor Web** (Apache/Nginx com suporte a PHP)
- **Banco de Dados PostgreSQL** (já hospedado no Supabase na nuvem)

### 2. Instalação

```bash
# Clone o repositório
git clone https://github.com/usuario/GoTicket-main.git

# Acesse a pasta do projeto
cd GoTicket-main

# Instale as dependências do Composer
composer install

# O banco de dados está no supabase (nuvem)
php -S localhost:8000

# OU configure seu servidor web (Apache/Nginx) para apontar para o diretório do projeto

### 3. Acesso
-   URL local: http://localhost:8000\
-   Usuário padrão: user@goticket.com
-   Senha padrão: user123

------------------------------------------------------------------------

## Checklist das 20 Funcionalidades

1. [x] Cadastro de Usuários - Registro de novos usuários com validação de CPF, email e senha
2. [x] Login com Autenticação -  Sistema de login seguro com criptografia de senha(password_hash)
3. [x] Recuperação de Senha por e-mail - Envio de codigo por email para redefinição de senha com token temporário
4. [x]  Sistema Bilingue - O sistema oferece suporte para as linguas portugues e ingles. 
5. [x] Perfil de Usuário - Visualização e edição de dados pessoais com upload de foto de perfil
6. [x] Três Tipos de Usuário - Sistema com diferentes permissões (ADMIN, ORGANIZADOR, CLIENTE)
7. [x] Criar Eventos - Organizadores podem criar novos eventos com detalhes completos (nome, data, horário, local, descrição, imagem)
8. [x] Editar Eventos - Modificação de informações de eventos existentes
9. [x] Excluir Eventos - Remoção de eventos com verificação de ingressos vendidos
10.[x] Listar Eventos - Visualização de todos os eventos disponíveis com filtros e busca
11.[x] Detalhes do Evento - Página detalhada com informações completas e opção de compra
12.[x] Atualização Automática de Status - Eventos expirados são marcados automaticamente como inativos
13.[x] Gerenciar Ingressos - Organizadores podem criar tipos de ingressos (Normal, VIP, Meia-Entrada) com preços diferenciados
14.[x] Comprar Ingressos - Clientes podem adquirir ingressos com múltiplas formas de pagamento (Cartão, PIX)
15.[x] Meus Ingressos - Visualização de todos os ingressos adquiridos organizados por evento
16.[x] Download de Ingresso - Geração de PDF do ingresso com QR Code
17.[x] Transferir Ingresso - Permite transferir ingressos para outros usuários cadastrados
18.[x] Validar Ingresso - Organizadores podem validar ingressos através do código único e marcar como usados
19. [x] Diferenciação de categorias de usuários, oferecendo opções gratuitas e pagas (Normal/Gold).
20. [x] Sistema permite personalização do design com base em esquemas de cores (Claro/Escuro).

------------------------------------------------------------------------

## Design Patterns Aplicados na Camada de Domínio

Design Patterns Aplicados na Camada de Domínio
🔹 Singleton

Uso: Classe Database.php para gerenciar a conexão única com o banco de dados.
Justificativa: Garante que exista apenas uma instância da conexão PDO em toda a aplicação, evitando múltiplas conexões simultâneas e otimizando o uso de recursos.

🔹 Factory Method

Uso: Classes EventoFactory.php e IngressoFactory.php para criação de diferentes tipos de eventos e ingressos.
Justificativa: Encapsula a lógica de criação de objetos específicos (Shows, Palestras, Teatros; Ingressos VIP, Meia, Normal), permitindo a extensão de novos tipos sem alterar o código existente.

🔹 Abstract Factory

Uso: Classe RepositoryFactory.php para criação de repositórios.
Justificativa: Centraliza a criação de famílias de objetos relacionados (repositórios de Usuário, Evento e Ingresso), facilitando manutenção e substituição de implementações sem alterar o código cliente.

🔹 Strategy

Uso: Estratégias de pagamento (PagamentoStrategy, PagamentoCartaoStrategy, PagamentoPixStrategy).
Justificativa: Permite alternar dinamicamente o algoritmo de processamento de pagamento em tempo de execução, facilitando a adição de novos métodos de pagamento sem modificar o código principal.

🔹 Decorator

Uso: Classes IngressoVipDecorator e IngressoMeiaEntradaDecorator para adicionar funcionalidades extras aos ingressos.
Justificativa: Adiciona dinamicamente características aos ingressos (VIP, Meia-Entrada) sem necessidade de criar múltiplas subclasses, mantendo o código flexível e extensível.

🔹 Observer

Uso: Sistema de notificações (EventoObserver, NotificadorEmail, NotificadorPush).
Justificativa: Permite notificar automaticamente diferentes interessados (usuários e administradores) sobre eventos importantes, como criação, atualização ou cancelamento, garantindo baixo acoplamento entre os componentes.

🔹 Repository

Uso: Camada de abstração de acesso a dados (RepositoryInterface, EventoRepository, UsuarioRepository, IngressoRepository).
Justificativa: Separa a lógica de negócio da persistência de dados, facilitando testes unitários e permitindo a troca do banco de dados sem impacto na camada de domínio.

> Diagramas UML ou imagens mostrando a modelagem dos patterns aplicados.
> GoTicket-main/
└── docs/
    └── uml/
        ├── singleton.png
        ├── factory_method.png
        ├── abstract_factory.png
        ├── strategy.png
        ├── decorator.png
        ├── observer.png
        └── repository.png


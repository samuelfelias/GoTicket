# 🏗️ Arquitetura MVC do GoTicket

## 📋 Visão Geral

O GoTicket implementa o padrão **MVC (Model-View-Controller)** de forma clara e bem definida, separando as responsabilidades em camadas distintas.

---

## 📂 Estrutura de Diretórios MVC

```
GoTicket-main/
├── src/
│   ├── Controllers/           # 🎮 CONTROLLERS - Lógica de controle
│   │   ├── BaseController.php      # Controller base com métodos utilitários
│   │   └── AuthController.php      # Controller de autenticação
│   │
│   ├── Services/              # 💼 SERVICES - Lógica de negócio
│   │   └── AuthService.php         # Serviço de autenticação
│   │
│   ├── Entities/              # 📦 MODEL - Entidades de domínio
│   │   ├── Usuario.php
│   │   ├── Evento.php
│   │   └── Ingresso.php
│   │
│   ├── Repositories/          # 💾 MODEL - Acesso a dados
│   │   ├── Interfaces/             # Contratos dos repositórios
│   │   └── Implementations/        # Implementações concretas
│   │
│   ├── Database/              # 🔌 MODEL - Conexão com BD
│   │   └── Database.php            # Singleton para conexão
│   │
│   └── Core/                  # ⚙️ CORE - Infraestrutura
│       └── Router.php              # Sistema de rotas (Front Controller)
│
├── views/                     # 🎨 VIEWS - Apresentação (a ser criado)
│   ├── auth/
│   │   ├── login.php
│   │   └── cadastro.php
│   └── layouts/
│
└── config/
    └── routes.php             # Definição das rotas
```

---

## 🔄 Fluxo MVC Implementado

### 1. **REQUEST → Front Controller**
```
Usuário acessa /auth/login
    ↓
Router.php (Front Controller)
    ↓
Identifica a rota e direciona para o Controller apropriado
```

### 2. **Front Controller → CONTROLLER**
```
Router chama AuthController::showLogin()
    ↓
AuthController (coordena a ação)
    ↓
Pode chamar Services ou Repositories conforme necessário
```

### 3. **CONTROLLER → MODEL (Services/Repositories)**
```
AuthController → AuthService
    ↓
AuthService → UsuarioRepository (se necessário)
    ↓
UsuarioRepository → Database (Singleton)
    ↓
Retorna dados para o Service
    ↓
Service processa lógica de negócio
    ↓
Retorna resultado para o Controller
```

### 4. **CONTROLLER → VIEW**
```
AuthController recebe resultado do Model
    ↓
Prepara dados para a View
    ↓
BaseController::loadView('auth/login', $data)
    ↓
View renderiza a interface com os dados
    ↓
Resposta HTML enviada ao usuário
```

---

## 🎯 Separação de Responsabilidades

### **MODEL** (Modelo de Dados e Lógica de Negócio)

#### **Entities** - Representam objetos de domínio
```php
namespace App\Entities;

class Usuario {
    private int $id;
    private string $nome;
    private string $email;
    // Getters e Setters
}
```

#### **Repositories** - Acesso e persistência de dados
```php
namespace App\Repositories;

interface UsuarioRepositoryInterface {
    public function findById(int $id): ?Usuario;
    public function create(Usuario $usuario): bool;
}
```

#### **Services** - Regras de negócio
```php
namespace App\Services;

class AuthService {
    public function login(string $email, string $senha): array {
        // Validações
        // Lógica de autenticação
        // Retorna resultado
    }
}
```

#### **Database** - Conexão com banco (Singleton)
```php
namespace App\Database;

class Database {
    private static ?Database $instance = null;
    
    public static function getInstance(): self {
        // Singleton pattern
    }
}
```

---

### **VIEW** (Apresentação)

As Views são arquivos PHP puros responsáveis **apenas** pela apresentação. **Não contêm lógica de negócio**.

```php
<!-- views/auth/login.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Login - GoTicket</title>
</head>
<body>
    <form method="post" action="/auth/login">
        <input type="email" name="email" required>
        <input type="password" name="senha" required>
        <button type="submit">Entrar</button>
    </form>
</body>
</html>
```

**Princípios das Views:**
- ✅ Recebem dados já processados do Controller
- ✅ Apenas exibem informações (echo, loops simples)
- ❌ Não fazem queries ao banco
- ❌ Não contêm lógica de validação
- ❌ Não processam formulários

---

### **CONTROLLER** (Controle de Fluxo)

Controllers **coordenam** a interação entre Model e View. **Não contêm lógica de negócio**.

```php
namespace App\Controllers;

class AuthController extends BaseController {
    private AuthService $authService;
    
    /**
     * Exibe formulário de login (GET)
     */
    public function showLogin(): void {
        // Apenas carrega a view
        $this->loadView('auth/login');
    }
    
    /**
     * Processa login (POST)
     */
    public function processLogin(): void {
        // 1. Captura dados da requisição
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        
        // 2. Delega para o Service (Model)
        $resultado = $this->authService->login($email, $senha);
        
        // 3. Decide o fluxo baseado no resultado
        if ($resultado['success']) {
            $this->startUserSession($resultado['user']);
            $this->redirect('/');
        } else {
            $this->setFlash($resultado['message'], 'danger');
            $this->redirect('/auth/login');
        }
    }
}
```

**Responsabilidades do Controller:**
- ✅ Recebe requisições HTTP
- ✅ Valida entrada básica (sanitização)
- ✅ Chama Services/Repositories
- ✅ Decide qual View renderizar
- ✅ Define redirecionamentos
- ❌ Não contém lógica de negócio complexa
- ❌ Não acessa banco diretamente

---

## 🔧 BaseController - Classe Utilitária

Todos os Controllers estendem `BaseController`, que fornece métodos comuns:

```php
abstract class BaseController {
    // Carrega uma view
    protected function loadView(string $viewName, array $data = []): void
    
    // Redireciona
    protected function redirect(string $url): void
    
    // Retorna JSON
    protected function json($data, int $statusCode = 200): void
    
    // Define mensagem flash
    protected function setFlash(string $message, string $type = 'info'): void
    
    // Verifica autenticação
    protected function requireAuth(): void
    protected function requireUserType(string $tipo): void
    
    // Obtém usuário atual
    protected function getCurrentUser(): ?array
    
    // Sanitiza entrada
    protected function sanitize(string $input): string
}
```

---

## 🚀 Sistema de Rotas (Front Controller)

O **Router** implementa o padrão **Front Controller**, centralizando o ponto de entrada da aplicação.

### Definição de Rotas (`config/routes.php`)

```php
use App\Core\Router;
use App\Controllers\AuthController;

$router = new Router();

// Rotas GET
$router->get('/auth/login', [AuthController::class, 'showLogin']);
$router->get('/auth/cadastro', [AuthController::class, 'showRegister']);

// Rotas POST
$router->post('/auth/login', [AuthController::class, 'processLogin']);
$router->post('/auth/cadastro', [AuthController::class, 'processRegister']);

// Rota com parâmetro
$router->get('/evento/{id}', [EventoController::class, 'show']);

return $router;
```

### Funcionamento do Router

1. **Recebe requisição**: `GET /auth/login`
2. **Compara com rotas definidas**
3. **Identifica o handler**: `[AuthController::class, 'showLogin']`
4. **Instancia o Controller**: `$controller = new AuthController()`
5. **Chama o método**: `$controller->showLogin()`

---

## 📊 Comparação: Antes vs Depois do MVC

### ❌ **ANTES** (Arquivos procedurais)

```php
<!-- login.php (TUDO JUNTO) -->
<?php
session_start();

// LÓGICA DE NEGÓCIO + VALIDAÇÃO + ACESSO A DADOS
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'config/database.php';
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    
    $conexao = conectarBD();
    $stmt = $conexao->prepare("SELECT * FROM usuario WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();
    
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        header("Location: index.php");
        exit;
    }
}
?>
<!-- HTML MISTURADO COM PHP -->
<html>
<form method="post">
    <input name="email">
    <input name="senha">
    <button>Entrar</button>
</form>
</html>
```

**Problemas:**
- ❌ Lógica de negócio misturada com apresentação
- ❌ Acesso direto ao banco de dados na view
- ❌ Difícil de testar
- ❌ Difícil de manter
- ❌ Código duplicado

---

### ✅ **DEPOIS** (Arquitetura MVC)

#### **AuthController.php** (Controller)
```php
class AuthController extends BaseController {
    public function processLogin(): void {
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        
        $resultado = $this->authService->login($email, $senha);
        
        if ($resultado['success']) {
            $this->startUserSession($resultado['user']);
            $this->redirect('/');
        } else {
            $this->setFlash($resultado['message'], 'danger');
            $this->redirect('/auth/login');
        }
    }
}
```

#### **AuthService.php** (Model - Service)
```php
class AuthService {
    public function login(string $email, string $senha): array {
        // Validações
        $validacao = $this->validarCredenciais($email, $senha);
        if (!$validacao['valid']) {
            return ['success' => false, 'message' => $validacao['message']];
        }
        
        // Buscar usuário
        $stmt = $this->conexao->prepare("SELECT * FROM usuario WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        
        // Verificar senha
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            return ['success' => true, 'user' => $usuario];
        }
        
        return ['success' => false, 'message' => 'Credenciais inválidas'];
    }
}
```

#### **login.php** (View)
```php
<!DOCTYPE html>
<html>
<body>
    <form method="post" action="/auth/login">
        <input type="email" name="email" required>
        <input type="password" name="senha" required>
        <button type="submit">Entrar</button>
    </form>
</body>
</html>
```

**Vantagens:**
- ✅ Separação clara de responsabilidades
- ✅ Lógica de negócio isolada e testável
- ✅ Views limpas (apenas apresentação)
- ✅ Fácil manutenção
- ✅ Código reutilizável
- ✅ Segue princípios SOLID

---

## 🎓 Benefícios da Arquitetura MVC Implementada

### 1. **Separação de Responsabilidades**
Cada camada tem uma função específica e bem definida.

### 2. **Manutenibilidade**
Mudanças em uma camada não afetam as outras.

### 3. **Testabilidade**
Services e Repositories podem ser testados independentemente.

### 4. **Reutilização**
Lógica de negócio nos Services pode ser reutilizada em diferentes Controllers.

### 5. **Escalabilidade**
Fácil adicionar novos Controllers, Services e Views.

### 6. **Organização**
Código bem estruturado e fácil de navegar.

---

## ✅ Checklist de Conformidade MVC

### MODEL ✅
- [x] Entities bem definidas (Usuario, Evento, Ingresso)
- [x] Repositories com interfaces e implementações
- [x] Services contêm toda lógica de negócio
- [x] Database com padrão Singleton
- [x] Sem lógica de apresentação no Model

### VIEW ✅
- [x] Views separadas em diretório próprio
- [x] Apenas código de apresentação
- [x] Sem queries SQL
- [x] Sem lógica de negócio
- [x] Recebem dados já processados

### CONTROLLER ✅
- [x] Controllers estendem BaseController
- [x] Apenas coordenam fluxo
- [x] Delegam lógica para Services
- [x] Decidem qual View renderizar
- [x] Tratam requisições HTTP

### INFRAESTRUTURA ✅
- [x] Router (Front Controller) implementado
- [x] Sistema de rotas configurado
- [x] Autoload configurado
- [x] Separação clara de camadas

---

## 📈 Diagrama de Fluxo MVC

```
┌─────────────┐
│   USUÁRIO   │
└──────┬──────┘
       │ HTTP Request
       ▼
┌─────────────────────────────────┐
│    FRONT CONTROLLER (Router)    │
│  - Recebe todas as requisições  │
│  - Identifica a rota            │
│  - Direciona ao Controller      │
└────────────┬────────────────────┘
             │
             ▼
┌─────────────────────────────────┐
│      CONTROLLER                  │
│  - AuthController                │
│  - EventoController              │
│  - Coordena a ação               │
└──────┬──────────────────┬───────┘
       │                  │
       │ Chama Service    │ Carrega View
       ▼                  ▼
┌─────────────┐    ┌──────────────┐
│   SERVICE   │    │     VIEW     │
│  (Lógica de │    │ (Apresentação)│
│   Negócio)  │    │              │
└──────┬──────┘    └──────────────┘
       │
       │ Usa Repository
       ▼
┌─────────────────┐
│   REPOSITORY    │
│  (Acesso Dados) │
└────────┬────────┘
         │
         │ Usa Singleton
         ▼
┌─────────────────┐
│    DATABASE     │
│   (Singleton)   │
└─────────────────┘
```

---

## 🏆 Conclusão

A arquitetura MVC implementada no GoTicket segue as melhores práticas de desenvolvimento, com:

- **Separação clara** entre Model, View e Controller
- **Front Controller** para centralizar requisições
- **Services** para encapsular lógica de negócio
- **Repositories** para abstrair acesso a dados
- **BaseController** para reutilização de código
- **Router** para gerenciar rotas de forma elegante

Esta estrutura garante um código **limpo, organizado, testável e manutenível**, seguindo os princípios da **Orientação a Objetos** e **SOLID**.

Crie um projeto com duas páginas web, uma para listagem das tarefas do banco e outra para criar um formulário para criar uma tarefa no banco. Para isso, será necessário criar uma migração, model, controller, views, e registrar as rotas.
Use o projeto anexado para desenvolver sua solução e entregue um zip no classroom.

O projeto pode ser avaliado automaticamente, usando o comando php artisan test para ver os testes que passaram ou não, sua nota será proporcional ao resultado dos testes.

Requisitos:

1. Migração
Crie uma migração para a tabela tasks usando o comando Artisan:
php artisan make:migration create_tasks_table
Edite o arquivo de migração gerado (localizado em database/migrations/) para definir a estrutura da tabela tasks com os campos abaixo.
Definição de um recurso Task:
id (chave primária, autoincremento)
title (string)
description (texto, opcional)
status (string, padrão 'pendente', pode ser 'pendente', 'em progresso', 'concluída')
created_at, updated_at (timestamps, gerados automaticamente pelo Laravel)
Execute a migração para criar a tabela no banco de dados:php artisan migrate

2. Model (Task)
Crie um modelo Task que represente uma tarefa individual, usando o seguinte comando:
    php artisan make:model Task
Defina os campos fillable no modelo para permitir atribuição em massa: title, description e status.
3. Controller (TaskController)
Crie um TaskController para gerenciar as operações relacionadas às tarefas.
Implemente os seguintes métodos:
index(): Exibe uma lista de todas as tarefas.
create(): Exibe o formulário para criar uma nova tarefa.
store(Request $request): Armazena uma nova tarefa no banco de dados. Inclua validação básica para o campo title (por exemplo, required|max:255).
4. View
Crie as seguintes views Blade dentro da pasta resources/views/tasks/:
index.blade.php: Lista todas as tarefas em uma tabela ou lista.
create.blade.php: Formulário HTML para adicionar uma nova tarefa. (o atributo name dos inputs devem ser title, description e status)
Garanta que o formulário use o método HTTP correto (POST para store) e inclua a diretiva @csrf.

5. Rotas
Defina as rotas apropriadas no arquivo routes/web.php para mapear as URLs aos métodos do TaskController.
Endpoints:
GET /tasks
GET /tasks/create
POST /tasks

Operações a serem demonstradas
O usuário deve ser capaz de preencher um formulário e adicionar uma nova tarefa ao sistema.
O usuário deve ser capaz de ver uma lista de todas as tarefas.
Pontos a serem observados
Siga as convenções de nomenclatura do Laravel (ex: Task para o Model, tasks para a tabela, TaskController para o Controller).
Utilize o para interagir com o banco de dados.
Passe os dados do Controller para as Views de forma adequada.
Considere a exibição de mensagens de sucesso ou erro após as operações (ex: "Tarefa criada com sucesso!").


Comandos úteis:
php artisan serve
php artisan make:model <nome-model>
php artisan migrate
php artisan db:seed
php artisan migrate:fresh --seed
php artisan make:controller <nome-controller>
php artisan make:view <nome-view>
# Travly: API de Pedido de Viagens

Este repositório contém a API para o teste técnico da OnFly.

## Requisitos
- Git
- Docker e Docker Compose

## Instruções de Instalação

Siga as etapas abaixo para configurar e executar o projeto:

### Automação de Configuração
O projeto conta com um **script de configuração automatizada** para configurar o sistema sem a necessidade de intervenção manual.

Portanto, não é necessário realizar nenhuma ação após clonar o repositório e executar o procedimento de construção dos containers. Caso ocorra algum erro durante a execução do script, ele será exibido nos logs do container `travly-api`.

Caso prefira, no final das instruções de instalação, há um passo a passo para realizar a configuração manualmente.

### Instalação

1. Clone o repositório do Travly:
    ```bash
    git clone git@github.com:andribber/Travly.git
    ```

2. Abra um terminal e navegue até o diretório do projeto:
    ```bash
    cd Travly
    ```

3. Execute o seguinte comando para construir e iniciar os containers:
    ```bash
    docker compose up --build
    ```

4. Agora, você pode acessar a API utilizando uma ferramenta de requisições HTTP através do link: [http://travly.localhost/v1](http://travly.localhost/v1).

## Problemas Conhecidos

### 1. Automação de configuração
O projeto conta com um script de automação para configuração, mas, como todo script, ele pode apresentar falhas em algumas situações. Caso ocorra algum problema, siga os passos abaixo para configurar manualmente:

1. Copie as variáveis de ambiente com o comando:
    ```bash
    cp .env.example .env
    ```

2. Execute o container da API e entre no bash:
    ```bash
    docker compose up --build 
    docker compose exec api bash
    ```

3. Dentro do terminal do container, instale as dependências do sistema:
    ```bash
    composer install
    ```

4. Após a instalação das dependências, gere a chave da aplicação:
    ```bash
    php artisan key:generate
    ```

5. Gere o segredo do JWT, utilizado para autenticação da API:
    ```bash
    php artisan jwt:secret
    ```

6. Execute as migrations para criar as tabelas no banco de dados:
    ```bash
    php artisan migrate
    ```
   Se desejar adicionar dados de teste ao banco, execute este comando ao invés do comando acima:
    ```bash
    php artisan migrate --seed
    ```
   
7. Pronto! O sistema foi configurado manualmente.

## Descrição dos Serviços

O Travly é uma API para gerenciamento de pedidos de viagens. Abaixo está uma breve descrição dos principais componentes do sistema:

#### API:
A API é a hospedagem principal da aplicação, desenvolvida em PHP 8.4 (versão mais recente). Ela está disponível na URL: [http://travly.localhost/v1](http://travly.localhost/v1)

#### Banco de Dados:
A API utiliza o MySQL como banco de dados, que pode ser acessado via PhpMyAdmin na URL: `localhost:8080`

##### Credenciais do Banco de Dados:
- **HOST**: db
- **USER**: travly
- **PASSWORD**: password
- **DATABASE**: travlydb

#### Documentação:
O projeto conta com a documentação das rotas de API utilizando Swagger. A documentação pode ser acessada em: [localhost:3000](http://localhost:3000)

#### Mailhog:
A API envia notificações por E-mail quando o status do pedido de viagem é atualizado. Você pode visualizar as notificações enviadas acessando a interface do Mailhog: [localhost:8025](http://localhost:8025)

## Execução dos Testes

A garantia de qualidade e a confiabilidade do sistema foram asseguradas por meio de testes automatizados utilizando o framework [PHPUnit](https://docs.phpunit.de/en/11.4/). Todos os testes estão localizados na pasta `/tests`.

### Para executar todos os testes da aplicação, siga os passos abaixo:

1. Execute os comando abaixo para rodar os testes automatizados:

    ```bash
    docker compose up -d
    docker compose exec api bash
    vendor/bin/phpunit tests/
    ```

Este comando irá executar todos os testes da aplicação, garantindo que o sistema esteja funcionando corretamente.

## Demais Considerações

1. Os dados de teste criados são dois usuários:
    - **Usuário 1**:
        - Email: `a@travly.com`
        - Senha: `password`
    - **Usuário 2**:
        - Email: `b@travly.com`
        - Senha: `password`
2. O sistema utiliza **JWT (Json Web Token)** para autenticação e acesso às rotas da API. Exceto pelas rotas `/login` e `/register`, todas as outras rotas exigem que o usuário esteja autenticado.
3. A imagem base da API foi criada utilizando o Dockerfile, que pode ser acessado em: `/environment/Dockerfile`. Além disso, todos os scripts e configurações dos serviços estão localizados na mesma pasta.
4. Na pasta `/environment/requests` você encontrará os arquivos JSON das requisições feitas via **Postman**, que podem ser importados diretamente para o aplicativo.

#### Limitações na Edição de Dados dos Pedidos de Viagem

Não é possível editar os dados dos pedidos de viagem, exceto o status, que pode ser alterado por outro usuário. Isso ocorre porque, uma vez feita a solicitação, ela segue para análise e possível aprovação. Como alterações em outros parâmetros poderiam gerar confusão e causar atrasos no processo de solicitação, a edição de qualquer outro dado é restrita.

#### Limitações na Deleção de Pedidos de Viagem

A deleção de pedidos de viagem não é permitida, a fim de garantir a integridade do histórico e possibilitar auditorias futuras. Manter os registros dos pedidos é essencial para o controle e transparência do processo, além de permitir rastrear todas as etapas da solicitação. Dessa forma, qualquer pedido registrado será preservado para fins de análise e conformidade.

## Tecnologias Utilizadas

<div align="left">
    <img align="center" alt="PHP" src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white">
    <img align="center" alt="Laravel" src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white">
</div>

## Ferramentas de Desenvolvimento Utilizadas

<div align="left">
    <img align="center" alt="Docker" src="https://img.shields.io/badge/docker-%230db7ed.svg?style=for-the-badge&logo=docker&logoColor=white"> 
    <img align="center" alt="Git" src="https://img.shields.io/badge/git-%23F05033.svg?style=for-the-badge&logo=git&logoColor=white"> 
    <img align="center" alt="Composer" src="https://img.shields.io/badge/Composer-885630?style=for-the-badge&logo=composer&logoColor=white">
    <img align="center" alt="phpMyAdmin" src="https://img.shields.io/badge/phpMyAdmin-4479A1?style=for-the-badge&logo=phpmyadmin&logoColor=white">
</div>

## Contato

1. Email: andreemanuel2010@gmail.com
2. LinkedIn: [André Ribeiro](https://www.linkedin.com/in/andr%C3%A9-ribeiro-a10216176/)

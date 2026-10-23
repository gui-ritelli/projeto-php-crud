ALUNOS:
GUILHERME EDUARDO RITELLI DORTA (RA:250532)
LIVIA ROMANO DE OLIVEIRA (RA:250446)
Projeto DevOps — PHP CRUD com Docker e GitHub Actions



Este projeto foi desenvolvido com o objetivo de colocar em prática conceitos de DevOps, utilizando uma aplicação web em PHP integrada a um banco de dados MySQL, executada em containers Docker e com um processo de deploy automatizado pelo GitHub Actions.

Durante o desenvolvimento, configuramos um ambiente Linux em uma máquina virtual, instalamos as ferramentas necessárias e integramos o ambiente local ao GitHub para automatizar a atualização da aplicação.

2. Tecnologias utilizadas

- **Ubuntu Server 24.04:** sistema operacional utilizado no ambiente de desenvolvimento e execução.
- **VirtualBox:** utilizado para criar e executar a máquina virtual.
- **Docker:** utilizado para executar a aplicação e o banco de dados em containers.
- **Docker Compose:** utilizado para configurar e iniciar os serviços da aplicação em conjunto.
- **PHP:** linguagem utilizada no desenvolvimento da aplicação web.
- **MySQL:** banco de dados responsável por armazenar as informações cadastradas.
- **Git e GitHub:** utilizados para versionar o código-fonte e manter o projeto em um repositório remoto.
- **GitHub Actions:** utilizado para executar automaticamente os workflows do projeto.
- **Self-hosted runner:** agente instalado no Ubuntu que permite executar os workflows do GitHub Actions na própria máquina virtual.

3. Configuração do ambiente Linux

Foi criada uma máquina virtual utilizando o VirtualBox, com o Ubuntu Server 24.04 instalado.

Nesse ambiente, foram instalados e configurados o Git, o Docker e o Docker Compose. Em seguida, o repositório do projeto foi clonado para a máquina virtual e os containers foram iniciados.

Também foi configurado o redirecionamento de porta no VirtualBox para permitir o acesso à aplicação pelo navegador do Windows, utilizando o endereço `http://127.0.0.1:8080`.

4. Integração com GitHub Actions

O projeto foi integrado ao GitHub Actions por meio de um self-hosted runner instalado na máquina virtual Ubuntu.

Foi criado um workflow de diagnóstico para verificar se o ambiente de execução estava configurado corretamente. Esse workflow executa comandos para consultar o hostname, o usuário atual, a versão do sistema Linux, a versão do Docker e a versão do Docker Compose.

Depois, foi criado um workflow de deploy para executar o processo de atualização da aplicação.

O workflow de deploy é acionado quando há um novo push na branch `main` ou quando é iniciado manualmente. Ele atualiza o código do repositório, reconstrói a aplicação e inicia os serviços com Docker Compose.

Após uma alteração no código e o envio dessa alteração ao GitHub, foi realizado um teste para confirmar que o workflow era executado e que a atualização aparecia na aplicação acessada pelo navegador.

Como funciona a GitHub Action:  A GitHub Action foi configurada por meio de um arquivo YAML localizado em `.github/workflows/`. Esse arquivo define os eventos que iniciam a automação, o ambiente em que ela será executada e os comandos que devem ser realizados.

No projeto, foram utilizados dois workflows:

**1. Workflow de diagnóstico (`diagnostico.yml`)**

Esse workflow foi criado para verificar se o ambiente da máquina virtual está preparado para executar as tarefas do projeto. Ele consulta informações do sistema operacional e verifica as versões do Docker e do Docker Compose.

**2. Workflow de deploy (`deploy.yml`)**

Esse workflow automatiza a atualização da aplicação. Ele pode ser iniciado quando ocorre um `push` na branch `main` ou manualmente pelo GitHub Actions.

As etapas do deploy são:

1. **Acionamento:** o workflow é iniciado após um `push` na branch `main` ou por execução manual.
2. **Execução no runner:** o GitHub direciona o trabalho para o self-hosted runner instalado na máquina virtual Ubuntu.
3. **Checkout do repositório:** a Action `actions/checkout@v4` disponibiliza o código do repositório para a execução do workflow.
4. **Atualização da aplicação:** o comando `git pull origin main` atualiza o código existente na máquina virtual.
5. **Construção e inicialização:** o comando `docker compose up -d --build` reconstrói a imagem da aplicação, quando necessário, e inicia os serviços em segundo plano.
6. **Verificação:** o comando `docker compose ps` mostra o estado dos containers.

O resultado da execução pode ser acompanhado na aba **Actions** do GitHub. Uma execução concluída com sucesso aparece com o status verde.

Com essa automação, o processo de atualização deixa de depender da execução manual de todos os comandos pelo desenvolvedor. Ao enviar uma alteração para a branch configurada, o workflow pode atualizar a aplicação automaticamente na máquina virtual.

5. O que entendemos durante o processo

Durante a realização do projeto, entendemos que DevOps não se resume apenas à programação de uma aplicação. Ele também envolve a configuração do ambiente, a integração entre ferramentas, a automação de tarefas e a verificação do funcionamento do sistema.

Aprendemos que o Docker permite executar aplicações em ambientes isolados, enquanto o Docker Compose facilita a configuração de serviços que dependem uns dos outros, como uma aplicação PHP e um banco de dados MySQL.

Também entendemos a importância do Git e do GitHub para manter o histórico das alterações e compartilhar o código. Com o GitHub Actions, aprendemos a automatizar tarefas por meio de workflows, e o self-hosted runner permitiu que essas tarefas fossem executadas diretamente na nossa máquina virtual Ubuntu.

Outro aprendizado importante foi perceber que a automação reduz a necessidade de executar manualmente os mesmos comandos a cada atualização. Com o deploy automatizado, uma alteração enviada à branch `main` pode iniciar o processo de atualização da aplicação.

Por fim, aprendemos que a integração entre aplicação, banco de dados, containers, repositório e pipeline exige que cada componente esteja configurado corretamente para que o processo funcione de ponta a ponta.

5. Conclusão

Ao final do projeto, conseguimos executar uma aplicação PHP com operações completas de CRUD, conectada a um banco de dados MySQL e organizada em containers Docker.

Também configuramos uma máquina virtual Ubuntu, integramos o ambiente ao GitHub Actions por meio de um self-hosted runner e implementamos um workflow de deploy automatizado.

O projeto permitiu compreender, na prática, como diferentes ferramentas trabalham em conjunto em um fluxo de desenvolvimento e entrega de software, desde a alteração do código até a atualização da aplicação em execução.

6. Como executar o projeto

**Pré-requisitos:**

- Docker instalado.
- Docker Compose disponível por meio do comando `docker compose`.
- Repositório do projeto clonado na máquina.

**Comandos para iniciar a aplicação:**

```bash
git clone https://github.com/gui-ritelli/projeto-php-crud.git
cd projeto-php-crud
docker compose up -d --build
```

Após a inicialização, acesse no navegador:

`http://localhost:8080`

**Para verificar os containers:**

```bash
docker compose ps
```

**Para interromper os serviços:**

```bash
docker compose down
```

Os dados do MySQL são armazenados em um volume Docker, permitindo que sejam preservados quando os containers são removidos sem apagar o volume.



     

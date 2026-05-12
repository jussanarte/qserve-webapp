# Guia do Projecto QSERVE

## Como Funciona

O QSERVE é uma aplicação para gestão digital de filas no refeitório universitário do ISPTEC. A ideia é substituir a espera desorganizada por um fluxo simples:

1. O estudante cria conta ou inicia sessão.
2. O estudante consulta as filas disponíveis.
3. Ao entrar numa fila aberta, recebe um ticket digital.
4. O ticket tem um QR code que pode ser apresentado no balcão.
5. O funcionário escolhe uma fila e chama o próximo ticket.
6. O estudante acompanha o estado do ticket.
7. O administrador acompanha estatísticas, gere filas, gere funcionários e exporta relatórios.

O sistema usa polling no frontend para manter as filas e os tickets actualizados sem exigir WebSockets. Isto simplifica a instalação em XAMPP/Apache e mantém o projecto fácil de demonstrar num ambiente académico.

## Perfis e Permissões

## Perfis do Sistema

### Estudante

O estudante é o utilizador final do sistema de filas.

Permissões:

- Criar conta e iniciar sessão.
- Consultar filas disponíveis.
- Entrar numa fila aberta.
- Receber ticket digital com QR code.
- Acompanhar o estado do ticket.

Restrições:

- Não pode gerir filas.
- Não pode chamar tickets.
- Não pode aceder à administração.

---

### Funcionário

O funcionário opera o atendimento das filas.

Permissões:

- Visualizar filas activas.
- Seleccionar fila de atendimento.
- Chamar próximo ticket.
- Marcar ticket como servido ou cancelado.

Restrições:

- Não pode gerir administradores.
- Não pode exportar relatórios administrativos.
- Não pode eliminar filas.

---

### Administrador

O administrador gere o sistema.

Permissões:

- Criar, editar e remover filas.
- Abrir, pausar e fechar filas.
- Gerir funcionários.
- Visualizar dashboard e estatísticas.
- Exportar relatórios CSV e PDF.
- Acompanhar operações do sistema.

## Porque o Projecto Foi Modelado Deste Jeito

### Separação por responsabilidades

O backend foi dividido em Controllers, Services e Repositories para manter responsabilidades claras:

- Controllers tratam HTTP: recebem pedidos, lêem input e devolvem respostas.
- Services tratam regras de negócio: validações, decisões e fluxos.
- Repositories tratam SQL: toda a comunicação com a base de dados.

Esta separação facilita manutenção, testes e evolução. Por exemplo, uma regra como "não eliminar fila com tickets em espera" fica no service, enquanto a query que conta tickets fica no repository.

### Angular standalone

O frontend usa componentes standalone para evitar módulos pesados e tornar cada página mais independente. Isto combina bem com lazy loading nas rotas:

- `/queue` carrega apenas a área do estudante.
- `/attendant` carrega apenas o painel de atendimento.
- `/admin` carrega apenas a área administrativa.

### JWT e guards

O JWT mantém a API stateless: o backend não precisa guardar sessão no servidor. No frontend, guards funcionais (`CanActivateFn`) controlam acesso por autenticação e role.

### Polling em vez de WebSockets

O polling a cada 5 segundos foi escolhido por simplicidade operacional:

- Funciona bem em Apache/XAMPP.
- Evita servidor Node/WebSocket separado.
- É suficiente para o cenário de refeitório, onde poucos segundos de atraso são aceitáveis.

### Relatórios por CSV e PDF

CSV serve para análise no Excel. PDF serve para arquivo e partilha formal. O CSV inclui BOM UTF-8 para preservar acentos em ferramentas como Excel.

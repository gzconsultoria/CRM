# InvestCRM Plugin

Este repositório agora contém o plugin **InvestCRM**, uma solução de CRM completa inspirada no Bitrix24 e voltada para consultorias de investimento que utilizam WordPress.

## Instalação

1. Copie a pasta `investcrm` para o diretório `wp-content/plugins/` da sua instalação WordPress.
2. Acesse o painel administrativo e ative o plugin “InvestCRM”.
3. Execute a ação “Salvar links permanentes” após a ativação para garantir que o portal do cliente esteja acessível.

## Principais módulos

- **CRM completo** com tipos de post personalizados para clientes, negócios, tarefas e produtos, campos financeiros e taxonomia de estágios.
- **Kanban de pipeline** no painel administrativo com atualização via REST API.
- **Automação e tarefas recorrentes** com cron interno, gatilhos por mudança de estágio e notificações por e-mail.
- **Portal do cliente** com resumo de AUM, reuniões, tarefas e download de documentos.
- **Compliance e LGPD** com trilha de auditoria, registro de consentimento e perfis de acesso dedicados.
- **Configurações de automação** via painel “Automação” para ajustar follow-up automático e link padrão de WhatsApp.

Consulte [`docs/architecture-review.md`](docs/architecture-review.md) para conhecer a visão de evolução do produto e diretrizes arquiteturais.

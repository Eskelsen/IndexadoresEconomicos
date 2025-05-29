# Microframework

## Estrutura de Projeto Derivado com Git (Upstream)

Este documento explica como criar um projeto derivado do Microframework, permitindo manter suas personalizações e, ao mesmo tempo, atualizar o projeto com melhorias do Microframework sempre que necessário.

---

## Conceito

- **Microframework**: Projeto original (repositório upstream). Nunca é alterado diretamente.
- **Novo projeto**: Projeto derivado, onde você faz suas implementações e extensões.

---

## 1. Clonar o Microframework como base do novo projeto

```bash
git clone https://github.com/Mfwks/Microframework.git .
```

---

## 2. Reconfigurar os remotes

Renomeie o remote original para `upstream` e adicione o seu repositório como `origin`.

```bash
git remote rename origin upstream
git remote add origin https://github.com/nome_usuario/novo_projeto.git
```

---

## 🚀 3. Criar sua branch de trabalho no novo projeto

```bash
git checkout -b master
# Faça suas alterações e commits normalmente
git push -u origin master
```

---

## 4. Puxar atualizações do Microframework (upstream)

Sempre que quiser atualizar o novo projeto com as novidades do Microframework:

```bash
git fetch upstream
git merge upstream/master
```

> Para visualizar as mudanças antes de mesclar:
```bash
git log HEAD..upstream/master
```

---

## Boas práticas

- **Evite editar diretamente os arquivos herdados do Microframework.**
- Prefira criar extensões, complementos ou sobrescritas em arquivos separados.
- Resolva conflitos com atenção ao fazer merge.

---

## Estrutura de pastas

```
app/
├── apis/          # Tratamento de API's
├── cmds/          # Scripts de commands
├── crons/         # Endpoints de crons
├── functions/     # Funções do projeto
├── infra/         # Core de dados do Microframework
├── libs/          # Pacotes do projeto
├── packs/         # Funções primárias do Microframework
├── streams/       # Streams e sheetviews
├── tmp/           # Arquivos temporários
├── views/         # Arquivos de templates
└── webhooks/      # Configurações do novo projeto
```

---

## Vantagens desse método

- Manutenção facilitada com atualizações diretas do projeto original.
- Total liberdade para evoluir o projeto derivado.
- Sem complicações com submódulos ou subtree.

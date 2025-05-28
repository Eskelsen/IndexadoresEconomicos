# Microframework

# Estrutura de Projeto Derivado com Git (Upstream)

Este documento explica como criar um projeto derivado do Microframework, permitindo manter suas personalizações e, ao mesmo tempo, atualizar o projeto com melhorias do Microframework sempre que necessário.

---

## Conceito

- **Microframework**: Projeto original (repositório upstream). Nunca é alterado diretamente.
- **Novo projeto**: Projeto derivado, onde você faz suas implementações e extensões.

---

## 1. Clonar o Microframework como base do novo projeto

```bash
git clone https://github.com/exemplo/projetoA.git projetoB
cd projetoB
```

---

## 2. Reconfigurar os remotes

Renomeie o remote original para `upstream` e adicione o seu repositório como `origin`.

```bash
git remote rename origin upstream
git remote add origin https://github.com/seuusuario/projetoB.git
```

---

## 🚀 3. Criar sua branch de trabalho no novo projeto

```bash
git checkout -b main  # ou o nome de sua branch principal
# Faça suas alterações e commits normalmente
git push -u origin main
```

---

## 4. Puxar atualizações do Microframework (upstream)

Sempre que quiser atualizar o novo projeto com as novidades do Microframework:

```bash
git fetch upstream
git merge upstream/main
```

> Para visualizar as mudanças antes de mesclar:
```bash
git log HEAD..upstream/main
```

---

## Boas práticas

- **Evite editar diretamente os arquivos herdados do Microframework.**
- Prefira criar extensões, complementos ou sobrescritas em arquivos separados.
- Resolva conflitos com atenção ao fazer merge.

---

## Sugestão de estrutura de pastas

```
projetoB/
├── core/          # Código herdado do Microframework (não modificar)
├── custom/        # Extensões e personalizações próprias
├── public/        # Arquivos públicos ou expostos (HTML, assets)
├── config/        # Configurações do novo projeto
└── README.md
```

---

## Vantagens desse método

- Manutenção facilitada com atualizações diretas do projeto original.
- Total liberdade para evoluir o projeto derivado.
- Sem complicações com submódulos ou subtree.